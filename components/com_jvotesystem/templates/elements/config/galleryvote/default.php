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

$pars = array(	
			"tmpl_galleryvote_preview_max_height" => 220, 
			"tmpl_galleryvote_preview_max_width" => 220, 
			"tmpl_galleryvote_preview_resize_type" => "cube", 
			"tmpl_galleryvote_popup_max_height" => 800, 
			"tmpl_galleryvote_popup_max_width" => 800, 
			"tmpl_galleryvote_max_upload_size" => 2048, 
			"tmpl_galleryvote_max_uploads_per_user" => 5,
			"tmpl_galleryvote_main_color" => "#07B7E3"
		);

foreach($pars AS $key => $value) {
	if(!isset($box->$key)) $box->$key = $value;
}

?>