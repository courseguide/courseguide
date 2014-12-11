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

//Alle Divs durch Spans ersetzen.. wegen Inline-Regeln in P-Elementen :/
$out = str_replace("<div ", "<span ", $out);
$out = str_replace("<div>", "<span>", $out);
$out = str_replace("</div>", "</span>", $out);

jVSConnect::getInstance()->setBBCode($jvs_easyquestion_bbcode);
?>