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

jimport( 'joomla.methods' );
jimport( 'joomla.application.component.helper' );

class VBGeneral
{
	private $db, $count, $spacer, $loaded;

	private function __construct($load) { 
		$this->loaded = $load;
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		if(!$load) return false;
		
		$this->_load();
	}
	
	public function _load() {
		$this->count = 0;
		$this->spacer = ' ';
		$this->vbparams =& VBParams::getInstance();
		$this->BBCodeToolbar = '';
		$this->vbuser =& VBUser::getInstance();
	}
	
	static function &getInstance($load = true) {
		static $instance;
		if(empty($instance)) {
			$instance = new VBGeneral($load);
		} else if($load && !$instance->loaded) {
			$instance->_load();
		}
		return $instance;
	}

	function shortText($s, $max, $onclick = true, $bbcode = true) {
		$this->spacer = '[(LZ)]';
		$this->count = $max;
		if($bbcode) $s = $this->BBCode($s);
	
		$short = (strlen($s) > ($this->count+1));
	
		$whitespaceposition = @strpos($s," ",$this->count)-1;
		if($short AND $whitespaceposition > 0) {
			$newS = substr($s, 0, ($whitespaceposition+1));
		} else $newS = $s;
		
		//Leerzeichen von BBCode wieder ersetzen
		$s = str_ireplace($this->spacer, ' ', $s);
		$newS = str_ireplace($this->spacer, ' ', $newS);
		//->keine HTML-Tags zerstört...
		
		if($newS == '' OR trim($newS) == trim($s)) {
			$newS = $s;
			$short = false;
		}
		
		if($short) {
			if($onclick == true) {
				$s = substr($s, strlen($newS) + 1, strlen($s));
				$o = $newS;
				$o .= ' <a class="jvs_showMoreText">['.JText::_('More').'...]';
				$o .= '<span style="display:none;">'.urlencode(nl2br($s)).'</span>';
				$o .= '</a>';
				//$o .= '<noscript>'.$s.'</noscript>';
			} else {
				$o = $newS;
				$o .= '...';
			}
			return trim($o);
		}
		
		return trim($newS);
	}
	
	private $bbcodes;
	function BBCode($s, $newSpacer = null) {
		if(!isset($this->spacer)) $this->spacer = ' ';
		if($newSpacer != null) $this->spacer = $newSpacer;
		
		if(!isset($this->bbcodes)) {
			$sql = "SELECT * FROM `#__jvotesystem_bbcodes` ";
			$this->db->setQuery($sql);
			$this->bbcodes = $this->db->loadObjectList();
		}
		
		foreach($this->bbcodes AS $bc) {
			$s = $this->replaceBBCode($s, $bc->published, $bc->regex, $bc->replace, $bc->replaceNot);
		}
		
		return $s;
	}
	
	function getBBCodeToolbar($divID, $textfieldID, $hidden = true, $path = null) {
		if($hidden == false) $style = "display: block;"; else $style = 'display: none;';
		if($path == null) $path = joomessLibrary::getInstance()->root();
	
		$out = '<div id="'.$divID.'" class="bbcodeToolbar" style="width:100%;'.$style.'">';
		
		$sql = "SELECT * FROM `#__jvotesystem_bbcodes` WHERE `withButton`=1 AND `buttonImage`!=''";
		$this->db->setQuery($sql);
		$bbcodes = $this->db->loadObjectList();
		
		if($textfieldID != "this") $textfieldID = "'".$textfieldID."'";
		
		foreach($bbcodes AS $bc) {
			$testReplace = explode("$1", $bc->replace);
			if(count($testReplace) <= 1) {
				$js = "jVoteSystemInsertCode($textfieldID, '".$bc->editorCode."'); ";
			} else {
				$js = "jVoteSystemInsertBBCode($textfieldID,'".$bc->editorCode."', '".$bc->buttonInfo."');";
			}
			
			$out .= '<img onMouseDown="jVoteBoxStopReset = true;" src="'.$path.'/components/com_jvotesystem/assets/images/bbcode/'.$bc->buttonImage.'" class="bbcodeIcon" onclick="'.$js.'" alt="'.$bc->name.'" title="'.$bc->name.'" />';
		}
		
		$out .= '</div>';
		return $out;
	}
	var $BBCodeToolbar;
	function getBBCodeToolbar2($hidden = null) {
		$path = joomessLibrary::getInstance()->root();
		if ($hidden !== null) $hidden = "display:none;";
		if (empty($this->BBCodeToolbar)) {
			$sql = "SELECT * FROM `#__jvotesystem_bbcodes` WHERE `withButton`=1 AND `buttonImage`!=''";
			$this->db->setQuery($sql);
			$bbcodes = $this->db->loadObjectList();
			
			foreach($bbcodes AS $bc) {
				$testReplace = explode("$1", $bc->replace);
				if(count($testReplace) <= 1) {
					$js = 'data-insert="'.$bc->editorCode.'"';
				} else {
					$js = 'data-bbcode='.$bc->editorCode.' data-bbinfo="'.$bc->buttonInfo.'"';
				}
				
				$this->BBCodeToolbar .= '<img src="'.$path.'/components/com_jvotesystem/assets/images/bbcode/'.$bc->buttonImage.'" title="'.$bc->name.'" '.$js.' />';
			}
		}
		
		return '<div class="bbcodeToolbar" style="'.$hidden.'width:100%;">'. $this->BBCodeToolbar .'</div>';
	}
	
