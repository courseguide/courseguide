<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla.com. All Rights Reserved.            ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
defined( '_JEXEC' ) or die( 'Restricted index access' );
// top panel params
$tpopen_text			             = $this->params->get("tpopen_text");
$tpclose_text			             = $this->params->get("tpclose_text");
$tpbtn_width			             = $this->params->get("tpbtn_width");
$tpbtn_height			             = $this->params->get("tpbtn_height");
$tptran_speed			             = $this->params->get("tptran_speed");

//bottom panel params
$bpopen_text			             = $this->params->get("bpopen_text");
$bpclose_text			             = $this->params->get("bpclose_text");
$bpbtn_width			             = $this->params->get("bpbtn_width");
$bpbtn_height			             = $this->params->get("bpbtn_height");
$bptran_speed			             = $this->params->get("bptran_speed");

// side panel params
$spbox_width			             = $this->params->get("spbox_width");
$spbtn_poz				             = $this->params->get("spbtn_poz");
$sptran_speed			             = $this->params->get("sptran_speed");
$sidepanel_module_style				 = $this->params->get("sidepanel_module_style");

?>
<?php 
// top panel
if ($yjsgTopPanel_loaded) { 
	$document->addStyleDeclaration("#yjsg_toppanel_open{height:".$tpbtn_height."px;width:".$tpbtn_width."px;line-height:".$tpbtn_height."px;}");
?>
<div id="yjsg_toppanel"<?php if($text_direction == 1){ ?>class="yjsgrtl"<?php } ?>>
	<div id="yjsg_toppanel_slide">
		<div id="yjsg_toppanel_content" class="yjsgsitew">
			<?php yjsg_print_grid_area('toppanel'); /* toppanel grid 1 tpan1=tpan5 */ ?>
		</div>
	</div>
	<a id="yjsg_toppanel_open" href="javascript:;">
		<?php echo $tpopen_text ?>
	</a>
	<div class="yjsg-clear-all"></div>
</div>
<script type="text/javascript">
function sizeTopPanel(){
    var YjsgTopPanel 				= $('yjsg_toppanel');
    var YjsgTopPaneSlide 			= $('yjsg_toppanel_slide');
	var YjsgTopPanelOpen			= $('yjsg_toppanel_open');
	var YjsgTopPanelDiv				= $('toppanel');
	var YjsgTopPanelGetHeight 		= YjsgTopPanelDiv.getSize().y;
	var YjsgTopPanelOpenGetHeight 	= YjsgTopPanelOpen.getSize().y;
    var isopen = YjsgTopPanelOpen.hasClass('TopPanOpen');
    if (isopen) {
        YjsgTopPanel.morph({
            height:YjsgTopPanelGetHeight + YjsgTopPanelOpenGetHeight
        });
        YjsgTopPaneSlide.morph({
            height:YjsgTopPanelGetHeight
        });
    }
}
window.addEvent('load', function () {
    var YjsgTopPanel 				= $('yjsg_toppanel');
    var YjsgTopPaneSlide 			= $('yjsg_toppanel_slide');
	var YjsgTopPanelOpen			= $('yjsg_toppanel_open');
	var YjsgTopPanelDiv				= $('toppanel');
	var YjsgTopPanelGetHeight 		= YjsgTopPanelDiv.getSize().y;
	var YjsgTopPanelOpenGetHeight 	= YjsgTopPanelOpen.getSize().y;
	
	YjsgTopPanel.setStyles({
		'height':YjsgTopPanelGetHeight + YjsgTopPanelOpenGetHeight,
		'top':-YjsgTopPanelGetHeight
	});
	YjsgTopPaneSlide.setStyles({
		'height':YjsgTopPanelGetHeight
	});
	
    YjsgTopPanelOpen.addEvent('click', function () {
        var YjsgTopPanGetPoz = YjsgTopPanel.getStyle('top').toInt();
        var YjsgTopMorph = new Fx.Morph(YjsgTopPanel, {
            duration: <?php echo $tptran_speed ?>,
            transition: Fx.Transitions.Sine.easeOut
        });
        if (YjsgTopPanGetPoz < 0) {
            YjsgTopMorph.start({
                'top': [-YjsgTopPaneSlide.getSize().y, 0]
            });
			YjsgTopPanelOpen.set('html','<?php echo $tpclose_text ?>').addClass('TopPanOpen');
        } else {
            YjsgTopMorph.start({
                'top': [0, -YjsgTopPaneSlide.getSize().y]
            });
			YjsgTopPanelOpen.set('html','<?php echo $tpopen_text ?>').removeClass('TopPanOpen');
        }
		sizeTopPanel();
    });
	
});
window.addEvent('resize', function () {
	sizeTopPanel();
});
</script>
<?php } ?>
<?php 
// bottom panel
if ($yjsgBotPanel_loaded) { 
	$document->addStyleDeclaration("#yjsg_botpanel_slide{margin-top:".$bpbtn_height."px;}#yjsg_botpanel_open{height:".$bpbtn_height."px;width:".$bpbtn_width."px;line-height:".$bpbtn_height."px;}");
?>
<div id="yjsg_botpanel"<?php if($text_direction == 1){ ?>class="yjsgrtl"<?php } ?>>
	<a id="yjsg_botpanel_open" href="javascript:;">
		<?php echo $bpopen_text ?>
	</a>
	<div id="yjsg_botpanel_slide">
		<div id="yjsg_botpanel_content" class="yjsgsitew">
			<?php yjsg_print_grid_area('botpanel'); /* botpanel grid 1 tpan1=tpan5 */ ?>
		</div>
	</div>
</div>
<script type="text/javascript">
function sizeBotPanel(){
    var YjsgBotPanel 				= $('yjsg_botpanel');
    var YjsgBotPaneSlide 			= $('yjsg_botpanel_slide');
	var YjsgBotPanelOpen			= $('yjsg_botpanel_open');
	var YjsgBotPanelDiv				= $('botpanel');
	var YjsgBotPanelGetHeight 		= YjsgBotPanelDiv.getSize().y;
	var YjsgBotPanelOpenGetHeight 	= YjsgBotPanelOpen.getSize().y;
    var isopen = YjsgBotPanelOpen.hasClass('BotPanOpen');
    if (isopen) {
        YjsgBotPanel.morph({
            height:YjsgBotPanelGetHeight + YjsgBotPanelOpenGetHeight
        });
        YjsgBotPaneSlide.morph({
            height:YjsgBotPanelGetHeight
        });
    }
}
window.addEvent('load', function () {
    var YjsgBotPanel 				= $('yjsg_botpanel');
    var YjsgBotPaneSlide 			= $('yjsg_botpanel_slide');
	var YjsgBotPanelOpen			= $('yjsg_botpanel_open');
	var YjsgBotPanelDiv				= $('botpanel');
	var YjsgBotPanelGetHeight 		= YjsgBotPanelDiv.getSize().y;
	var YjsgBotPanelOpenGetHeight 	= YjsgBotPanelOpen.getSize().y;
	
	YjsgBotPanel.setStyles({
		'height':YjsgBotPanelGetHeight + YjsgBotPanelOpenGetHeight,
		'bottom':-YjsgBotPanelGetHeight
	});
	YjsgBotPaneSlide.setStyles({
		'height':YjsgBotPanelGetHeight
	});
	
    YjsgBotPanelOpen.addEvent('click', function () {
        var YjsgBotPanGetPoz = YjsgBotPanel.getStyle('bottom').toInt();
        var YjsgTopMorph = new Fx.Morph(YjsgBotPanel, {
            duration: <?php echo $bptran_speed ?>,
            transition: Fx.Transitions.Sine.easeOut
        });
        if (YjsgBotPanGetPoz < 0) {
            YjsgTopMorph.start({
                'bottom': [-YjsgBotPaneSlide.getSize().y,0]
            });
			YjsgBotPanelOpen.set('html','<?php echo $bpclose_text ?>').addClass('BotPanOpen');
        } else {
            YjsgTopMorph.start({
                'bottom': [0, -YjsgBotPaneSlide.getSize().y]
            });
			YjsgBotPanelOpen.set('html','<?php echo $bpopen_text ?>').removeClass('BotPanOpen');
        }
		sizeBotPanel();
    });
});
window.addEvent('resize', function () {
	sizeBotPanel();
});
</script>
<?php } ?>
<?php
// side panel
if ($this->countModules('sidepanel')) {
	$document->addStyleDeclaration("#yjsg_sidepanel{width:".$spbox_width."px;right:-".($spbox_width -30)."px;}#yjsg_sidepanel_open{top:".$spbtn_poz.";}");
?>
<div id="yjsg_sidepanel"<?php if($text_direction == 1){ ?>class="yjsgrtl"<?php } ?>>
	<a id="yjsg_sidepanel_open" href="javascript:;"></a>
	<div id="yjsg_sidepanel_slide">
		<div id="yjsg_sidepanel_slideIn">
			<jdoc:include type="modules" name="sidepanel" style="<?php echo $sidepanel_module_style ?>" />
		</div>
	</div>
</div>
<script type="text/javascript">
window.addEvent('load', function () {
    var YjsgPanGetHeight 	= window.getSize().y;
    var YjsgSidePanel 		= $('yjsg_sidepanel');
    var YjsgSidePaneSlide 	= $('yjsg_sidepanel_slide');
	var YjsgSidePanelOpen	= $('yjsg_sidepanel_open');
	
    YjsgSidePanel.setStyle('height', YjsgPanGetHeight);
    YjsgSidePaneSlide.setStyle('height', YjsgPanGetHeight);
    YjsgSidePanelOpen.addEvent('click', function () {
        var YjsgSidePanGetPoz = YjsgSidePanel.getStyle('right').toInt();
        var YjsgSideMorph = new Fx.Morph(YjsgSidePanel, {
            duration: <?php echo $sptran_speed?>,
            transition: Fx.Transitions.Sine.easeOut
        });
        if (YjsgSidePanGetPoz < 0) {
            YjsgSideMorph.start({
                'right': [-<?php echo $spbox_width -30 ?>, 0]
            });
			YjsgSidePanelOpen.addClass('SidePanOpen');
        } else {
            YjsgSideMorph.start({
                'right': [0, -<?php echo $spbox_width -30 ?>]
            });
			YjsgSidePanelOpen.removeClass('SidePanOpen');
        }
    });
});
</script>
<?php } ?>