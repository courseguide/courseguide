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

jimport( 'joomla.utilities.date' );

class VBCharts
{
	//Variablen
	var $db, $user, $document;
	
	private function __construct() {
		//Feste Variablen laden
		$this->user =& VBUser::getInstance();
		$this->document = & JFactory::getDocument();
		$this->db = & JFactory::getDBO();
		$this->general =& VBGeneral::getInstance();
		$this->vbparams =& VBParams::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBCharts();
		}
		return $instance;
	}
	
	function getFrontendChart($boxID) {
		$this->general->VDTS('com_jvotesystem\classes\charts.class.php getFrontendChart Call (deprecated function) PLEASE CHECK!');
	}
	
	function getFrontendChart2($boxID) {
		$this->vote =& VBVote::getInstance();
		
		VBCharts::addchartjs('corechart');
		$addjs =  'google.setOnLoadCallback(draw'.$boxID.');'
		. 'function draw'.$boxID.'() {'
		. 'var data = new google.visualization.DataTable();'
		. 'data.addColumn("string", "Answer");'
		. 'data.addColumn("number", "Votes");'
		. 'data.addRows([';
		//Parameter setzen
		$activate_bbcode = $this->vbparams->get("activate_bbcode");
		$this->vbparams->set("global", "activate_bbcode", 0);
		$general_published_bbcode = $this->vbparams->get("general_published_bbcode");
		$this->vbparams->set("global", "general_published_bbcode", 1);
		
		//Daten aus Datenbank laden
		$data = $this->vote->getData($boxID, false);
		$i = 0;
		$colors = "";
		foreach($data->answers AS $answer) {
			$answer->answer = $this->general->shortText($answer->answer, 100, false);
			$answer->answer = str_replace( "\r\n", ' ', $answer->answer);
			$answer->answer = str_replace( "\n", ' ', $answer->answer);
			$answer->answer = str_replace('"', "&quot;", $answer->answer);
			
			if ($i != 0) {$addjs .=','; $colors .= ",";}
			$addjs .= '["'.$answer->answer.'",'.$answer->votes.']';
			$colors .= '"#'.($answer->color != "" ? $answer->color : "000000").'"';
			$i++;
		}
		
		$addjs .= ']);'
				. 'var options = {"width":793,"height":468, "colors":['.$colors.']};'
				. 'var chart = new google.visualization.PieChart(document.getElementById("gchart'.$boxID.'"));'
				. 'chart.draw(data, options);}';
				
		$document = & JFactory::getDocument();
		$document->addScriptDeclaration($addjs);
		$out = '<div id="gchart'.$boxID.'"></div>';
		
		$this->vbparams->set("global", "activate_bbcode", $activate_bbcode);
		$this->vbparams->set("global", "general_published_bbcode", $general_published_bbcode);
		
		return $out;
	}
	
	function getFrontendChartJSON($boxID) {
		$this->vote =& VBVote::getInstance();
		//Parameter setzen
		$activate_bbcode = $this->vbparams->get("activate_bbcode");
		$this->vbparams->set("global", "activate_bbcode", 0);
		$general_published_bbcode = $this->vbparams->get("general_published_bbcode");
		$this->vbparams->set("global", "general_published_bbcode", 1);
		
		//Daten aus Datenbank laden
		$data = $this->vote->getData($boxID, false);
		
		$xml = array();
		$xml['answers']=array();
		$xml['values']=array();
		$xml['ids']=array();
		$xml['colors']=array();
		
		foreach($data->answers AS $answer) {
			$answer->answer = $this->general->shortText($answer->answer, 80, false);
			$answer->answer = str_replace( "\r\n", ' ', $answer->answer);
			$answer->answer = str_replace( "\n", ' ', $answer->answer);
			$answer->answer = str_replace('"', "&quot;", $answer->answer);
			
			$xml['answers'][] = $answer->answer;
			$xml['values'][] = (int)$answer->votes;
			$xml['ids'][] = (int)$answer->id;
			$xml['colors'][] = "#".($answer->color != "" ? $answer->color : "000000");
		}
		
		$this->vbparams->set("global", "activate_bbcode", $activate_bbcode);
		$this->vbparams->set("global", "general_published_bbcode", $general_published_bbcode);
		
		return $xml;
	}
	
	function addchartjs($package) {
		$document = & JFactory::getDocument();
		$document->addScript('https://www.google.com/jsapi');
		$document->addScriptDeclaration('google.load("visualization", "1", {packages:["'.$package.'"]});');
	}
	
	function getBackendChart($what, $id = null) {
		$chart = array();
	
		switch($what) {
			case "answerVotesSmallgoogle" :

				$addjs =  'google.setOnLoadCallback(draw'.$id.');'
						. 'function draw'.$id.'() {'
						. 'var data = new google.visualization.DataTable();'
						. 'data.addColumn("string", "Date");'
						. 'data.addColumn("number", "Votes");'
						. 'data.addRows([';

				//Start-EndDatum (14 Tage)
				$endDateTimeStamp = time();
				$startDateTimeStamp = $endDateTimeStamp - 14*86400;

				//Data
				$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date \n"
					. "FROM `#__jvotesystem_votes` AS v LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`), `#__jvotesystem_answers` AS a\n"
					. "WHERE a.`id`=v.`answer_id` AND a.`id`='".$id."'\n"
					. "AND UNIX_TIMESTAMP(`voted_time`) >= ".$startDateTimeStamp."\n"
					. "GROUP BY voted_date\n"
					. "ORDER BY voted_date ASC";

				$this->db->setQuery($sql);
				$data = $this->general->flatten($this->db->loadAssocList(),array('voted_date','votes'));

				//Beschriftungen
				for($date = $startDateTimeStamp; $date <= $endDateTimeStamp; $date += 86400) {
					if ($date != $startDateTimeStamp) {$addjs .=',';}
					$dateInFormat = date("Y-m-d", $date);
					$addjs .= '["'.$dateInFormat.'",'. (isset($data[$dateInFormat]) ? $data[$dateInFormat] : 0) .']';
				}
				
				$addjs .= ']);'
						. 'var options = {"width":200,"height":30,"legend":{"position":"none"},"chartArea":{"height":56,"width":196}, vAxis: {viewWindow:{min: 0}}};'
						. 'var chart = new google.visualization.AreaChart(document.getElementById("'.$what.'gchart'.$id.'"));'
						. 'chart.draw(data, options);}';
				break;
			case "boxVotesSmallgoogle" :
			
				$addjs =  'google.setOnLoadCallback(draw'.$id.');'
						. 'function draw'.$id.'() {'
						. 'var data = new google.visualization.DataTable();'
						. 'data.addColumn("string", "Date");'
						. 'data.addColumn("number", "Votes");'
						. 'data.addRows([';
				
				//Start-EndDatum (14 Tage)
				$endDateTimeStamp = time();
				$startDateTimeStamp = $endDateTimeStamp - 14*86400;
				
				//Data
				$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date \n"
					. "FROM `#__jvotesystem_votes` AS v LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`), `#__jvotesystem_answers` AS a\n"
					. "WHERE a.`id`=v.`answer_id` AND a.`box_id`='".$id."'\n"
					. "AND UNIX_TIMESTAMP(`voted_time`) >= ".$startDateTimeStamp."\n"
					. "GROUP BY voted_date\n"
					. "ORDER BY voted_date ASC";
					
				$this->db->setQuery($sql);
				$data = $this->general->flatten($this->db->loadAssocList(),array('voted_date','votes'));
				
				//Beschriftungen
				for($date = $startDateTimeStamp; $date <= $endDateTimeStamp; $date += 86400) {
					if ($date != $startDateTimeStamp) {$addjs .=',';}
					$dateInFormat = date("Y-m-d", $date);
					$addjs .= '["'.$dateInFormat.'",'. (isset($data[$dateInFormat]) ? $data[$dateInFormat] : 0) .']';
				}
				
				$addjs .= ']);'
						. 'var options = {"width":200,"height":30,"legend":{"position":"none"},"chartArea":{"height":56,"width":196},"vAxis":{"baseline":0}, "hAxis":{"minValue":0, format:"#"}, vAxis: {viewWindow:{min: 0}}};'
						. 'var chart = new google.visualization.AreaChart(document.getElementById("'.$what.'gchart'.$id.'"));'
						. 'chart.draw(data, options);}';
				break;
				
			case "answerStats":
				
				$addjs =  'google.setOnLoadCallback(draw'.$id.');'
				. 'function draw'.$id.'() {'
				. 'var data = new google.visualization.DataTable();'
				. 'data.addColumn("string", "Date");';
				
				//Chart-Data
				$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date\n"
				. "FROM `#__jvotesystem_votes` AS v \n"
				. "LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`)\n"
				. "WHERE v.`answer_id` = ".$this->db->quote($id)."\n"
				. "GROUP BY voted_date\n"
				. "ORDER BY voted_date ASC";
				
				$this->db->setQuery($sql);
				$data = $this->db->loadAssocList();
				
				if(isset($data[0])) $startDate = JFactory::getDate($data[0]["voted_date"]);
				else $startDate = JFactory::getDate();
				if(isset($data[count($data)])) $endDate = JFactory::getDate($data[count($data)]["voted_date"]);
				else $endDate = JFactory::getDate();
				
				$startDateTimeStamp = $startDate->toUnix();
				$endDateTimeStamp = $endDate->toUnix();
				
				//Beschriftungen
				$js = array();
				$i = 0;
				for($date = $startDateTimeStamp; $date < $endDateTimeStamp; $date += 24*60*60) {
					$columnDate =& JFactory::getDate($date);
					$js[$i] = '"'.((int)$columnDate->toFormat("%d")).$columnDate->toFormat(".%b").'"';
					$i++;
				}
				
				$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date \n"
				. "FROM `#__jvotesystem_votes` AS v LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`) \n"
				. "WHERE v.`answer_id`='".$id."'\n"
				. "GROUP BY voted_date \n"
				. "ORDER BY voted_date ASC";
				$this->db->setQuery($sql);
				$dataBox = $this->db->loadAssocList();
					
				//Array umwandeln
				$dataBoxNew = array();
				foreach($dataBox AS $dateBox) {
					$dataBoxNew[$dateBox["voted_date"]] = $dateBox["votes"];
				}
					
				$i = 0;
				$votes = 0;
				$started = false;
				for($date = $startDateTimeStamp; $date < $endDateTimeStamp; $date += 24*60*60) {
					$dateInFormat = date("Y-m-d", $date);
					if(isset($dataBoxNew[$dateInFormat])) {
						$value = $dataBoxNew[$dateInFormat];
					} else {
						$value = 0;
					}
					$votes += $value;
					$js[$i] .= ','.$value;//.','.$votes;
					$i++;
				}
					
				//Settings
				$addjs .= 'data.addColumn("number", "'.JText::_("Votes").'");';
				
				$addjs .= 'data.addRows([';
				
				foreach($js as $j) {
					$addjs .= '['.$j.'],';
				}
				
				$addjs .= ']); function drawChart(data, options) { var chart = new google.visualization.ColumnChart(document.getElementById("'.$what.'gchart'.$id.'")); chart.draw(data, options); }'
				. 'var options = {"width":"100%", height:260, chartArea:{left:50,top:20,width:"100%",height:200}, legend: {position: "none"} };'
				. 'var resizeTimer; JMQuery(window).resize(function () { clearTimeout(resizeTimer); JMQuery("#'.$what.'gchart'.$id.'").html(""); resizeTimer = setTimeout(function() {drawChart(data, options);}, 200); });'
				. 'drawChart(data, options);}';
				
				break;
				
			case "votesgoogle" :

				$addjs =  'google.setOnLoadCallback(draw'.$id.');'
				. 'function draw'.$id.'() {'
				. 'var data = new google.visualization.DataTable();'
				. 'data.addColumn("string", "Date");';
				
				//Chart-Data
				$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date\n"
					. "FROM `#__jvotesystem_votes` AS v \n"
					. "LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`)\n"
					. "WHERE DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= `voted_time`\n"
					. "GROUP BY voted_date\n"
					. "ORDER BY voted_date ASC";

				$this->db->setQuery($sql);
				$data = $this->db->loadAssocList();
				
				if(isset($data[0])) $startDate = JFactory::getDate($data[0]["voted_date"]);
				else $startDate = JFactory::getDate();
				if(isset($data[count($data)])) $endDate = JFactory::getDate($data[count($data)]["voted_date"]);
				else $endDate = JFactory::getDate();
				
				$startDateTimeStamp = $startDate->toUnix();
				$endDateTimeStamp = $endDate->toUnix();
				
				//Beschriftungen
				$js = array();
				$i = 0;
				for($date = $startDateTimeStamp; $date < $endDateTimeStamp; $date += 24*60*60) {
					$columnDate =& JFactory::getDate($date);
					$js[$i] = '"'.((int)$columnDate->toFormat("%d")).$columnDate->toFormat(".%b").'"';
					$i++;
				}
				
				//Boxen holen
				$sql = "SELECT * \n"
					. "FROM `#__jvotesystem_boxes`\n"
					. "WHERE published=1 ";
				$this->db->setQuery($sql);
				$boxen = $this->db->loadObjectList();
				
				$bi = 0;

				foreach($boxen AS $box) {
					$sql = "SELECT SUM(v.`votes`) AS votes,DATE_FORMAT(DATE(`voted_time`), \"%Y-%m-%d\") AS voted_date \n"
						. "FROM `#__jvotesystem_votes` AS v LEFT JOIN `#__jvotesystem_users` AS u ON ( u.`blocked`=0 AND u.`id`=v.`user_id`), `#__jvotesystem_answers` AS a \n"
						. "WHERE a.`id`=v.`answer_id` AND a.`box_id`='".$box->id."'\n"
						. "GROUP BY voted_date \n"
						. "ORDER BY voted_date ASC";
					$this->db->setQuery($sql);
					$dataBox = $this->db->loadAssocList();
					
					//Array umwandeln
					$dataBoxNew = array();
					foreach($dataBox AS $dateBox) {
						$dataBoxNew[$dateBox["voted_date"]] = $dateBox["votes"];
					}
					
					$i = 0;
					$started = false;
					for($date = $startDateTimeStamp; $date < $endDateTimeStamp; $date += 24*60*60) {
						$dateInFormat = date("Y-m-d", $date);
						if(isset($dataBoxNew[$dateInFormat])) {
							$value = $dataBoxNew[$dateInFormat];
						} else {
							$value = 0;
						}
						
						$js[$i] .= ','.$value;
						$i++;
					}
					
					//Settings
					$addjs .= 'data.addColumn("number", "'.$box->title.'");';
					$bi++;
				}
				
				$addjs .= 'data.addRows([';
				
				foreach($js as $j) {
					$addjs .= '['.$j.'],';
				}
				
				$addjs .= ']); function drawChart(data, options) { var chart = new google.visualization.LineChart(document.getElementById("'.$what.'gchart'.$id.'")); chart.draw(data, options); }'
				. 'var options = {"width":"100%", height:260, chartArea:{left:50,top:20, right: 200}};'
				. 'var resizeTimer; JMQuery(window).resize(function () { clearTimeout(resizeTimer); JMQuery("#'.$what.'gchart'.$id.'").html(""); resizeTimer = setTimeout(function() {drawChart(data, options);}, 200); });'
				. 'drawChart(data, options);}';
				break;
		}

		$document = & JFactory::getDocument();
		$document->addScriptDeclaration($addjs);
		$out = '<div id="'.$what.'gchart'.$id.'"></div>';

		return $out;
	}
}//class
