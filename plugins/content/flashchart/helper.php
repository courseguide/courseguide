<?php

/**
 * flashChart Joomla! Plugin
 *
 * @author     Joachim Schmidt <joachim.schmidt@jschmidt-systembeatung.de>
 * @copyright  Copyright (C) 2011 Joachim Schmidt. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * change activity:
 * 06.02.2014: restructured code (added most code to this helper)
 *
 */

defined('_JEXEC') or die('Restricted access');

class plgContentflashChartHelper
{

	function getChartProperties($id, $plugin_parms, $param_line)
	{
		$result = array();

		$type = $this->getParam($param_line, 'type', $this->getParam($plugin_parms, 'type', 'bar_fill'));
		$bg_color = $this->getParam($param_line, 'bg_color', $this->getParam($plugin_parms, 'bg_color', 'FFFFFF'));
		$bg_image = $this->getParam($param_line, 'bg_image', $this->getParam($plugin_parms, 'bg_image', ''));
		$chart_colors = $this->getParam($param_line, 'chart_colors', $this->getParam($plugin_parms, 'chart_colors', '00FF00,ff0000,0000ff'));
		$title_style = $this->getParam($param_line, 'title_style', $this->getParam($plugin_parms, 'title_style', 'padding:10px; font-size:14px; font-weight:bold; font-family:Sans-Serif,Arial,Helvetica;color:51698F;'));
		$axis_legend_style = $this->getParam($param_line, 'axis_legend_style', $this->getParam($plugin_parms, 'axis_legend_style', 'font-size:12px; font-weight:normal; font-family:Sans-Serif,Arial,Helvetica;color:000000;'));
		$tag_style = $this->getParam($param_line, 'tag_style', $this->getParam($plugin_parms, 'tag_style', 'font-family:Arial; font-size:11px; font-weight:normal; color:000000;font-rotate: 0;padding-right:0;padding-bottom:0;'));
		$tooltip_style = $this->getParam($param_line, 'tooltip_style', $this->getParam($plugin_parms, 'tooltip_style', 'font-size:10px; font-weight:normal;color:000000;'));
		$x_axis_color = $this->getParam($param_line, 'x_axis_color', $this->getParam($plugin_parms, 'x_axis_color', 'b0b0b0'));
		$label_color = $this->getParam($param_line, 'label_color', $this->getParam($plugin_parms, 'label_color', '000000'));
		$y_axis_color = $this->getParam($param_line, 'y_axis_color', $this->getParam($plugin_parms, 'y_axis_color', 'b0b0b0'));
		$grid_color = $this->getParam($param_line, 'grid_color', $this->getParam($plugin_parms, 'grid_color', 'bfbfbf'));
		$bar_animation = $this->getParam($param_line, 'bar_animation', $this->getParam($plugin_parms, 'bar_animation', 'null'));
		$line_animation = $this->getParam($param_line, 'line_animation', $this->getParam($plugin_parms, 'line_animation', 'null'));
		$pie_animation = $this->getParam($param_line, 'pie_animation', $this->getParam($plugin_parms, 'pie_animation', '1'));
		$scatter_animation = $this->getParam($param_line, 'scatter_animation', $this->getParam($plugin_parms, 'scatter_animation', '0'));
		$pie_label_values = $this->getParam($param_line, 'pie_label_values', $this->getParam($plugin_parms, 'pie_label_values', '0'));
		$pie_legend = $this->getParam($param_line, 'pie_legend', $this->getParam($plugin_parms, 'pie_legend', '1'));
		$right_legend = $this->getParam($param_line, 'right_legend', $this->getParam($plugin_parms, 'right_legend', '0'));
		$label_fontsize = $this->getParam($param_line, 'label_fontsize', $this->getParam($plugin_parms, 'label_fontsize', '12'));
		$legend_fontsize = $this->getParam($param_line, 'legend_fontsize', $this->getParam($plugin_parms, 'legend_fontsize', '10'));
		$width = $this->getParam($param_line, 'width', $this->getParam($plugin_parms, 'width', '100%'));
		$height = $this->getParam($param_line, 'height', $this->getParam($plugin_parms, 'height', 350));
		$axis_3d = $this->getParam($param_line, 'axis_3d', $this->getParam($plugin_parms, 'axis_3d', '1'));
		$create_image = $this->getParam($param_line, 'create_image', $this->getParam($plugin_parms, 'create_image', '0'));
		$number_format = $this->getParam($param_line, 'number_format', $this->getParam($plugin_parms, 'number_format', 'c'));
		$precision = $this->getParam($param_line, 'precision', $this->getParam($plugin_parms, 'precision', '2'));
		$alpha = $this->getParam($param_line, 'alpha', $this->getParam($plugin_parms, 'alpha', '0.8'));
		$multibar_color = $this->getParam($param_line, 'multibar_color', $this->getParam($plugin_parms, 'multibar_color', '0'));
		$debug = $this->getParam($param_line, 'debug');
		$hide_chart = $this->getParam($param_line, 'hide_chart');
		$menu = $this->getParam($param_line, 'menu');
		$data = $this->getParam($param_line, 'data');
		$rearrange_data = $this->getParam($param_line, 'rearrange_data');
		$create_popup = $this->getParam($param_line, 'create_popup');
		$force_create_popup = $this->getParam($param_line, 'force_create_popup');
		$create_script = $this->getParam($param_line, 'create_script');
		$file = $this->getParam($param_line, 'file');
		$url = $this->getParam($param_line, 'url');
		$sql = $this->getParam($param_line, 'sql');
		$sql_labels = $this->getParam($param_line, 'sql_labels');
		$tooltip = $this->getParam($param_line, 'tooltip');
		$dimension = $this->getParam($param_line, 'dimension');
		$t_fontsize = $this->getParam($param_line, 't_fontsize');
		$bcolor = $this->getParam($param_line, 'bcolor');
		$c_color = $this->getParam($param_line, 'c_color');
		$x_min = $this->getParam($param_line, 'x_min');
		$x_max = $this->getParam($param_line, 'x_max');
		$x_interval = $this->getParam($param_line, 'x_interval');
		$y_min = $this->getParam($param_line, 'y_min');
		$y_max = $this->getParam($param_line, 'y_max');
		$revert_x_range = $this->getParam($param_line, 'revert_x_range');
		$revert_y_range = $this->getParam($param_line, 'revert_y_range');
		$title = $this->getParam($param_line, 'title');
		$modal_title = $this->getParam($param_line, 'modal_title');
		$x_axis_labels = $this->getParam($param_line, 'x_axis_labels');
		$x_legend = $this->getParam($param_line, 'x_legend');
		$x_label_rotate = $this->getParam($param_line, 'x_label_rotate');
		$x_label_truncate = $this->getParam($param_line, 'x_label_truncate');
		$hide_pie_labels = $this->getParam($param_line, 'hide_pie_labels');
		$show_barvalues = $this->getParam($param_line, 'show_barvalues');
		$show_linevalues = $this->getParam($param_line, 'show_linevalues');
		$show_regression_line = $this->getParam($param_line, 'show_regression_line');
		$show_regression_formula = $this->getParam($param_line, 'show_regression_formula');
		$modal_chart = $this->getParam($param_line, 'modal_chart');
		$show_trend = $this->getParam($param_line, 'show_trend');
		$target_line = $this->getParam($param_line, 'target_line');
		$target_linecolor = $this->getParam($param_line, 'target_linecolor');
		$formula = $this->getParam($param_line, 'formula');
		$pie_radius = $this->getParam($param_line, 'pie_radius');
		$radar_spoke_labels = $this->getParam($param_line, 'radar_spoke_labels');
		$radar_axis_labels = $this->getParam($param_line, 'radar_axis_labels');
		$y_label_color = $this->getParam($param_line, 'y_label_color');
		$y_legend = $this->getParam($param_line, 'y_legend');
		$x_axis_steps = $this->getParam($param_line, 'x_axis_steps');
		$x_step = $this->getParam($param_line, 'x_step');
		$y_step = $this->getParam($param_line, 'y_step');
		$alert_value = $this->getParam($param_line, 'alert_value');
		$alert_color = $this->getParam($param_line, 'alert_color');
		$alert_tooltip = $this->getParam($param_line, 'alert_tooltip');
		$alert_onclick = $this->getParam($param_line, 'alert_onclick');
		$key_onclick = $this->getParam($param_line, 'key_onclick');
		$onclick = $this->getParam($param_line, 'onclick');
		$legend = $this->getParam($param_line, 'legend');
		$flashchart_shadowbox = $this->getParam($plugin_parms, 'flashchart_shadowbox', '1');
		$flashchart_jquery = $this->getParam($plugin_parms, 'flashchart_jquery', '1');
		$allow_formula = $this->getParam($plugin_parms, 'allow_formula', '0');
		$display_as_image = $this->getParam($param_line, 'display_as_image');
		$dbhost = $this->getParam($plugin_parms, 'dbhost', 'localhost');
		$dbname = $this->getParam($plugin_parms, 'dbname', '');
		$dbuser = $this->getParam($plugin_parms, 'dbuser', '');
		$dbpassword = $this->getParam($plugin_parms, 'dbpassword', '');

		if ($data != '')
		{
			$data_request = $data;
			$error_string = "";
			/** ----------------------------------------------------------------------------------- */
			/**  process parameters for flashchart object                                            */
			/** ----------------------------------------------------------------------------------- */
			$props = $this->initializeProps();

			if ($id == '')
				$id = "chart_" . rand(1, 9999);
			$props["chart_id"] = $id;

			$popup_window = null;
			$shadow_box = null;

			$props['gen_chart'] = true;

			if ($data == "popup_window")
			{
				$popup_window = "1";
				$props['gen_chart'] = false;
			}

			if ($data == "shadow_box")
			{
				$props['flashchart_shadowbox'] = $flashchart_shadowbox;
				$shadow_box = "1";
				$props['gen_chart'] = false;
			}

			if ($url != '' && $data == "json")
			{
				$rc = $this->checkURL($url);
				if ($rc['success'] == true)
				{
					$props["url"] = $url;
					$props['data'] = 'json';
					//$props['gen_chart'] = false;
				}
				else
					$error_string .= "<br />&nbsp; - &quot;$url&quot; - " . JText::_('URL_NOT_FOUND') . "  " . $rc['error'];
			}

			$props['width'] = $width;
			if (!is_numeric($width))
			{
				$pos = strrpos($width, "%");
				if ($pos > 0)
				{
					if (!is_numeric(substr($width, 0, $pos)))
						$error_string .= JText::_('INVALID_VALUE') . " width ($width) " . JText::_('DATA_NOT_NUMERIC');
				}
				else
					$error_string .= JText::_('INVALID_VALUE') . " width ($width) " . JText::_('DATA_NOT_NUMERIC');
			}

			$props['height'] = $height;
			if (!is_numeric($height))
			{
				$pos = strrpos($height, "%");
				if ($pos > 0)
				{
					if (!is_numeric(substr($height, 0, $pos)))
						$error_string .= JText::_('INVALID_VALUE') . " height ($height) " . JText::_('DATA_NOT_NUMERIC');
				}
				else
					$error_string .= JText::_('INVALID_VALUE') . " height ($height) " . JText::_('DATA_NOT_NUMERIC');
			}

			if ($bg_color != '')
			{
				if (ctype_xdigit($bg_color))
					$props["bg_color"] = $bg_color;
				else
					$error_string .= JText::_('INVALID_VALUE') . " bcolor ($bg_color) ";
			}

			if (($popup_window || $shadow_box || $modal_chart) && !is_numeric($width))
			{
				$error_string .= JText::_('INVALID_VALUE') . " width ($width) " . JText::_('DATA_NOT_NUMERIC');
			}

			if (($popup_window || $shadow_box || $modal_chart) && !is_numeric($height))
			{
				$error_string .= JText::_('INVALID_VALUE') . " height ($height) " . JText::_('DATA_NOT_NUMERIC');
			}

			if (($popup_window || $shadow_box) && $url == '')
				$error_string .= JText::_('NOPOPUP_URL_SPECIFIED');
			else
				$props['url'] = $url;

			if ($modal_title != '')
			{
				$props['modal_title'] = $modal_title;
			}

			if ($title != '')
			{
				if (strpos($title, "#varinfo#") !== false)
					$error_string = $this->replaceVar($row, $title);
				elseif (strpos($title, "#") !== false)
					$props["title"] = $this->replaceVar($row, $title);
				else
					$props["title"] = $title;

				if ($title_style != '')
					$props['title_style'] = $title_style;
			}

			if ($create_popup || $force_create_popup)
				$error_string .= "<br />" . JText::_('PARM_NOT_SUPPORTED');

			if ($flashchart_jquery != "")
				$props['flashchart_jquery'] = $flashchart_jquery;

			if ($props['gen_chart'])
			{
				if ($create_script != '')
					$props['create_script'] = $create_script;

				if ($hide_chart != "")
					$props['hide_chart'] = $hide_chart;

				if ($type != '')
					$props["type"] = $type;

				if ($modal_chart != "")
					$props["modal_chart"] = $modal_chart;

				if ($bcolor != '')
					$bg_color = $bcolor;

				if ($axis_legend_style != '')
				{
					$props['legend_style'] = $axis_legend_style;
				}

				if ($tooltip_style != '')
				{
					$props['tooltip_style'] = $tooltip_style;
				}

				if ($tag_style != '')
				{
					$props['tag_style'] = $tag_style;
				}

				if ($bg_image != '')
					$props["bg_image"] = $bg_image;

				if ($menu != '')
					$props["menu"] = $menu;

				if ($t_fontsize != '')
				{
					if (is_numeric($t_fontsize))
						$props["t_fontsize"] = $t_fontsize;
					else
						$error_string .= JText::_('INVALID_VALUE') . " t_fontsize ($t_fontsize) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($c_color != '')
					$chart_colors = $c_color;

				if ($chart_colors != '')
				{
					$rc = false;
					$color = explode(",", $chart_colors);
					for ($k = 0; $k < count($color); $k++)
					{
						if (ctype_xdigit($color[$k]) == false)
						{
							$rc = true;
							$k = count($color);
						}
					}
					if ($rc == true)
						$error_string .= JText::_('INVALID_VALUE') . " chart_colors ($chart_colors) ";
					else
						$props["c_color"] = $chart_colors;
				}

				if ($x_axis_color != '' && $type != "pie")
				{
					if (ctype_xdigit($x_axis_color))
						$props["x_axis_color"] = $x_axis_color;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_axis_color ($x_axis_color) ";
				}

				if ($y_axis_color != '' && $type != "pie")
				{
					if (ctype_xdigit($y_axis_color))
						$props["y_axis_color"] = $y_axis_color;
					else
						$error_string .= JText::_('INVALID_VALUE') . " y_axis_color ($y_axis_color) ";
				}

				if ($label_color != '' && $type != "pie")
				{
					if (ctype_xdigit($label_color))
						$props["label_color"] = $label_color;
					else
						$error_string .= JText::_('INVALID_VALUE') . " label_color ($label_color) ";
				}

				if ($y_label_color != '' && $type != "pie")
				{
					if (ctype_xdigit($y_label_color))
						$props["y_label_color"] = $y_label_color;
					else
						$error_string .= JText::_('INVALID_VALUE') . " y_label_color ($y_label_color) ";
				}

				if ($grid_color != '' && $type != "pie")
				{
					if (ctype_xdigit($grid_color))
						$props["grid_color"] = $grid_color;
					else
						$error_string .= JText::_('INVALID_VALUE') . " grid_color ($grid_color) ";
				}

				if ($target_linecolor != '' && $type != "pie")
				{
					if (ctype_xdigit($target_linecolor))
						$props["target_linecolor"] = $target_linecolor;
					else
						$error_string .= JText::_('INVALID_VALUE') . " target_linecolor ($target_linecolor) ";
				}

				if ($y_min != '' && $type != "pie")
				{
					if (is_numeric($y_min))
						$props["y_min"] = $y_min;
					else
						$error_string .= JText::_('INVALID_VALUE') . " y_min ($y_min) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($x_min != '' && $type != "pie")
				{
					if (is_numeric($x_min))
						$props["x_min"] = $x_min;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_min ($x_min) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($alpha != '')
				{
					if (is_numeric($alpha))
					{
						if ($alpha <= 1 && $alpha > 0)
							$props["alpha"] = $alpha;
						else
							$props["alpha"] = 0.8;
					}
					else
						$error_string .= JText::_('INVALID_VALUE') . " alpha ($alpha) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($x_max != '' && $type != "pie")
				{
					if (is_numeric($x_max))
						$props["x_max"] = $x_max;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_max ($x_max) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($y_max != '' && $type != "pie")
				{
					if (is_numeric($y_max))
						$props["y_max"] = $y_max;
					else
						$error_string .= JText::_('INVALID_VALUE') . " y_max ($y_max) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($x_step != '' && $type != "pie")
				{
					if (is_numeric($x_step))
						$props["x_step"] = $x_step;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_step ($x_step) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($x_axis_steps != '' && $type != "pie")
				{
					if (is_numeric($x_axis_steps))
						$props["x_axis_steps"] = $x_axis_steps;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_axis_steps ($x_axis_steps) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($y_step != '' && $type != "pie")
				{
					if (is_numeric($y_step))
						$props["y_step"] = $y_step;
					else
						$error_string .= JText::_('INVALID_VALUE') . " y_step ($y_step) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($target_line != '' && $type != "pie")
				{
					if (is_numeric($target_line))
						$props["target_line"] = $target_line;
					else
						$error_string .= JText::_('INVALID_VALUE') . " target_line ($target_line) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($tooltip != '')
					$props["tooltip"] = $this->correctLinebreak($tooltip);

				if ($radar_spoke_labels != '')
					$props["radar_spoke_labels"] = $this->correctLinebreak($radar_spoke_labels);

				if ($radar_axis_labels != '')
					$props["radar_axis_labels"] = $this->correctLinebreak($radar_axis_labels);

				if ($x_axis_labels != '')
					$props["x_axis_labels"] = $x_axis_labels;

				if ($x_label_rotate != '' && $type != "pie")
				{
					if (is_numeric($x_label_rotate))
						$props["x_label_rotate"] = $x_label_rotate;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_label_rotate ($x_label_rotate) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($x_label_truncate != '')
				{
					if (is_numeric($x_label_truncate))
						$props["x_label_truncate"] = $x_label_truncate;
					else
						$error_string .= JText::_('INVALID_VALUE') . " x_label_truncate ($x_label_truncate) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($alert_value != '')
				{
					$nok = false;
					$tmpvals = explode("|", $alert_value);
					$props['alert_value'] = "";
					for ($k = 0; $k < count($tmpvals); $k++)
					{
						$tmp = explode(",", $tmpvals[$k]);
						if (count($tmp) == 2)
						{
							$value = $tmp[0];
							$op = $tmp[1];
						}
						elseif (count($tmp) == 1)
						{
							$value = $tmp[0];
							$op = "gt";
						}
						else
							$error_string .= JText::_('INVALID_VALUE') . " alert_value=&quot;$alert_value&quot;";

						if (is_numeric($value))
						{
							if ($op == "lt" || $op == "gt" || $op == "eq")
								$props['alert_value'] .= $value . "," . $op . "|";
							else
								$props['alert_value'] .= $value . ",gt|";

							if ($alert_tooltip != '')
							{
								$props["alert_tooltip"] = $this->correctLinebreak($alert_tooltip);
							}
							if ($alert_onclick != '')
							{
								$props["alert_onclick"] = $alert_onclick;
							}
							if ($alert_color != '')
							{
								$nok = false;
								$props['alert_color'] = $alert_color;
								$tmp = explode(",", $alert_color);
								for ($j = 0; $j < count($tmp); $j++)
								{
									if (!ctype_xdigit($tmp[$j]))
										$nok = true;
								}
							}
						}
						else
							$error_string .= JText::_('INVALID_VALUE') . " alert_value=&quot;$alert_value&quot; " . JText::_('DATA_NOT_NUMERIC');
					}
					if ($nok == true)
						$error_string .= JText::_('INVALID_VALUE') . " alert_color=&quot;$alert_color&quot; ";
					$nok = false;
					$props['alert_value'] = trim($props['alert_value'], "|");
				}

				if ($x_legend != '' && $type != "pie")
					$props["x_legend"] = $x_legend;

				if ($y_legend != '' && $type != "pie")
					$props["y_legend"] = $y_legend;

				if ($legend_fontsize != '')
				{
					if (is_numeric($legend_fontsize))
						$props["legend_fontsize"] = $legend_fontsize;
					else
						$error_string .= JText::_('INVALID_VALUE') . " legend_fontsize ($legend_fontsize) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($label_fontsize != '')
				{
					if (is_numeric($label_fontsize))
						$props["label_fontsize"] = $label_fontsize;
					else
						$error_string .= JText::_('INVALID_VALUE') . " label_fontsize ($label_fontsize) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($legend != '')
					$props["key"] = $legend;

				if ($key_onclick != '')
				{
					$props["key_onclick"] = $key_onclick;
					if (strpos($key_onclick, "show_shadowbox") !== false)
						$shadow_box = "1";
				}

				if ($onclick != '')
				{
					$props["onclick"] = $onclick;
					if (strpos($onclick, "show_shadowbox") !== false)
						$shadow_box = "1";
				}

				if ($display_as_image != '' && $create_image == "1")
				{
					if (is_numeric($display_as_image))
					{
						$bar_animation = '0';
						$line_animation = '0';
						$pie_animation = '0';
						$props['display_as_image'] = $display_as_image * 1000;
					}
					else
						$error_string .= JText::_('INVALID_VALUE') . " display_as_image ($display_as_image) " . JText::_('DATA_NOT_NUMERIC');
				}
				else
					$props['display_as_image'] = null;

				if (strpos($type, "bar") !== false)
				{
					if ($bar_animation != '' && $bar_animation != "null")
						$props["bar_animation"] = $bar_animation;
					if ($multibar_color != '')
						$props['multibar_color'] = $multibar_color;
				}

				if (strpos($type, "line") !== false)
				{
					if ($line_animation != '' && $line_animation != "null")
						$props["line_animation"] = $line_animation;
				}

				if (strpos($type, "scatter") !== false)
				{
					if ($scatter_animation != '' && $scatter_animation != "0")
						$props["scatter_animation"] = "1";
				}

				if ($type == "pie")
				{
					if ($pie_animation != '' && $pie_animation != "0")
						$props["pie_animation"] = $pie_animation;

					if ($hide_pie_labels != '' && $hide_pie_labels != '0')
						$props["hide_pie_labels"] = "1";

					if ($pie_label_values != '' && $pie_label_values != '0')
						$props["pie_label_values"] = "1";

					if ($pie_legend != '' && $pie_legend != '0')
						$props["pie_legend"] = "1";

					if ($pie_radius != '')
					{
						if (is_numeric($pie_radius))
							$props["pie_radius"] = $pie_radius;
						else
							$error_string .= JText::_('INVALID_VALUE') . " pie_radius ($pie_radius) " . JText::_('DATA_NOT_NUMERIC');
					}
				}

				if ($show_barvalues != '' && $show_barvalues != '0')
					$props["show_barvalues"] = $show_barvalues;

				if ($show_linevalues != '' && $show_linevalues != '0')
					$props["show_linevalues"] = "1";

				if ($show_regression_line != '' && $show_regression_line != '0' && $data != "formula")
				{
					$props["show_regression_line"] = "1";
					if ($show_regression_formula != '' && $show_regression_formula != '0')
						$props["show_regression_formula"] = "1";
				}

				if ($show_trend != '' && $show_trend != '0')
				{
					$props["show_trend"] = "1";
					if ($show_regression_formula != '' && $show_regression_formula != '0')
						$props["show_regression_formula"] = "1";
				}

				if ($formula != '')
				{
					if ($allow_formula == "1")
						$props["formula"] = $formula;
					else
						$error_string .= JText::_('FORMULA_NOT_ALLOWED');
				}

				if ($right_legend != '' && $right_legend != '0')
					$props["right_legend"] = "1";

				if ($dimension != '')
					$props["dimension"] = $dimension;

				if ($number_format != '')
					$props["number_format"] = $number_format;

				if ($precision != '')
				{
					if (is_numeric($precision))
					{
						if ($precision >= 0 && $precision < 7)
							$props["precision"] = $precision;
						else
							$props["precision"] = "2";
					}
					else
						$error_string .= JText::_('INVALID_VALUE') . " precision ($precision) " . JText::_('DATA_NOT_NUMERIC');
				}

				if ($axis_3d != '' && $axis_3d != "0" && $type != "pie")
					$props["axis_3d"] = $axis_3d;

				if ($revert_x_range == "1")
					$props['revert_x_range'] = "1";

				if ($revert_y_range == "1")
					$props['revert_y_range'] = "1";

				if ($debug == '1')
				{
					$props["debug"] = 1;
					$debug_file = dirname(__FILE__) . "/flashchart.debug";
					$debug_fp = fopen($debug_file, "a+");
					if ($debug_fp)
						fputs($debug_fp, "\n*** debug - helper.php: \n");
					else
					{
						$error_string .= "<br /><b>Could not open/create debug file &quot;$debug_file&quot;</b>";
						$debug = null;
					}
				}

				if ($file != '' && $data == "file")
				{
					$corr_file = trim($file, "/");
					if (file_exists($corr_file))
						$props["file"] = $corr_file;
					else
						$error_string .= "<br />&nbsp; - &quot;$file&quot; - " . JText::_('FILE_NOT_FOUND');
				}

				if ($url != '' && $data == "url")
				{
					$rc = $this->checkURL($url);
					if ($rc['success'] == true)
						$props["url"] = $url;
					else
						$error_string .= "<br />&nbsp; - &quot;$url&quot; - " . JText::_('URL_NOT_FOUND') . "  " . $rc['error'];
				}

				if ($data == "formula" && $error_string == "")
				{
					if ($formula === null && $allow_formula == "1")
						$error_string .= JText::_('NOFORMULA_SPECIFIED');

					if ($allow_formula == "0")
						$error_string .= JText::_('FORMULA_NOT_ALLOWED');

					if ($x_interval != '')
					{
						if (!is_numeric($x_interval))
							$error_string .= JText::_('INVALID_VALUE') . " x_interval ($x_interval) " . JText::_('DATA_NOT_NUMERIC');
					}
					if ($error_string == "")
					{
						require_once dirname(__FILE__) . '/lib/evalMath.php';

						$result = $this->calc_Data($formula, $x_interval, $x_min, $x_max);
						if ($result['error'])
							$error_string .= JText::_('NOFORMULA_DATA') . $result['error'];
						else
							$data = $result['data'];
					}
				}

				if ($data == "file" && $file == '')
					$error_string .= JText::_('NOFILE_SPECIFIED');

				if ($data == "url" && $url == '')
					$error_string .= JText::_('NOURL_SPECIFIED');

				if ($data == "json" && $url == '')
					$error_string .= JText::_('NOURL_SPECIFIED');

				if ($data == "database" && $sql == '')
					$error_string .= JText::_('NOSQL_SPECIFIED');

				if ($rearrange_data != '' && $data != "database")
					if ($rearrange_data != '0')
						$props["rearrange_data"] = "1";

				$sql_processed = "No";
				if ($data == "database" && $error_string == '')
				{
					if ($dbhost != '' && $dbname != '' && $dbuser != '')
					{
						$db_props['host'] = $dbhost;
						$db_props['dbname'] = $dbname;
						$db_props['dbuser'] = $dbuser;
						$db_props['password'] = $dbpassword;
					}
					else
						$db_props = null;

					$result = $this->getDatafromDB($sql, $sql_labels, $type, $db_props, $debug);
					$props['data'] = $result['data'];
					$error_string .= $result['error'];

				}
				else
				{
					$data = str_replace("&nbsp;", " ", $data);
					$data = str_replace("&lt;", "<", $data);
					$data = str_replace("&gt;", ">", $data);
					$props['data'] = $data;
				}
			}
			else
			{
				$props['shadow_box'] = $shadow_box;
				$props['popup_window'] = $popup_window;
				$props['gen_chart'] = false;
			}
			if ($debug == "1")
			{
				fputs($debug_fp, $result['debug']);
				foreach ($props as $key => $value)
					fputs($debug_fp, "\nPassed Parameter: $key = $value");
				fclose($debug_fp);
			}

		}
		else
		{
			$error_string = "<br />" . JText::_('NO_DATA_TAG_FOUND');
			$props['gen_chart'] = false;
		}

		$result['error'] = $error_string;
		$result['props'] = $props;
		return $result;
	}

	function getDatafromDB($sql, $sql_labels, $type, $db_props, $debug)
	{
		JLoader::import('joomla.version');
		$version = new JVersion();
		/** ----------------------------------------------------------------------------------------- */
		/** process data from database                                                                */
		/** ----------------------------------------------------------------------------------------- */
		/** ----------------------------------------------------------------------------------------- */
		/** for security reasons reject requests other than "select" and on table #_users             */
		/** ----------------------------------------------------------------------------------------- */
		$data = "";
		$error_string = "";
		if ($sql_labels === null)
			$sql_labels = "1";

		// Remove html-code to cleanup user input
		$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
'@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
'@<![\s\S]*?--[ \t\n\r]*>@'); // Strip multi-line comments including CDATA
		$sql = preg_replace($search, '', $sql);
		$sql = str_replace("&nbsp;", "", $sql);
		$sql = str_replace("&lt;", "<", $sql);
		$sql = str_replace("&gt;", ">", $sql);
		$sql = str_replace("\n", "", $sql);

		$check = trim($sql, " ");

		if (stripos($check, "select") != 0)
			$error_string .= JText::_('SQL_NOTALLOWED') . $check;

		if (stripos($check, "_users") != false)
			$error_string .= JText::_('SQL_NOTALLOWED') . $check;

		// Replace variables in sql-statement
		if ($error_string == '')
			if (stripos($sql, "#") !== false)
				$sql = $this->replaceVar($row, $sql);

		$db_error = false;
		if ($db_props)
		{
			$option = array();
			$option['driver'] = 'mysqli';
			$option['host'] = $db_props['host'];
			$option['user'] = $db_props['dbuser'];
			$option['password'] = $db_props['password'];
			$option['database'] = $db_props['dbname'];
			$option['select'] = true;

			try
			{
				if (version_compare($version->RELEASE, '2.5', '<='))
					$db = JDatabase::getInstance($option);
				else
					$db = JDatabaseDriver::getInstance($option);
				$db->connect();
			}
			catch (Exception $e)
			{
				$db_error = true;
				$error_string .= "<br />" . $e->getMessage();
				$error_string .= "<br />Check DB Options: dbhost=" . $db_props['host'] . ", dbname=" . $db_props['dbname'] . ", dbuser=" . $db_props['dbuser'] . ", dbpassword";
			}
		}
		elseif ($error_string == '')
			$db = JFactory::getDbo();

		if ($error_string == '')
		{
			// JError::setErrorHandling(E_ALL, 'message');
			$db->setQuery("SET NAMES 'utf8'");
			try
			{
				$db->setQuery($sql);
				$sql_processed = "yes";
			}
			catch (RuntimeException $e)
			{
				$error_string .= "<br />Error: " . $e->getMessage() . "<br />SQL=" . $sql;
			}

			/** ----------------------------------------------------------------------------------- */
			/** get the data from DB - store if we have labels (or data) from column 0              */
			/** remove reserved chars ("," and "/")  from labels                                    */
			/** ----------------------------------------------------------------------------------- */
			$debug_result = "";
			try
			{
				$data_cols = count($db->loadRow());

				if ($debug == "1" && $error_string == '' && $data_cols)
				{
					$debug_result = implode(", ", $db->loadColumn(0));
				}
			}
			catch (RuntimeException $e)
			{
				$error_string .= "<br /><b>SQL Error: </b>";
			}

			$label = null;
			if ($data_cols)
			{
				$tmp = implode("%%", $db->loadColumn(0));
				$tmp = str_replace(",", ".", $tmp);
				$tmp = str_replace("/", " ", $tmp);
				$label = str_replace("%%", ",", $tmp);
			}
			else
			{
				$error_string .= JText::_('SQL_NO_DATA') . $sql . "&quot;";
				if (version_compare($version->RELEASE, '2.5', '<='))
					$error_string .= "<br /><b>(Error Message: </b>&quot;" . $db->getErrorMsg() . "&quot;)";
			}

			if ($sql_labels == "0")
				$data = $label . "|";
			else
				$data = null;

			for ($k = 1; $k < $data_cols; $k++)
			{
				$col = $db->loadColumn($k);
				$data .= implode(",", $col) . "|";
			}

			if ($data != null)
			{
				/** ------------------------------------------------------------------------------- */
				/** we have the data - reformat it for flashchart processing                        */
				/** ------------------------------------------------------------------------------- */
				$data = trim($data, "|");

				if ($type == "bar_stack" && $data_cols > 0)
				{
					/** --------------------------------------------------------------------------- */
					/** if we have more than one datagroup and want a stacked bar chart, we must    */
					/** rearrange datagroups                                                        */
					/** --------------------------------------------------------------------------- */
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
						$data = trim($data, ",");
						$data .= "|";
					}

					$data = trim($data, "|");
				}

				if ($sql_labels == "1")
					$data = $label . "/" . $data;
			}
			/** ------------------------------------------------------------------------------- */
			/** we have only data from column 0 - so we do not have labels                      */
			/** ------------------------------------------------------------------------------- */
			else
				$data = $label;

		}
		/** ---------------------------------------------------- */
		/** end of database processing                           */
		/** ---------------------------------------------------- */

		if ($debug == "1")
		{
			if ($sql)
			{
				$debug_info = "\nPassed SQL statement: $sql";
				$debug_info .= "\nSQL Statement processed: $sql_processed";
				$debug_info .= "\nData from columns 0: $debug_result ";
				$result['debug'] = $debug_info;
			}
		}

		$result['error'] = $error_string;
		$result['data'] = $data;
		return $result;
	}

	function convert2Array($param_line)
	{
		preg_match_all('/(\w+)(\s*=\s*\".*?\")/s', $param_line, $matches);
		$count = count($matches[1]);
		$parms = array();

		for ($i = 0; $i < $count; $i++)
		{
			$value = ltrim($matches[2][$i], " \n\r\t=");
			$value = trim($value, '"');
			$parm = $matches[1][$i];
			$parms[$parm] = $value;
		}
		return $parms;
	}

	function getParam($parms, $type, $attribute = null)
	{
		if (array_key_exists($type, $parms))
			return $parms[$type];
		else
			return $attribute;
	}

	protected function replaceVar(&$row, $string)
	{
		if (strpos($string, "#varinfo#") !== false)
		{
			$string = "<br /><b>" . JText::_('VAR_INFO') . "</b>";
			$vars = JRequest::get();
			foreach ($vars as $key => $value)
				$string .= "<br > $key" . " = " . "$value";

			return $string;
		}

		$regex = "%#\\s*(.*?)(.*?)#%s";
		preg_match_all($regex, $string, $matches);
		for ($i = 0; $i < count($matches[1]); $i++)
		{
			switch ($matches[2][$i])
			{
				case "date":
					$lang = & JFactory::getLanguage();
					$date = new JDate('now');
					if ($lang->getTag() == "de-DE")
						$string = str_replace("#date#", $date->format("l, d.m.Y"), $string);
					else
						$string = str_replace("#date#", $date->format("l, Y-m-d"), $string);
					break;

				case "datesql":
					$date = new JDate('now');
					$string = str_replace("#datesql#", $date->format("Y-m-d"), $string);
					break;

				case "time":
					$time_zone = JFactory::getApplication()->getCfg('offset');
					$date = new DateTime('now', new DateTimeZone($time_zone));
					$string = str_replace("#time#", $date->format("H:i"), $string);
					break;

				case "articleid":
					$string = str_replace("#articleid#", @$row->id, $string);
					break;

				case "articlename":
					$string = str_replace("#articlename#", @$row->alias, $string);
					break;

				case "catid":
					$string = str_replace("#catid#", @$row->catid, $string);
					break;

				case "username":
					$user = & JFactory::getUser();
					if ($user->get('id') != 0)
						$string = str_replace("#username#", $user->get('name'), $string);
					else
						$string = str_replace("#username#", JText::_('GUEST_USER'), $string);
					break;

				case "userid":
					$user = & JFactory::getUser();
					$string = str_replace("#userid#", $user->get('id'), $string);
					break;

				default:
					$var = JRequest::getVar($matches[2][$i]);
					if ($var)
						$string = str_replace("#" . $matches[2][$i] . "#", $var, $string);
			}
		}

		return $string;
	}

	function correctLinebreak($string)
	{
		$string = str_replace("<br />", "<br>", $string);
		$string = str_replace("&lt;br&gt;", "<br>", $string);

		return $string;
	}

	function checkUrl($url)
	{
		$rc['success'] = false;

		$hdrs = @get_headers($url);
		if (is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false)
			$rc['success'] = true;
		elseif (preg_match('/^HTTP\/.*\s+(300|301|302|303|307|308)/', $hdrs[0]))
			$rc['success'] = true;
		else
			$rc['error'] = $hdrs[0];

		return $rc;
	}

	function createModal($id, $url, $width, $height, $link, $title)
	{
		$box_width = $width + 60;
		$box_height = $height + 60;
		$height += 30;
		$width += 30;
		if ($height > 600)
			$top = " top: 33%; ";
		else
			$top = "";
		if ($width > 800)
			$left = " left: 33%; ";
		else
			$left = "";
		$html = "\n<a data-toggle='modal' href='#modal_" . $id . "'>" . $link . "</a>";
		$html .= "\n<div id='modal_" . $id . "' class='modal hide fade' style='display: none;" . $top . $left . "height:" . $box_height . "px; width:" . $box_width . "px; background-color: #FFFFFF;'>";
		$html .= "\n<div class='modal-header'>" . $title . "<div class='close' data-dismiss='modal' title='" . JText::_('CLOSE') . "'>&nbsp;</div>\n</div>";
		$html .= "\n<div class='modal-body'>";
		$html .= "\n<iframe src='" . $url . "' width='" . $width . "px' height='" . $height . "px' frameborder='0' allowtransparency='true' scrolling='no'></iframe>";
		$html .= "\n</div>\n</div>";

		return $html;
	}

	function addBootrapResources($base, $version, $document)
	{
		if (version_compare($version->RELEASE, '2.5', '<='))
		{
			if (JFactory::getApplication()->get('jquery') !== true)
			{
				$document->addScript($base . '/js/jquery.min.js');
				JFactory::getApplication()->set('jquery', true);
			}
			$document->addScript($base . '/js/bootstrap.min.js');
		}
		else
		{
			JHtml::_('jquery.framework');
			$document->addCustomTag("<script src='" . $base . "/js/bootstrap.min.js' type='text/javascript'></script>");
		}
		$document->addStyleSheet($base . '/css/bootstrap-modal.css');
		$this->bootstrap_resources = true;
	}

	function addShadowboxScript($flashchart_shadowbox, $document, $language)
	{
		$script = "                        Shadowbox.loadSkin('nova', '" . JURI::base() . "plugins/content/flashchart/shadowbox/js/skin');
    	                Shadowbox.loadLanguage('" . $language . "',  '" . JURI::base() . "plugins/content/flashchart/shadowbox/js/lang');
    	                Shadowbox.loadPlayer(['img','iframe','html'], '" . JURI::base() . "plugins/content/flashchart/shadowbox/js/player');
    	                jQuery.noConflict();
    	                jQuery(document).ready(function(){
		                window.onload = Shadowbox.init;
	                    });";

		$document->addScriptDeclaration($script);
		if ($flashchart_shadowbox == "1")
		{
			$document->addScript(JURI::base() . 'plugins/content/flashchart/shadowbox/js/shadowbox-jquery.js');
			$document->addScript(JURI::base() . 'plugins/content/flashchart/shadowbox/js/shadowbox.js');
		}
	}

	function getJsonFromUrl($url)
	{
		$result = "-1";

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

		return $content;
	}

	function createChart($url_base, $props, $data)
	{
		/** ------------------------------------------------------------ */
		/** embed swfobject tag for flash and add function to load chart */
		/** ------------------------------------------------------------ */
		$qq = '"';
		$div_set = false;
		if (is_numeric($props['height']))
		{
			$html = "\n<div style='min-height:" . $props['height'] . "px; background-color: #" . $props['bg_color'] . ";'>";
			$div_set = true;
		}
		$html .= "\n<script type='text/javascript'>";
		$html .= "\n  // <![CDATA[";
		$html .= "\n  swfobject.embedSWF('$url_base/open-flash-chart.swf', '" . $props['chart_id'] . "', '" . $props['width'] . "', '" . $props['height'] . "', '9.0.0',  'expressInstall.swf',";
		$html .= "\n  { 'get-data':'get_data_" . $props['chart_id'] . "', 'id':'" . $props['chart_id'] . "', 'loading':'" . JText::_('LOADING_DATA') . "'}, {'wmode':'opaque'} );";
		$html .= "\n  function get_data_" . $props['chart_id'] . "() { return JSON.stringify(data_" . $props['chart_id'] . "); }";
		$html .= "\n  var data_" . $props['chart_id'] . " = $data;";
		$html .= "\n  if (playerVersion.major == 0) document.write($qq<div style='color: red; padding:20px;'><img src='$url_base/images/notice-note.png' align='middle' alt='notice' hspace='5' /><b>Chartid &quot;";
		$html .= $props['chart_id'] . "&quot; - " . JText::_('NO_PLUGIN_FOUND') . "</b></div><p>&nbsp;</p>$qq); ";
		$html .= "\n  // ]]>";
		$html .= "\n</script>";
		$html .= "\n<div id='" . $props['chart_id'] . "'></div>";
		if ($div_set)
			$html .= "\n</div>";

		if ($props['display_as_image'] != null)
		{
			$qq = '"';
			$html .= "\n<script type='text/javascript'>";
			$html .= "\n 	setTimeout($qq" . "save_image('" . $props['chart_id'] . "')" . $qq . "," . $props['display_as_image'] . ") ";
			$html .= "\n</script>";
		}

		return $html;

	}

	function createModalChart($url_base, $props, $data)
	{
		if (!is_numeric($props['height']))
			$props['height'] = 250;
		if (!is_numeric($props['width']))
			$props['width'] = 550;

		if ($props['modal_title'] == "")
			$props['modal_title'] = "&nbsp;";

		$html = "";
		$qq = '"';
		$box_height = $props['height'] + 25 . "px";
		$box_width = $props['width'] + 25 . "px";
		$background = "background-color: #" . $props['bg_color'] . ";";
		if ($props['hide_chart'] != "1")
			$html = "\n<a data-toggle='modal' href='#modal_" . $props['chart_id'] . "'>" . $props['modal_chart'] . "</a>";
		$html .= "\n<div id='modal_" . $props['chart_id'] . "' class='modal hide fade' style='display: none; height:" . $box_height . "; width:" . $box_width . "; " . $background . "'>";
		$html .= "\n <div class='modal-header'>" . $props['modal_title'];
		$html .= "\n  <div class='close' data-dismiss='modal' title='" . JText::_('CLOSE') . "'>&nbsp;</div>";
		$html .= "\n </div>";
		$html .= "\n <div class='modal-body'>";
		$html .= "\n<script type='text/javascript'>";
		$html .= "\n  // <![CDATA[";
		$html .= "\n  swfobject.embedSWF('$url_base/open-flash-chart.swf', '" . $props['chart_id'] . "', '" . $props['width'] . "', '" . $props['height'] . "', '9.0.0',  'expressInstall.swf',";
		$html .= "\n  { 'get-data':'get_data_" . $props['chart_id'] . "', 'id':'" . $props['chart_id'] . "', 'loading':'" . JText::_('LOADING_DATA') . "'}, {'wmode':'opaque'} );";
		$html .= "\n  function get_data_" . $props['chart_id'] . "() { return JSON.stringify(data_" . $props['chart_id'] . "); }";
		$html .= "\n  var data_" . $props['chart_id'] . " = $data;";
		$html .= "\n  if (playerVersion.major == 0) document.write($qq<div style='color: red; padding:20px;'><img src='$url_base/images/notice-note.png' align='middle' alt='notice' hspace='5' /><b>Chartid &quot;";
		$html .= $props['chart_id'] . "&quot; - " . JText::_('NO_PLUGIN_FOUND') . "</b></div><p>&nbsp;</p>$qq); ";
		$html .= "\n  // ]]>";
		$html .= "\n</script>";
		$html .= "\n<div id='" . $props['chart_id'] . "'></div>";
		$html .= "\n</div>\n</div>";

		return $html;
	}

	function initializeProps()
	{
		$props = array();
		$props['title'] = null;
		$props['modal_title'] = null;
		$props['modal_chart'] = null;
		$props['shadow_box'] = null;
		$props['popup_window'] = null;
		$props['hide_chart'] = null;
		$props['create_script'] = null;

		return $props;
	}

	function calc_Data($formula, $x_interval = 1, $x_min = 0, $x_max = 5, $max_iterations = 500)
	{
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
