/**
 * Core Design Mini Polls Forms plugin for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla
 * @subpackage	Content
 * @category   	Plugin
 * @version		1.0.0
 * @copyright	Copyright (C) 2007 - 2011 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 * 
 * This file is part of Great Joomla! extension.   
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (typeof(jQuery) === 'function') {
	
	(function($) {
		
		$.fn.cdminipolls = function(options) {

			// set defaults
			var fncname = 'cdminipolls',
			defaults = {
				isAuthorized : false,
				language : {
					
				}
			},
			$this = null,
			opts = $.extend(defaults, options);
			opts.language = $.extend(opts.language, $.fn.cdminipolls.language);
			
			return this.each(function() {
				$this = $(this);
				managePoll('create');
			});
			
			/**
			 * Progressbar
			 * 
			 * @param	string	method
			 * @return	boolean
			 */
			function managePoll(method) {
				if (typeof method === 'undefined') {
					method = 'create';
				}
				
				var disable_poll = false;
				
				if ($this.data('total') * 1 !== 0) {
					if ($this.data('multiple_voting') * 1 === 0) {
						if (cookie(fncname + '_voted_for_' + $this.data('identifier'))) {
							disable_poll = cookie(fncname + '_voted_for_' + $this.data('identifier'));
							if (disable_poll === 'true') {
								disable_poll = true;
							} else {
								disable_poll = false;
							}
						}
					}
				}
				
				$('.' + fncname + '_progressbar', $this).each(function() {
					var element = $(this),
					count = element.data('votes') * 1;
					
					var progressbar = element.progressbar();
					progressbar.progressbar('option', 'value', count / ($this.data('total') * 1) * 100);
					
					if (disable_poll === false) {
						progressbar.progressbar('widget')
							.unbind('mouseenter mouseleave')
							.hover(
								function(e) {
									
									$('.' + fncname + '_votebutton_layer', $(this)).stop(true, true).fadeIn();
									$('.' + fncname + '_votebutton_container', $(this)).position({
										of : $(this),
										my : 'center center',
										at : 'center center'
									}).hover(
										function() { $(this).addClass('ui-state-hover'); },
										function() { $(this).removeClass('ui-state-hover'); }
									);
								},
								function() {
									$('.' + fncname + '_votebutton_layer', $(this)).stop(true, true).fadeOut();
								}
						);
						
						$('.' + fncname + '_votebutton_container', progressbar.progressbar('widget'))
						.unbind('click')
						.click(function(e) {
							e.preventDefault();
							
							var element = $(this), 
							form = $('form', element);
							
							$.ajax({
								type: 'POST',
								data: form.serialize(),
								cache: false,
								async: true,
								beforeSend: function() {
									element.closest('.' + fncname + '_progressbar').progressbar('disable')
									.trigger('mouseleave')
									.unbind('mouseenter mouseleave');
								},
								success: function(response) {
									
									element.closest('.' + fncname + '_progressbar').progressbar('enable').
									bind('mouseener mouseleave');
									
									response = parseResponse(response);
									
									// error
									if (response.status === 'error') {
										alert(response.content);
										return false;
									}
									
									// success
									if (response.status === 'success') {
										// increase progressbar value
										var actual_progressbar = form.closest('.' + fncname + '_progressbar');
										actual_progressbar.progressbar('option', 'value', (actual_progressbar.progressbar('option', 'value') * 1) + 1);
										
										actual_progressbar.data('votes', actual_progressbar.data('votes') * 1 + 1);
										
										$this.data('total', $this.data('total') * 1 + 1);
										
										// no multiple voting
										if ($this.data('multiple_voting') * 1 === 0) {
											cookie(fncname + '_voted_for_' + $this.data('identifier'), true);
										}
										
										// update statistics panel
										$('.' + fncname + '_statistics', $this).replaceWith(response.content);
										
										managePoll('update');
										
										return true;
									}
									
									return false;
								}
							});
						});
						
					}
					
					switch(method) {
						case 'create':
							
							progressbar.progressbar('widget').prepend($('<div />', {
								'class' : fncname + '_votecount',
								text : count
							}));
							
							break;
							
						case 'update':
							
							if (disable_poll) {
								progressbar.progressbar('widget')
								.trigger('mouseleave')
								.unbind('mouseenter mouseleave');
								
								$('.' + fncname + '_votebutton_container', progressbar.progressbar('widget')).unbind('click');
							}
							
							$('.' + fncname + '_votecount', progressbar.progressbar('widget'))
								.empty()
								.text(count);
							break;
					}
				});
				
				return true;
			};
			
			/**
			 * Cookie manipulation
			 * Based on jQuery Cookie plugin:
			 * https://github.com/carhartl/jquery-cookie
			 * 
			 * @param	string	key
			 * @param	string	value
			 * @param	object	options
			 * @return	string
			 */
			function cookie(key, value, options) {
				// key and at least value given, set cookie...
			    if (arguments.length > 1 && String(value) !== "[object Object]") {
			        options = jQuery.extend({}, options);

			        if (value === null || value === undefined) {
			            options.expires = -1;
			        }

			        if (typeof options.expires === 'number') {
			            var days = options.expires, t = options.expires = new Date();
			            t.setDate(t.getDate() + days);
			        }
			        
			        value = String(value);
			        
			        return (document.cookie = [
			            encodeURIComponent(key), '=',
			            options.raw ? value : encodeURIComponent(value),
			            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
			            options.path ? '; path=' + options.path : '',
			            options.domain ? '; domain=' + options.domain : '',
			            options.secure ? '; secure' : ''
			        ].join(''));
			    }

			    // key and possibly options given, get cookie...
			    options = value || {};
			    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
			    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
			}
			
			/**
			 * Parse response
			 * 
			 * @param	string
			 * @return 	object
			 */
			function parseResponse(response) {
				var
				status = $.trim($('status', response).text() || 'error'),
				content = $.trim($('content', response).text()),
				response_object = {
						status : status,
						content : content
				};
				return response_object;
			};
			
		};
		
	})(jQuery);
}