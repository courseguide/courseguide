<?php
/**
 * chart-data.php: interface module for "open flash chart" needed by flashChart Joomla! plugin
 *
 * @author Joachim Schmidt - joachim.schmidt@jschmidt-systemberatung.de
 * @copyright Copyright (C) 2010 Joachim Schmidt. All rights reserved.
 * @license	 http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * change activity: 23.12.2010 - Version V1.0.0
 * 18.01.2011 - changed db-interface to provide joomla-basedir (used only for samples)
 * 03.02.2011 - Release V1.1.0
 * corrected null-value handling
 * Added support to process parameter "dimension" for Pie-chart
 * Added support to process different number formats
 * 25.02.2011:  added option to have muliple colored bars on barchart with single datagroup
 * added option to show "alert-color" and "alert-tooltip" on barcharts with single datagroup
 * and line charts if data-value is higher/lower than alert value
 * 05.03.2011:  added option "dimension" to add additional character/text to y-axis values
 * 09.03.2011:  added support for horizontal bar charts
 * 03.05.2011:  changed calculation for y-axis steps (now call function "calc_steps")
 * 03.06.2011 - Release V1.2.0
 * added support for multiple alert values, alert tooltips and alert colors
 * improved tooltips for line charts
 * added support for radar charts
 * added support for area charts
 * added support for reverse values on y-axis
 * misc. code cleanups
 * 11.08.2011: changed code for multibar_color option to handle null-values correctly
 * 13.09.2011: changed / added default handling for steps and labels (ticks) on x-axis
 * for bar and line charts
 * 15.09.2011: added missing check of multibar_color for horizontal bars
 * 12.01.2012: changed / added css-style support for title, legend and tooltip
 * added option to hide pie-labels
 * added option to set values to pie-labels: "pie_label_values"
 * added option to truncate x-axis labels
 * 01.02.2012:  Release 1.2.0
 * support new functions which implements click events
 * added option for pie legend
 * added option for right legend
 * added option for ofc menu
 * added option for bar values on bar charts
 * correct display for horizontal bars (support for multiple dataseries)
 * correct settings for line and area charts (dots, solid_dots for mouse hover))
 * support added for re-arrangement of data for stacked bar charts
 * 25.04.2012:  Release 1.2.1
 * added support for scatter charts
 * added support for regression and trend lines
 * 20.12.2013 added joomla framwork check ( defined('_JEXEC') )
 * 17.01.2014 corrected check for null values
 * 10.02.2014 changed to class
 *
 */
defined('_JEXEC') or die('Restricted access');

$lib = dirname(__FILE__) . '/open-flash-chart.php';
include_once ($lib);
$lib = dirname(__FILE__) . '/evalMath.php';
include_once ($lib);

class chart_data
{

