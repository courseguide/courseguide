<?php
/*
* @name freevote 1.0
* Created By Guarneri Iacopo
* http://www.the-html-tool.com/
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$solo_registrati_error=JText::_('MOD_FREEVOTES_SOLO_REGISTRATI_ERROR');
$nuovo_voto_mex=JText::_('MOD_FREEVOTES_NUOVO_VOTO_MEX');
$hai_gia_votato_mex=JText::_('MOD_FREEVOTES_HAI_GIA_VOTATO_MEX');
$voti_aggiornati_mex=JText::_('MOD_FREEVOTES_VOTI_AGGIORNATI_MEX');
$errore_range=JText::_('MOD_FREEVOTES_ERRORE_RANGE');
$errore_campi_vuoti=JText::_('MOD_FREEVOTES_ERRORE_CAMPI_VUOTI');
$voti_txt=JText::_('MOD_FREEVOTES_VOTI_TXT');
$vota_txt=JText::_('MOD_FREEVOTES_VOTA_TXT');
$inserisci=JText::_('MOD_FREEVOTES_INSERISCI');
$inserisci_nuova=JText::_('MOD_FREEVOTES_INSERISCI_NUOVA');
$oggetto_mail=JText::_('MOD_FREEVOTES_OGGETTO_MAIL');
$corpo_mail=JText::_('MOD_FREEVOTES_CORPO_MAIL');

$Jroot=JURI::base();
$id_user = JFactory::getUser()->get('id');
echo"
	<link rel='stylesheet' href='".$Jroot."modules/mod_freevotes/css/style.css' type='text/css' />
	<script>Jroot='".$Jroot."';</script>
";

if(!function_exists('jomail')) {
	function jomail($destinatario, $oggetto, $corpo)
	{
		$mailer = JFactory::getMailer();
		
		//mittente
		$config = JFactory::getConfig();
		$sender = array( 
		$config->getValue( 'config.mailfrom' ),
		$config->getValue( 'config.fromname' ) );

		$mailer->setSender($sender);
		
		//destinatario o array destinatari
		$recipient = $destinatario;
		$mailer->addRecipient($recipient);
		
		//messaggio e oggetto
		$mailer->setSubject($oggetto);
		$mailer->setBody($corpo);
		
		//invia mail
		$send = $mailer->Send();
		/*if ( $send !== true ) {
			JError::raiseWarning( 100, 'Error sending email: ' . $send->message);
		} else {
			JFactory::getApplication()->enqueueMessage('Mail sent');
		}*/
	}
}

//recupero i valori del modulo
$domanda 		= $params->get('domanda', '');
$range_da 		= $params->get('range_da', '0');
$range_a		= $params->get('range_a', '5');
$type_graph		= $params->get('type_graph', 'pie');
$label			= $params->get('label', '1');
$legend			= $params->get('legend', '0');
$img_vuoto		= $params->get('img_vuoto', 'modules/mod_freevotes/images/vuoto.png');
$img_pieno		= $params->get('img_pieno', 'modules/mod_freevotes/images/pieno.png');
$height_modulo	= $params->get('height_modulo', '350');

//errori
if($range_da<0 || $range_a<0 || !is_numeric($range_da) || !is_numeric($range_a) || $range_a<=$range_da){
	echo"<script type='text/javascript'>alert('".$errore_range."');</script>";
}
if($domanda==""){echo"<script type='text/javascript'>alert('".$errore_campi_vuoti."');</script>";}
//fine recupero i valori del modulo

//inserisco nuova risposta
$database = JFactory::getDBO();
if($id_user!=0 && JRequest::getVar('nuova_risposta', '', 'post')!="" && $params->get('aggiungi_risposte', '1')==1){
	$r=dechex(rand(0,255)); if(strlen($r)<2){$r.=0;}
	$g=dechex(rand(0,255)); if(strlen($g)<2){$g.=0;}
	$b=dechex(rand(0,255)); if(strlen($b)<2){$b.=0;}
	
	$query='INSERT INTO #__free_votes_risposte (nome,colore,domanda) VALUES ('.$database->quote(JRequest::getVar('nuova_risposta', '', 'post')).',"#'.$r.$g.$b.'",'.$domanda.')';
	$database->setQuery($query);
	$results=$database->query();
	
	//invio mail all'admin
	$config=new JConfig();

	jomail($config->mailfrom, str_replace("[site]",$config->sitename,$oggetto_mail), str_replace("[response]",JRequest::getVar('nuova_risposta', '', 'post'),str_replace("[site]",$config->sitename,$corpo_mail)));
}else if($id_user==0 && JRequest::getVar('nuova_risposta', '', 'post')!="" && $params->get('aggiungi_risposte', '1')==1){
	echo"<script type='text/javascript'>alert('".$solo_registrati_error."');</script>";
}
//fine inserisco nuova risposta

//stampo a video
$query = "SELECT * FROM #__free_votes_domande WHERE id=$domanda";
$database->setQuery($query);
$results = $database->loadAssocList();

$view_graph=""; if($params->get('view_graph', '1')==0){$view_graph="display:none;";}
echo "<div id='freevotes_titolo'>".$results[0]['nome']."</div>
<div id='freevotes_voti".$domanda."' style='".$view_graph." height:".$height_modulo."px;'></div>";

$query = "SELECT * FROM #__free_votes_risposte WHERE domanda=$domanda";
$database->setQuery($query);
$results = $database->loadAssocList();

