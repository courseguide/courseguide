<?php
/**
 * @version		1.0.0
 * @package		Polar Vote
 * @author		StyleWare - http://www.styleware.eu
 * @copyright	Copyright (c) 2010 - 2014 StyleWare, All rights reserved.
 * @license		GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');


class plgContentPolarVote extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{


		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, 'polarvote') === false && strpos($article->text, 'polarvote') === false)
		{
			return true;
		}

		// Expression to search for (positions)
		$regex		= '/{polarvote\s(.*?)}/i';

		// Find all instances of plugin and put in $matches for loadposition
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				$position = trim($matcheslist[0]);
				
			
			
			$output='<iframe 
							seamless="seamless" 
							style="border: none; overflow: hidden;" 
							height="450" 
							width="100%" 
							scrolling="no" 
							src="http://assets-polarb-com.a.ssl.fastly.net/api/v4/publishers/styleware/embedded_polls/iframe?poll_id='.$position.'">
					</iframe>';


				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}

	

	} // END FUNCTION

} // END CLASS