	final public function json_data($props)
	{
		if (array_key_exists('chart_id', $props))
			$chart_id = $props['chart_id'];
		else
			$chart_id = "chart_99";

		if (array_key_exists('title_style', $props))
			$title_style = $props['title_style'];
		else
			$title_style = "padding:10px; font-size:14px; font-weight:bold; font-family:Sans-Serif,Arial,Helvetica;color:#000000";

		if (array_key_exists('legend_style', $props))
			$legend_style = $props['legend_style'];
		else
			$legend_style = "font-size:11px; font-weight:normal; font-family:Sans-Serif,Arial,Helvetica;color:51698F;";

		if (array_key_exists('tag_style', $props))
			$tag_style = $props['tag_style'];
		else
			$tag_style = "font-size:11px; font-weight:normal; font-family:Arial,Helvetica;color:000000;padding-right:0;padding-bottom:0";

		if (array_key_exists('legend_fontsize', $props))
		{
			$legend_fontsize = $props['legend_fontsize'];
		}
		else
			$legend_fontsize = "12";

		if (array_key_exists('label_color', $props))
			$label_color = $props['label_color'];
		else
			$label_color = "000000";

		if (array_key_exists('y_label_color', $props))
			$y_label_color = $props['y_label_color'];
		else
			$y_label_color = $label_color;

		if (array_key_exists('label_fontsize', $props))
		{
			$label_fontsize = $props['label_fontsize'];
		}
		else
			$label_fontsize = "11";

		if (array_key_exists('tooltip_style', $props))
			$tooltip_style = $props['tooltip_style'];
		else
			$tooltip_style = "font-size:11px; font-weight:normal; color:000000;";

		// title font and color (parameter "t_color" and "t_fontsize") are deprecated but are still supported they overrite style of title:
		if (array_key_exists('t_color', $props) || array_key_exists('t_fontsize', $props))
		{
			if (array_key_exists('t_color', $props))
				$title_fcolor = $props['t_color'];
			else
				$title_fcolor = $label_color;

			if (array_key_exists('t_fontsize', $props))
				$title_fsize = $props['t_fontsize'];
			else
				$title_fsize = "14";

			$title_style = "padding:10px; font-size:$title_fsize; font-weight:bold; font-family:Sans-Serif,Arial,Helvetica;color:$title_fcolor";
		}

		$title = null;
		if (array_key_exists('title', $props))
		{
			$title = str_replace("<br />", "\n", $props['title']);
			$title = new title($title);
			$title->set_style($title_style);
		}

		if (array_key_exists('x_axis_color', $props))
			$x_axis_colour = $props['x_axis_color'];
		else
			$x_axis_colour = "000000";

		$create_menu = null;
		if (array_key_exists('menu', $props))
		{
			$menu = array();
			$create_menu = true;
			$tmp = explode("|", $props['menu']);
			for ($i = 0; $i < count($tmp); $i++)
			{
				$items = explode(",", $tmp[$i]);
				$menu[$i]['text'] = $items[0];
				$menu[$i]['script'] = $items[1];
			}
		}

		$data = array();
		$check = $props['data'];
		$labels_found = 0;

		if (array_key_exists('debug', $props))
			$DEBUG = true;
		else
			$DEBUG = false;

		$file_data = array();
		$url_data = array();

		$rearrange_data = "0";
		if (array_key_exists('rearrange_data', $props))
			if ($props['rearrange_data'] == "1")
				$rearrange_data = "1";

		$x_labels = array();
		switch ($check)
		{

			case "file":
				if (array_key_exists('file', $props))
				{
					$file_data = $this->getDataFromFile($props['file'], $DEBUG);
					if (array_key_exists('label', $file_data))
					{
						$x_labels = explode(",", $file_data['label']);
						$labels_found = true;
						if (array_key_exists('x_label_truncate', $props))
							$x_labels = $this->truncateLabels($x_labels, $props['x_label_truncate']);
					}
					else
						$labels_found = false;

					if (array_key_exists('data', $file_data))
					{
						if ($rearrange_data == "1")
							$file_data['data'] = $this->arrangeDataforStackbar($file_data['data']);
						$data = explode("|", $file_data['data']);
					}
					else
						$data = 0;
				}
				else
					$data = 0;
				break;

			case "url":
				if (array_key_exists('url', $props))
				{
					$url_data = $this->getDataFromUrl($props['url'], $DEBUG);
					if (array_key_exists('label', $url_data))
					{
						$x_labels = explode(",", $url_data['label']);
						$labels_found = true;
						if (array_key_exists('x_label_truncate', $props))
							$x_labels = $this->truncateLabels($x_labels, $props['x_label_truncate']);
					}
					else
						$labels_found = false;

					if (array_key_exists('data', $url_data))
					{
						if ($rearrange_data == "1")
							$url_data['data'] = $this->arrangeDataforStackbar($url_data['data']);
						$data = explode("|", $url_data['data']);
					}
					else
						$data = 0;
				}
				else
					$data = 0;
				break;

			default:
				$local_data = explode("/", $props['data']);
				if (count($local_data) > 1)
				{
					$local_data[0] = trim($local_data[0]);
					$x_labels = explode(",", $local_data[0]);
					$labels_found = true;
					if (array_key_exists('x_label_truncate', $props))
						$x_labels = $this->truncateLabels($x_labels, $props['x_label_truncate']);
					if ($rearrange_data == "1")
						$local_data[1] = $this->arrangeDataforStackbar($local_data[1]);
					$data = explode("|", $local_data[1]);
				}
				else
				{
					if ($rearrange_data == "1")
						$props['data'] = $this->arrangeDataforStackbar($props['data']);
					$data = explode("|", trim($props['data']));
				}
		}

		$data_series = count($data);

		$min = 0;
		$max = 0;
		$x_max = null;
		$x_min = 0;
		if ($data_series == 1)
		{
			$data[0] = explode(",", $data[0]);
			$num = count($data[0]);
			for ($i = 0; $i < $num; $i++)
			{
				$data[0][$i] = trim($data[0][$i]);
				if (is_numeric($data[0][$i]) && !is_null($data[0][$i]))
				{
					$data[0][$i] = (float) $data[0][$i];
				}
				else
					$data[0][$i] = null;
			}
			$max = max($data[0]);
			$min = min($data[0]);
			if ($min > 0)
				$min = 0;
		}
		else
		{
			for ($j = 0; $j < count($data); $j++)
			{
				$data[$j] = explode(",", $data[$j]);
				$num = count($data[$j]);
				if ($max < max($data[$j]))
					$max = max($data[$j]);
				if ($min > min($data[$j]))
					$min = min($data[$j]);

				for ($i = 0; $i < $num; $i++)
				{
					$data[$j][$i] = trim($data[$j][$i]);
					if (is_numeric($data[$j][$i]) && !is_null($data[$j][$i]))
					{
						$data[$j][$i] = (float) $data[$j][$i];
					}
					else
						$data[$j][$i] = null;
				}
			}
		}

		if ($max > 5 && $min < -5)
		{
			$max = ceil($max);
			$min = (int) $min;
		}

		if (array_key_exists('y_step', $props))
		{
			if ($props['y_step'] !== null)
				$steps = $props['y_step'];
		}
		else
			$steps = $this->calc_steps($max);

		// create elements for chart:
		if (array_key_exists('type', $props))
			$type = $props['type'];
		else
			$type = "line";
		if ($type == "bar_horizontal")
			$type = "hbar";

		if (strpos($type, "scatter") !== false)
			$animation = "explode";
		else
			$animation = "pop";
		$cascade = 1;
		$delay = 0.3;

		if (array_key_exists('pie_animation', $props))
			$pie_animation = $props['pie_animation'];

		if (array_key_exists('scatter_animation', $props))
			$scatter_animation = "explode";
		else
			$scatter_animation = false;

		// check if color array is OK
		if (array_key_exists('multibar_color', $props))
			$multibar_color = $props['multibar_color'];
		else
			$multibar_color = "0";

		if (array_key_exists('precision', $props))
			$precision = $props['precision'];
		else
			$precision = "2";

		if (array_key_exists('number_format', $props))
			$number_format = $props['number_format'];
		else
			$number_format = "c";

		$element_color = array();
		if (array_key_exists('c_color', $props))
			$element_color = explode(",", $props['c_color']);
		elseif (array_key_exists('chart_colors', $props))
			$element_color = explode(",", $props['chart_colors']);
		if (count($element_color) == 0)
			array_push($element_color, $this->getRandomColor());

		if ($type != "bar_stack" && count($element_color) < $data_series)
		{
			while (count($element_color) < ($data_series + 1))
			{
				array_push($element_color, $this->getRandomColor());
			}
		}

		if (($type == "bar_stack" || $type == "pie" || (strpos($type, "bar_") !== false && $multibar_color == "1")) && count($element_color) < count($data[0]) + 1)
		{
			while (count($element_color) < (count($data[0]) + 1))
			{
				array_push($element_color, $this->getRandomColor());
			}
		}

		$keys = array();
		$legend = false;
		if (array_key_exists('x_axis_steps', $props))
			$x_axis_steps = $props['x_axis_steps'];
		else
			$x_axis_steps = null;

		if (array_key_exists('onclick', $props))
			$onclick = explode(",", $props['onclick']);
		else
			$onclick = null;

		$key_onclick = null;
		if (array_key_exists('key', $props))
		{
			$keys = $props['key'];
			$keys = explode(",", $keys);
			$bar_legend = $keys;

			if (array_key_exists('key_onclick', $props))
			{
				$key_onclick = explode(",", $props['key_onclick']);
				for ($i = 0; $i < count($keys); $i++)
				{
					if (count($key_onclick) <= $i && $type != "bar_stack")
						$key_onclick[$i] = "toggle-visibility";
				}
			}
			elseif ($type != "bar_stack")
				for ($i = 0; $i < count($keys); $i++)
					$key_onclick[$i] = "toggle-visibility";
		}

		if (array_key_exists('pie_legend', $props) && $type == "pie")
		{
			if (array_key_exists('key_onclick', $props))
			{
				$key_onclick = explode(",", $props['key_onclick']);
				for ($i = 0; $i < count($data[0]); $i++)

				{
					if (count($key_onclick) <= $i)
						$key_onclick[$i] = "toggle-visibility";
				}
			}
			else
				for ($i = 0; $i < count($data[0]); $i++)
					$key_onclick[$i] = "toggle-visibility";
		}

		if (count($keys) > 1 && count($keys) >= count($data) && $type != "bar_stack" && $type != "pie")
		{
			$legend = true;
			while (count($keys) > count($data))
				array_pop($keys);
		}

		if ($type == "bar_stack" && count($keys) >= count($data[0]))
		{
			$legend = true;
		}

		$spoke_label_array = null;
		if (strpos($type, "radar") !== false)
		{
			if (array_key_exists('radar_spoke_labels', $props))
				$spoke_label_array = explode(",", $props['radar_spoke_labels']);
			if ($labels_found)
				$spoke_label_array = $x_labels;
		}

		if (array_key_exists('tooltip', $props))
			$tooltip = (string) $props['tooltip'];
		else
			$tooltip = "#val#";

		$alpha = "0.8";
		if (array_key_exists('alpha', $props))
			$alpha = $props['alpha'];

		if (array_key_exists('dimension', $props))
			$dimension = $props['dimension'];
		else
			$dimension = '';

		$alert_values = array();
		$alert_value = false;
		if (array_key_exists('alert_value', $props))
		{
			$alert_value = true;
			$tmp_colors = array();
			$tmp_tooltips = array();
			$tmp_onclick = array();

			$tmp_values = explode("|", $props['alert_value']);
			if (array_key_exists('alert_tooltip', $props))
				$tmp_tooltips = explode("|", $props['alert_tooltip']);
			if (array_key_exists('alert_color', $props))
				$tmp_colors = explode(",", $props['alert_color']);
			if (array_key_exists('alert_onclick', $props))
				$tmp_onclick = explode(",", $props['alert_onclick']);

			$alert_values = array();
			for ($i = 0; $i < count($tmp_values); $i++)
			{
				$tmp = explode(",", $tmp_values[$i]);
				$alert_values[$i]['alert_value'] = $tmp[0];
				$alert_values[$i]['alert_op'] = $tmp[1];
				if (count($tmp_tooltips) > $i)
					$alert_values[$i]['alert_tooltip'] = $tmp_tooltips[$i];
				else
					$alert_values[$i]['alert_tooltip'] = $tooltip;
				if (count($tmp_colors) > $i)
					$alert_values[$i]['alert_color'] = $tmp_colors[$i];
				else
					$alert_values[$i]['alert_color'] = "FF0000";
				if (count($tmp_onclick) > $i)
					$alert_values[$i]['alert_onclick'] = $tmp_onclick[$i];
				else
					$alert_values[$i]['alert_onclick'] = null;
			}
		}

		$show_barvalues = "0";
		if (array_key_exists('show_barvalues', $props))
		{
			if ($props['show_barvalues'] >= "1")
				$show_barvalues = "1";
			else
				$show_barvalues = "0";
		}

		$show_trend = false;
		if (array_key_exists('show_trend', $props))
		{
			if ($props['show_trend'] >= "1")
				$show_trend = true;
			else
				$show_trend = false;
		}

		$calc_slr = false;
		$show_regression_formula = false;
		if (array_key_exists('show_regression_line', $props) || array_key_exists('show_trend', $props))
		{
			if (array_key_exists('show_regression_line', $props))
				if ($props['show_regression_line'] == "1")
					$calc_slr = true;
				else
					$calc_slr = false;

			if (array_key_exists('show_regression_formula', $props))
			{
				if ($props['show_regression_formula'] == "1")
					$show_regression_formula = true;
				else
					$show_regression_formula = false;
			}
		}

		if (array_key_exists('formula', $props) && $calc_slr == true)
			$user_formula = $props['formula'];
		else
			$user_formula = null;

		$show_linevalues = "0";
		if (array_key_exists('show_linevalues', $props))
		{
			if ($props['show_linevalues'] == "1")
				$show_linevalues = "1";
			else
				$show_linevalues = "0";
		}

		if ($show_linevalues == "1" || $show_barvalues == "1")
		{
			$style = $this->getStyle($tag_style, "font-size");
			if ($style)
				$tag_value_style['font-size'] = $style;
			else
				$tag_value_style['font-size'] = $label_fontsize;

			$style = $this->getStyle($tag_style, "font-family");
			if ($style)
				$tag_value_style['font-family'] = $style;
			else
				$tag_value_style['font-family'] = "Arial";

			$style = $this->getStyle($tag_style, "font-weight");
			if ($style == "bold")
				$tag_value_style['bold'] = true;
			else
				$tag_value_style['bold'] = false;

			$style = $this->getStyle($tag_style, "color");
			if ($style)
				$tag_value_style['color'] = $style;
			else
				$tag_value_style['color'] = $label_color;

			$style = $this->getStyle($tag_style, "font-rotate");
			if ($style)
				$tag_value_style['font-rotate'] = $style;
			else
				$tag_value_style['font-rotate'] = 0;

			$style = $this->getStyle($tag_style, "padding-bottom");
			if ($style)
				$tag_value_style['padding-bottom'] = $style;
			else
				$tag_value_style['padding-bottom'] = 0;

			$style = $this->getStyle($tag_style, "padding-right");
			if ($style)
				$tag_value_style['padding-right'] = $style;
			else
				$tag_value_style['padding-right'] = 0;
		}

		$add_tags = false;

		switch ($type)
		{
			case "scatter":
			case "scatter_dot":
			case "scatter_star":
			case "scatter_box":
			case "scatter_hollow":
				{
					$dot_type = "solid_dot";
					$halo_size = 4;
					$dot_size = 3;

					if (strpos($type, "_hollow") !== false)
					{
						$dot_type = "hollow_dot";
						$dot_size = 3;
						$halo_size = 4;
					}

					if (strpos($type, "_star") !== false)
					{
						$dot_type = "star";
						$dot_size = 4;
						$halo_size = 4;
					}

					if (strpos($type, "_box") !== false)
					{
						$dot_type = "s_box";
						$dot_size = 3;
						$halo_size = 4;
					}

					$use_labels = false;
					$label = array();
					$k = 0;
					if ($labels_found == true)
					{
						foreach ($x_labels as $test)
						{
							$test = trim($test);
							if (!is_numeric($test))
							{
								$use_labels = true;
								if (!array_key_exists($test, $label))
								{
									$label[$test] = $k;
									$k++;
								}
							}
							else
							{
								$label[$test] = $test;
								asort($label);
								$x_max = max($label);
								$x_min = min($label);
							}
						}

						$k = 0;
						foreach ($label as $key => $new)
						{
							$new_labels[$k] = trim($key);
							$k++;
						}
					}
					else
					{
						for ($k = 0; $k < count($data[$i]); $k++)
							$new_labels[$k] = $k;
					}

					$type = "scatter";

					for ($i = 0; $i < 1; $i++)
					{
						for ($k = 0; $k < count($data[$i]); $k++)
						{
							if ($use_labels == false)
							{
								if ($labels_found)
									$x_values[$k] = trim($x_labels[$k]);
								else
								{
									$x_values[$k] = $k;
									$x_labels[$k] = $k;
								}

								$values[] = new scatter_value($x_values[$k], $data[$i][$k], $dot_size);
							}
							else
							{
								$x_values[$k] = $label[$x_labels[$k]];
								$values[] = new scatter_value($x_values[$k], $data[$i][$k], $dot_size);
							}
						}

						$x_labels = $new_labels;
						$labels_found = $use_labels;

						$dot = new $dot_type($element_color[$i], $dot_size);
						$dot->tooltip($tooltip);
						$element[$i] = new $type($element_color[$i], $dot_size);
						$element[$i]->set_default_dot_style($dot);
						$element[$i]->set_values($values);
						if ($scatter_animation)
							$element[$i]->set_on_show(new scatter_on_show($animation, $cascade, $delay));

		// calculate Regression line
						if ($calc_slr)
						{
							$result = $this->calculate_Regression($x_values, $data[$i], $user_formula, $number_format, $precision);
							if ($result)
							{
								$values = $result['values'];
								$formula_out = $result['formula_out'];
								/* draw Regression line: */
								$i++;
								$dota = new solid_dot($element_color[$i - 1], 1);
								$dota->size(1)
									->halo_size(0)
									->tooltip($tooltip);
								$element[$i] = new scatter_line($element_color[$i - 1], 2);
								$element[$i]->set_default_dot_style($dota);
								$element[$i]->set_values($values);

								// add formula to key
								if ($show_regression_formula)
								{
									$element[$i]->set_key($formula_out, $legend_fontsize);
									$element[$i]->set_key_on_click("toggle-visibility");
								}
								else
								{
									$element[$i]->set_key("Trend", $legend_fontsize);
									$element[$i]->set_key_on_click("toggle-visibility");
								}
							}
						}

					} // end for
				}
				break;

			case "bar_cylinder":
			case "bar_3d":
			case "bar_cylinder_outline":
			case "bar_dome":
			case "bar_glass":
			case "bar_round":
			case "bar_round3d":
			case "bar_filled":
			case "bar_rounded_glass":
			case "bar_plastic":
			case "bar_plastic_flat":
			case "bar_simple":
				for ($i = 0; $i < $data_series; $i++)
				{
					$element[$i] = new $type();
					$element[$i]->set_alpha($alpha);

					// $element [$i]->set_chart_id($chart_id);
					if ($tooltip)
						$element[$i]->set_tooltip($tooltip);
					if ($legend == true)
					{
						$element[$i]->set_key($keys[$i], $legend_fontsize, $chart_id);
						$element[$i]->set_key_on_click($key_onclick[$i]);

					}

					if ($onclick[$i] !== null)
						$element[$i]->set_on_click($onclick[$i]);

					if (array_key_exists('bar_animation', $props))
					{
						$animation = $props['bar_animation'];
						if ($animation != "0" && $show_barvalues == "0")
							$element[$i]->set_on_show(new bar_on_show($animation, $cascade, $delay));
					}

					if ($show_barvalues == "1" && $data_series < 2)
					{
						$tags[$i] = new ofc_tags();
						$tags[$i]->font($tag_value_style['font-family'], $tag_value_style['font-size'])
							->colour($tag_value_style['color'])
							->align_x_center()
							->align_y_above()
							->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom'])
							->style($tag_value_style['bold'], false, false, 1.0)
							->rotate($tag_value_style['font-rotate']);
						$x = 0;
						$j = 0;

						foreach ($data[$i] as $v)
						{
							$text = $this->formatNumber($number_format, $data[$i][$j], $precision) . $dimension;
							if ($props['show_barvalues'] > 1)
								$v = $v * 0.8;
							$ofc_tag = new ofc_tag($x, $v);
							if ($props['show_barvalues'] > 1)
								$ofc_tag->align_y_below();
							$ofc_tag->text($text);
							$tags[$i]->append_tag($ofc_tag);
							$x++;
							$j++;
						}
						$add_tags = true;
					}
				}
				if ($show_trend)
					$element[$data_series] = $this->drawTrendLine($data, $data_series, $element_color[$data_series], $legend_fontsize, $dimension, $show_regression_formula, $precision, $number_format);
				break;

			case "hbar":

				if (array_key_exists('x_min', $props))
					$min = $props['x_min'];
				else
					$min = 0;

				for ($i = 0; $i < $data_series; $i++)
					$data[$i] = array_reverse($data[$i]);

				for ($i = 0; $i < $data_series; $i++)
				{
					$element[$i] = new $type($element_color[$i]);
					$element[$i]->set_label_colour($label_color);
					$element[$i]->set_label_fontsize($label_fontsize);

					if ($legend == true)
					{
						$element[$i]->set_key($keys[$i], $legend_fontsize, $chart_id);
						$element[$i]->set_key_on_click($key_onclick[$i]);
					}
					$hbar_value = null;

					for ($j = 0; $j < count($data[$i]); $j++)
					{
						if (array_key_exists('x_min', $props))
							$min = $props['x_min'];
						else
							$min = 0;

						$hbar_value[$j] = new hbar_value($min, $data[$i][$j] + $min);
						if ($multibar_color == "1" && $data_series == 1)
						{
							$color = count($data[0]) - ($j + 1);
							$hbar_value[$j]->set_colour($element_color[$color]);
						}

						else
							$hbar_value[$j]->set_colour($element_color[$i]);
						$temp_tooltip = $tooltip;

						if ($onclick[$i] !== null)
							$hbar_value[$j]->set_on_click($onclick[$i]);

						$alert = false;
						if ($alert_value && $data_series == 1)
						{
							$alert = checkAlert($alert_values, $data[$i][$j]);
							if ($alert)
							{
								$hbar_value[$j]->set_colour($alert['alert_color']);
								if ($alert['alert_onclick'])
									$hbar_value[$j]->set_on_click($alert['alert_onclick']);
								$temp_tooltip = str_replace("#legend#", $keys[$i], $alert['alert_tooltip']);
							}
						}
						if ($alert == false)
						{
							if ($legend == true)
								$temp_tooltip = str_replace("#legend#", $keys[$i], $tooltip);
						}

						$hbar_value[$j]->set_tooltip($temp_tooltip);
						$hbar_value[$j]->set_alpha($alpha);
						$element[$i]->append_value($hbar_value[$j]);
					}

					if ($show_barvalues == "1" && $data_series < 2)
					{
						$tags = new ofc_tags();
						$tags->font($tag_value_style['font-family'], $tag_value_style['font-size'])->colour($tag_value_style['color']);
						if ($props['show_barvalues'] > 1)
							$tags->align_x_left();
						else
							$tags->align_x_right();
						$tags->align_y_center()
							->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom'])
							->rotate($tag_value_style['font-rotate'])
							->style($tag_value_style['bold'], false, false, 1.0);
						$y = 0;
						$j = 0;

						$tmp = array_reverse($data[$i]);
						foreach ($tmp as $x)
						{
							// $text = $this->formatNumber($number_format, $data [$i] [$j], $precision) . $dimension;
							$text = $this->formatNumber($number_format, $tmp[$j], $precision) . $dimension;
							$ofc_tag = new ofc_tag($x, $y);
							$ofc_tag->text($text);
							$tags->append_tag($ofc_tag);
							$y++;
							$j++;
						}

						$add_tags = true;
					}
				}

				break;

			case "bar_stack":
				$element[0] = new $type();
				$element[0]->set_chartid($chart_id);
				$max = 0;
				$sum = 0;
				for ($i = 0; $i < count($data); $i++)
				{
					for ($j = 0; $j < count($data[$i]); $j++)
					{
						$sum = $sum + $data[$i][$j];
					}
					if ($max < $sum)
						$max = $sum;
					$total[$i] = $sum;
					$sum = 0;
				}

				if ($max > 5)
					$max = (int) $max;

				if (array_key_exists('y_step', $props))
				{
					if ($props['y_step'] !== null)
						$steps = $props['y_step'];
				}
				else
					$steps = $this->calc_steps($max);

				$element[0]->set_colours($element_color);
				if ($onclick)
					$element[0]->set_on_click($onclick[0]);

				$n = 0;
				if ($show_barvalues == "1" && ($props['show_barvalues'] == "1" || $props['show_barvalues'] == "2"))
				{
					$tags[0] = new ofc_tags();
					$text = "#y#" . $dimension;
					$tags[0]->font($tag_value_style['font-family'], $tag_value_style['font-size'])
						->colour($tag_value_style['color'])
						->align_x_center()
						->align_y_above()
						->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom'])
						->rotate($tag_value_style['font-rotate'])
						->style($tag_value_style['bold'], false, false, 1.0)
						->text($text);
					$x = 0;

					foreach ($total as $v)
					{
						$tags[0]->append_tag(new ofc_tag($x, $v));
						$x++;
					}
					$add_tags = true;
					$n = 1;
				}

				$x = 0;
				for ($i = 0; $i < count($data); $i++)
				{
					if ($show_barvalues == "1")
					{
						$v = 0;
						$y = 0;
						$tags[$i + $n] = new ofc_tags();
						$tags[$i + $n]->font($tag_value_style['font-family'], $tag_value_style['font-size'])
							->align_x_center()
							->align_y_center()
							->style($tag_value_style['bold'], false, false, 1.0)
							->rotate($tag_value_style['font-rotate']);
					}

					for ($j = 0; $j < count($data[$i]); $j++)
					{
						$temp_tooltip = $tooltip;
						if ($legend)
						{
							$temp_tooltip = str_replace("#key#", $keys[$j], $tooltip);
							$temp_tooltip = str_replace("#legend#", $keys[$j], $temp_tooltip);
						}
						$bar_stack_value[$i][$j] = new bar_stack_value($data[$i][$j], $element_color[$j]);
						$bar_stack_value[$i][$j]->set_tooltip($temp_tooltip);

						if ($show_barvalues == "1" && $props['show_barvalues'] > "1")
						{
							$y = $y + $data[$i][$j];
							$v = $y - ($data[$i][$j] / 2);

							$text = $this->formatNumber($number_format, $data[$i][$j], $precision) . $dimension;
							$ofc_tag = new ofc_tag($x, $v);
							$ofc_tag->text($text);
							if ($props['show_barvalues'] == "2")
								$ofc_tag->colour("F0F0F0");
							else
								$ofc_tag->colour($tag_value_style['color']);
							$tags[$i + $n]->append_tag($ofc_tag);
							$add_tags = true;
						}
					}
					$element[0]->append_stack($bar_stack_value[$i]);
					$x++;

				}

				if ($legend == true)
				{
					for ($i = 0; $i < count($keys); $i++)
					{

						$stack_key[$i] = new bar_stack_key($element_color[$i], $keys[$i], $legend_fontsize);
						if ($key_onclick[$i] !== null)
							$stack_key[$i]->set_key_on_click($key_onclick[$i]);
					}

					$element[0]->set_keys($stack_key);
				}

				if ($tooltip)
					$element[0]->set_tooltip($tooltip);
				$element[0]->set_alpha($alpha);

				if (array_key_exists('bar_animation', $props))
				{
					$animation = $props['bar_animation'];
					if ($animation != "0" && $show_barvalues == "0")
						$element[0]->set_on_show(new bar_on_show($animation, $cascade, $delay));
				}

				if ($show_trend)
					$element[1] = $this->drawTrendLine($total, 0, $element_color[$j], $legend_fontsize, $dimension, $show_regression_formula, $precision, $number_format);

				break;

			case "bar_sketch":
				if (count($element_color) < $data_series + 1)
					array_push($element_color, $this->getRandomColor());

				for ($i = 0; $i < $data_series; $i++)
				{
					$element[$i] = new $type($element_color[$i], $element_color[$i + 1], 7);
					if ($legend == true)
					{
						$element[$i]->set_key($keys[$i], $legend_fontsize, $chart_id);
						$element[$i]->set_key_on_click($key_onclick[$i]);
					}
					if ($tooltip)
						$element[$i]->set_tooltip($tooltip);

					if ($onclick[$i] !== null)
						$element[$i]->set_on_click($onclick[$i]);

					if (array_key_exists('bar_animation', $props))
					{
						$animation = $props['bar_animation'];
						if ($animation != "0" && $show_barvalues == "0")
							$element[$i]->set_on_show(new bar_on_show($animation, $cascade, $delay));
					}

					if ($show_barvalues == "1" && $data_series < 2)
					{
						$tags[$i] = new ofc_tags();
						$tags[$i]->font($tag_value_style['font-family'], $tag_value_style['font-size'])
							->colour($tag_value_style['color'])
							->align_x_center()
							->align_y_above()
							->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom'])
							->style($tag_value_style['bold'], false, false, 1.0)
							->rotate($tag_value_style['font-rotate']);
						$x = 0;
						$j = 0;

						foreach ($data[$i] as $v)
						{
							$text = $this->formatNumber($number_format, $data[$i][$j], $precision) . $dimension;
							if ($props['show_barvalues'] > 1)
								$v = $v * 0.8;
							$ofc_tag = new ofc_tag($x, $v);
							if ($props['show_barvalues'] > 1)
								$ofc_tag->align_y_below();
							$ofc_tag->text($text);
							$tags[$i]->append_tag($ofc_tag);
							$x++;
							$j++;
						}
						$add_tags = true;
					}
				}
				if ($show_trend)
					$element[$data_series] = $this->drawTrendLine($data, $data_series, $element_color[$i + 1], $legend_fontsize, $dimension, $show_regression_formula, $precision, $number_format);
				break;

			case "pie":
				$element[0] = new pie();
				$element[0]->set_alpha($alpha);

				if (array_key_exists('pie_radius', $props))
					$element[0]->set_radius($props['pie_radius']);

				if (array_key_exists('pie_legend', $props))
					if ($props['pie_legend'] == "1")
						$legend = true;

				$pie_label_values = "0";
				if (array_key_exists('pie_label_values', $props))
					if ($props['pie_label_values'] != "0")
						$pie_label_values = "1";

				if ($pie_animation == "1")
				{
					$element[0]->set_animate(true);
					$element[0]->add_animation(new pie_fade());
					$element[0]->add_animation(new pie_bounce(10));
				}
				else
					$element[0]->set_animate(false);

				$element[0]->set_key_on_click("");

				$element[0]->colours($element_color);
				$element[0]->set_tooltip($tooltip);

				$pie_label = array();
				if ($labels_found == true || array_key_exists('x_axis_labels', $props) || array_key_exists('dimension', $props))
				{
					if ($labels_found == true)
						$pie_label = $x_labels;
					else if (array_key_exists('x_axis_labels', $props))
						$pie_label = explode(",", $props['x_axis_labels']);

					if (array_key_exists('x_label_truncate', $props) && count($pie_label) >= count($data[0]))
						$pie_label = $this->truncateLabels($pie_label, $props['x_label_truncate']);

					if (count($pie_label) >= count($data[0]) && !array_key_exists('dimension', $props))
					{
						for ($i = 0; $i < count($data[0]); $i++)
						{
							if ($pie_label_values)
								$label = $pie_label[$i] . ": " . $this->formatNumber($number_format, $data[0][$i], (int) $precision);
							else
								$label = $pie_label[$i];

							$pievalue[$i] = new pie_value($data[0][$i], '');
							if ($data[0][$i] !== null)
								$pievalue[$i]->set_label($label, $element_color[$i], $label_fontsize);
							$pievalue[$i]->set_text($label);
							if ($legend)
								$pievalue[$i]->set_key_on_click($key_onclick[$i], $chart_id);
							if (count($onclick) > $i)
								$pievalue[$i]->set_on_click($onclick[$i]);

						}
						$element[0]->set_values($pievalue);
					}
					elseif (count($pie_label) >= count($data[0]) && array_key_exists('dimension', $props))
					{
						for ($i = 0; $i < count($data[0]); $i++)
						{
							if ($pie_label_values)
								$label = $pie_label[$i] . ": " . $this->formatNumber($number_format, $data[0][$i], (int) $precision) . $props['dimension'];
							else
								$label = $pie_label[$i];

							$pievalue[$i] = new pie_value($data[0][$i], '');
							if ($data[0][$i] !== null)
								$pievalue[$i]->set_label($label, $element_color[$i], $label_fontsize);
							$pievalue[$i]->set_text($label);

							if ($legend)
								$pievalue[$i]->set_key_on_click($key_onclick[$i], $chart_id);
							if (count($onclick) > $i)
								$pievalue[$i]->set_on_click($onclick[$i]);
						}
						$element[0]->set_values($pievalue);
					}
					elseif (array_key_exists('dimension', $props))
					{
						for ($i = 0; $i < count($data[0]); $i++)
						{

							$label = $this->formatNumber($number_format, $data[0][$i], (int) $precision) . $props['dimension'];
							$pievalue[$i] = new pie_value($data[0][$i], '');
							if ($data[0][$i] !== null)
								$pievalue[$i]->set_label($label, $element_color[$i], $label_fontsize);
							$pievalue[$i]->set_text($label);

							if ($legend)
								$pievalue[$i]->set_key_on_click($key_onclick[$i], $chart_id);
							if (count($onclick) > $i)
								$pievalue[$i]->set_on_click($onclick[$i]);
						}
						$element[0]->set_values($pievalue);
						if ($pie_label_values == "0")
							$element[0]->set_no_labels();
					}
					else
					{
						$element[0]->set_values($data[0]);
						if ($pie_label_values == "0")
							$element[0]->set_no_labels();
					}

				}
				else
				{
					for ($i = 0; $i < count($data[0]); $i++)
					{

						$label = $pie_label[$i] . $this->formatNumber($number_format, $data[0][$i], (int) $precision);
						$pievalue[$i] = new pie_value($data[0][$i], "");
						$pievalue[$i]->set_label($label, $element_color[$i], $label_fontsize);
						if ($onclick[$i] !== null)
							$pievalue[$i]->set_on_click($onclick[$i]);
					}
					$element[0]->set_values($pievalue);
					if ($pie_label_values == '0')
						$element[0]->set_no_labels();
				}
				if (array_key_exists('hide_pie_labels', $props))
					if ($props['hide_pie_labels'] == "1")
						$element[0]->set_no_labels();
				break;

			case "radar_area":
			case "line_area":
			case "line_area_dotted":
			case "radar_area_dotted":

				$dot_type = "dot";
				$dot_size = 3;

				if (strpos($type, "dotted") !== false)
				{
					$dot_type = "solid_dot";
					$dot_size = 3;
				}

				for ($i = 0; $i < $data_series; $i++)
				{
					$element[$i] = new area();
					if ($legend == true)
					{
						$element[$i]->set_key($keys[$i], $legend_fontsize, $chart_id);
						$element[$i]->set_key_on_click($key_onclick[$i]);
					}
					$element[$i]->set_width(2);
					$dot[$i] = new $dot_type();
					$dot[$i]->size($dot_size)
						->halo_size(0)
						->colour($element_color[$i]);
					if (count($onclick) > $i)
						$dot[$i]->set_on_click($onclick[$i]);
					$element[$i]->set_default_dot_style($dot[$i]);
					$element[$i]->set_fill_colour($element_color[$i]);
					$element[$i]->set_fill_alpha($alpha);

					if ($alert_value || strpos($type, "radar_") !== false)
					{
						for ($j = 0; $j < count($data[$i]); $j++)
						{

							$alert = $this->checkAlert($alert_values, $data[$i][$j]);
							if ($alert)
							{
								$dota[$j] = new $dot_type($data[$i][$j]);
								$temp_tooltip = str_replace("#legend#", $keys[$i], $alert['alert_tooltip']);
								if ($spoke_label_array)
									$temp_tooltip = str_replace("#spoke#", $spoke_label_array[$j], $temp_tooltip);
								$data[$i][$j] = $dota[$j]->size(3)
									->colour($alert['alert_color'])
									->tooltip($temp_tooltip);
								if ($alert['alert_onclick'])
									$data[$i][$j]->set_on_click($alert['alert_onclick']);
							}

							if ($tooltip && !$alert && strpos($type, "radar_") !== false)
							{
								$dota[$j] = new $dot_type($data[$i][$j]);
								if (count($keys))
									$temp_tooltip = str_replace("#legend#", $keys[$i], $tooltip);
								else
									$temp_tooltip = $tooltip;
								if ($spoke_label_array)
									$temp_tooltip = str_replace("#spoke#", $spoke_label_array[$j], $temp_tooltip);
								$data[$i][$j] = $dota[$j]->size($dot_size)
									->colour($element_color[$i])
									->tooltip($temp_tooltip);
							}
						}
					}

					if ($tooltip)
					{
						if (count($keys))
							$temp_tooltip = str_replace("#legend#", $keys[$i], $tooltip);
						else
							$temp_tooltip = $tooltip;
						$dot[$i]->size($dot_size)
							->colour($element_color[$i])
							->tooltip($temp_tooltip);
					}

					if (array_key_exists('line_animation', $props))
					{
						$animation = $props['line_animation'];
						if ($animation != "0")
							$element[$i]->set_on_show(new line_on_show($animation, $cascade, $delay));
					}

					if (strpos($type, "radar_") !== false)
						$element[$i]->loop();
				}
				break;

			default:
				$type = "line";
			case "radar_line_star":
			case "line_star":
			case "radar_line_hollow":
			case "line_hollow":
			case "radar_line":
			case "line":
			case "radar_line_dotted":
			case "line_dotted":
			case "radar_line_bow":
			case "line_bow":
				if ($show_trend)
					$saved_data = $data;
				$dot_type = "dot";
				$halo_size = 1;
				$dot_size = 3;
				$alert_dot_size = 4;

				if (strpos($type, "hollow") !== false)
				{
					$dot_type = "hollow_dot";
					$dot_size = 3;
					$halo_size = 2;
					$alert_dot_size = 4;
				}

				if (strpos($type, "star") !== false)
				{
					$dot_type = "star";
					$dot_size = 5;
					$halo_size = 2;
					$alert_dot_size = 5;
				}

				if (strpos($type, "bow") !== false)
				{
					$dot_type = "bow";
					$dot_size = 5;
					$alert_dot_size = 5;
				}

				if (strpos($type, "dotted") !== false)
				{
					$dot_type = "solid_dot";
					$dot_size = 3;
					$halo_size = 1;
				}

				for ($i = 0; $i < $data_series; $i++)
				{
					$element[$i] = new line();
					if ($legend == true)
					{
						$element[$i]->set_key($keys[$i], $legend_fontsize, $chart_id);
						$element[$i]->set_key_on_click($key_onclick[$i]);
					}

					$dot[$i] = new $dot_type();
					if (strpos($type, "bow") !== false)
						$dot[$i]->size($dot_size)
							->halo_size($halo_size)
							->colour($element_color[$i])
							->rotation(90);
					else
						$dot[$i]->size($dot_size)
							->halo_size($halo_size)
							->colour($element_color[$i]);
					if ($onclick[$i] !== null)
						$dot[$i]->set_on_click($onclick[$i]);
					$element[$i]->set_width(3);

					if ($show_linevalues == "1" && strpos($type, "line") !== false && strpos($type, "radar") === false)
					{
						$tags[$i] = new ofc_tags();
						$tags[$i]->font($tag_value_style['font-family'], $tag_value_style['font-size'])
							->colour($tag_value_style['color'])
							->align_x_center()
							->align_y_above()
							->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom'])
							->style($tag_value_style['bold'], false, false, 1.0)
							->rotate($tag_value_style['font-rotate']);
						$x = 0;

						if (array_key_exists("x_step", $props))
							$x_steps = $props['x_step'];
						else
							$x_steps = 1;

						foreach ($data[$i] as $v)
						{
							if ($x % $x_steps == 0)
							{
								$ofc_tag = new ofc_tag($x, $v);
								$ofc_tag->padding($tag_value_style['padding-right'], $tag_value_style['padding-bottom']);
								if ($v !== null)
									$ofc_tag->text("#y#" . $dimension);
								else
									$ofc_tag->text("");
								if ($alert_value)
								{
									$alert = $this->checkAlert($alert_values, $v);
									if ($alert)
										$ofc_tag->colour($alert['alert_color']);
								}
								$tags[$i]->append_tag($ofc_tag);
							}
							$x++;
						}
						$add_tags = true;
					}

					if ($alert_value || strpos($type, "radar_line") !== false)
					{
						for ($j = 0; $j < count($data[$i]); $j++)
						{
							$alert = $this->checkAlert($alert_values, $data[$i][$j]);
							if ($alert)
							{
								$dota[$j] = new $dot_type($data[$i][$j]);
								if (count($keys))
									$temp_tooltip = str_replace("#legend#", $keys[$i], $alert['alert_tooltip']);
								elseif ($alert['alert_tooltip'])
								   $temp_tooltip = $alert['alert_tooltip'];
								else
									$temp_tooltip = $tooltip;
								if ($spoke_label_array)
									$temp_tooltip = str_replace("#spoke#", $spoke_label_array[$j], $temp_tooltip);
								$data[$i][$j] = $dota[$j]->size($alert_dot_size)
									->colour($alert['alert_color'])
									->tooltip($temp_tooltip);
								if ($alert['alert_onclick'] !== null)
									$data[$i][$j]->set_on_click($alert['alert_onclick']);
							}
							if ($tooltip && !$alert && strpos($type, "radar_line") !== false)
							{
								$dota[$j] = new $dot_type($data[$i][$j]);
								if (count($keys))
									$temp_tooltip = str_replace("#legend#", $keys[$i], $tooltip);
								elseif ($alert['alert_tooltip'])
								   $temp_tooltip = $alert['alert_tooltip'];
								else
									$temp_tooltip = $tooltip;
								if ($spoke_label_array)
									$temp_tooltip = str_replace("#spoke#", $spoke_label_array[$j], $temp_tooltip);
								$data[$i][$j] = $dota[$j]->size($dot_size)
									->colour($element_color[$i])
									->tooltip($temp_tooltip);
							}
						}
					}

					if ($tooltip)
					{
						if (count($keys))
							$temp_tooltip = str_replace("#legend#", $keys[$i], $tooltip);
						else
							$temp_tooltip = $tooltip;
						$dot[$i]->size($dot_size)
							->colour($element_color[$i])
							->tooltip($temp_tooltip);
					}

					$element[$i]->set_default_dot_style($dot[$i]);
					if (array_key_exists('line_animation', $props))
					{
						$animation = $props['line_animation'];
						if ($animation != "0" && $show_linevalues == "0")
							$element[$i]->set_on_show(new line_on_show($animation, $cascade, $delay));
					}
					if (strpos($type, "radar_") !== false)
						$element[$i]->loop();
				}
				if (strpos($type, "radar") == false && $show_trend)
					$element[$data_series] = $this->drawTrendLine($saved_data, $data_series, $element_color[$data_series], $legend_fontsize, $dimension, $show_regression_formula, $precision, $number_format);
		}

		// setup color and values for elements (not for pie, hbar or bar_stack)
		if ($type != "pie" && $type != "bar_stack" && $type != "hbar" && $type != "scatter")
		{
			if ($data_series == 1 && strpos($type, "bar") !== false && ($multibar_color == "1" || $alert_value == true))
			{
				$j = 0;
				for ($i = 0; $i < count($data[0]); $i++)
				{
					if ($alert_value)
					{
						$alert = $this->checkAlert($alert_values, $data[0][$i]);
						if ($alert)
						{
							$data[0][$i] = new bar_value($data[0][$i]);
							$data[0][$i]->set_colour($alert['alert_color']);
							if ($alert['alert_onclick'])
								$data[0][$i]->set_on_click($alert['alert_onclick']);
							$data[0][$i]->set_tooltip($alert['alert_tooltip']);
						}
						elseif ($multibar_color == "1" && $data[0][$i] !== null)
						{
							$data[0][$i] = new bar_value($data[0][$i]);
							$data[0][$i]->set_colour($element_color[$j]);
							$j++;
						}
						else
						{
							if ($data[0][$i] !== null)
							{
								$data[0][$i] = new bar_value($data[0][$i]);
								$data[0][$i]->set_colour($element_color[0]);
							}
						}
					}

					elseif ($multibar_color == "1" && $data[0][$i] !== null)
					{
						$data[0][$i] = new bar_value($data[0][$i]);
						$data[0][$i]->set_colour($element_color[$j]);
						$j++;
					}
					else
					{
						if ($data[0][$i] !== null)
						{
							$data[0][$i] = new bar_value($data[0][$i]);
							$data[0][$i]->set_colour($element_color[0]);
						}
					}
				}
				$element[0]->set_values($data[0]);

			}
			elseif (strpos($type, "scatter") === false)
			{
				for ($i = 0; $i < $data_series; $i++)
				{
					if ($element_color[$i] !== null)
						$element[$i]->set_colour($element_color[$i]);
					$element[$i]->set_values($data[$i]);
				}
			}
		}

		$chart = new open_flash_chart();
		$chart->set_chartid($chart_id);

		if ($title !== null)
			$chart->set_title($title);

		if ($type != "pie")
		{
			for ($i = 0; $i < count($element); $i++)
				$chart->add_element($element[$i]);
		}
		else
		{
			$chart->add_element($element[0]);
		}

		if ($type == "hbar")
		{
			//setup x-axis for horizontal bar
			$x_axis = new x_axis();
			$x_axis->set_offset(false);
			$x_axis->colour = $x_axis_colour;

			if (array_key_exists('target_line', $props))
			{
				$x_axis->set_target_line($props['target_line']);
				if (array_key_exists('target_linecolor', $props))
				 $x_axis->set_target_linecolor($props['target_linecolor']);
				elseif (array_key_exists('grid_color', $props))
				 $x_axis->set_target_linecolor($props['grid_color']);
			}

			if (array_key_exists('x_max', $props))
				$max = $props['x_max'];
			else
				$max = $max + $steps / 2;

			if (array_key_exists('x_min', $props))
				$min = $props['x_min'];
			elseif ($min < 0)
				$min = $min - $steps;

			if (array_key_exists('revert_x_range', $props))
				$x_axis->set_range($max, $min);
			else
				$x_axis->set_range($min, $max);

			$x_label_text = "#val#" . $dimension;
			$x_axis_labels = new x_axis_labels();
			if (array_key_exists('x_step', $props))
				$x_axis_labels->set_steps($props['x_step']);
			else
				$x_axis_labels->set_steps($steps);
			$x_axis_labels->text = $x_label_text;
			$x_axis_labels->set_colour($label_color);
			$x_axis_labels->set_size($label_fontsize);
			$x_axis_labels->visible_steps(1);

			if (array_key_exists('x_label_rotate', $props))
				$x_axis_labels->rotate($props['x_label_rotate']);

			$x_axis->set_labels($x_axis_labels);

			//setup y-axis for horizontal bar
			$y_axis = new y_axis();
			$y_axis->set_offset(true);
			$y_axis->set_range(0, null, 1);

			if (array_key_exists('y_axis_color', $props))
			{
				$y_axis->colour = $props['y_axis_color'];
			}

			// setup y-Axis labels:
			if (!array_key_exists('x_axis_labels', $props) && $labels_found == false)
			{
				for ($i = 0; $i < count($data[0]); $i++)
					$x_labels[$i] = $i + 1;
				$labels_found = true;
			}
			if (array_key_exists('x_axis_labels', $props) && $labels_found == false)
			{
				$x_labels_in = $props['x_axis_labels'];
				$x_labels = explode(",", $x_labels_in);
				if (array_key_exists('x_label_truncate', $props))
					$x_labels = $this->truncateLabels($x_labels, $props['x_label_truncate']);
				$labels_found = true;

			}
			if ($labels_found == true)
			{
				//array_reverse($x_labels);
				$y_axis->set_labels($x_labels);
			}

		} // end hbar props
		// setup radar chart props
		elseif (strpos($type, "radar_") !== false)
		{
			if (array_key_exists('y_max', $props))
				$max = $props['y_max'];
			else
				$max = $max + $steps / 2;
			$radar_axis = new radar_axis($max);

			if (array_key_exists('y_axis_color', $props))
				$radar_axis->set_colour($props['y_axis_color']);
			else
				$radar_axis->set_colour($label_color);

			if (array_key_exists('grid_color', $props))
				$radar_axis->set_grid_colour($props['grid_color']);

			if ($spoke_label_array)
			{
				$radar_spoke_labels = new radar_axis_labels($spoke_label_array);
				$radar_spoke_labels->set_colour($label_color);
				$radar_spoke_labels->set_size($label_fontsize);
				$radar_axis->set_spoke_labels($radar_spoke_labels);
			}

			// setup radar axis labels:
			if (!array_key_exists('radar_axis_labels', $props))
			{
				$labels = array();
				$tmp = "0" . $dimension;
				for ($i = 1; $i <= $max + $steps; $i++)
				{
					if (fmod($i, $steps) == 0 && $i <= $max)
						$tmp .= "," . $i . $dimension;
					else
						$tmp .= ",";
				}
				$labels = explode(",", $tmp);

				$radar_axis->set_steps($steps);
				$radar_axis_labels = new radar_axis_labels($labels);
				$radar_axis_labels->set_colour($label_color);
				$radar_axis_labels->set_size($label_fontsize);
				$radar_axis->set_labels($radar_axis_labels);
			}
			if (array_key_exists('radar_axis_labels', $props))
			{
				$labels = explode(",", $props['radar_axis_labels']);

				$tmp = $labels[0] . $dimension;
				$j = 0;
				for ($i = 1; $i <= $max + $steps; $i++)
				{
					if (fmod($i, $steps) == 0 && $i <= $max)
					{
						$j++;
						$tmp .= "," . $labels[$j] . $dimension;
					}
					else
						$tmp .= ",";
				}
				$t_labels = explode(",", $tmp);

				$labels = new radar_axis_labels($t_labels);
				$radar_axis->set_steps($steps);
				$labels->set_colour($label_color);
				$labels->set_size($label_fontsize);

				$radar_axis->set_labels($labels);
			}

			$chart->set_radar_axis($radar_axis);
		} //end radar chart


		elseif (strpos($type, "radar_") === false && $type != "pie")
		{
			//setup y-axis
			$y_axis = new y_axis();

			if (array_key_exists('target_line', $props))
			{
				$y_axis->set_target_line($props['target_line']);
				if (array_key_exists('target_linecolor', $props))
				 $y_axis->set_target_linecolor($props['target_linecolor']);
				elseif (array_key_exists('grid_color', $props))
				 $y_axis->set_target_linecolor($props['grid_color']);
			}

			if (array_key_exists('y_max', $props))
			{
				if ($props['y_max'] !== null)
					$max = $props['y_max'];
			}
			else
				$max = $max + round($steps / 2, 1);

			if (array_key_exists('y_min', $props))
				$min = $props['y_min'];
			elseif ($min < 0)
				$min = $min - $steps;

			if (array_key_exists('revert_y_range', $props))
				$y_axis->set_range($max, $min, $steps);
			else
				$y_axis->set_range($min, $max, $steps);

			if (array_key_exists('y_axis_color', $props))
			{
				$y_axis->colour = $props['y_axis_color'];
				$y_axis_labels = new y_axis_labels();
				$y_label_text = "#val#" . $dimension;
				$y_axis_labels->set_colour($y_label_color);
				$y_axis_labels->set_size($label_fontsize);
				$y_axis_labels->set_text($y_label_text);
				if (array_key_exists('y_label_rotate', $props))
					$y_axis_labels->rotate($props['y_label_rotate']);
				$y_axis->set_label_text($y_label_text);
				$y_axis->set_labels($y_axis_labels);
			}

			//setup x-axis
			$x_axis = new x_axis();

			if (stristr($type, "bar_") === false)
				$x_axis->set_offset(false);

			elseif (stristr($type, "line") === true && count($data[0]) < 2)
				$x_axis->set_offset(true);

			else
				$x_axis->set_offset(true);

			if (array_key_exists('x_max', $props))
				$x_max = $props['x_max'];
			elseif (!$x_max)
				$x_max = null;

			if (array_key_exists('x_min', $props))
				$x_min = $props['x_min'];
			elseif (!$x_min)
				$x_min = 0;

			if ($type == "scatter")
				$x_axis->set_range($x_min, $x_max);
			else
				$x_axis->set_range($x_min, null);

			if (array_key_exists('axis_3d', $props))
			{
				if ($props['axis_3d'] == '1')
					$x_axis->set_3d(6);
			}

			$x_axis->colour = $x_axis_colour;

			// setup x-Axis labels:
			if (!array_key_exists('x_axis_labels', $props) && $labels_found == false)
			{
				$x_axis_labels = new x_axis_labels();
				$x_axis_labels->set_colour($label_color);
				$x_axis_labels->set_size($label_fontsize);
				if (array_key_exists('x_axis_steps', $props) && strpos($type, "scatter") !== false)
				{
					$x_axis_labels->set_steps($props['x_axis_steps']);
					$x_axis_labels->visible_steps(1);
				}
				elseif (array_key_exists('x_step', $props))
					$x_axis_labels->visible_steps((int) $props['x_step']);
				$x_axis->set_labels($x_axis_labels);
			}

			if (array_key_exists('x_axis_labels', $props) && $labels_found == false)
			{
				$x_labels_in = $props['x_axis_labels'];
				$x_labels = explode(",", $x_labels_in);
				if (array_key_exists('x_label_truncate', $props))
					$x_labels = $this->truncateLabels($x_labels, $props['x_label_truncate']);
				$labels_found = true;

			}

			if ($labels_found == true)
			{
				$labels = new x_axis_labels();
				$labels->set_labels($x_labels);

				if (array_key_exists('x_step', $props))
					$labels->visible_steps((int) $props['x_step']);
				else
				{
					if (count($x_labels) > 15)
					{
						$visible = count($x_labels) / 15;
						$labels->visible_steps((int) $visible + 1);
					}
					else
						$labels->visible_steps(1);
				}

				if (array_key_exists('x_label_rotate', $props))
					$labels->rotate($props['x_label_rotate']);
				$labels->set_size($label_fontsize);
				$labels->set_colour($label_color);

				$x_axis->set_labels($labels);
			}

		} // end else other charts


		if (strpos($type, "radar_") === false && $type != "pie")
		{

			if (array_key_exists('x_step', $props))
				$x_axis->set_steps($props['x_step']);

			elseif (array_key_exists('x_axis_steps', $props))
				$x_axis->set_steps($props['x_axis_steps']);

			elseif (array_key_exists('x_axis_steps', $props) && $type == "scatter")
				$x_axis->set_steps($x_axis_steps);

			elseif ($type == "hbar")
				$x_axis->set_steps($steps);

			elseif ($labels_found == true && !array_key_exists('x_step', $props))
			{
				if (count($x_labels) > 15)
				{
					$visible = count($x_labels) / 15 + 1;
					$x_axis->set_steps((int) $visible);
				}
			}

			if (array_key_exists('grid_color', $props))
			{
				$x_axis->set_grid_colour($props['grid_color']);
				$y_axis->set_grid_colour($props['grid_color']);
			}

			if (array_key_exists('x_legend', $props))
			{
				$x_legend = $props['x_legend'];
				$x_legend = new x_legend($x_legend);
				$x_legend->set_style($legend_style);
				$chart->set_x_legend($x_legend);
			}

			if (array_key_exists('y_legend', $props))
			{
				$y_legend = $props['y_legend'];
				$y_legend = new y_legend($y_legend);
				$y_legend->set_style($legend_style);
				$chart->set_y_legend($y_legend);
			}

			$chart->set_x_axis($x_axis);
			$chart->set_y_axis($y_axis);

		}

		if (array_key_exists('bg_color', $props))
			$bg_color = $props['bg_color'];
		else
			$bg_color = "ffffff";

		$chart->set_bg_colour($bg_color);

		if (array_key_exists('bg_image', $props))
		{
			$bg_image = $props['bg_image'];
			$chart->set_bg_image($bg_image);
		}
		else
			$bg_image = null;

		if ($create_menu)
		{
			$ofc_menu = new ofc_menu("#E6E6FF", $label_color);
			for ($i = 0; $i < count($menu); $i++)
			{
				$ofc_menu_item[$i] = new ofc_menu_item($menu[$i]['text'], $menu[$i]['script']);
			}
			$ofc_menu->values($ofc_menu_item);
			$chart->set_menu($ofc_menu);
		}

		if ($legend)
		{
			$legend = new legend();
			$legend->set_visible(true);
			if (array_key_exists('right_legend', $props))
				if ($props['right_legend'] == "1")
					$legend->set_position("right");
			$legend->set_stroke(2);
			$legend->set_bg_colour($bg_color);
			if ($bg_image)
				$legend->set_alpha(0.0);
			$legend->set_shadow(true);
			$legend->set_border(true);
			$legend->set_margin(5);
			$chart->set_legend($legend);
		}

		if ($tooltip != '')
		{
			$chart_tooltip = new tooltip($tooltip);
			if (strpos($type, "bar") !== false)
				$chart_tooltip->set_hover();
			else
				$chart_tooltip->set_proximity();
			$chart_tooltip->set_shadow(true);
			$chart_tooltip->set_stroke(4);
			$color = $this->getStyle($tooltip_style, "color");
			if ($color === null)
				$color = $element_color[0];
			$chart_tooltip->set_colour($color);
			$chart_tooltip->set_background_colour($bg_color);
			$chart_tooltip->set_title_style($tooltip_style);
			$chart_tooltip->set_body_style($tooltip_style);
			$chart->set_tooltip($chart_tooltip);
		}

		if ($add_tags == true)
		{
			if (is_array($tags))
			{
				for ($i = 0; $i < count($tags); $i++)
					$chart->add_element($tags[$i]);
			}
			else
				$chart->add_element($tags);
		}

		// default numberformat is german/european format
		if (!array_key_exists('number_format', $props))
			$props['number_format'] = "c";

		switch ($props['number_format'])
		{
			case "a":
				$chart->set_number_format($precision, 0, 0, 1);
				break;
			case "b":
				$chart->set_number_format($precision, 0, 1, 1);
				break;
			case "c":
				$chart->set_number_format($precision, 0, 1, 0);
				break;
			case "d":
				$chart->set_number_format($precision, 0, 0, 0);
				break;
			default:
				$chart->set_number_format($precision, 0, 1, 0);
		}

		return $chart;
	}

