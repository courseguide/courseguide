<?php
/**
 * ------------------------------------------------------------------------
 * JA Google Chart Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die( 'Restricted access' );

class jaimportcsv{   
    public function import(){
		$return = array('status'=>0, 'data'=>'', 'message'=>JText::_('MOD_JA_GOOGLE_CHART_ERROR'));
		$ext = @JFile::getExt($_FILES['file']['name']);
		
		if(strtolower($ext) != 'csv'){
			$return['message'] = JText::_('MOD_JA_GOOGLE_CHART_FILE_TYPE_INVALID');
			return $return;
		}
		if ($_FILES['file']['error'] > 0) {
			$retrun['message'] = $_FILES['file']['error'];
		}
		else {
			$return['status'] = 1;
			$return['data'] = trim(file_get_contents($_FILES["file"]["tmp_name"]));
			$return['message'] = JText::sprintf('MOD_JA_GOOGLE_CHART_IMPORT_CSV_DONE', $_FILES["file"]["name"], $_FILES["file"]["type"], $_FILES["file"]["size"] / 1024);
		}
		return $return;
	}
}