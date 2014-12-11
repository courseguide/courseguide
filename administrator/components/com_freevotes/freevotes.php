<?php
/*
* @name freevote 1.0
* Created By Guarneri Iacopo
* http://www.the-html-tool.com/
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

echo"<style>iframe{overflow:hidden; border: 0 none;}</style>";
if(JRequest::getVar('task', '', 'get')=="domanda"){
	echo'<link rel="stylesheet" href="templates/isis/css/template.css" type="text/css" />';
	include("domanda.php");
	JFactory::getApplication()->close();
}
else if(JRequest::getVar('task', '', 'get')=="risposta"){
	echo'<link rel="stylesheet" href="templates/isis/css/template.css" type="text/css" />';
	include("risposta.php");
	JFactory::getApplication()->close();
}
else
{
	function imgDelete(){
		if(version_compare(JVERSION,'3') < 0){
			return("toolbar/icon-32-cancel.png");
		}else{
			//maggiore della 2.5
			return("admin/publish_r.png");
		}
	}
	
	$domande=JText::_('COM_FREEVOTES_DOMANDE');
	$risposte=JText::_('COM_FREEVOTES_RISPOSTE');
	$inserisci_domanda=JText::_('COM_FREEVOTES_INSERISCI_DOMANDA');
	$inserisci_risposta=JText::_('COM_FREEVOTES_INSERISCI_RISPOSTA');
	$free_votes=JText::_('COM_FREEVOTES_FREE_VOTES');
	$colore=JText::_('COM_FREEVOTES_COLORE');
	$cancella=JText::_('COM_FREEVOTES_CANCELLA');
	$cerca=JText::_('COM_FREEVOTES_CERCA');

	$app = JFactory::getApplication();
	$template_name = $app->getTemplate();
	$database = JFactory::getDBO();

	JToolBarHelper::title($free_votes);
	JHTML::_('behavior.modal');

		//per cambiar l'immagine dare il nome di una presente nella cartella administrator/templates/bluestork/images/toolbar
		if(JRequest::getVar('view', '', 'get')=="domande" || JRequest::getVar('view', '', 'get')==""){
			$bar= JToolBar::getInstance( 'toolbar' )->appendButton( 'Popup', 'new', $inserisci_domanda, 'index.php?option=com_freevotes&task=domanda', 300, 80 );
		}
		if(JRequest::getVar('view', '', 'get')=="risposte"){
			$bar= JToolBar::getInstance( 'toolbar' )->appendButton( 'Popup', 'new', $inserisci_risposta, 'index.php?option=com_freevotes&task=risposta', 400, 200 );
		}
	 
		JSubMenuHelper::addEntry(
			$domande,
			'index.php?option=com_freevotes&view=domande'
		);
		JSubMenuHelper::addEntry(
			$risposte,
			'index.php?option=com_freevotes&view=risposte'
		);

	if(JRequest::getVar('nome_domanda', '', 'post')!="")
	{
		if(JRequest::getVar('id', '', 'get')!=""){
			$query='UPDATE #__free_votes_domande SET nome='.$database->quote(JRequest::getVar('nome_domanda', '', 'post')).' WHERE id='.JRequest::getVar('id', '', 'get');
		}
		else{
			$query="INSERT INTO #__free_votes_domande (nome) VALUES (".$database->quote(JRequest::getVar('nome_domanda', '', 'post')).")";
		}
		$database->setQuery($query);
		$results=$database->query();
	}
	if(JRequest::getVar('nome_risposta', '', 'post')!="" && JRequest::getVar('seleziona_domande', '', 'post')!="" && JRequest::getVar('colore', '', 'post')!="")
	{
		if(JRequest::getVar('id', '', 'get')!=""){
			$query='UPDATE #__free_votes_risposte SET nome='.$database->quote(JRequest::getVar('nome_risposta', '', 'post')).', colore="'.JRequest::getVar('colore', '', 'post').'", domanda="'.JRequest::getVar('seleziona_domande', '', 'post').'" WHERE id='.JRequest::getVar('id', '', 'get');
		}
		else{
			$query="INSERT INTO #__free_votes_risposte (nome, colore, domanda) VALUES (".$database->quote(JRequest::getVar('nome_risposta', '', 'post')).", '".JRequest::getVar('colore', '', 'post')."', ".JRequest::getVar('seleziona_domande', '', 'post').")";
		}
		$database->setQuery($query);
		$results=$database->query();
	}
	if(JRequest::getVar('cancella_domanda', '', 'get')!="")
	{
		$query="DELETE FROM #__free_votes_domande WHERE id=".JRequest::getVar('cancella_domanda', '', 'get');
		$database->setQuery($query);
		$results=$database->query();
	}
	if(JRequest::getVar('cancella_risposta', '', 'get')!="")
	{
		$query="DELETE FROM #__free_votes_risposte WHERE id=".JRequest::getVar('cancella_risposta', '', 'get');
		$database->setQuery($query);
		$results=$database->query();
	}
	if(JRequest::getVar('view', '', 'get')=="domande" || JRequest::getVar('view', '', 'get')=="")
	{
		$query = "SELECT * FROM #__free_votes_domande";
		$database->setQuery($query);
		$results = $database->loadAssocList();

		$i=1;
		echo"<table class='adminlist table table-striped'><thead><tr><td><strong>Id</strong></td><td><strong>".$domande."</strong></td><td><strong>".$cancella."</strong></td></tr></thead>";
		foreach($results as $result)
		{
			if($i%2==0){$row="row0";}
			else{$row="row1";}
			echo "<tr class='".$row."'><td>".$result['id']."</td><td>
			<a class='modal' rel='{handler: \"iframe\", size: {x: 400, y: 80}, onClose: function() {}}' href='index.php?option=com_freevotes&task=domanda&id=".$result['id']."'>".$result['nome']."</a>
			</td><td><a href='index.php?option=com_freevotes&cancella_domanda=".$result['id']."'><div style='background-repeat:no-repeat; width:32px; height:32px; background-image:url(\"templates/".$template_name."/images/".imgDelete()."\");'></div></a></td></tr>";
			$i++;
		}
		echo"</table>";
	}
	if(JRequest::getVar('view', '', 'get')=="risposte")
	{
		//creo i filtri
		echo"<form method='post'>
			<table><tr><td>".$cerca." ".$risposte."</td><td><input type='text' name='cerca_risposte' value='".JRequest::getVar('cerca_risposte', '', 'post')."'></td></tr>
			<tr><td>".$cerca." ".$domande."</td><td><input type='text' name='cerca_domande' value='".JRequest::getVar('cerca_domande', '', 'post')."'></td></tr>
			<tr><td><input type='submit' value='".$cerca."'></td><td></td></tr></table>
		</form>";
		
		//stampo le risposte
		if(JRequest::getVar('cerca_risposte', '', 'post')=="" && JRequest::getVar('cerca_domande', '', 'post')=="")
			$query = "SELECT * FROM #__free_votes_risposte";
		else if(JRequest::getVar('cerca_risposte', '', 'post')!="" && JRequest::getVar('cerca_domande', '', 'post')=="")
			$query = "SELECT * FROM #__free_votes_risposte WHERE nome LIKE '%".JRequest::getVar('cerca_risposte', '', 'post')."%'";
		else if(JRequest::getVar('cerca_risposte', '', 'post')=="" && JRequest::getVar('cerca_domande', '', 'post')!="")
			$query = "SELECT * FROM #__free_votes_risposte AS R, #__free_votes_domande AS D WHERE D.id=R.domanda AND D.nome LIKE '%".JRequest::getVar('cerca_domande', '', 'post')."%'";
		else if(JRequest::getVar('cerca_risposte', '', 'post')!="" && JRequest::getVar('cerca_domande', '', 'post')!="")
			$query = "SELECT * FROM #__free_votes_risposte AS R, #__free_votes_domande AS D WHERE D.id=R.domanda AND D.nome LIKE '%".JRequest::getVar('cerca_domande', '', 'post')."%' AND R.nome LIKE '%".JRequest::getVar('cerca_risposte', '', 'post')."%'";
		
		$database->setQuery($query);
		$results = $database->loadAssocList();

		$i=1;
		echo"<table class='adminlist table table-striped'><thead><tr><td><strong>".$risposte."</strong></td><td><strong>".$domande."</strong></td><td><strong>".$colore."</strong></td><td><strong>".$cancella."</strong></td></tr></thead>";
		foreach($results as $result)
		{
			$query_d = "SELECT nome FROM #__free_votes_domande WHERE id=$result[domanda]";
			$database->setQuery($query_d);
			$results_d = $database->loadAssocList();
		
			if($i%2==0){$row="row0";}
			else{$row="row1";}
			echo "<tr class='".$row."'><td>
			
			<a class='modal' rel='{handler: \"iframe\", size: {x: 400, y: 200}, onClose: function() {}}' href='index.php?option=com_freevotes&task=risposta&id=".$result['id']."'>".$result['nome']."
			
			</a></td><td>".$results_d[0]['nome']."</td><td>".$result['colore']."<div style='width:30px; height:30px; background:".$result['colore']."'></div></td><td><a href='index.php?option=com_freevotes&view=risposte&cancella_risposta=".$result['id']."'><div style='background-repeat:no-repeat; width:32px; height:32px; background-image:url(\"templates/".$template_name."/images/".imgDelete()."\");'></div></a></td></tr>";
			$i++;
		}
		echo"</table>";
	}
}