	private function replaceBBCode($s, $published, $regex, $replace, $replaceNot = "") {
		$replace = str_replace("{bbCodeImagePath}", joomessLibrary::getInstance()->root().'/components/com_jvotesystem/assets/images/bbcode', $replace);
	
		$testReplace = explode("$1", $replace);
		if(count($testReplace) <= 1) {
			if($published AND $this->vbparams->get('activate_bbcode')) {
				$replace = str_replace(' ', $this->spacer, $replace);
				
				$s = str_replace($regex, $replace, $s, $count);
				$this->count += strlen($replace)*$count;
			}
		} else {		
			preg_match_all($regex, $s, $matches);
			if(!$matches[0]) return $s;
			$mI = 0;
			
			$replacements = array();
			
			if($published AND $this->vbparams->get('activate_bbcode')) {
				foreach($matches AS $gap) {
					if($gap != $matches[0]) {
						$i = 0;
						foreach($gap AS $match) {
							$match = str_replace(' ', $this->spacer, $match);
							//$match = str_replace("\n", '', $match);
							if(!isset($replacements[$i])) $replacements[$i] = str_replace(' ', $this->spacer, $replace);
							$replacements[$i] = str_replace('$'.($mI), $match, $replacements[$i]);
							$i++;
						}
					}
					$mI++;
				}
			} elseif($published OR $this->vbparams->get('general_published_bbcode')) {
				$i = 0;
				foreach($matches[0] AS $match) {
					$replacements[$i] = ($replaceNot != "") ? $replaceNot : $matches[1][$i];
					$i++;
				}
			} else {
				$i = 0;
				foreach($matches[0] AS $match) {
					$replacements[$i] = $matches[1][$i];
					$i++;
				}
			}
			
			$i = 0;
			foreach($matches[0] AS $match) {
				$s = str_replace($match, $replacements[$i].' ', $s, $count);
				$this->count += strlen($replace)*$count;
				$i++;
			}
		}
		return $s;
	}
	
	function getNewHash() {
		return sha1(uniqid(rand())).md5(uniqid(rand()));
	}
	
	/* EMails Hash Tasks */
	public static $_changePublishStateAnswer 	= 1;
	public static $_removeAnswer 				= 2;
	public static $_changePublishStateComment 	= 3;
	public static $_removeComment 				= 4;
	public static $_changePublishStatePoll 		= 5;
	public static $_removePoll 					= 6;
	
	function generateHash($task, $id, $uid, $params = array()) {
		$params["task"] 	= $task;
		$params["id"] 		= $id;
		
		$date = new JDate();
	
		$ins 			= new JObject();
		$ins->hash 		= $this->getNewHash();
		$ins->params 	= json_encode($params);
		$ins->uid 		= $uid;
		$ins->created 	= $date->toMySQL();
		$ins->active 	= true;
		
		$this->db->insertObject('#__jvotesystem_email_tasks', $ins);
		
		return $ins->hash;
	}
	
	function getTask($hash) {
		$sql = 'SELECT * FROM `#__jvotesystem_email_tasks` WHERE `hash`="'.$hash.'" ORDER BY `created` DESC';
		$this->db->setQuery($sql);
		$task = $this->db->loadObject();
		
		if(!empty($task)) {
			$task->params = json_decode($task->params);
			foreach($task->params AS $key => $value) {
				$task->$key = $value;
			}
			unset($task->params);
			
			if(!isset($task->active)) $task->active = true;
			$task->active = ($task->active == 1) ? true : false;
			
			return $task;
		}
		return null;
	}
	
