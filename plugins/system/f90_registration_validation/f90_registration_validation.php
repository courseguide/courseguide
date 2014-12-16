<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Registration Validation
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemF90_registration_validation extends JPlugin
{        
	protected $autoloadLanguage = true;
	public function onBeforeRender()
	{
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			return true;
		}
		
		if($app->input->get('option', '') != 'com_users'){
			return true;
		}
		
		if($app->input->get('view', '') != 'registration'){
			return true;
		}
		
		JText::script('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_REGISTER_EMAIL2_MESSAGE');
		JText::script('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_REGISTER_PASSWORD1_MESSAGE');
		
		JHtml::script('plugins/'.$this->_type.'/'.$this->_name.'/tmpl/validation.js');

		return true;
	}
	
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();
		if($app->input->get('plg', '') != 'f90_registration_validation'){
			return true;
		}
	
		$task = $app->input->get('task', '');
		switch ($task){
			case 'validate_password' : 
					$password = $app->input->post->getHTML('password', '');
					$response = $this->isPasswordValid($password);
					break;
					
			case 'validate_username' : 
					$username = $app->input->post->getString('username', '');
					$response = array('error' => false, 'msg' => '');
					if($this->isUsernameExists($username)){
						$response = array('error' => true, 'msg' => JText::_('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_USERNAME_MESSAGE'));
					}
					break;
					
			case 'validate_email' : 
					$email 	  = $app->input->post->getHTML('email', '');
					$response = $this->validateEmail($email);
					break;
		}
		
		echo json_encode($response);
		exit();		
	}
	
	public function isUsernameExists($username)
	{
		// Get the database object and a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('COUNT(*)')
			->from('#__users')
			->where('username = ' . $db->quote($username));
			
		// Set and query the database.
		$db->setQuery($query);
		$duplicate = (bool) $db->loadResult();

		if ($duplicate)
		{
			return true;
		}

		return false;
	}
	
	public function validateEmail($value)
	{
		if (empty($value))
		{
			return array('error' => false, 'msg'=>'');
		}
		
		$regex = '^[a-zA-Z0-9.!#$%&‚Äô*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$';
		// Test the value against the regular expression.
		if (!preg_match(chr(1) . $regex . chr(1) , $value))
		{
			return array('error' => true,  'msg' => JText::_('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_INVALID_EMAIL'));
		}
		
		// Get the database object and a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('COUNT(*)')
			->from('#__users')
			->where('email = ' . $db->quote($value));
			
		// Set and query the database.
		$db->setQuery($query);
		$duplicate = (bool) $db->loadResult();

		if ($duplicate)
		{
			return array('error' => true, 'msg' => JText::_('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_REGISTER_EMAIL1_MESSAGE'));
		}
		
		return array('error' => false, 'msg' => '');
	}
	
	public function isPasswordValid($value)
	{
		$meter		= '1';
		$threshold	= 66;
		$minimumLength = 4;
		$minimumIntegers = 0;
		$minimumSymbols = 0;
		$minimumUppercase = 0;

		// If we have parameters from com_users, use those instead.
		// Some of these may be empty for legacy reasons.
		$params = JComponentHelper::getParams('com_users');

		if (!empty($params))
		{
			$minimumLengthp = $params->get('minimum_length');
			$minimumIntegersp = $params->get('minimum_integers');
			$minimumSymbolsp = $params->get('minimum_symbols');
			$minimumUppercasep = $params->get('minimum_uppercase');
			$meterp = $params->get('meter');
			$thresholdp = $params->get('threshold');

			empty($minimumLengthp) ? : $minimumLength = (int) $minimumLengthp;
			empty($minimumIntegersp) ? : $minimumIntegers = (int) $minimumIntegersp;
			empty($minimumSymbolsp) ? : $minimumSymbols = (int) $minimumSymbolsp;
			empty($minimumUppercasep) ? : $minimumUppercase = (int) $minimumUppercasep;
			empty($meterp) ? : $meter = $meterp;
			empty($thresholdp) ? : $threshold = $thresholdp;
		}
	

		if (empty($value))
		{
			return array('error' => false, 'msg'=>'');
		}

		$valueLength = strlen($value);

		/*
		 * We set a maximum length to prevent abuse since it is unfiltered.
		 * 55 is the length we use because that is roughly the maximum for bcrypt
		 */
		if ($valueLength > 55)
		{
			return array('error' => true, 'msg' => JText::_('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_PASSWORD_TOO_LONG'));
		}

		// We don't allow white space inside passwords
		$valueTrim = trim($value);

		// Set a variable to check if any errors are made in password
		$validPassword = true;

		if (strlen($valueTrim) != $valueLength)
		{
			return array('error' => true, 'msg' => JText::_('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_SPACES_IN_PASSWORD'));
		}

		// Minimum length option
		if (!empty($minimumLength))
		{
			if (strlen((string) $value) < $minimumLength)
			{
				return array('error' => true, 'msg' => JText::plural('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_PASSWORD_TOO_SHORT_N', $minimumLength));
			}
		}
		
		// Minimum number of integers required
		if (!empty($minimumIntegers))
		{
			$nInts = preg_match_all('/[0-9]/', $value, $imatch);

			if ($nInts < $minimumIntegers)
			{
				return array('error' => true, 'msg' => JText::plural('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_NOT_ENOUGH_INTEGERS_N', $minimumIntegers));
			}
		}

		// Minimum number of symbols required
		if (!empty($minimumSymbols))
		{
			$nsymbols = preg_match_all('[\W]', $value, $smatch);

			if ($nsymbols < $minimumSymbols)
			{
				return array('error' => true, 'msg' => JText::plural('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols));
			}
		}

		// Minimum number of upper case ASII characters required
		if (!empty($minimumUppercase))
		{
			$nUppercase = preg_match_all("/[A-Z]/", $value, $umatch);

			if ($nUppercase < $minimumUppercase)
			{
				return array('error' => true, 'msg' => JText::plural('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase));
			}
		}		

		return array('error' => false, '');
	}
}

