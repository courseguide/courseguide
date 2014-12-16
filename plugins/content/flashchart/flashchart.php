<?php
/**
 * flashChart Joomla! Content Plugin
 *
 * @author     Joachim Schmidt <joachim.schmidt@jschmidt-systembeatung.de>
 * @copyright  Copyright (C) 2011 Joachim Schmidt. All rights reserved.
 * @license    GNU General Public License version 2 or later
 * see: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * change activity:
 *
 * 12.01.2011: Release V1.0.0 for Joomla 1.6
 * 22.01.2011: Release V1.1 (sql interface redesigned)
 * 27.01.2011: Release V1.1.1
 * Added support for converting chart to image
 * 12.02.2011: Release V1.1.3
 * Added support for transparency option for chart elements
 * changed interface for converting images (option now in OFC/SWF menu via mouse right-click)
 * Added Optional parameters to specify min/max y-axis values
 * Added Optional parameter "sql_labels=0" to request data from 1. column instead of labels
 * 17.02.2011: Release V1.1.4
 * changed processing: instead posting chart-data.php now use javscript function to load chart
 * this solves the problem with code-pages - now all data ist coded in UTF-8
 * 28.02.2011: Release 1.1.5
 * added option to have muliple colored bars on barchart with single datagroup
 * added option to show "alert-color" and "alert-tooltip" on barcharts with single datagroup
 * and line chart if data-value is higher/lower than alert value
 * enable option to use 2 OFC interfaces (javascript and HTTP GET/POST)
 * 15.03.2011: Release 1.1.6
 * added color option for labels of y-axis
 * added support for horizontal bar charts (type="hbar")
 * added options to set x-axis minimum ("x_min") and maximum ("x_max") values (options for type="hbar")
 * added options to use Joomla's JRequest variables in string-tags (sql, title)
 *
 * 11.04.2011:
 * added support for debugging code - parameter "debug"
 * added check for valid file and valid url (cause error if invalid)
 *
 * 14.05.2011:
 * added flash-parm 'wmode':'opaque' to avoid overlapping of html-objects
 *
 * 05.06.2011:  Release 1.1.7
 * added circumvention for Joomla's V1.6 editor problem: correct editor genned "&quot;" for double quotes to '"'
 * added support for multiple alert values, alert tooltips and alert colors
 * added support for defining number of decimal points
 * added suport for radar charts
 *
 * 23.07.2011:
 * added support for displaying chart (after creation) as image
 *
 * 18.08.2011:  Release 1.1.8
 * added support for popup window and shadowbox (included complete javascript libs for shadowbox)
 * parameter: data="popup_window" and data="shadow_box"
 *
 * 22.12.2011:
 * check for installed / activated flash plugin
 *
 * 14.01.2012:  Release 1.1.9
 * modified check for installed / activated flash plugin
 * added css style option for title, axis legend and tooltip
 * added option to hide pie-labels: "hide_pie_labels"
 * added option to set values to pie-labels: "pie_label_values"
 * added option to truncate x-axis labels and pie-labels: "x_label_truncate"
 *
 * 15.02.2012:  Release 1.2.0
 * added new function which implements click events
 * added option for pie legend
 * added option for pie radius
 * added option for right legend
 * added option for ofc menu
 * added option for values on bar and line charts
 * added option to create and imbed javascript files used for click events
 * added option for background image
 * added option for rearranging data for stacked bars (type="bar_stack")
 * changed parameter 'bcolor' to more meaningful name 'bg_color'
 *
 * 25.04.2012:  Release 1.2.1
 * added support for data="formula" (generate data from math-formula)
 * added support for scatter charts ("scatter_animation")
 * added support for regression and trend lines ("show_regression_line", "show_regression_formula", "show_trend")
 * added parameter 'flashchart_jquery' to have option for alternate jquery javascript library
 * changed parameter 'c_color' to more meaningful name 'chart_colors'
 * fixed bug for x-values on scatter charts
 *
 * 31.05.2013
 * changed api-call depricated dbLoadResultArray to loadColumn()
 *
 * 05.12.2013
 * Version 1.2.2: removed option 'ofc_interface' (use of POST/GET Interface)
 *
 * 20.12.2013
 * Version 1.2.2: added modal (bootstrap) windows support
 *
 * 23.01.2014
 * Version 1.2.2: redesign/recode support for modal windows (popup_window)
 *
 * 06.02.2014:
 * Version 1.2.2: add parameter "modal_title" as optional title for modal windows
 * restructure code (use helper class)
 *
 */

defined('_JEXEC') or die('Restricted access');

class plgContentflashChart extends JPlugin
{

	var $shadowbox_script;

	var $init_script;

	var $bootstrap_resources;

	var $msgbox_script;

	function plgContentflashChart(&$subject, $params)
	{
		parent::__construct($subject, $params);
		$this->_plugin = JPluginHelper::getPlugin('content', 'flashchart');
	}

	function onContentPrepare($context, $row, &$params, $page = 0)
	{

		// A database connection is created
		$db = JFactory::getDBO();

		// Here there is a check to determine whether it should process further
		if (JString::strpos($row->text, '{flashchart') === false)
			return true;
		else
		{
			$regex = "#{flashchart\\s*(.*?)}(.*?){/flashchart}#s";

			// Perform the replacement
			preg_match_all($regex, $row->text, $matches);
			$count = count($matches[0]);
			if ($count)
			{
				include_once 'helper.php';
				$this->helper = new plgContentflashChartHelper();

				$this->replaceContent($row, $matches, $count, $regex, $this->params);
				return true;
			}
			else
				return true;
		}
	}