	function unactivateTask($id) {
		$upd = new JObject();
		$upd->hash = $id;
		$upd->active = false;
		
		$this->db->updateObject('#__jvotesystem_email_tasks', $upd, 'hash');
	}
	
	function getAdminFooter() {
		$lib =& joomessLibrary::getInstance();
		?>
		<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td>
			<table class="adminlist"><tr><td style="text-align: center;">
				<div style="height: 15px; display: inline-block;">
					<a href="http://joomess.de/projekte/18-jvotesystem">jVoteSystem</a> developed and designed by <a href="http://www.joomess.de">www.joomess.de</a>.
				</div>
				<?php echo $lib->special("googlePlusButton", array("href" => "http://joomess.de/projects/jvotesystem.html", "size" => "small"));?>
			</td></tr></table>
		</td></tr></table>
		<?php
	}
	
	/*DB - orderBy*/
	function getSqlOrderBy($set, $dir, $prepend = "") {
		$order_by = ' ORDER BY ';
		if($prepend != "") $order_by .= $prepend.",";
		switch($set) {
			default:
			case "votes":
				$order_by .= ' votes '.$dir.', lastvote ASC, created ASC'; 
				break;
			case "id":
				$order_by .= ' `id` '.$dir.', lastvote ASC, created ASC'; 
				break;
			case "name":
				$order_by .= ' `answer` '.$dir.', lastvote ASC, created ASC'; 
				break;
			case "created":
				$order_by .= ' `created` '.$dir.', lastvote ASC'; 
				break;
		}
		return $order_by;
	}

	function VDTS ($var)
	{
		ob_start();
		var_dump($var);
		$result = ob_get_clean();
		error_log($result);
		return;
	}
	
	function flatten($array,$keys = null) {
		$out = array();
		if ( $keys == null ) {
			$keys = array('key','value');
		}
		foreach($array AS $a) {
			$out[$a[$keys[0]]]=$a[$keys[1]];
		}
		return $out;
	}
	
	private $menuIds;
	private function getMenuItem($link) {
		if(empty($this->menuIds)) {
			$lang =& JFactory::getLanguage();
			$config =& JFactory::getConfig();
			if(version_compare( JVERSION, '1.6.0', 'lt' ) || $config->get('sef', 0) == 0) {
				$this->menuIds = null;
			} else {
				$sql = 'SELECT link,id FROM `#__menu` WHERE LEFT(link,length("index.php?option=com_jvotesystem")) = "index.php?option=com_jvotesystem" AND published = 1 AND (language = "*" OR language = "'.$lang->getTag().'");';
				$this->db->setQuery($sql);
				$this->menuIds = $this->db->loadAssocList('link');
			}
		}
		return isset($this->menuIds[$link]) ? $this->menuIds[$link]['id'] : null;
	}
	
	private $pollMenuData;
	private function getPollMenuData($id) {
		if(empty($this->pollMenuData)) $this->pollMenuData = array();
		if(empty($this->pollMenuData[$id])) {
			$sql = 'SELECT `catid`, `alias` FROM `#__jvotesystem_boxes` WHERE `id` = '.$id;
			$this->db->setQuery($sql); 
			$this->pollMenuData[$id] = $this->db->loadObject();
		}
		return $this->pollMenuData[$id];
	}
	
	function buildHtmlLink($src, $content, $args = array()) {
		$out = '<a href="'.$src.'"';
		foreach($args AS $key => $value)
			$out .= ' '.$key.'="'.$value.'"';
		$out .= '>';
			$out .= $content;
		$out .= '</a>';
		
		$out = str_replace("&", "&amp;", $out);
		
		return $out;
	}
	
