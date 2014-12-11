<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');


$document = &JFactory::getDocument();
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'element/recaptchalib.php');
$publickey = $params->get('public');
$privatekey= $params->get('private');
$error='';
$document->addScript(JURI::root() .'media/system/js/validate.js');

$jquery_source=$params->get('jqueryload');
if($jquery_source=='local')
{
$document->addScript(JURI::root() .'modules/mod_loginregister/tmpl/element/jquery.min.js');
}
else
{
$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
}


$document->addCustomTag('<script type="text/javascript">
var dom = {};
dom.query = jQuery.noConflict(true);
   function checkcapcha(){
   var agree=""; 
		if(agree= document.getElementById("formagree"))
		{
     if (!agree.checked) 
	 {
        alert("You Must Agree to the Terms of Use.");
        return false;
		} 
	 }
   var chell;    
if(chell=document.getElementById("recaptcha_challenge_field"))
{
	   var chell=   document.getElementById("recaptcha_challenge_field").value;

       var resp = document.getElementById("recaptcha_response_field").value ;
       var prikey = "'.$privatekey.'";
       document.getElementById("myDiv").innerHTML="";


var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();

  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {

  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
                            var responss=xmlhttp.responseText;
                            //alert (responss);
                             if(responss =="false2"){
                                       document.getElementById("myDiv").innerHTML= "'.JText::_( 'MOD_INVALID_KEY').'" ;
                                    }else{
                                       if(responss =="true")  {
                                       document.josForm.submit();
                                                      }else   { document.getElementById("myDiv").innerHTML= "'.JText::_( 'MOD_INVALID_CAPTCHA').'" ;
                                                                Recaptcha.reload ();
                                    }
                                    }
    }else  document.getElementById("myDiv").innerHTML= "<img src=\"'.JURI::root().'modules/mod_loginregister/tmpl/element/loads.gif\" border=\"0\">" ;
  }

xmlhttp.open("GET","'.JURI::root().'modules/mod_loginregister/tmpl/element/captchacheck.php?field1="+chell+"&field2="+resp+"&field3="+prikey,true);
xmlhttp.send();

   return false;
 }
}

   function xi(s){
                    if(s=="y") {
                    dom.query(".popup_register").hide(300);
                    dom.query(".passwret").show(300);
                    dom.query("#form2").hide();
                     dom.query("#form1").show();

                      }
                    if(s=="n")
                    {dom.query(".popup_register").show(300);
                    dom.query(".passwret").hide(300);
                    dom.query("#form2").show();
                     dom.query("#form1").hide();
                    }
                  }



</script>');
//JQuery Load
	
if($params->get('disablelog'))
	{

		$document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
	
		$flags=1;
					}
	else
	{
		$flags=0;
	}
 /* if($params->get('disablelog')){
 $flags= 1;

$document->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );
//$document->addScript(JURI::root() .'modules/mod_loginregister/tmpl/element/fade.js');
 }
 else{$flags=0; } */

function jm_getthem($params)
	{
		switch ($params->get('jmtheme'))
		{
			case '0':
				return 'red';
				break;
			case '1':
				return 'white';
				break;
			case '2':
				return 'blackglass';
				break;
			case '3':
				return 'clean';
				break;
		}
	}
 ?>
 <?php
 
$usersConfig = &JComponentHelper::getParams( 'com_users' );
 if(!$flags && $usersConfig->get('allowUserRegistration') && $type != 'logout' ) :
 $check= $params->get('checkbox1'); 
 if($check == 1) {
 
 ?>
<div class="btn-group" data-toggle="buttons-radio">
	  <button type="button" onclick="xi('y')" class="btn btn-primary <?php if($params->get('view')==0) echo 'active"';?>"><?php echo JText::_('LOGIN') ?></button>
	  <button type="button" onclick="xi('n')" class="btn btn-primary <?php if($params->get('view')==1) echo 'active"';?>"><?php echo JText::_('REGISTER'); ?></button>
	</div>
	<div class="clearfix"></div>
	<br/>
   <?php } endif; ?>


