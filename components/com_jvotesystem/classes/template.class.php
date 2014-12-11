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
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.application.component.helper' );

class VBTemplate
{
	var $db, $document, $template, $root, $demo_mode;

	private function __construct() {
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		if(!isset($this->template)) $this->template = 'default'; 
		$this->root = rtrim(JPATH_SITE, "\\/").DS.'components'.DS.'com_jvotesystem'.DS.'templates';
		$this->demo_mode = false;
		$this->vbparams =& VBParams::getInstance();
		$this->lib =& joomessLibrary::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBTemplate();
		}
		return $instance;
	}
	
	function setTemplate($newTemplate, $demo = false) {
		$this->template = $newTemplate;
		$this->demo_mode = $demo;
		
		//make sure jQuery is loaded first!
		$this->lib->jQuery();
		
		//Load Files
		if($this->vbparams->get("load_domwrite") && $this->vbparams->get("adsense")) $this->lib->js('components/com_jvotesystem/assets/js/domWrite.js');
		
		$this->lib->plugin("zebra_dialog");
		$this->lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
		
		$this->lib->css("components/com_jvotesystem/templates/assets/css/".$this->template."/default.css");
		if(!JFile::exists($this->root.DS.'assets'.DS.'css'.DS.$this->template.DS.'default.css'))
			$this->lib->css("components/com_jvotesystem/templates/assets/css/default/default.css");
		if(JFile::exists($this->root.DS.'assets'.DS.'js'.DS.$this->template.DS.'default.js'))
			$this->lib->js("components/com_jvotesystem/templates/assets/js/".$this->template."/default.js");
	}
	
	function getTemplatePath($view = "main") {
		return $this->root.DS.'elements'.DS.$view.DS.$this->template;
	}

	function getTemplate() {
		return $this->template;
	}
	
	var $templates;
	function getTemplates($resultList = false) {
		if(!isset($this->templates)) {
			$this->templates = array();
			
			//Stammordner auslesen
			$i = 0;
			foreach(JFolder::folders($this->root.DS.'elements'.DS.'main') AS $folder) { 
				if(!JFile::exists($this->root.DS.'elements'.DS.'main'.DS.$folder.DS.'.noselect')) {
					if($resultList) {
						$this->templates[] = $folder;
					} else {
						$this->templates[$i] = array();
						$this->templates[$i]["name"] = $folder;
						$this->templates[$i]["id"] = $folder;
							
						$i++;
					}
				}
			}
		}
		
		return $this->templates;
	}
	
	var $viewPaths;
	function loadTemplate($view, $par = null, $onlyLoad = false) { //echo "LoadTemplate: ".$view.@$par->aid."<br>";
		if(!isset($this->viewPaths)) $this->viewPaths = array();
		if(!isset($this->viewPaths[$this->template])) $this->viewPaths[$this->template] = array();
		if(!isset($this->viewPaths[$this->template][$view])) $this->viewPaths[$this->template][$view] = "";
		
		if($this->viewPaths[$this->template][$view] == "") {
			$pathSelectedTemplate = $this->root.DS.'elements'.DS.$view.DS.$this->template;
			
			//Templatedatei überprüfen
			$pathPHP = $pathSelectedTemplate;
			if(!JFile::exists($pathSelectedTemplate.DS.'default.php')) {
				//Standard-Template laden
				$pathPHP = $this->root.DS.'elements'.DS.$view.DS.'default';
			}
			
			//Path speichern
			$this->viewPaths[$this->template][$view] = $pathPHP;
			
			//General css
			$this->lib->css('components/com_jvotesystem/assets/css/general.css');
		}
		
		if($onlyLoad == true) return null;
		
		//Puffer leeren und vorbereiten..
		$old = $this->prepare();
		
		//Datei laden
		require $this->viewPaths[$this->template][$view].DS.'default.php';
		
		//Puffer zurückgeben
		$out = $this->getHtml($old);
		
		return $out;
	}
	
	function prepare() {
		$old = ob_get_contents();
		ob_clean();
		return $old;
	}
	
	function getHtml($old) {
		$out = ob_get_contents();
		ob_clean();
		echo $old;
		
		//HTML cleanen
		$out = str_replace("\n", "", str_replace( "\t", "", $out ) ); // <-Tabs entfernen
		return $out;
	}
}//class