	function buildAdminLink($view = "", $id = null, $task = "", $pars = array()) {
		$link = "index.php?option=com_jvotesystem";
		$category =& VBCategory::getInstance();
		$user =& VBUser::getInstance();
		
		switch($view) {
			case "answer":
			case "comment":
			case "apikey":
				$pars["controller"] 	= $view."s";
				$pars["view"] 			= $view;
				$pars["model"] 			= $view;
				$pars["task"]			= ($task == "") ? "edit" : $task;
				
				$pars["cid[]"] = $id;
				
				break;
				
			case "poll": //Assistent
				$lib =& joomessLibrary::getInstance();
				$link = $lib->root()."/components/com_jvotesystem/assistant/index.php?interface=administrator";
				$pars["view"] 	= "poll";
				$pars["id"] 	= $id;
		
				foreach($pars AS $key => $par) {
					$link .= "&".$key."=".$par;	
				}
				
				$args = array();
				$args["onclick"] = "loadAssistant(this, '".$lib->root()."', 'poll', '&id=".$id."'); return false;";
				
				return 	$this->buildHtmlLink(
							$link,
							$task,
							$args
						);
				
				break;
				
			case "user":
				$lib =& joomessLibrary::getInstance();
				
				$curuser = $user->getUserData($id);
				
				if($curuser->jid == 0) return "#";
				
				$link = "index.php?option=com_users";
				switch($lib->getJoomlaVersion()) {
					case joomessLibrary::jVersion15:
						$pars["view"] 	= "user";
						$pars["task"]	= "edit";
						$pars["cid[]"] 	= $curuser->jid;						
						break;
					default:
						$pars["task"] 	= "user.edit";
						$pars["id"] 	= $curuser->jid;					
						break;
				}
				break;
		}
		
		foreach($pars AS $key => $par) {
			$link .= "&".$key."=".$par;	
		}
		
		return $link;
	}
	
	function buildLink($view = "", $id = null, $task = "", $pars = array(), $local = true, $route = true) {
		$link = "index.php?option=com_jvotesystem";
		$category =& VBCategory::getInstance();
		$user =& VBUser::getInstance();
		
		switch($view) {
			case "list":
				$params = $this->vbparams->getActiveMenuParams();
				
				if($this->getMenuItem($link."&view=polls")) $link = "index.php?Itemid=".$this->getMenuItem($link."&view=polls");
				else $link .= "&view=polls";
				//Filter
				if(isset($pars["cat"])) {
					if($pars["cat"] != $params->get("cat", "all")) $link .= "&cat=".$pars["cat"];
				} elseif(isset($pars["cid"]) AND @$pars["cid"] != $category->getCatIdByAlias($params->get("cat", "all"))) $link .= "&cat=".$category->getCategory($pars["cid"])->alias;
				
				if(isset($pars["order"]) AND @$pars["order"] != $params->get("order", "popular")) $link .= "&order=".$pars["order"];
				if(isset($pars["time"]) AND @$pars["time"] != $params->get("time", "all-time")) $link .= "&time=".$pars["time"];
				if(isset($pars["keyword"]) AND @$pars["keyword"] != "") $link .= "&keyword=".$pars["keyword"];
				if(isset($pars["page"]) AND @$pars["page"] != 1) $link .= "&page=".$pars["page"];
				
				return JRoute::_($link)."#jvotesystem";
				break;
			case "category":
				$cat = $category->getCategory($id); 
				
				if($mid = $this->getMenuItem($link."&view=polls")){
					$mitem = JFactory::getApplication()->getMenu()->getItem($mid);
					$mparams = $this->vbparams->getMenuParams($mitem);
					
					if($mparams->get("cat") == $cat->alias) $link = "index.php?Itemid=".$this->getMenuItem($link."&view=polls");
					else $link = "index.php?Itemid=".$this->getMenuItem($link."&view=polls")."&cat=".$cat->alias;
				} else $link .= "&view=polls&cat=".$cat->alias;
				
				break;
			case "poll":
				$poll = $this->getPollMenuData($id);
				$cat = $category->getCategory($poll->catid);
				switch($task) {
					default:
						if($route && $this->getMenuItem($link."&view=poll&id=".$id)) $link = "index.php?Itemid=".$this->getMenuItem($link."&view=poll&id=".$id);
						elseif($route && $this->getMenuItem($link."&view=polls")) $link = "index.php?Itemid=".$this->getMenuItem($link."&view=polls")."&cat=".$cat->alias."&alias=".$poll->alias;
						elseif($route) $link .= "&view=poll&cat=".$cat->alias."&alias=".$poll->alias;
						else $link .= "&view=poll&id=".$id;
						break;
				}
				break;
			case "user":
				$com = $this->vbparams->get("com_profile");
				$curuser = $user->getUserData($id);
				
				if($curuser->jid == 0) return "#";
				
				$local = false;
				
				switch($com) {
					case "cb":
						$link = "index.php?option=com_comprofiler&task=userProfile&user=".$curuser->jid;
						break;
					case "jomsocial":
						$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
						@include_once($jspath.DS.'libraries'.DS.'core.php');
						// Get CUser object
						if(class_exists("CFactory"))
							return str_replace("/components/com_jvotesystem", "", CRoute::_('index.php?option=com_community&view=profile&userid='.$curuser->jid) );
						break;
					case "kunena":
						$link = 'index.php?option=com_kunena&view=profile&userid='.$curuser->jid;
						break;
					default:
						return "#";
					break;
				}
				break;
			case "ajax":
				$pars["view"] = "ajaxjson";
				$pars["task"] = $task;
				$pars["type"] = "GET";
				
				$session =& JFactory::getSession();
				$pars[(version_compare( JVERSION, '1.6.0', 'lt' )) ? JUtility::getToken() : $session->getFormToken()] = 1;
				
				break;
			case "task":
				$link .= "&view=tasks&hash=".$id;
				break;
		}
		
		foreach($pars AS $key => $par) {
			$link .= "&".$key."=".$par;	
		}
		
		if($route) $link = JRoute::_($link);
		if(!$local) {
			$lib =& joomessLibrary::getInstance();
			return $lib->root(false)."/".ltrim(str_replace(JUri::root(true), "", $link), "/");
		}
		return $link;
	}
	
