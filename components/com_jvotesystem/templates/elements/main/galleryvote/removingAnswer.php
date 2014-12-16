<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

$obj = $this->getAnswer($answer, false);

if($obj) { //Bilder entfernen
	$targetDir = JPATH_SITE.DS."images".DS."jvotesystem".DS.$obj->id;
	if(JFolder::exists($targetDir)) JFolder::delete($targetDir);
}

?>