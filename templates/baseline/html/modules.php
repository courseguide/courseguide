<?php
defined('_JEXEC') or die('Restricted access');
/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * three arguments.
 */
/**
 * Custom module chrome, echos the whole module in a <div> and the header in <h{x}>. The level of
 * the header can be configured through a 'headerLevel' attribute of the <jdoc:include /> tag.
 * Defaults to <h2> if none given
 */
 /* usable module chromes
 
 YJsghtml  = square mods  2 divs 
 YJsground = round mods  adding class addround
 YYsgplain = square mods , no title no additional divs
 
  */
 // DEFAULT SQUARE



function modChrome_YJsgxhtml($module, &$params, &$attribs){
	$module_tag 	= $params->get('module_tag','div');
	$header_tag 	= $params->get('header_tag','h2');
	if(intval(JVERSION) >= 3 && $params->get('bootstrap_size') !=0) {
		$bootstrap_size = ' span'.$params->get('bootstrap_size','').'';
		$header_class 	= ' class="module_title '.$params->get('header_class','').'"';
	}else{
		$bootstrap_size ='';
		$header_class 	=' class="module_title"';
	}
	// force width
	if(strstr($params->get('moduleclass_sfx'),'forceright')){
		$widthis = explode('forceright',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:right;"';
	}elseif(strstr($params->get('moduleclass_sfx'),'forceleft')){
		$widthis = explode('forceleft',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:left;"';
	}else{
		$add_mod_width='';
	}
	
	// icon suffix
	$show_icon ='';
	if (strstr($params->get('moduleclass_sfx'),'icon-')){
		$findIcon =  explode(' ',$params->get('moduleclass_sfx'));
		foreach($findIcon as $key =>$value){
			if(strstr($value,'icon-')){
				$has_icon[]= $value;
				
			}else{
				$has_icon[]='';
			}
		}
		
		$show_icon = '<span class="'.implode($has_icon,' ').'"></span>';
	}
	// title span
	$title = $module->title;
	$title = explode(' ', $title);
	$title[0] = '<span>'.$title[0].'</span>';
	$title= join(' ', $title);
	$title = str_replace("&","&amp;",$title);
?>
<<?php echo $module_tag ?> class="yjsquare <?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?><?php echo $bootstrap_size ?>"<?php echo $add_mod_width?>>
  <?php if ($module->showtitle) : ?>
  <div class="h2_holder">
    <<?php echo $header_tag.$header_class ?>>
      <?php echo $show_icon.$title;?>
    </<?php echo $header_tag ?>>
  </div>
  <?php endif; ?>
  <div class="yjsquare_in"><?php echo $module->content; ?></div>
</<?php echo $module_tag ?>>
<?php } ?>
<?php 
// DEFAULT PLAIN OUTPUT NO TITLE OR SURROUNDING DIVS
function modChrome_YJsgplain($module, &$params, &$attribs){
	$module_tag 	= $params->get('module_tag','div');
	$header_tag 	= $params->get('header_tag','h2');
	if(intval(JVERSION) >= 3 && $params->get('bootstrap_size') !=0) {
		$bootstrap_size = ' span'.$params->get('bootstrap_size','').'';
	}else{
		$bootstrap_size ='';
	}
	$header_class 	= $params->get('header_class','');
	// force width
	if(strstr($params->get('moduleclass_sfx'),'forceright')){
		$widthis = explode('forceright',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:right;"';
	}elseif(strstr($params->get('moduleclass_sfx'),'forceleft')){
		$widthis = explode('forceleft',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:left;"';
	}else{
		$add_mod_width='';
	}
?>
<<?php echo $module_tag ?> class="yjplain <?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?><?php echo $bootstrap_size ?>"<?php echo $add_mod_width?>>
<?php echo $module->content; ?></<?php echo $module_tag ?>>
<?php } ?>
<?php 
// DEFAULT ROUND CORNERS
function modChrome_YJsground($module, &$params, &$attribs) {
	$module_tag 	= $params->get('module_tag','div');
	$header_tag 	= $params->get('header_tag','h2');
	if(intval(JVERSION) >= 3 && $params->get('bootstrap_size') !=0) {
		$bootstrap_size = ' span'.$params->get('bootstrap_size','').'';
		$header_class 	= ' class="module_title '.$params->get('header_class','').'"';
	}else{
		$bootstrap_size ='';
		$header_class 	=' class="module_title"';
	}
	// force width
	if(strstr($params->get('moduleclass_sfx'),'forceright')){
		$widthis = explode('forceright',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:right;"';
	}elseif(strstr($params->get('moduleclass_sfx'),'forceleft')){
		$widthis = explode('forceleft',$params->get('moduleclass_sfx'));
		$widthis = explode(' ',$widthis[1]);
		$add_mod_width = ' style="width:'.$widthis[0].'px;float:left;"';
	}else{
		$add_mod_width='';
	}
	// icon suffix
	$show_icon ='';
	if (strstr($params->get('moduleclass_sfx'),'icon-')){
		$findIcon =  explode(' ',$params->get('moduleclass_sfx'));
		foreach($findIcon as $key =>$value){
			if(strstr($value,'icon-')){
				$has_icon[]= $value;
				
			}else{
				$has_icon[]='';
			}
		}
		
		$show_icon = '<span class="'.implode($has_icon,' ').'"></span>';
	}
	// title span
	$title = $module->title;
	$title = explode(' ', $title);
	$title[0] = '<span>'.$title[0].'</span>';
	$title= join(' ', $title);
	$title = str_replace("&","&amp;",$title);	
?>
<<?php echo $module_tag ?> class="yjsquare addround <?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?><?php echo $bootstrap_size ?>"<?php echo $add_mod_width?>>
  <?php if ($module->showtitle) : ?>
   <div class="h2_holder">
    <<?php echo $header_tag.$header_class ?>>
      <?php echo $show_icon.$title;?>
    </<?php echo $header_tag ?>>
  </div>
  <?php endif; ?>
  <div class="yjsquare_in"><?php echo $module->content; ?></div>
</<?php echo $module_tag ?>>
<?php } ?>