	function convertTimeTight($date) {
		if($date == null || $date == '0000-00-00 00:00:00') return JText::_("JVS_TIME_NEVER");
		
		$app =& JFactory::getApplication();
		$curDate = JFactory::getDate(null, $app->getCfg('offset'));
		$date = JFactory::getDate($date, $app->getCfg('offset'));
		
		$tsCur = $curDate->toUnix();
		$ts = $date->toUnix();
		
		$diff = $tsCur - $ts;
		
		$tsToday = JFactory::getDate($curDate->toFormat("%Y-%m-%d 00:00:00")) -> toUnix();
		if($diff < ($tsCur-$tsToday)) {
			return $date->toFormat("%H:%M");
		} elseif($diff < 60*60*24*6) {
			return $date->toFormat("%A");
		} else {
			return $date->toFormat("%d.%b");
		}
	}
	
	function convertTime($date) {
		if($date == null || $date == '0000-00-00 00:00:00') return JText::_("JVS_TIME_NEVER");
		
		$app =& JFactory::getApplication();
		$curDate = JFactory::getDate(null, $app->getCfg('offset'));
		$date = JFactory::getDate($date, $app->getCfg('offset'));
		
		$tsCur = $curDate->toUnix();
		$ts = $date->toUnix();
		
		$diff = $tsCur - $ts;
		
		$comment = null;
		
		if($diff < 60) {
			if($diff < 10) {
				$comment = JText::_("Just_Now");
			} else {
				$count = $diff;
				$text = "Seconds";
			}
		} elseif($diff < 60*60) {
			$count = round($diff/60);
			$text = "Minutes";
		} elseif($diff < 60*60*24) {
			$count = round($diff/60/60);
			$text = "Hours";
		} elseif($diff < 60*60*24*7) {
			$count = floor($diff/60/60/24);
			$text = "Days";
		} elseif($diff < 60*60*24*30) {
			$count = floor($diff/60/60/24/7);
			$text = "Weeks";
		} elseif($diff < 60*60*24*365) {
			$count = floor($diff/60/60/24/30);
			$text = "Months";
		} else {
			$count = floor($diff/60/60/24/365);
			$text = "Years";
		}
		
		$con = ($comment == null) ? ( $count." ".JText::_(($count == 1) ? substr($text, 0, strlen($text)-1) : $text) ) : $comment;
		
		$html = '<span title="'.$date->toFormat("%d.%B %Y, %H:%M").'" class="update_timestamp jvs" data-time="'.$ts.'">'.$con.'</span>';
		return '<span class="time_container">'.sprintf(JText::_(($comment == null) ? "TIME_AGO" : "TIME_AGO_COMMENT"), $html).'</span>';
	}
	
	function convertUser($id) {
		$vote =& VBVote::getInstance(); //CSS-JS laden
		$app =& JFactory::getApplication();
		
		$user = $this->vbuser->getUserData($id);
		$link = $app->isAdmin() ? $this->buildAdminLink("user", $id) : $this->buildLink("user", $id);
		
		$html = '<span class="jvs_avatar" data-u="'.$user->id.'"><a href="'.$link.'">'.$user->name.'</a></span>';
		
		return $html;
	}
	
