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

//Maximum of allowed answers
$db = JFactory::getDbo();
$db->setQuery(' SELECT COUNT(`id`) FROM `#__jvotesystem_answers` WHERE `autor_id`= '.$db->quote($this->user->id).' AND `no_spam_admin`=0 AND `box_id` = '.$db->quote($box->id));
$acount = $db->loadResult();
if($acount < $box->tmpl_galleryvote_max_uploads_per_user || $this->access->isAdmin($box, true)) {
	//Upload Skript for a new answer
	$fileName = $_FILES['answerimg']['name'];
	$targetDir = JPATH_SITE.DS."images".DS."jvotesystem".DS.$oAr["answer"];
	
	// Clean the fileName for security reasons
	$valid_chars_regex = '.A-Z0-9_ !()+={}\[\]\',~`-';
	$fileName = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", $fileName);
	
	$extension_whitelist = array("jpg", "jpeg", "png");
	// Validate file extension
	$file_extension = JFile::getExt($targetDir . DS . $fileName);
	$is_valid_extension = false;
	foreach ($extension_whitelist as $extension) {
		if (strtolower($file_extension) == $extension) {
			$is_valid_extension = true;
			break;
		}
	}
	if (!$is_valid_extension) {
		$oAr['failed'] = true;
		$oAr['error'] = JText::_("JVS_TMPL_GV_Invalid_file_extension");
	} else {
		if (isset($_FILES['answerimg']['tmp_name']) && is_uploaded_file($_FILES['answerimg']['tmp_name'])) {
			//Check upload size
			if(($_FILES['answerimg']['size']/1024) > $box->tmpl_galleryvote_max_upload_size) {
				$oAr['failed'] = true;
				$oAr['error'] = sprintf(JText::_("JVS_TMPL_GV_IMAGE_TOO_BIG"), $box->tmpl_galleryvote_max_upload_size);
			} else {
				// Read binary input stream and append it to temp file
				$in = JFile::read($_FILES['answerimg']['tmp_name']);
				if(JFile::exists($targetDir.DS.$fileName))
					JFile::delete($targetDir.DS.$fileName);
				if ($in) {
					JFile::write($targetDir.DS.$fileName, $in);
						
					require_once(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'templates'.DS.'elements'.DS.'main'.DS.'galleryvote'.DS.'jm.thumb.php');
						
					$bild = new JMThumb();
					$bild->create($targetDir.DS.$fileName);
					$bild->setQuality(90);
					$bild->setMaxHeight($box->tmpl_galleryvote_popup_max_height);
					$bild->setMaxWidth($box->tmpl_galleryvote_popup_max_width);
					$result = $bild->resize();
					JFile::delete($targetDir.DS.$fileName);
					$bild->save($targetDir.DS."large.jpg");
						
					if(!$result) {
						$oAr['failed'] = true;
						$oAr['error'] = JText::_("JVS_TMPL_GV_FAILED_TO_RESIZE_IMAGE");
					} else {
						//MiniThumbs erstellen
						switch($box->tmpl_galleryvote_preview_resize_type) {
							case "cube":
								$bild->cubecut($box->tmpl_galleryvote_preview_max_width, $box->tmpl_galleryvote_preview_max_height);
								break;
							case "resize":
								$bild->setMaxHeight($box->tmpl_galleryvote_preview_max_height);
								$bild->setMaxWidth($box->tmpl_galleryvote_preview_max_width);
								$bild->resize();
								break;
						}
				
						$bild->save($targetDir.DS."medium.jpg");
				
						$bild->cube(50, 2);
						$bild->save($targetDir.DS."small.jpg");
					}
					$bild->destroy();
				} else {
					$oAr['failed'] = true;
					$oAr['error'] = JText::_("JVS_TMPL_GV_FAILED_TO_OPEN_INPUT_STREAM");
				}
			}
			@unlink($_FILES['answerimg']['tmp_name']);
		} else {
			$oAr['failed'] = true;
			$oAr['error'] = JText::_("JVS_TMPL_GV_FAILED_TO_MOVE_UPLOADED_FILE");
		}
	}
} else {
	$oAr['failed'] = true;
	$oAr['error'] = JText::_("JVS_TMPL_GV_REACHED_MAXIMUM_ANSWERS_PER_USER");
}



?>