<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$return = array();
foreach ($this->results as $result) {
	$tmp = new stdClass();
	$tmp->title = $result->title;
	$tmp->link = $result->link;
	$return[] = $tmp;
}

header('Content-Type: application/json');
echo json_encode($return);
die();