	function cleanStr($str, $short = 25) {
		return substr(strtolower(str_replace("STRIPE", "-", preg_replace("/[^a-zA-Z0-9]/", "", str_replace(" ", "STRIPE",str_replace("-", "STRIPE",trim($str)))))), 0, $short);
	}
	
	private $aliases;
	function checkAlias($str, $type = "boxes") {
		//Aliase abrufen
		if(empty($this->aliases)) $this->aliases = array();
		if(empty($this->aliases[$type])) {
			$sql = "SELECT `alias` FROM `#__jvotesystem_$type` ";
			$this->db->setQuery($sql);
			$this->aliases[$type] = $this->db->loadObjectList("alias");
		}
		
		$newStr = $str;
		$i = 2;
		while(isset($this->aliases[$type][$newStr])) {
			$newStr = $str."-".$i;
			$i++;
		}
		return $newStr;
	}
	
	function dumpTree($arr) {
		//Puffer leeren und vorbereiten..
		$old = ob_get_contents();
		ob_clean();
		
		//Datei laden
		var_dump($arr);
		
		//Puffer zurückgeben
		$out = ob_get_contents();
		ob_clean();
		echo $old;
		
		return $out;
	}
	
	function root($pathonly = false) {
		$root = JUri::root($pathonly);
		
		$root = rtrim($root, "/");
		$parts = explode("/", $root);
		
		switch($parts[count($parts) - 1]) {
			case "assistant": 
				if($parts[count($parts) - 3] == "components") {
					$root = "";
					for($i = 0; $i < count($parts) - 3; $i++) $root .= $parts[$i]."/";
				}
				break;
		}
		
		$root = rtrim($root, "/");
		
		return $root;
	}
	
