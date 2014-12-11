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
jimport( 'joomla.file.file' );
jimport( 'joomla.application.component.helper' );

class VBExport
{

	private function __construct() { 
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		$this->vbparams =& VBParams::getInstance();
		$this->general =& VBGeneral::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBExport();
		}
		return $instance;
	}

	public function exportPollData($id, $format = 'csv') {
		$vbvote = VBVote::getInstance();
		
		//Daten holen
		$box = $vbvote->getBox($id); 
		if(!$box) return false;
		
		$answers = $vbvote->getAnswers($id, array( "only_published" => true ), 0, false);
		
		$filename 	= JFile::makeSafe($box->title);
		$title 		= JText::_("Poll").": ".$box->title;
		$question 	= $box->question;
		
		$labels = array( JText::_('Answer'), JText::_('Votes'), JText::_('Ranking') );
		
		$data = array(); 
		foreach($answers AS $i => $answer) {
			$row = 	array(
						$answer->answer,
						(int)$answer->votes,
						(int)$answer->rank
					);
			$data[] = $row;
		}
		
		//Header ausgeben
		switch($format) { 
			case 'xls': 
			case 'csv':
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: inline;filename=$filename.$format ");
				break;
		}
		
		//Daten konvertieren & ausgeben
		switch($format) {
			case 'xls':?>
				<table>
					<tr> <td style="font-size:15pt"><?php echo utf8_decode($title)?></td> </tr>
					<tr> <td style="font-size:13pt"> <i><?php echo utf8_decode($question)?></i> </td> </tr>
					
					<tr> </tr>
					
					<tr>
					<?php foreach($labels AS $i => $label) {?>
						<td style="font-size:12pt"><?php echo utf8_decode($label);?></td>
					<?php }?>
					</tr>
					
					<?php foreach($data AS $u => $row) {?>
					<tr>
						<?php foreach($row AS $i => $val) {?>
						<td><?php echo utf8_decode($val);?></td>
						<?php }?>
					</tr>
					<?php }?>
				</table>
			
				<?php
				break;
			case 'csv':
				//Labels
				foreach($labels AS $i => $label) { 
					if($i != 0) echo ";";
					echo utf8_decode($label);
				}
				echo "\n";
				//Daten
				foreach($data AS $u => $row) {
					foreach($row AS $i => $val) {
						if($i != 0) echo ";";
						echo utf8_decode( str_replace(";", ',', $val) );
					}
					echo "\n";
				}
				break;
		}
		
		exit();
	}
	
}//class
