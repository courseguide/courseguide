<?php

/*
* @name freevote 1.0
* Created By Guarneri Iacopo
* http://www.the-html-tool.com/
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$database = JFactory::getDBO();
if(JRequest::getVar('id', '', 'get')!=""){
	$database->setQuery("SELECT * FROM #__free_votes_risposte WHERE id=".JRequest::getVar('id', '', 'get'));
	$results = $database->loadAssocList();
}

	if(JRequest::getVar('id', '', 'get')==""){
		$r=dechex(rand(0,255)); if(strlen($r)<2){$r.=0;}
		$g=dechex(rand(0,255)); if(strlen($g)<2){$g.=0;}
		$b=dechex(rand(0,255)); if(strlen($b)<2){$b.=0;}
		$rgb="#".$r.$g.$b;
	}
	else{$rgb=$results[0]['colore'];}

echo"
<style>
	#toolbar-box, #border-top, #header-box, #footer{display:none;}
	#content-box, .submenu-box, div.m, body, html{border:none; width:240px;}
</style>
<table>
	<form method='post' action='index.php?option=com_freevotes&view=risposte&id=".JRequest::getVar('id', '', 'get')."' target='_parent'>
		<tr><td>".JText::_('COM_FREEVOTES_NOME')."</td><td><input type='text' name='nome_risposta' value='".htmlentities(@$results[0]['nome'],ENT_QUOTES)."'></td></tr>
		<tr><td>".JText::_('COM_FREEVOTES_COLORE')."</td><td><input type='text' name='colore' id='colore' value='".$rgb."'> <span id='genera' style='border:1px solid #000; cursor:pointer; padding:4px; position:absolute; background:#fff;'>".JText::_('COM_FREEVOTES_GENERA')."</span></td></tr>
		<tr><td>".JText::_('COM_FREEVOTES_DOMANDE')."</td><td><select name='seleziona_domande'>";
		
		$query = "SELECT * FROM #__free_votes_domande";
		$database->setQuery($query);
		$results1 = $database->loadAssocList();
		foreach($results1 as $result)
		{
			if(@$results[0]['domanda']==$result['id']){$sel="selected='selected'";}else{$sel="";}
			echo "<option ".$sel." value='".$result['id']."'>".$result['nome']."</option>";
		}
		
		echo"
		</select></td></tr>
		<tr><td><input type='submit' value='".JText::_('COM_FREEVOTES_SALVA')."'></td><td></td></tr>
	</form>
	</table>
<br /><div id='colore_prew' style='width:30px; height:30px; background:".$rgb.";'></div>";
?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
$("#colore").keyup(function(){
	$("#colore_prew").css("background",$(this).val());
});

function generaColore(){
	col=Math.ceil(Math.random()*255);
	col=col.toString(16);
	if(col.length<2){col+="0";}
	return col;
}

$("#genera").click(function(){
	var r=generaColore(); 
	var g=generaColore(); 
	var b=generaColore(); 
	$("#colore").val("#"+r+g+b);
	$("#colore_prew").css("background","#"+r+g+b);
});
</script>