	private function calc_steps($max)
	{
		$steps = $max / 5;

		$x = 2;
		if ($max > 20)
			$x = 5;
		if ($max > 100)
			$x = 10;
		if ($max > 500)
			$x = 50;
		if ($max > 1000)
			$x = 100;
		if ($max > 5000)
			$x = 500;

		if ($max > 10)
		{
			$steps = ceil($steps);
			while ($steps % $x != 0)
				$steps++;
		}
		elseif ($max < 10 && $max > 5)
		{
			$steps = round($steps, 0);
		}
		elseif ($max < 5 && $max > 2)
		{
			$steps = round($steps, 2);
		}
		elseif ($max < 2 && $max > 0)
		{
			$steps = round($steps, 3);
		}
		else
		{
			$steps = round($steps, 1);
		}

		if ($steps == 0)
			$steps = 1;

		return $steps;
	}

	private function formatNumber($format, $number, $precision = 2)
	{
		switch ($format)
		{
			case "a":
				$string = number_format($number, $precision, ".", "");
				for ($i = 0; $i < $precision; $i++)
					$string = rtrim($string, 0);
				$string = trim($string, ".");
				break;

			case "b":
				$string = number_format($number, $precision, ",", "");
				for ($i = 0; $i < $precision; $i++)
					$string = rtrim($string, 0);
				$string = trim($string, ",");
				break;

			case "c":
				$string = number_format($number, $precision, ",", ".");
				for ($i = 0; $i < $precision; $i++)
					$string = rtrim($string, 0);
				$string = trim($string, ",");
				break;

			case "d":
				$string = number_format($number, $precision, ".", ",");
				for ($i = 0; $i < $precision; $i++)
					$string = rtrim($string, 0);
				$string = trim($string, ".");
				break;

			default:
				$string = number_format($number, $precision, ",", ".");
				for ($i = 0; $i < $precision; $i++)
					$string = rtrim($string, 0);
				$string = trim($string, ",");
				break;
		}

		return $string;
	}

