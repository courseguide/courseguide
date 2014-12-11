<?php

/**
 * flashChartUtil.php: Utilities for flashChart Joomla! Plugin
 *
 * (1) interface to Joomla! mysql database
 * (2) interface to access files on local filesystem
 * (3) interface to acces data via webserver (uses/needs "curl" PEAR)
 * (4) utilities
 *
 * @version  V1.2.0
 *
 * change activity:
 * 11.04.2011: rework of getDatafromFile and getDatafromUrl
 * added support for debugging code
 * 03.05.2011: added function "calc_steps" for calculating axis steps
 *
 * 13.05.2013 add support for ajax in flashchart class
 * 21.01.2014 rework of flashchart class (add support for formula and modal windows)
 *
 * @author Joachim Schmidt - joachim.schmidt@oberquembach.de
 * @copyright Copyright (C) 2010 Joachim Schmidt. All rights reserved.
 * @license	 http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class flashChart
{

	protected  $chart_props = array();

	protected  $chartid;

	protected  $width;

	protected  $height;

	protected  $json;

	function flashChart($chartid, $properties_file = null, $width = "100%", $height = "100%")
	{
		if (!$properties_file)
			$this->chart_props = $this->setDefaultProperties();
		elseif (file_exists($properties_file))
			$this->chart_props = parse_ini_file($properties_file);
		else
		{
			echo "properties file ($properties_file) not found ";
			return null;
		}
		$this->chartid = $chartid;
		$this->width = $width;
		$this->height = $height;
	}

	function setDefaultProperties()
	{
		$props = array();

		$props['type'] = "bar_cylinder";
		$props['bg_color'] = "FFFFFF";
		$props['title_style'] = "padding:10px; font-size:15px; font-weight:bold; font-family:Sans-Serif,Arial,Helvetica;color:0B55C4;";
		$props['tooltip_style'] = "padding:10px; font-size:11px; font-weight:normal;";
		$props['chart_colors'] = "0169E1,008000,00BFFF,DC143C,191970,FF6600,A52A2A,CC00CC,228B22,990033,0869E1";
		$props['axis_legend_style'] = "font-family: Sans-Serif,Arial,Helvetica; font-size:12px; color:#555555";
		$props['x_axis_color'] = "b0b0b0";
		$props['y_axis_color'] = "b0b0b0";
		$props['label_fontsize'] = "12";
		$props['label_color'] = "555555";
		$props['grid_color'] = "b0b0b0";
		$props['line_animation'] = "mid-slide";
		$props['bar_animation'] = "grow-up";
		$props['multibar_color'] = "1";
		$props['axis_3d'] = "1";
		$props['legend_fontsize'] = "12";
		$props['x_label_rotate'] = "0";
		$props['number_format'] = "c";
		$props['precision'] = "2";
		$props['right_legend'] = "1";
		$props['pie_label_values'] = "0";
		$props['hide_pie_labels'] = "0";
		$props['pie_legend'] = "0";
		$props['pie_animation'] = "1";
		$props['sql_labels'] = "1";
		$props['alpha'] = "0.8";
		// flashchart configuration
		$props['flashchart_root'] = "/plugins/content/flashchart";
		$props['url_path'] = "http://" . $_SERVER['SERVER_NAME'] . $props['flashchart_root'];
		$props['db_config_file'] = $_SERVER['DOCUMENT_ROOT'] . "/configuration.php";

		return $props;
	}

	function getChartWidth()
	{
		return $this->width;
	}

	function getChartHeight()
	{
		return $this->height;
	}

	function getChartId()
	{
		return $this->chartid;
	}

	function setChartWidth($width)
	{
		$rc = true;
		if (is_numeric($width))
			$this->width = $width;
		else
		{
			$pos = strrpos($width, "%");
			if ($pos > 0)
			{
				if (!is_numeric(substr($width, 0, $pos)))
					$rc = false;
				else
					$this->width = $width;
			}
			else
				$rc = false;
		}

		return $rc;
	}

	function setChartHeight($height)
	{
		$rc = true;
		if (is_numeric($height))
			$this->height = $height;
		else
		{
			$pos = strrpos($height, "%");
			if ($pos > 0)
			{
				if (!is_numeric(substr($height, 0, $pos)))
					$rc = false;
				else
					$this->height = $height;
			}
			else
				$rc = false;
		}

		return $rc;
	}

	function setChartId($chartid)
	{
		$this->chartid = $chartid;
	}

	function getChartProperty($key)
	{
		if (array_key_exists($key, $this->chart_props))
			return $this->chart_props[$key];
		else
			return null;
	}

	function setChartProperties($properties)
	{
		$result = true;
		foreach ($properties as $key => $value)
		{
			$rc = $this->setChartProperty($key, $value);
			if ($rc == false)
				$result = false;
		}

		return $result;
	}

	function getChartProperties()
	{
		return $this->chart_props;
	}

	function setChartProperty($key, $value)
	{
		if ($key == "legend") $key = "key";
		$this->chart_props[$key] = $value;
		$rc = true;

		if ($key == "url")
		{
			if ($this->chart_props['data'] == "url")
			{
				$hdrs = @get_headers($value);
				$rc = is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false;
				if ($rc == false)
					$rc = "<br><b>Error!</b> cannot open url: $value";
			}
			else
				$rc = "<br><b>Error!</b> no data type (url) requested";
		}

		if ($key == "file")
		{
			if ($this->chart_props['data'] == "file")
			{
				if (!file_exists($value))
				{
					$rc = "<br><b>Error!</b> File: $value not found";
				}
			}
			else
				$rc = "<br><b>Error!</b> no data type (file) requested";
		}

		if ($key == "sql")
		{
			if ($this->chart_props['data'] == "database")
			{
				$data = $this->processSQLRequest($this->chart_props['db_config_file'], $value, $this->chart_props['type'], $this->chart_props['sql_labels']);
				if ($data != "-1")
				{
					$this->chart_props['data'] = $data;
				}
				else
				{
					$rc = "<br><b>Error!</b> No data found with sql: $value";
				}
			}
			else
				$rc = "<br><b>Error!</b> no data type (database) requested";
		}
		if ($key == "formula")
		{
			if ($this->chart_props['data'] == "formula")
			{
				$x_interval = 1; $x_min = 0; $x_max = 5; $max_iterations = 500;
				if ( array_key_exists('x_interval', $this->chart_props) )
				 $x_interval = $this->chart_props['x_interval'];
				if ( array_key_exists('x_min', $this->chart_props) )
				  $x_min = $this->chart_props['x_min'];
				if ( array_key_exists('x_max', $this->chart_props) )
				   $x_max = $this->chart_props['x_max'];
				if ( array_key_exists('max_iteration', $this->chart_props) )
				  $max_iterations = $this->chart_props['max_iteration'];

				$result = $this->calc_Data($value, $x_interval, $x_min, $x_max, $max_iterations);

				if ($result['error'] === null)
				  $this->chart_props['data'] = $result['data'];
				else
				  $rc = $result['error'];
			}
			else
			  $rc = "<br><b>Error!</b> no data type (formula) requested";
		}

		return $rc;
	}

	private function getJsonData ()
	{
		$lib = dirname(__FILE__) . '/chart-data-v1.4.php';
        include_once ($lib);

		$chart = new chart_data();
		$json = $chart->json_data($this->chart_props);

		return $json;
	}

	function getJavascripts()
	{
		$urlpath = $this->chart_props['url_path'];
		$html = "\n <script type='text/javascript' src='$urlpath/js/swfobject.js'></script>";
		$html .= "\n <script type='text/javascript' src='$urlpath/js/json/json2.js'></script>";
		$html .= "\n <script type='text/javascript' src='$urlpath/js/jquery.min.js'></script>";
		$html .= "\n <script type='text/javascript' src='$urlpath/js/bootstrap.min.js'></script>";
		$html .= "\n <script type='text/javascript' src='$urlpath/js/flashchart.js'></script>";

		return $html;
	}

	function createChartasScript($script, $html_tags = true)
	{
		// create chart as javascript:
		$html = "";
		$data = $this->getJsonData();
		$data = $data->toString();
		$base = $this->chart_props['url_path'];
		$height = $this->height;
		$width = $this->width;
		$id = $this->chartid;
		if ($html_tags)
			$html .= "\n<script type='text/javascript'>";
		$html .= "\n // flashchart script";
		$html .= "\n function $script () {";
		$html .= "\n   swfobject.embedSWF('$base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
		$html .= "\n        { 'get-data':'get_data_$script', 'id':'$id', 'loading':'Loading data ...'}, {'wmode':'transparent', 'allowFullScreen':'true'} );";
		$html .= "\n }";
		$html .= "\n\n function get_data_$script() {";
		$html .= "\n   var data_$script = $data;";
		$html .= "\n   return ( JSON.stringify(data_$script) );";
		$html .= "\n }";
		if ($html_tags)
			$html .= "\n</script>";

		return $html;

	}

	private function setupSWFobject($base, $id, $data, $width, $height, $bg_color = "FFFFFF", $html_tags = true)
	{
		$qq = '"';
		$div_set = false;
		$html = "";
		if ($html_tags)
		{
			if (is_numeric($height))
			{
				$html = "\n<div style='min-height:$height" . "px; background-color: #$bg_color;'>";
				$div_set = true;
			}
			$html .= "\n<script type='text/javascript'>";
		}
		$html .= " \n // flashchart script";
		$html .= " \n  swfobject.embedSWF('$base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
		$html .= " \n  { 'get-data':'get_data_$id', 'id':'$id' } )";
		$html .= " \n      function get_data_$id() { return JSON.stringify(data_$id); }";
		$html .= " \n      var data_$id = $data;";
		$html .= " \n  var playerVersion = swfobject.getFlashPlayerVersion();";
		$html .= " \n   if (playerVersion.major == 0) alert('Cannot display chart - flash plugin is not available');";
		if ($html_tags)
		{
			$html .= "\n</script>";
			$html .= "\n<div id='$id'></div>";
			if ($div_set)
				$html .= "\n</div>\n";
		}

		return $html;
	}

	private function setupSWFobjectScript($base, $id, $data, $width, $height, $bg_color = "FFFFFF", $html_tags = true)
	{
		$qq = '"';
		$div_set = false;
		$html = "";
		if ($html_tags)
		{
			if (is_numeric($height))
			{
				$html = "\n<div style='min-height:$height" . "px; background-color: #$bg_color;'>";
				$div_set = true;
			}
			$html .= "\n<script type='text/javascript'>";
		}
		$html .= "\n // flashchart script";
		$html .= " function showChart() {";
		$html .= "\n   swfobject.embedSWF('$base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
		$html .= "\n   { 'get-data':'get_data_$id', 'id':'$id' } )";
		$html .= "\n       function get_data_$id() { return JSON.stringify(data_$id); }";
		$html .= "\n       var data_$id = $data;";
		$html .= "\n var playerVersion = swfobject.getFlashPlayerVersion();";
		$html .= "\n if (playerVersion.major == 0) document.write($qq<p style='color:red;'><b>&nbsp;Cannot display chart - flash plugin is not available</b></p>$qq);";
		$html .= "\n }";
		if ($html_tags)
		{
			$html .= "\n</script>";
			$html .= "\n<div id='$id'></div>";
			if ($div_set)
				$html .= "\n</div>\n";
		}

		return $html;
	}

	private function setupSWFobjectJSON($script = null)
	{
		// create chart as javascript:
		$html = "";
		$base = $this->chart_props['url_path'];
		$height = $this->height;
		$width = $this->width;
		$id = $this->chartid;
		if ($script)
		{
			$html .= " function $script () {";
			$html .= "  swfobject.embedSWF('$base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
			$html .= "      { 'get-data':'get_data_$script', 'id':'$id', 'loading':'Loading data ...'}, {'wmode':'transparent', 'allowFullScreen':'true'} );";
			$html .= " }";
		}
		else
		{
			$html .= " swfobject.embedSWF('$base/open-flash-chart.swf', '$id', '$width', '$height', '9.0.0',  'expressInstall.swf',";
			$html .= "        { 'get-data':'get_data_$id', 'id':'$id', 'loading':'Loading data ...'}, {'wmode':'transparent', 'allowFullScreen':'true'} );";
			$html .= "    function get_data_$id() { return JSON.stringify(data_$id); }";
		}
		return $html;
	}

	function createChartasJSON()
	{
		$data = array();
		$data['type'] = 'chart';
		$data['chart_id'] = $this->chartid;
		$data['width'] = $this->width;
		$data['height'] = $this->height;
		$data['swfobject'] = $this->setupSWFobjectJSON();
		$data = json_encode($data);
		$data = rtrim($data, '}');
		$json_data = $this->getJsonData();
		$data .= ', "chartdata": ' . $json_data->toString() . "}";

		$this->appendtoJSON($data);
	}

	function createChartasJSONScript($script)
	{
		$data = array();
		$data['type'] = 'script';
		$data['chart_id'] = $this->chartid;
		$data['width'] = $this->width;
		$data['height'] = $this->height;
		$data['scriptname'] = $script;
		$data['script'] = $this->setupSWFobjectJSON($script);
		$data = json_encode($data);
		$data = rtrim($data, '}');
		$json_data = $this->getJsonData();
		$data .= ', "chartdata": ' . $json_data->toString() . "}";
		$this->appendtoJSON($data);

	}

	function createHTMLasJSON($html, $node = "html_text")
	{
		$data = array();
		$data['type'] = "html";
		$data['node'] = $node;
		$data['html_text'] = $html;
		$data = json_encode($data);

		$this->appendtoJSON($data);
	}

	private function appendtoJSON($data)
	{
		if ($this->json == null)
			$this->json = '{ "data_type":"flashcharts", "flashcharts" : [' . $data . ']}';
		else
		{
			$pos = strrpos($this->json, "]}");
			//$this->json = rtrim ( $this->json, "]}" );
			$this->json = substr($this->json, 0, $pos);
			$this->json .= "," . $data . "]}";
		}
	}

	function getJSON()
	{
		return $this->json;
	}

	function createChart($html_tags = true)
	{
		// create chart and swfobject:
		$data = $this->getJsonData();
		$data = $data->toString();

		$html = $this->setupSWFobject($this->chart_props['url_path'], $this->chartid, $data, $this->width, $this->height, $this->chart_props['bg_color'], $html_tags);
		if ($html)
			return $html;
		else
			echo "error";
	}

	function createChartScript($html_tags = true)
	{
		// create chart and swfobject:
		$data = $this->getJsonData();
		$data = $data->toString();
		$html .= $this->setupSWFobjectScript($this->chart_props['url_path'], $this->chartid, $data, $this->width, $this->height, $this->chart_props['bg_color'], $html_tags);
		if ($html)
			return $html;
		else
			return "error";
	}

	function createChartasModal($modal_chart, $close="Close Window", $title = "")
	{
		if ($modal_chart === "")
			$modal_chart = $this->getChartId();

		if ($title == "")
		  $title = "&nbsp";

		$box_height = $this->height + 25 . "px";
		$box_width = $this->width + 35 . "px";
		$background = "background-color: #" . $this->getChartProperty('bg_color') . ";";
		$html = "";
		$html .= "\n<a data-toggle='modal' href='#modal_" . $this->getChartId() . "'>" . $modal_chart . "</a>";
		$html .= "\n<div id='modal_" . $this->getChartId() . "' class='modal hide fade' style='display: none; height:" . $box_height . "; width:" . $box_width . "; " . $background . "'>";
		$html .= "\n<div class='modal-header'>$title";
		$html .= "\n  <div class='close' data-dismiss='modal' title='" . $close . "'>&nbsp;</div>";
		$html .= "\n </div>";
		$html .= "\n <div class='modal-body'>";
		$html .= $this->createChart(true);
		$html .= "\n</div>\n</div>";

		return $html;

	}

	private function processSQLRequest()
	{

		/* --------------------------------------------------------------------------*/
		/*  we will use db-settings from joomla's configuration.php                  */
		/* --------------------------------------------------------------------------*/
		$config_file = $this->chart_props['db_config_file'];

		if (file_exists($config_file))
			require_once ($config_file);
		else
		{
			echo "<b>configfile $config_file not found";
			exit() - 1;
		}

		$jconfig = new JConfig();
		$db = $jconfig->db;
		$dbuser = $jconfig->user;
		$dbpw = $jconfig->password;
		$dbhost = $jconfig->host;

		if ($this->chart_props['sql'] == '')
			return "-1";

		$connection = mysql_connect($dbhost, $dbuser, $dbpw);
		if ($connection == null)
		{
			echo "could not connect to database $db";
			return "-1";
		}
		$rc = mysql_select_db($db, $connection);
		if ($rc == 0)
		{
			echo "mysql_select_db failed with $db";
			return "-1";
		}
		mysql_query("SET NAMES 'utf8'");
		$result = mysql_query($this->chart_props['sql']);

		if ($result == false)
		{
			//echo "no result object via sql $sql";
			return "-1";
		}

		$data = "";
		$label = null;
		$cols = mysql_num_fields($result);

		if (mysql_num_rows($result) == 0)
			return "-1";

		$data_cols = array();
		for ($i = 0; $i < $cols; $i++)
			$data_cols[$i] = "";

		$row = mysql_fetch_row($result);
		while ($row)
		{
			for ($i = 0; $i < $cols; $i++)
			{
				if ($i == 0)
					$row[$i] = str_replace(",", " ", $row[$i]);
				$data_cols[$i] .= $row[$i] . ",";
			}
			$row = mysql_fetch_row($result);
		}

		for ($i = 0; $i < $cols; $i++)
			$data_cols[$i] = rtrim($data_cols[$i], ",");

		if ($cols > 1 && $this->chart_props['type'] != "bar_stack")
		{
			if ($this->chart_props['sql_labels'] == "1")
				$label = $data_cols[0] . "/";
			else
				$label = $data_cols[0] . "|";
			for ($i = 1; $i < $cols; $i++)
				$data .= $data_cols[$i] . "|";
			$data = rtrim($data, "|");
		}
		elseif ($cols > 1 && $this->chart_props['type'] == "bar_stack")
		{
			if ($this->chart_props['sql_labels'] == "1")
			{
				$label = $data_cols[0] . "/";
				$k = 1;
			}
			else
			{
				$label = $data_cols[0] . "|";
				$k = 0;
			}

			for ($i = $k; $i < $cols; $i++)
				$temp[$i - $k] = explode(",", $data_cols[$i]);

			$c = 0;

			for ($j = 0; $j < count($temp[0]); $j++)
			{
				for ($c = 0; $c < count($temp); $c++)
				{
					$data .= $temp[$c][$j] . ",";
				}
				$data = rtrim($data, ",");
				$data .= "|";
			}

			$data = rtrim($data, "|");

		}
		elseif ($cols == 1)
			$data = $data_cols[0];

		mysql_free_result($result);
		//mysql_close ( $connection );


		if ($data == "")
			return "-1";
		if ($cols > 1)
			$data = $label . $data;

		return $data;
	}

	private function calc_Data($formula, $x_interval = 1, $x_min = 0, $x_max = 5, $max_iterations = 500)
	{
		$lib = dirname(__FILE__) . '/evalMath.php';
        include_once ($lib);

		if (strpos($formula, "=") === false)
		{
			$user_formula = trim($formula);
			$user_formula = str_replace(",", ".", $user_formula);
		}
		else
		{
			$tmp = explode("=", $formula);
			$user_formula = trim($tmp[1]);
			$user_formula = str_replace(",", ".", $tmp[1]);
		}
		if ($user_formula)
		{
			$evalc = new EvalMath();
			$evalc->evaluate("y(x)=" . $user_formula);
		}
		else
			return null;

		if ($x_min === null)
			$x_min = 0;
		if ($x_max === null)
			$x_max = 5;
		if ($x_interval === null)
			$x_interval = 1;

		$x_labels = "";
		$y_values = "";
		$max_iter = $x_max + $x_interval;

		$j = 0;

		for ($x_value = $x_min; $x_value <= $max_iter; $x_value += $x_interval)
		{
			if ($j > $max_iterations)
			{
				$result['error'] = " x_max=&quot;$x_max&quot;, x_interval=&quot;$x_interval&quot;. Maximum ($max_iterations)  " . JText::_('MAX_CALCULATIONS');
				return $result;
			}
			$j++;
			$x = round($x_value, 5);
			$value = $evalc->e("y($x)");
			if ($evalc->last_error)
			{
				$result['error'] = " &quot;$formula&quot; Error:" . $evalc->last_error;
				return $result;
			}
			else
			{
				$x_labels .= round($x_value, 5) . ",";
				$y_values .= round($value, 5) . ",";
			}

		}
		$result['data'] = rtrim($x_labels, ",") . "/" . rtrim($y_values, ",");
		$result['error'] = null;
		return $result;
	}
}

?>