	function getToken() {
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			return JUtility::getToken();
		} else {
			$session =& JFactory::getSession();
			return $session->getFormToken();
		}
		
	}
	
	function arrayToXML($ar) {
		$xml = new SimpleXMLElement('<data></data>');
		
		$this->_arrayToXML($xml, $ar);
		
		return $xml->asXML();
	}
	
	private function _arrayToXML(&$xml, $ar) {
		foreach($ar as $key => $value) {
			if(is_array($value) || is_object($value)) {
				$child = $xml->addChild($key);
				$this->_arrayToXML($child, $value);
			} else {
				$key = trim(str_replace(':', '', $key));
				$value = trim(strip_tags($value));
				$xml->addChild($key, $value);
			}
		}
	}
	
	private $colors;
	public function getColors() {
		if(!isset($this->colors)) 
			$this->colors = array( 
					"#3366cc", 
					"#dc3912", 
					"#ff9900", 
					"#109618", 
					"#990099", 
					"#0099c6", 
					"#dd4477", 
					"#66aa00", 
					"#b82e2e", 
					"#316395",
					"#994499", 
					"#22aa99", 
					"#aaaa11", 
					"#6633cc", 
					"#e67300", 
					"#8b0707",
					"#651067",
					"#329262",
					"#5574a6",
					"#3b3eac",
					"#b77322",
					"#16d620",
					"#b91383",
					"#f4359e",
					"#9c5935",
					"#a9c413",
					"#2a778d",
					"#668d1c",
					"#bea413",
					"#0c5922",
					"#743411");
		return $this->colors;
	}
	
	public function getColorCode($index = null) {
		$this->getColors();
		if($index == null) $index = rand(0, sizeof($this->colors) - 1);
		$i = $index % sizeof($this->colors);
		return $this->colors[$i];
	}
	
	public function updateColorIndex($id, $index) {
		$sql = 'SELECT `params` FROM `#__jvotesystem_boxes` WHERE `id` = "'.$id.'" ';
		$this->db->setQuery($sql);
		if($params = $this->db->loadResult()) {
			$pars = json_decode($params);
			$pars->cur_color_index = $index;
				
			$upd = new JObject();
			$upd->id = $id;
			$upd->params = json_encode($pars);
				
			$this->db->updateObject('#__jvotesystem_boxes', $upd, 'id');
		}
	}
	
	public function resetColorsPoll($id) {
		//Antworten holen
		$sql = 'SELECT `id`
				FROM `#__jvotesystem_answers`
				WHERE `box_id` = "'.$id.'"
				ORDER BY `id` ASC ';
		$this->db->setQuery($sql);
		$answers = $this->db->loadResultArray();
		
		//Farben setzen
		$i = 0;
		foreach($answers AS $aid) {
			$upd = new JObject();	
			$upd->id = $aid;
			$upd->color = substr($this->getColorCode($i), 1);
			
			$this->db->updateObject('#__jvotesystem_answers', $upd, 'id');
			$i++;
		}
		
		//Color-index setzen
		$this->updateColorIndex($id, count($answers));
	}

    public function setHeadData( $title, $url, $image, $desc ) {
    	$doc =& JFactory::getDocument();
    	$headData = $doc->getHeadData();
    	//OG
    	$headData["custom"]["jvs_og_title"] = '<meta property="og:title" content="'.htmlspecialchars($title).'" />';
    	$headData["custom"]["jvs_og_url"] = '<meta property="og:url" content="'.str_replace("&", "&amp;", $url).'" />';
    	$headData["custom"]["jvs_og_type"] = '<meta property="og:type" content="website" />';
    	$headData["custom"]["jvs_og_image"] = '<meta property="og:image" content="'.$image.'" />';
    	$headData["custom"]["jvs_og_desc"] = '<meta property="og:description" content="'.htmlspecialchars($desc).'" />';
    	//Schema
    	//$headData["custom"]["jvs_schema"] = '<html itemscope itemtype="http://schema.org/Thing">';
    	$headData["custom"]["jvs_schema_title"] = '<meta itemprop="name" content="'.htmlspecialchars($title).'">';
    	$headData["custom"]["jvs_schema_desc"] = '<meta itemprop="description" content="'.htmlspecialchars($desc).'">';
    	$headData["custom"]["jvs_schema_image"] = '<meta itemprop="image" content="'.$image.'">';
    	$doc->setHeadData($headData);
    }
    
    /* CACHING */
    function disableCacheAnswers($pid) {
//     	$session = JFactory::getSession();
//     	$cached = $session->get('cacheAnswers', array(), 'jVS');
//     	$cached[$pid] = array();
//     	$session->set('cacheAnswers', $cached, 'jVS');
    }
    
    function enableCacheAnswer($pid, $cache_id) {
//     	$session = JFactory::getSession();
//     	$cached = $session->get('cacheAnswers', array(), 'jVS');
//     	if(isset($cached[$pid])) {
//     		$cached[$pid][$cache_id] = true;
//     	}
    }
    
    function checkCache($pid, $cache_id) {
//     	$session = JFactory::getSession();
//     	$cached = $session->get('cacheAnswers', array(), 'jVS');
//     	if(!isset($cached[$pid])) {
//     		return true;	
//     	} else {
//     		if(isset($cached[$pid][$cache_id]))
//     			return true;
//     		else
//     			return false;
//     	}
    }
    
    function clearCacheAnswers($pid) {
//     	$cache = & JCache::getInstance();
//     	$cache->clean('jVoteSystem - Answers['.$pid.']');
//     	$session = JFactory::getSession();
//     	$cached = $session->get('cacheAnswers', array(), 'jVS');
//     	if(isset($cached[$pid])) {
//     		unset ($cached[$pid]);
//     		$session->set('cacheAnswers', $cached, 'jVS');
//     	}
    }
    
    function charset($type) {
		$chars = array('a','b','c','d','e','f','g', 'x','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','y','z'); 
		$symbols = array('<','>',' ','=','"','/','-',':',';','1','.'); 
		
		//Read charset
		$data = JFile::read(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'assets'.DS.'charsets'.DS.$type.'.txt');
		
		$header = "";
		if($data != "")
			$header .= $this->buildCharset($chars, $symbols, $data);
    	
		return $header;
    }
    
    function buildCharset($chars, $symbols, $ids) {
    	$cload = explode('-', $ids);
    	$set = "";
    	foreach($cload AS $c) {
    		$t = substr($c, 0, 1);
    		if($t == 'c') $set .= substr($c, 1, 1) == "1" ? strtoupper($chars[substr($c, 2)]): $chars[substr($c, 2)];
    		elseif($t == 's') $set .= $symbols[substr($c, 1)];
    		else $set = '';
    	}
    	echo $set;
    	
    	if($set = '') return '';
    	return $set;
    }
    	
}//class