	private function checkAlert($alert_values, $data_value)
	{
		if ($data_value === null)
			return false;

		for ($i = 0; $i < count($alert_values); $i++)
		{
			if ($alert_values[$i]['alert_op'] == "gt" && $data_value > $alert_values[$i]['alert_value'])
				return $alert_values[$i];

			if ($alert_values[$i]['alert_op'] == "lt" && $data_value < $alert_values[$i]['alert_value'])
				return $alert_values[$i];

			if ($alert_values[$i]['alert_op'] == "eq" && $data_value == $alert_values[$i]['alert_value'])
				return $alert_values[$i];

		}

		return false;
	}

	private function getRandomColor()
	{

		$hex_rgb = dechex(rand(0, 255)) . dechex(rand(0, 255)) . dechex(rand(0, 255));
		if (strlen($hex_rgb) == 4)
		{
			$hex_rgb = $hex_rgb . rand(10, 99);
		}
		else if (strlen($hex_rgb) == 5)
		{
			$hex_rgb = $hex_rgb . rand(0, 9);
		}

		return $hex_rgb;
	}

	private function truncateLabels($labels, $size)
	{
		if ($size == '0')
			return $labels;

		for ($i = 0; $i < count($labels); $i++)
		{
			if (strlen($labels[$i]) > $size)
				$labels[$i] = mb_substr($labels[$i], 0, $size - 3) . "...";
		}

		return $labels;

	}