echo "<div id='freevotes_votes_container'>";
echo "<div id='freevotes_risposta'>";
foreach($results as $campo){
	echo "
	<div class='freevotes_altezza'>".$campo['nome']."</div>";
}
echo "</div><div id='freevotes_percentuale'>";
foreach($results as $campo){
	echo "
	<div class='freevotes_altezza'>";
			for($i=$range_da;$i<=$range_a;$i++)
				echo "<div class='freevotes_voto freevotes_voto".$domanda."' id='_".$campo['id']."_".$i."'>".$i."</div>";
	echo"</div>";
}
echo"</div></div>";

if($params->get('aggiungi_risposte', '1')==1){
	echo"<form method='post' id='freevotes_ins_nuova_risp'>
		".$inserisci_nuova." <input type='text' name='nuova_risposta' id='freevotes_nuova_risposta'>
		<input id='freevotes_inserisci_button' class='freevotes_bottoni' type='submit' value='".$inserisci."'>
	</form>";
}

echo"<p style='float:right; clear:left; margin-top:10px;'><span style='font-size:10px;'>Click on the score star to vote</span></p>";

//fine stampo a video
echo '<script type="text/javascript">var id_user="'.$id_user.'", voti_txt="'.$voti_txt.'", label='.$label.', legend='.$legend.', type_graph="'.$type_graph.'";</script>';
?>
<script>
   window.jQuery || document.write('<script src="http://code.jquery.com/jquery-latest.js"> \x3C/script>');
</script>
<?php echo '<script type="text/javascript" src="'.$Jroot.'modules/mod_freevotes/highcharts.js"></script>'; ?>
<script type="text/javascript">
	//creazione del grafico
		
	if(label==1){label=true;}else{label=false;}
	if(legend==1){legend=true;}else{legend=false;}
	
	var chart;
	( function($) {//riga per evitare il conflitto della variabile $
	
	function aggiorna_grafico(){
		var browserData=[];
		
		$.ajax({
			url: "index.php?option=com_freevotes",
			type: "POST",
			data: {aggiorna:<?php echo $domanda; ?>},
			success: function(e){
				browserData=[];
				eval(e);
			
				$(document).ready(function() {
					chart = new Highcharts.Chart({
						chart: {
							renderTo: 'freevotes_voti<?php echo $domanda; ?>',
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							backgroundColor:"rgba(0,0,0,0)"
						},
						title: {
							text: ''
						},
						tooltip: {
							formatter: function() {
								if(type_graph=="pie"){
									return '<strong>'+ this.point.name +'</strong>: '+ Math.floor(this.percentage) +' %<br />'+this.y+": "+voti_txt;
								}else{
									return '<strong>'+ this.point.name +'</strong>:<br />'+this.y+": "+voti_txt;
								}
							}
						},
						plotOptions: {
							allowPointSelect: true,
							cursor: 'pointer',
						},
						legend: {
							enabled: legend
						},
						series: [{
							type: type_graph,
							size: '70%',
							data: browserData,
							dataLabels: {
								enabled: label,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<strong>'+ this.point.name +'</strong>: '+this.y+": "+voti_txt;
								}
							}
						}]
					});
				});
			}
		});
	}
	aggiorna_grafico();
	
	richieste_sovrapposte=0;
	$(".freevotes_voto<?php echo $domanda; ?>").click(function(){
		if(id_user==0){
			alert("<?php echo $solo_registrati_error; ?>");
		}
		else{
			richieste_sovrapposte++;
			
			id=$(this).attr("id").split("_");
			risposta1=id[1];
			voto1=id[2];
			
			//sostituisco il grafico con l'immagine di caricamento
			$("#freevotes_voti<?php echo $domanda; ?>").html("<img style='width:<?php echo $params->get('width_wait', '50%'); ?>; max-width:300px; display:block; margin:0 auto;' src='<?php echo $Jroot; ?>modules/mod_freevotes/images/wait.gif' />");
			
			$.ajax({
				url: "index.php?option=com_freevotes",
				type: "POST",
				data: {risposta: risposta1, voto: voto1},
				success: function(e){
					e=e.split("[RETURN]");
					dati_inviati=""; try{dati_inviati=JSON.parse(e[1]);}catch(e){}
					e=e[0];
					
					if(e=="ERROR_USER"){
						alert("<?php echo $solo_registrati_error; ?>");
					}
					if(e=="ERROR_VOTO"){
						alert("<?php echo $hai_gia_votato_mex; ?>");
					}
					if(e=="AGGIORNATO" || e=="NUOVO_VOTO"){
						//rimuove la grafica dai voti pieni
						if(dati_inviati!=""){
							for(i=<?php echo $range_da; ?>;i<=<?php echo $range_a; ?>;i++){
								$(".freevotes_voto<?php echo $domanda; ?>#_"+dati_inviati.risposta+"_"+i).css("background-image","url(<?php echo $Jroot.$img_vuoto; ?>)")
							}
							//disegna i voti pieni
							for(i=<?php echo $range_da; ?>;i<=dati_inviati.voto;i++){
								$(".freevotes_voto<?php echo $domanda; ?>#_"+dati_inviati.risposta+"_"+i).css("background-image","url(<?php echo $Jroot.$img_pieno; ?>)")
							}
						}
					}
					//senza questo sistema se uno cliccava su 5 voti abbastanza velocemente il grafico lampeggerà (viene cancellato e ridisegnato) per 5 volte
					setTimeout(function(){
						richieste_sovrapposte--;
						if(richieste_sovrapposte==0){
							aggiorna_grafico();
						}
					},1000);
				}
			});
		}
	}).css("background-image","url(<?php echo $Jroot.$img_vuoto; ?>)");	
	
	} ) ( jQuery );//riga per evitare il conflitto della variabile $
</script>
<div style="width:100%; height:0px; clear:left;"></div>
