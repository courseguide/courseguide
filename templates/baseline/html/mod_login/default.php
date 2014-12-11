<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
 if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')) { 
 		$addinputc ='';
 }else{
	 	$addinputc =' nbs';
 }
?>
<?php if ($type == 'logout') : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
	<?php if($params->get('name') == 0) : {
		echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?>
	</div>
<?php endif; ?>
	<div class="logout-button">
        <button type="submit" tabindex="3" name="Submit" class="btn btn-small button"><?php echo JText::_('JLOGOUT') ?></button>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
        <?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php else : ?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
	<div class="pretext">
	<?php echo $params->get('pretext'); ?>
	</div>
	<div class="input-prepend<?php echo $addinputc ?>">
    	<?php if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')) { ?>
    	<span class="add-on"><span class="icon-user"></span></span>
        <?php } ?>
		<input id="modlgn_username" type="text" name="username" class="inputbox"  size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
    </div>
    <div class="input-prepend<?php echo $addinputc ?>">
    	<?php if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')) { ?>
    	<span class="add-on"><span class="icon-lock"></span></span>
        <?php } ?>
		<input id="modlgn_passwd" type="password" name="password" class="inputbox" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
    </div>
        
        
        
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
       <label class="checkbox" id="modlgn_remember_l">
        <input id="modlgn_remember" type="checkbox"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
      </label>
	<?php endif; ?>
    <button type="submit" tabindex="3" name="Submit" class="btn btn-small button"><?php echo JText::_('JLOGIN') ?></button>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="posttext">
	<?php echo $params->get('posttext'); ?>
	</div>
</form>
<?php endif; ?>