	private function getStyle($style, $property)
	{
		if (strpos($style, $property) !== false)
		{
			$style = str_replace("#", "", $style);
			$style = str_replace("px", "", $style);
			$styles = explode(";", $style);

			for ($i = 0; $i < count($styles); $i++)
			{
				if (strpos($styles[$i], $property) !== false)
				{
					$value = explode(":", $styles[$i]);
					return trim($value[1]);
				}
			}
		}
		else
			return null;
	}

	private function arrangeDataforStackbar($data)
	{

		$cols = array();
		$col = array();

		$temp = explode("|", $data);
		for ($k = 0; $k < count($temp); $k++)
			$cols[$k] = explode(",", $temp[$k]);

		$data = null;
		$c = 0;

		for ($j = 0; $j < count($cols[0]); $j++)
		{
			for ($c = 0; $c < count($cols); $c++)
			{
				$data .= $cols[$c][$j] . ",";
			}
			$data = rtrim($data, ",");
			$data .= "|";
		}

		$data = rtrim($data, "|");

		return $data;
	}

	private function getDataFromUrl($url, $html = false)
	{
		$result = array();
		$result['data'] = "-1";

		if (function_exists('file_get_contents'))
		{
			$content = trim(file_get_contents($url));
		}
		elseif (function_exists('curl_init'))
		{
			$fp = curl_init($url);
			curl_setopt($fp, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($fp, CURLOPT_HEADER, 0);
			$content = trim(curl_exec($fp));
			curl_close($fp);
		}
		else
			return $result;

		if ($html)
			return $content;

		$file_data = explode("/", $content);

		if (count($file_data) > 1)
		{
			if (function_exists("mb_detect_encoding"))
			{
				if (mb_detect_encoding($file_data[0], "UTF-8", true))
					$result['label'] = $file_data[0];
				else
					$result['label'] = $this->correct_code($file_data[0]);
			}
			else
				$result['label'] = $this->correct_code($file_data[0]);

			$result['data'] = trim($file_data[1]);
		}
		else
			$result['data'] = trim($file_data[0]);

		return $result;

	}

	private function getDataFromFile($file)
	{
		$result = array();
		$result['data'] = "-1";
		$OK = false;

		if (file_exists($file))
		{
			if (!function_exists('file_get_contents'))
			{
				$fp = fopen($file, "r");
				$content = fgets($fp);
				fclose($fp);
			}
			else
			{
				$content = trim(file_get_contents($file));
			}
			$OK = true;
		}

		if ($OK == false)
			return $result;

		$file_data = explode("/", $content);

		if (count($file_data) > 1)
		{
			if (function_exists("mb_detect_encoding"))
			{
				if (mb_detect_encoding($file_data[0], "UTF-8", true))
					$result['label'] = $file_data[0];
				else
					$result['label'] = $this->correct_code($file_data[0]);
			}
			else
				$result['label'] = $this->correct_code($file_data[0]);

			$result['data'] = trim($file_data[1]);
		}
		else
			$result['data'] = trim($file_data[0]);

		return $result;

	}

	private function correct_code($string)
	{
		$string = str_replace(" />", ">", $string);
		$string = str_replace("\\'", "'", $string);
		$string = iconv("ISO-8859-15", "UTF-8//TRANSLIT//IGNORE", $string);

		return $string;
	}

	private function drawTargetLine($target, $count, $color, $dimension)
	{
		$values = array();
		for ($i = 0; $i < $count; $i++)
			$values[$i] = new scatter_value($i, $target, "");

		/* draw target line: */
		$dot = new solid_dot($color, 2);
		$dot->size(2)
			->halo_size(0)
			->tooltip("Target: #val#$dimension");

		$element = new scatter_line($color, 3);
		$element->set_default_dot_style($dot);
		$element->set_values($values);

		return $element;
	}

	private function drawTrendLine($data, $data_series, $color, $fontsize, $dimension, $show_regression_formula, $precision, $number_format)
	{
		//  setup data for calculation:
		$calc_data = array();
		if ($data_series < 2)
		{
			for ($i = 0; $i < count($data[0]); $i++)
				$x_values[$i] = $i;
			$calc_data = $data[0];
		}
		else
		{
			for ($j = 0; $j < count($data[0]); $j++)
				$calc_data[$j] = null;

			for ($i = 0; $i < count($data); $i++)
			{
				for ($j = 0; $j < count($data[$i]); $j++)
					$calc_data[$j] += $data[$i][$j];
			}

			for ($i = 0; $i < count($calc_data); $i++)
				$calc_data[$i] = $calc_data[$i] / $data_series;

			for ($i = 0; $i < count($calc_data); $i++)
				$x_values[$i] = $i;
		}

		$result = $this->calculate_Regression($x_values, $calc_data, null, $number_format, $precision);

		if ($result)
		{
			$values = $result['values'];
			$formula_out = $result['formula_out'];

			/* draw Regression line: */
			$dot = new solid_dot($color, 2);
			$dot->size(2)
				->halo_size(0)
				->tooltip("Trend: #val#$dimension");
			$element = new scatter_line($color, 2);
			$element->set_default_dot_style($dot);
			$element->set_values($values);

			// add formula to key
			if ($show_regression_formula == true)
				$element->set_key($formula_out, $fontsize);
			else
				$element->set_key("Trend $show_regression_formula", $fontsize);
			$element->set_key_on_click("toggle-visibility");

			return $element;

		}
		else
			return null;

	}

	private function calculate_Regression($x_values, $data, $user_formula = null, $number_format = "c", $precision)
	{
		$R = utf8_encode("R²");
		$formula_out = "";

		if ($user_formula)
		{
			$formula_out = "Trend: " . $user_formula;
			if (strpos($user_formula, "=") === false)
			{
				$user_formula = trim($user_formula);
				$user_formula = str_replace(",", ".", $user_formula);
				$formula = "4";

				$calc = new EvalMath();
				$calc->evaluate("y(x)=" . $user_formula);
			}
			else
			{
				$tmp = explode("=", $user_formula);
				$user_formula = trim($tmp[1]);
				$user_formula = str_replace(",", ".", $tmp[1]);
				if ($user_formula)
				{
					$formula = 4;
					$calc = new EvalMath();
					$calc->evaluate("y(x)=" . $user_formula);
				}
				else
					$formula = false;
			}
		}
		else
		{
			$j = 0;
			for ($k = 0; $k < count($data); $k++)
			{
				if (($data[$k] != 0 && $x_values[$k] != 0) && ($data[$k] !== null && $x_values[$k] !== null))
				{
					$test_Y[$j] = log10($data[$k]);
					$test_X[$j] = log10($x_values[$k]);
					$test_x_values[$j] = $x_values[$k];
					$j++;
				}
			}

			$slr1 = new SimpleLinearRegression($x_values, $data);
			$slr2 = new SimpleLinearRegression($test_x_values, $test_Y);
			$slr3 = new SimpleLinearRegression($test_X, $test_Y);

			// test three regression formulas:
			array_multisort($x_values, $data);

			if ($slr1)
				$tst1_R = $slr1->RSquared;
			if ($slr2)
			{
				$slr2_A_value = pow(10, $slr2->YInt);
				$slr2_B_value = pow(10, $slr2->Slope);
			}
			if ($slr3)
			{
				$slr3_A_value = pow(10, $slr3->YInt);
				$slr3_B_value = $slr3->Slope;
			}

			foreach ($x_values as $k => $x_value)
			{
				// equation: Y = a * b^X
				$slr2_value[$k] = $slr2_A_value * pow($slr2_B_value, $x_value);
				//equation: Y = a * X^b
				$slr3_value[$k] = $slr3_A_value * pow($x_value, $slr3_B_value);
			}

			$slr2_R = calcR($data, $slr2_value);
			$slr3_R = calcR($data, $slr3_value);

			$tst2_R = pow($slr2_R, 2);
			$tst3_R = pow($slr3_R, 2);

			$tst2_R = $slr2_R;
			$tst3_R = $slr3_R;

			if ($tst1_R > $tst2_R && $tst1_R > $tst3_R)
				$formula = 1;

			if (is_finite($tst2_R))
			{
				if ($tst2_R > $tst1_R && $tst2_R > $tst3_R)
					$formula = 2;
			}

			if (is_finite($tst3_R))
			{
				if ($tst3_R > $tst1_R && $tst3_R > $tst2_R)
					$formula = 3;
			}

			if ($formula == 1)
			{
				if ($slr1->YInt >= 0)
					$plus = "+";
				else
					$plus = "";
				$formula_out = "Trend: y = " . $this->formatNumber($number_format, $slr1->Slope, $precision) . "x" . "$plus" . $this->formatNumber($number_format, $slr1->YInt, $precision);
			}
			$formula_out .= " ($R=" . $this->formatNumber($number_format, $slr1->RSquared, $precision) . ")";
			if ($formula == 2)
			{
				$formula_out = "Trend: y = " . $this->formatNumber($number_format, $slr2_A_value, $precision) . " * " . $this->formatNumber($number_format, $slr2_B_value, $precision) . "^x";
				$formula_out .= " ($R=" . $this->formatNumber($number_format, $slr2_R, $precision) . ")";
			}
			if ($formula == 3)
			{
				$formula_out = "Trend: y = " . $this->formatNumber($number_format, $slr3_A_value, $precision) . " * x^" . $this->formatNumber($number_format, $slr3_B_value, $precision);
				$formula_out .= " ($R=" . $this->formatNumber($number_format, $slr3_R, $precision) . ")";
			}
		}
		if ($formula)
		{
			$values = array();
			$j = 0;

			foreach ($x_values as $k => $x_value)
			{
				if ($formula == 1)
					// equation: Y = X * $Slope + $YInt
					//$value [$k] = ($x_value * $slr1->Slope) + $slr1->YInt;
					$value[$k] = $slr1->PredictedY[$k];
				if ($formula == 2)
					$value[$k] = $slr2_value[$k];

				if ($formula == 3)
					$value[$k] = $slr3_value[$k];

				if ($formula == 4)
				{
					$x = round($x_value, 5);
					$value[$k] = $calc->e("y($x)");

					if ($calc->last_error)
						return null;
				}
				if ($j == 0)
				{
					$values[$j] = new scatter_value($x_value, $value[$k], "");
					$j++;
				}
				elseif ($x_values[$k] != $x_values[$k - 1])
				{
					$values[$j] = new scatter_value($x_value, $value[$k], "");
					$j++;
				}
			}

			$result['values'] = $values;
			if ($formula == "4")
				$formula_out .= " ($R=" . $this->formatNumber($number_format, calcR($data, $value), $precision) . ")";
			$result['formula_out'] = $formula_out;
			$calc = null;

			return $result;

		}
		return null;
	}

}
?>