	function replaceContent($row, $matches, $count, $regex, $params)
	{

		JLoader::import('joomla.version');
		$version = new JVersion();
		$lang = JFactory::getLanguage();
		$document = JFactory::getDocument();
		$base = JURI::root(true) . '/plugins/content/flashchart';
		$url_base = JURI::base() . 'plugins/content/flashchart';
		$lang_subtag = explode("-", $lang->getTag());
		$plugin_parms = $params->toString();
		$plugin_parms = json_decode($plugin_parms, true);

		$rc = $lang->load('plg_content_flashchart', JPATH_ADMINISTRATOR, null, true, true);
		if (!$rc)
		{
			$lang->load('plg_content_flashchart', JPATH_ADMINISTRATOR, "en-GB");
			$lang_subtag[0] = "en";
		}

		for ($i = 0; $i < $count; $i++)
		{
			$replace = "";
			$props = array();
			$param_line = $this->helper->convert2Array($matches[1][$i]);
			$id = trim($matches[2][$i]);

			$result = $this->helper->getChartProperties($id, $plugin_parms, $param_line);
			$error_string = $result['error'];
			$props = $result['props'];

			$id = $props['chart_id'];
			$width = $props['width'];
			$height = $props['height'];

			// add flashchart resources
			if ($this->init_script === null && $error_string == "")
			{
				$this->init_script = true;
				$script = "                var playerVersion = swfobject.getFlashPlayerVersion();";
				$document->addScriptDeclaration($script);

				$document->addScript(JURI::base() . 'plugins/content/flashchart/js/swfobject.js');
				$document->addScript(JURI::base() . 'plugins/content/flashchart/js/flashchart-min.js');

				if (ini_get('browscap') != "" && ini_get('browscap') !== false)
				{
					$browser = get_browser(null, true);
					if (stristr($browser['browser_name_pattern'], "msie") && $browser['majorver'] < 10)
						$document->addScript(JURI::base() . 'plugins/content/flashchart/js/json/json2.js');
				}

				if ($props['flashchart_jquery'] == "1")
				{
					if (version_compare($version->RELEASE, '2.5', '<='))
					{
						if (JFactory::getApplication()->get('jquery') !== true)
						{
							$document->addScript(JURI::base() . 'plugins/content/flashchart/js/jquery.min.js');
							JFactory::getApplication()->set('jquery', true);
						}
					}
					else
						JHtml::_('jquery.framework');
				}
			}

			if ($props['gen_chart'] && $error_string == "")
			{
				if ($props['data'] == 'json')
				{
					$data = $this->helper->getJsonfromUrl($props['url']);
				}
				else
				{
					$lib = dirname(__FILE__) . '/lib/chart-data-v1.4.php';
					require_once $lib;

					$chart = new chart_data();
					$data = $chart->json_data($props);
					$data = $data->toString();
				}

				if ($props['gen_chart'] == true && (array_key_exists('onclick', $props) || array_key_exists('key_onclick', $props) || array_key_exists('alert_onclick', $props)))
				{
					$document->addScript(JURI::base() . 'plugins/content/flashchart/js/jquery_msgbox-min.js', null, true, false);
					$document->addStyleSheet(JURI::base() . 'plugins/content/flashchart/css/msgbox.css');
				}

				if ($props['create_script'])
				{
					$script = " function " . $props['create_script'] . "() {";
					$script .= "\n          swfobject.embedSWF('$url_base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
					$script .= "\n        { 'get-data':'get_data_" . $props['create_script'] . "', 'id':'$id', 'loading':'" . JText::_('LOADING_DATA') . "'}, {'wmode':'opaque'} );";
					$script .= "\n }";
					$script .= "\n\n function get_data_" . $props['create_script'] . "() {";
					$script .= "\n   var data_" . $props['create_script'] . " = $data;";
					$script .= "\n   return ( JSON.stringify(data_" . $props['create_script'] . ") );";
					$script .= "\n }";

					if ($props['hide_chart'] == "1")
						$replace = '';

					$document->addScriptDeclaration($script);
				}

				if ($props['modal_chart'])
				{
					if (!$this->bootstrap_resources)
					{
						$this->helper->addBootrapResources($base, $version, $document);
						$this->bootstrap_resources = true;
					}
					$replace = $this->helper->createModalChart($url_base, $props, $data);
				}

				elseif (!$props['hide_chart'])
					$replace = $this->helper->createChart($url_base, $props, $data);
			}

			if ($error_string == "" && ($props['shadow_box'] || $props['popup_window']))
			{
				$qq = '"';
				if ($props['title'] == '')
					$props['title'] = "popup window ($id)";

				if ($props['shadow_box'])
				{
					$replace = "<a rel='shadowbox;width=$width;height=$height' href='" . $props['url'] . "'>" . $props['title'] . "</a>";
					if ($this->shadowbox_script === null)
					{
						$this->shadowbox_script = true;
						$this->helper->addShadowboxScript($props['flashchart_shadowbox'], $document, $lang_subtag[0]);
					}
				}
				else
				{
					if ($props['modal_title'] == "")
						$props['modal_title'] = $props['title'];

					if ($this->bootstrap_resources === null)
					{
						$this->helper->addBootrapResources($base, $version, $document);
						$this->bootstrap_resources = true;
					}
					$replace = $this->helper->createModal($id, $props['url'], $width, $height, $props['title'], $props['modal_title']);
				}
			}

			if (strlen($error_string) != null)
				$replace = $matches[0][$i] . "<br />" . JText::_('INVALID_MSG') . $error_string;

			$row->text = str_replace($matches[0][$i], $replace, $row->text);

		}
		return true;
	}

}