<?php if($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGINREGISTER_HINAME', $user->get('name'));
	} else : {
		echo JText::sprintf('MOD_LOGINREGISTER_HINAME', $user->get('username'));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button">
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>
<?php elseif(!$params->get('disablelog')) : ?>
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var modlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; ?>


<div style="margin:0px;display:<?php if($params->get('view')) {echo "none";} else {echo "block" ;}?>;" class="passwret">
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
	<?php if ($params->get('pretext')) : ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="userdata">
		<div id="form-login-username" class="control-group">
			<div class="controls">
				<?php if (!$params->get('usetext')) : ?>
					<div class="input-prepend input-append">
						<span class="add-on">
							<span class="icon-user tip" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></span>
							<label for="modlgn-username" class="element-invisible"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
						</span>
						<input id="modlgn-username" type="text" name="username" class="input-medium" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
					</div>
				<?php else: ?>
					<label for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
						<input id="modlgn-username" type="text" name="username" class="input-medium" tabindex="0" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
				<?php endif; ?>
			</div>
		</div>
		<div id="form-login-password" class="control-group">
			<div class="controls">
				<?php if (!$params->get('usetext')) : ?>
					<div class="input-prepend input-append">
						<span class="add-on">
							<span class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>">
							</span>
								<label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
							</label>
						</span>
						<input id="modlgn-passwd" type="password" name="password" class="input-medium" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
				</div>
				<?php else: ?>
					<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
					<input id="modlgn-passwd" type="password" name="password" class="input-medium" tabindex="0" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
				<?php endif; ?>

			</div>
		</div>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="control-group checkbox">
			<label for="modlgn-remember" class="control-label"><input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label> 
		</div>
		<?php endif; ?>
		<div id="form-login-submit" class="control-group">
			<div class="controls">
				<button type="submit" tabindex="0" name="Submit" class="btn btn-primary btn"><?php echo JText::_('JLOGIN') ?></button>
			</div>
		</div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<ul class="unstyled">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
					  <?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>

			</ul>
		<?php endif; ?>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
</div>

<?php endif;   ?>

             <?php
			 
			 if($params->get('view')) 
			 $flag2=1;
			 else $flag2=0;
			      ?>

<div style="margin:0px; display:<?php if($flag2 && $type != 'logout' || ($flag2==0 && $params->get('disablelog')== 1)) {echo "block";} else {echo "none" ;}?>;" class="popup_register registration2">
	<?php if ($params->get('regpretext')) : ?>
		<div class="regpretext">
		<p><?php echo $params->get('regpretext'); ?></p>
		</div>
		<br/>
	<?php endif; ?>
 		<form action="<?php echo JRoute::_( 'index.php?option=com_users&task=registration.register' ); ?>" method="post" id="login-form" name="josForm" class="form-validate form-horizontal"  onSubmit="return checkcapcha()" >

					<div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="jform_spacer-lbl" class=""><strong class="red">*</strong> Required field</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						
						</div>
					</div>
				
				
					<div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="namemsg" for="name">
								<?php echo JText::_( 'JNAME' ); ?>:
							*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-user tip" title="User Name"></span>
						</span>
							<input tabindex="1" type="text" name="jform[name]" id="jform_name" size="20" value="" class="inputbox required" />
						</div>
						</div>
					</div>
					
					
					<div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="usernamemsg" for="username">
							<?php echo JText::_( 'USERNAME' ); ?>:
						*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-user tip" title="User Name"></span>
						</span>
							<input tabindex="2" type="text" id="jform_username" name="jform[username]" size="20" value="" class="inputbox validate-username required"  />
						</div>
						</div>
					</div>

                    <div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="pwmsg" for="password">
							<?php echo JText::_( 'PASSWORD' ); ?>:
						*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-lock tip" title="Password"></span>
						</span>
							<input tabindex="3" class="inputbox validate-password required" type="password" id="jform_password1" name="jform[password1]" size="20" value=""  />
						</div>
						</div>
					</div>

					
					<div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="pw2msg" for="password2">
                             <?php echo JText::_( 'VERIFY_PASSWORD' ); ?>:
						*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-lock tip" title="Verify Password"></span>
						</span>
							<input tabindex="4"  class="inputbox validate-password required" type="password" id="jform_password2" name="jform[password2]" size="20" value=""  />
						</div>
						</div>
					</div>

                    <div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="emailmsg" for="email">
							<?php echo JText::_( 'EMAIL' ); ?>:
						*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-envelope tip" title="Email"></span>
						</span>
							<input tabindex="5" type="text" id="jform_email1" name="jform[email1]" size="20" value="" class="inputbox validate-email required" />
						</div>
						</div>
					</div>

                    <div class="control-group">
						<div class="control-label">
							<span class="spacer"><span class="before"></span>
							<span class="text"><label id="emailmsg" for="email">
							<?php echo JText::_( 'VERIFY_EMAIL' ); ?>:
						*</label></span>
							<span class="after"></span>
							</span>						
						</div>
						<div class="controls">
						<div class="input-prepend">
						<span class="add-on">
							<span class="icon-envelope tip" title="Verify Email"></span>
						</span>
							<input tabindex="6" type="text" id="jform_email2" name="jform[email2]" size="20" value="" class="inputbox validate-email required" />
						</div>
						</div>
					</div>

	  <?php 
	 $tou= $params->get('tou');
	$articleid= $params->get('articleid');
	$newwindow= $params->get('newwindow');
	$title= $params->get('title');
	 $check= $params->get('checkbox');
	 if($check=='checked')
	 {
	$terms_of_use="<input name='terms' type='checkbox' checked='checked' tabindex='7' id='formagree' />&nbsp<a href='index.php?option=com_content&view=article&id=$articleid' target='$newwindow'> $title </a><br>";
		
	 }
	 else
	 $terms_of_use="<input name='terms' type='checkbox' tabindex='8' id='formagree' /><a href='index.php?option=com_content&view=article&id=$articleid' target='$newwindow'> $title </a><br>";
	
	if ($tou==1) {
	echo $terms_of_use;
	 } 
	 else 
	 {
	
		}
	?>


  <?php
 if ($params->get('enablecap') ) :
      if($publickey && $privatekey):
                    $theme= jm_getthem($params);

                    echo recaptcha_get_html($publickey, $error, $theme);
                    echo'<div style="height:130px; margin:0px; padding0px;"> </div>';
		      else: echo '<div style="color:red;font-weight:bold; margin:0px; padding0px;">'.JText::_( 'Enter a valid Recaptcha Public and Private key.').'</div>';
	  
    endif;
 endif; ?><br>
 
 <div id="myDiv" style="color: #CF1919;  font-weight: bold;   margin: 0 0 0 20px;   padding: 0 0 0 20px; "></div>
 
 <input type="submit"  name="Submit" class="btn btn-primary validate" value="<?php echo JText::_('JREGISTER') ?>" /><BR/>
                      <input type="hidden" value="com_users" name="option">
			<input type="hidden" value="registration.register" name="task">
			<input type="hidden" value="1" name="adf3f0a374d112893c41c9de2abd5c54">
			
					<?php echo JHTML::_('form.token'); ?>
				</form>
	<?php if ($params->get('regposttext')) : ?>
	<br/>			
		<div class="regposttext">
		<p><?php echo $params->get('regposttext'); ?></p>
		</div>
	<?php endif; ?>

</div>
<?php 
 $usersConfig = &JComponentHelper::getParams( 'com_users' );
  if(!$flags && $usersConfig->get('allowUserRegistration') && $type != 'logout' ) :
  $check= $params->get('checkbox1'); 
 if($check== 0) {?>
	<div class="btn-group" data-toggle="buttons-radio">
	  <button type="button" onclick="xi('y')" class="btn btn-primary <?php if($params->get('view')==0) echo 'active"';?>"><?php echo JText::_('LOGIN') ?></button>
	  <button type="button" onclick="xi('n')" class="btn btn-primary <?php if($params->get('view')==1) echo 'active"';?>"><?php echo JText::_('REGISTER'); ?></button>
	</div>
   <?php }
?>


<?php endif;?>
<style>
#login-form label {
    display: block;
    float: left;
    margin-right: 10px;
    width: 9.4em;
}
</style>