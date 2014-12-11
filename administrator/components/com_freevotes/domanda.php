<?php

/*
* @name freevote 1.0
* Created By Guarneri Iacopo
* http://www.the-html-tool.com/
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if(JRequest::getVar('id', '', 'get')!=""){
	$database = JFactory::getDBO();
	$database->setQuery("SELECT * FROM #__free_votes_domande WHERE id=".JRequest::getVar('id', '', 'get'));
	$results = $database->loadAssocList();
}

echo"
<style>
	#toolbar-box, #border-top, #header-box, #footer{display:none;}
	#content-box, .submenu-box, div.m, body, html{border:none; width:240px;}
</style>
<form method='post' action='index.php?option=com_freevotes&id=".JRequest::getVar('id', '', 'get')."' target='_parent'>
	".JText::_('COM_FREEVOTES_NOME')." <input type='text' name='nome_domanda' value='".htmlentities(@$results[0]['nome'],ENT_QUOTES)."'>
	<input type='submit' value='".JText::_('COM_FREEVOTES_SALVA')."'>
</form>";
