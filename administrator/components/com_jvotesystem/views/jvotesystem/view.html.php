<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jVoteSystem Component
 *
 * @package    jVoteSystem
 * @subpackage Views
 */

class jVoteSystemViewjVoteSystem extends JView
{
    /**
     * jVoteSystem view display method
     *
     * @return void
     **/
    function display($tpl = null)
    {
		$this->charts =& VBCharts::getInstance();
		$this->general =& VBGeneral::getInstance();
		
		$this->charts->addchartjs('corechart');
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$user 		= & JFactory::getUser();
		
		//AJAX-Javascript file
		$lib =& JoomessLibrary::getInstance();
		$lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
		$lib->js("administrator/components/com_jvotesystem/assets/js/log.js");
		
		/* Loads a update script by www.joomess.de - Asynchron*/
		/*$js = '(function()
		{
			var po = document.createElement("script");
			po.type = "text/javascript"; po.async = true;po.src = "http://joomess.de/index.php?option=com_je&view=tools&id=1&task=script&version=3.00&url='.urlencode(JUri::current()).'";
			var s = document.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(po, s);
		})();';
		$document->addScriptDeclaration($js);*/
				
		//build Toolbar
		if(VBAccess::getInstance()->isUserAllowedToConfig()) 
			JToolBarHelper::preferences('com_jvotesystem', 500);
			
		//Tutorials
		//$this->tutController = JoomessLibrary::getInstance()->getTutorialController()->get( "jVoteSystem" );
		//var_dump($this->tutController);
		
		//assign vars to the template
		$this->assignRef('pane'			, $pane);
		$this->assignRef('user'			, $user);
		
        parent::display($tpl);
    }//function
	
	function quickiconButton( $link, $image, $text, $modal = 0, $target="")
	{
		//initialise variables
		$lang 		= & JFactory::getLanguage();
  		?>

		<div id="cpanel" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal == 1) {
					JHTML::_('behavior.modal');
				?>
					<a href="<?php echo $link.'&amp;tmpl=component'; ?>" style="cursor:pointer" <?php if($target != "") echo 'target="'.$target.'"'; ?> class="modal" rel="{handler: 'iframe', size: {x: 875, y: 650}, closable: false, closeBtn: false, onOpen: function(){JMQuery('#sbox-btn-close').remove();JMQuery('object').hide();}, onClose: function() {JMQuery('object').show();}}">
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>" <?php if($target != "") echo 'target="'.$target.'"'; ?> >
				<?php
				}

					echo JHTML::_('image', 'administrator/components/com_jvotesystem/assets/images/'.$image, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

}//class
