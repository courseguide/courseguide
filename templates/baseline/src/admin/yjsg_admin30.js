/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla.com. All Rights Reserved.            ||
|| # Authors - Dragan Todorovic and Constantin Boiangiu                 ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
function scroll_toolbar() {
    var dscroll = $(document).getScroll();
    if (dscroll.y > 70) {
        $$('#repsonsebox').addClass('moving');
    } else {
        $$('#repsonsebox').removeClass('moving');
    }
}
window.addEvent('scroll', function () {
    scroll_toolbar();
});
//YJSGtime
var YJTime = {
    start: function () {
        YJTime.startTime = new Date().getTime();
        // checks if browser has ajax feature otherwise default to standard Joomla actions
        var ajaxSupport = Browser.Features.xhr;
        var cookie_name = $('jform_template').get('value') + $('YJSG_template_id').get('value');
        Cookie.write(cookie_name, $('style-form').get('action'), {
            duration: 2
        });
        Joomla.submitbutton = function (task) {
            if (task == "style.apply") {
                //stop do ajax if ajax support
                if (!ajaxSupport) {
                    Joomla.submitform(task, document.getElementById("style-form"));
                }
            } else if (task == "style.save" || task == "style.cancel" || task == "style.save2copy") {
                Joomla.submitform(task, document.getElementById("style-form"));
            }
        }
        var toolbar = $$('#toolbar');
        var makeCacheBtn = new Element('div', {
            'id': 'toolbar-cache',
            'class': 'btn-group',
            html: '<button href="javascript:;" class="btn btn-small btn-warning"><i class="icon-refresh icon-white"></i> Clear cache & Save</button>'
        });
        var makeResetBtn = new Element('div', {
            'id': 'toolbar-reset',
            'class': 'btn-group',
            html: '<button href="javascript:;" class="btn btn-small btn-info"><i class="icon-refresh icon-white"></i>Reset to Default</button>'
        });
		
        var makeYjsgBtn = new Element('div', {
            'id': 'toolbar-cache',
            'class': 'btn-group',
            html: '<a  href="http://www.yjsimplegrid.com/documentation" target="_blank" class="btn btn-small btn-info"><i class="icon-support icon-white"></i> YJSG Docs</a>'
        });
        var repsonsebox = new Element('div', {
            'id': 'repsonsebox'
        });
        var progress_bar = new Element('div', {
            'id': 'pbar',
            styles: {
                width: '100%'
            }
        });
		makeYjsgBtn.inject(toolbar[0], 'top');
        makeCacheBtn.inject(toolbar[0], 'top');
        makeResetBtn.inject(toolbar[0], 'top');
        progress_bar.inject($('toolbar'), 'top');
        repsonsebox.inject(document.body, 'top');
        var saveBtn = $('toolbar-save').getElement('button');
        var applyBtn = $('toolbar-apply').getElement('button');
        var cacheBtn = $('toolbar-cache').getElement('button');
        var resetBtn = $('toolbar-reset').getElement('button');
		
		
		
		var FindAssigements = $$('#menu-assignment input:checked').get('value');
		if(FindAssigements.length > 0){
			$('jform_params_yjsg_assigements').set('value',FindAssigements);
		}else{
			$('jform_params_yjsg_assigements').set('value','');
		}
		$$(".menu-link input").addEvent('click', function (el) {
			var NewAssigements = $$('#menu-assignment input:checked').get('value');
			if(NewAssigements.length > 0){
				$('jform_params_yjsg_assigements').set('value',NewAssigements);
			}else{
				$('jform_params_yjsg_assigements').set('value','');
			}
		});	
		
        // check if compiler is on if no disable cacheBtn
        var compiler_on = $('jform_params_less_compiler_on');
        var component_disabled = $$('#jform_params_component_switch');
        var component_disabled_on = $$('#jform_params_component_switch').get('value');
        var compiler_is_on = $$('#jform_params_less_compiler_on input:checked').get('value');
        if (compiler_is_on == 0 || !ajaxSupport) {
            cacheBtn.addClass('hidecache');
            YJTime.status_text_check();
        } else {
            cacheBtn.removeClass('hidecache');
            YJTime.status_text_check();
        }
        compiler_on.addEvent('click', function (el) {
            YJTime.status_text_check();
            var compiler_is_on = $$('#jform_params_less_compiler_on input:checked').get('value');
            if (compiler_is_on == 0) {
                cacheBtn.addClass('hidecache');
            } else {
                cacheBtn.removeClass('hidecache');
            }
        });
        component_disabled.addEvent('click', function (el) {
            YJTime.status_text_check();
        });
        saveBtn.addEvent('click', function (event) {
            new Event(event).preventDefault();
            repsonsebox.setStyle('display', 'none');
            Joomla.submitform();
            YJTime.setTimer();
        });
        if (ajaxSupport) {
            cacheBtn.addEvent('click', function (event) {
                new Event(event).preventDefault();
                var set_task = 'clearCache';
                YJTime.adminUpdate(set_task);
                YJTime.setTimer();
            });
            applyBtn.addEvent('click', function (event) {
                new Event(event).preventDefault();
                var set_task = 'adminUpdate';
                YJTime.adminUpdate(set_task);
                YJTime.setTimer();
            });
            resetBtn.addEvent('click', function (event) {
                new Event(event).preventDefault();
                //cacheBtn.addClass('hidecache');
                $('repsonsebox').setStyle('display', 'none');
                YJTime.resetForm();
                YJTime.status_text_check();
                if ($('selectors_override').value == 2) {
                    $$('#css_font_family,#jform_params_affected_selectors').set('disabled', 'disabled');
                }
				//  radio btn groups reset
				  (function($){
						 $(".btn-group label:not(.active)").change(function() {
						  var label = $(this);
						  var input = $('#' + label.attr('for'));
						  if (input.prop('checked')) {
							  label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
							  if (input.val() == '') {
								  label.addClass('active btn-primary');
							  } else if (input.val() == 0) {
								  label.addClass('active btn-danger');
							  } else {
								  label.addClass('active btn-success');
							  }
						  }
					  }).change();		
				  })(jQuery);
				  
				var compiler_is_on = $$('#jform_params_less_compiler_on input:checked').get('value');
				if (compiler_is_on == 0) {
					cacheBtn.addClass('hidecache');
				} else {
					cacheBtn.removeClass('hidecache');
				}
            });
        }
        // check recompiled file. if not there advise
        var selected_compiled = $$('#jform_params_use_compiled_css input:checked').get('value');
        if (selected_compiled == 1) {
            var set_task = 'checkCompiled';
            YJTime.adminUpdate(set_task);
            YJTime.setTimer();
        }
        $('jform_params_use_compiled_css0').addEvent('click', function (event) {
            var set_task = 'checkCompiled';
            YJTime.adminUpdate(set_task);
            YJTime.setTimer();
        });
        $('jform_params_use_compiled_css1').addEvent('click', function (event) {
            // $('less_compiler_on1').set('checked','checked');
            $('repsonsebox').setStyle('display', 'none');
        });
        // make sure buffer and ajax recompile are not both on
        $('jform_params_ajax_front_recompile0').addEvent('click', function (event) {
            $('lbl-jform_params_buffer_front_recompile1').removeClass('active btn-success').addClass('active btn-danger');
            $('lbl-jform_params_buffer_front_recompile0').removeClass('active btn-success');
            $('jform_params_buffer_front_recompile1').set('checked', 'checked');
            $('jform_params_buffer_front_recompile0').set('checked', '');
        });
        $('jform_params_buffer_front_recompile0').addEvent('click', function (event) {
            $('lbl-jform_params_ajax_front_recompile1').removeClass('active btn-success').addClass('active btn-danger');
            $('lbl-jform_params_ajax_front_recompile0').removeClass('active btn-success');
            $('jform_params_ajax_front_recompile1').set('checked', 'checked');
            $('jform_params_ajax_front_recompile0').set('checked', '');
        });
        YJTime.setColors();
		YJTime.status_text_check();
    },
    status_text_check: function () {
		var cacheBtn = $('toolbar-cache').getElement('button');
        var compiler_is_on = $$('#jform_params_less_compiler_on input:checked').get('value');
        var component_disabled = $$('#jform_params_component_switch').get('value');
        var system_tab = $$('#templatestyleOptions a')[0];
if(!system_tab){
	system_tab = $$('#myTabTabs a')[1];
}
        $$(system_tab).set('tween', {
            duration: 2000
        });
        // compiler text
        if (compiler_is_on == 1) {
            $$('.lesscom_check').addClass('ison').set('html', lesscom_on_txt);
            system_tab.highlight('#D5EEFF');
			cacheBtn.removeClass('hidecache');
        } else {
            $$('.lesscom_check').removeClass('ison').set('html', lesscom_off_txt);
        }
        if (component_disabled != '') {
            $('option-resut').set('html', comp_dis);
            system_tab.highlight('#D5EEFF');
        } else {
            $('option-resut').set('html', '');
            system_tab.highlight('#D5EEFF');
        }
    },
    setColors: function () {
        var path = $('YJSG_template_path').get('value');
        var default_color_input = $('def_link_color').get('value')
        $$('.show_ranibow_in').setStyle("background-color", default_color_input);
		(function ($) {
			
				$('#yjsg_get_styles').on('change', function(){
				var split_value = this.get('value').split("|");
				var new_value = split_value[1];				
				$('.moor-hexInput').val('#' + new_value);
				$('#def_link_color').val('#' + new_value);
				$('.show_ranibow_in').css("background-color", '#' + new_value);
				
				
			});
		})(jQuery);
        $('yjsg_get_styles').addEvent('change', function () {
            var split_value = this.get('value').split("|");
            var new_value = split_value[1];
            $$('.moor-hexInput').set('value', '#' + new_value);
            $('def_link_color').set('value', '#' + new_value).fireEvent('keydown');
             
            $$('.show_ranibow_in').setStyle("background-color", '#' + new_value);
        });
        $('def_link_color').addEvent('change', function () {
            var split_default_value = $('yjsg_get_styles').get('value').split("|");
            var style_name = split_default_value[0];
            var style_color = this.get('value').replace("#", "");
            $('yjsg_get_styles').getSelected()[0].set('value', style_name + '|' + style_color);
            $$('.show_ranibow_in').setStyle("background-color", '#' + style_color);
            this.set('defaultValue', '#' + style_color);
        });
        var nativeColorUi = false;
        if (Browser.opera && (Browser.version >= 11.5)) {
            nativeColorUi = true;
        }
        $$('.yjsg-colorpicker').each(function (item) {
            if (nativeColorUi) {
                item.type = 'color';
            } else {
                new MooRainbow(item, {
                    id: item.id,
                    imgPath: path + '/images/admin/moorainbow/',
                    onChange: function (color) {
                        this.element.value = color.hex;
                        $$('.show_ranibow_in').setStyle("background-color", color.hex);
                        $('def_link_color').fireEvent('change');
                         
                    },
                    startColor: item.value.hexToRgb(true) ? item.value.hexToRgb(true) : [0, 0, 0]
                });
            }
        });
    },
    // nice progress bar
    resetForm: function () {
        var adminform = $('style-form');
        Object.each(tplDefaults, function (i, key) {
            var filed_name = adminform.getElement('*[name="jform[params][' + key + ']"]');
            var filed_type = filed_name.get('type');
            var filed_curr_value = filed_name.get('value');
            var default_value = i;
            // text 
            if (filed_type == 'text') {
                filed_name.set('value', default_value);
            }
            // checkboxes 

            if (filed_type == 'radio') {
                var checked_value = $$('*[name="jform[params][' + key + ']"]:checked')[0];
                var default_checked = $$('*[name="jform[params][' + key + ']"][value="' + default_value + '"]');
                default_checked.set('checked', 'checked');
            }
            // textarea 
            if (filed_type == 'textarea') {
                filed_name.set('text', default_value);
            }
            // lists  yjsg_get_styles
            if (filed_type == 'select-one') {
                if (filed_name.get('name') == 'jform[params][yjsg_get_styles]') {
                    $$('#yjsg_get_styles option').each(function (index, element) {
                        var get_defaults = index.get('rel');
                        if (get_defaults != index.get('value')) {
                            index.set('value', get_defaults);
                        }
                        filed_name.fireEvent('change');
                    });
                    filed_name.set('value', default_value);
                    filed_name.fireEvent('change');
                } else {
                    filed_name.set('value', default_value);
                    filed_name.fireEvent('change');
                }
            }
			(function ($) {$(filed_name).trigger('liszt:updated');})(jQuery);
        });
        $('jform_params_component_switch').set('value', '');
        $('jform_params_define_itemid').set('value', '');
        $('jform_params_logo_image').set('value', '');
		$('jform_params_turn_topmenu_off').set('value', '');
		(function ($) {
			$("#jform_params_component_switch").trigger('liszt:updated');
			$("#jform_params_define_itemid").trigger('liszt:updated');
			$("#jform_params_logo_image").trigger('liszt:updated');
			$("#jform_params_turn_topmenu_off").trigger('liszt:updated');
		})(jQuery);
    },
    // nice progress bar
    progress: function () {
        $('toolbar').addClass('ajax_progress');
        $('pbar').setStyle('width', '100%');
        $('repsonsebox').setStyle('display', 'none');
        var duration = 40000;
        var length = 8000;
        var count = 0;
        var tweener;
        var run = function () {
            tweener.tween("background-position", "-" + (++count * length) + "px 0px");
        };
        tweener = $("pbar").setStyle("background-position", "0px 0px").set("tween", {
            duration: duration,
            transition: Fx.Transitions.linear,
            onComplete: run,
            link: "cancel"
        });
        run();
    },
    // something changed set timer and reset styleswitcher cookies
    setTimer: function () {
        var path = $('YJSG_template_path').get('value');
        var setChange = new Request({
            method: 'get',
            url: path + '/yjsgcore/yjsgajax_reset.php' + '?ajaxreset=' + Math.random()
        });
        Cookie.write('yjsg_fs', $('default_font').value.toInt(), {
            'duration': 2
        });
        setChange.send();
    },
    // admin update form via ajax
    adminUpdate: function (set_task) {
        var adminform = $('style-form');
        var path = $('YJSG_template_path').get('value');
        var repsonsebox = $('repsonsebox');
        var cookie_name = $('jform_template').get('value') + $('YJSG_template_id').get('value');
        adminform.task.value = set_task;
        adminform.set('action', path + '/elements/yjsgupdate.php');
        var adminsend = new Form.Request(adminform, repsonsebox, {
            resetForm: false,
            useSpinner: false,
            onSend: function () {
                if (adminform.task.value != 'checkCompiled') {
                    YJTime.progress();
                }
            },
            onComplete: function (response) {
                adminform.set('action', Cookie.read(cookie_name));
                // save 
                if (adminform.task.value == 'adminUpdate') {
                    // to fast delay a bit
                    (function () {
                        $("pbar").get('tween').stop();
                        $('toolbar').removeClass('progress');
                        $('pbar').setStyle('width', '0%');
                    }).delay(200);
                }
                // clear cache and save
                if (adminform.task.value == 'clearCache') {
                    $("pbar").get('tween').stop();
                    $('toolbar').removeClass('progress');
                    $('pbar').setStyle('width', '0%');
                }
                if (!response.status) {
                    var json = JSON.decode(repsonsebox.get('html'));
                    if (json.error) {
                        repsonsebox.setStyle('display', 'block').set('html', json.error);
                    }
                    if (json.message_er) {
                        var cacheBtn = $('toolbar-cache').getElement('button');
                        $('lbl-jform_params_less_compiler_on0').removeClass('active btn-success').addClass('active btn-success');
                        $('lbl-jform_params_less_compiler_on1').removeClass('active btn-danger');
                        $('jform_params_less_compiler_on0').set('checked', 'checked');
                        $('jform_params_less_compiler_on1').set('checked', '');
                        cacheBtn.removeClass('hidecache');
                        repsonsebox.setStyle('display', 'block').set('html', json.message_er);
                        $("pbar").get('tween').stop();
                        $('toolbar').removeClass('progress');
                        $('pbar').setStyle('width', '0%');
                    }
                }
                var close_response = new Element('a', {
                    'id': 'close_response',
                    'href': 'javascript:;',
                    html: 'Close'
                });
                close_response.inject(repsonsebox, 'top');
                close_response.addEvent('click', function (event) {
                    repsonsebox.setStyle('display', 'none');
                });
                adminform.task.value = '';
                adminform.removeEvents();
            },
            onFailure: function (xmlHttpRequest) {
                var status = xmlHttpRequest.status;
                switch (status) {
                    case 403:
                        bad_response = '403. Accsess to ajax file is forbidden. Check files permisions.';
                        break;
                    case 404:
                        bad_response = '404 Page not found. Seems like the link to Ajax file is bad.';
                        break;
                    case 500:
                        bad_response = '500 Server error. Check server log for more information.';
                        break;
                    default:
                        bad_response = 'Just bad response. No code given';
                }
                repsonsebox.setStyle('display', 'block').set('html', bad_response);
                $("pbar").get('tween').stop();
                $('toolbar').removeClass('progress');
                $('pbar').setStyle('width', '0%');
                //	adminform.removeEvents();
            }
        });
        adminsend.send();
    }
}
window.addEvent('domready', YJTime.start);
//window.addEvent('domready', YJTime.start);
/**
 * Cascade select boxes enable/disable for Twitter Bootstrap elements in Joomla administration.
 * Compatible with Joomla 3.0
 * Release date - September 18th 2012
 */
var YJSelectBoxes = {
    start: function () {
        // loop all selects
        $$('select').each(function (el, i) {
            // check if select has disabled/enabled options capability
            var children = el.getElements('option.enable_next, option.disable_next'),
                selectID = el.get('id');
            // if no children, bail out
            if (children.length == 0) return;
            //*			
            // make click events on bootstrap elements
            el.addEvent('change', function (e) {
                // get li class to check if anything should be enabled or disabled
                var cls = el.getSelected().get('class')[0].split(' ');
                // loop classes
                cls.each(function (btCls, index) {
                    // if class to enable or disable is found, proceed, else, bail out
                    if (btCls == 'enable_next' || btCls == 'disable_next') {
                        var affected = cls[index + 1].split('|');
                    } else {
                        return;
                    }
                    // store all consequent elements disable or enable to keep them disabled or enabled, depending on what the parent will do
                    // this will use the inverse action meaning if parent disabled, this keeps them enabled
                    var affected2 = new Array();
                    // loop affected elements to see what should be kept enabled or disabled
                    affected.each(function (a, i) {
                        var selectedResult = $(a).getSelected(),
                            ccls = selectedResult.get('class');
                        if (!ccls[0]) {
                            return;
                        }
                        var classes = ccls[0].split(' '),
                            searchClass = 'enable_next' == btCls ? 'disable_next' : 'enable_next';
                        if (!selectedResult.hasClass(searchClass)) {
                            return;
                        }
                        // get enable/disable index from array
                        var clsIndex = classes.indexOf(searchClass) + 1;
                        if (0 == clsIndex) {
                            return;
                        }
                        // put elements in second array
                        affected2.combine(classes[clsIndex].split('|'));
                    })
                    // see what action should be taken
                    switch (btCls) {
                        // enable items
                        case 'enable_next':
                            affected.each(function (elId) {
                                var el = $(elId);
                                if (el && affected2.indexOf(elId) == -1) {
                                    $(elId).set({
                                        'disabled': ''
                                    });
                                }
                            });
                            break;
                            // disable items
                        case 'disable_next':
                            affected.each(function (elId) {
                                var el = $(elId);
                                if (el) {
                                    $(elId).set({
                                        'disabled': 'disabled'
                                    });
                                }
                            });
                            break;
                    }
                })
            })
            //*/
            // if element was disabled by some previous select, bail out so you won't interfere with previous disable/enable
            if ($(selectID).get('disabled')) {
                return;
            }
            // get selected option
            var selectedOption = el.getSelected();
            // if we have a selected option, run enable/disable
            if (selectedOption) {
                // all is stored on classes, get them
                var classes = selectedOption.get('class'),
                    cssClasses = classes[0].split(' ');
                // loop classes
                cssClasses.each(function (cls, iii) {
                    // if class is enable_next or disable_next, next class contains elements ids that should be affected
                    if (cls == 'enable_next' || cls == 'disable_next') {
                        var affected = cssClasses[iii + 1].split('|');
                    } else { // bail out if the two classes aren't found
                        return;
                    }
                    // enable or disable options on page load
                    if (cls == 'enable_next') {
                        affected.each(function (affectedEl) {
                            var el = $(affectedEl);
                            if (el) {
                                $(affectedEl).set({
                                    'disabled': ''
                                });
                            }
                        })
                    } else if (cls == 'disable_next') {
                        affected.each(function (affectedEl) {
                            var el = $(affectedEl);
                            if (el) {
                                $(affectedEl).set({
                                    'disabled': 'disabled'
                                });
                            }
                        })
                    }
                })
            }
        })
    }
}
window.addEvent('domready', YJSelectBoxes.start);
// YJSGcheckboxes
var YJCheckboxes = new Class({
    initialize: function (options) {
        this.options = Object.extend({
            labels: '.option',
            checkboxes: '.check'
        }, options || {});
        this.start();
    },
    start: function () {
        this.makeCheckBoxes();
    },
    makeCheckBoxes: function () {
        this.lbls = $$(this.options.labels);
        this.lbls.fx = [];
        this.parseChecks();
        var allinputs = $$(this.options.labels + ' ' + this.options.checkboxes);
        allinputs.each(function (chk) {
            chk.inputElement = chk.getElement('input');
            chk.inputElement.setStyle('opacity', .001);
        }.bind(this));
        allinputs.each(function (chk, i) {
            if (chk.inputElement.checked == 1) {
                chk.index = i;
                this.selectBox(chk);
            }
        }.bind(this));
    },
    parseChecks: function () {
        this.lbls.each(function (lbl, i) {
             
            var chk = lbl.getElement(this.options.checkboxes);
            chk.index = i;
            lbl.addEvent('click', function () {
                if (!chk.hasClass('selected')) {
                    this.selectBox(chk);
                } else if (lbl.hasClass('check')) {
                    this.deselectBox(chk);
                }
                lbl.getElement('input[type=checkbox]').fireEvent('click');
            }.bind(this));
            lbl.addEvent('mouseover', function (el) {
                lbl.setStyle('cursor', 'pointer');
            }.bind(this))
        }.bind(this));
    },
    selectBox: function (chk) {
        chk.inputElement.checked = 'checked';
         		
        this.lbls[chk.index].removeClass('unlocked').addClass('locked');
        chk.addClass('selected');
        this.checkAny += 1;
    },
    deselectBox: function (chk) {
        chk.inputElement.checked = false;
        //this.lbls.fx[chk.index].start({ 'color': '#000000' });
        this.lbls[chk.index].removeClass('locked').addClass('unlocked');
        chk.removeClass('selected');
        this.checkAny -= 1;
    }
});
window.addEvent('domready', function () {
    var YJChecks = new YJCheckboxes();
})
// YJSGserialize
var YJSerialize = {
    start: function () {
        var containers = $$('div.YJSG_multiple');
        containers.each(function (e, i) {
            var elems = e.getElements('input.serialize_multiple');
            if (elems.length == 0) return;
            var locks = e.getElements('input[type=checkbox]');
            var hiddenInput = new Element('input')
                .set({
                'type': 'hidden',
                'name': elems[0].getProperty('name').replace('[]', ''),
                'value': ''
            }).inject(elems.getLast(), 'after');
            hiddenInput.addClass(elems[0].getProperty('class'));
            var initialValue = '';
            elems.each(function (el, i) {
                initialValue += el.get('value');
                 
                initialValue += '|';
            });
            locks.each(function (el, i) {
                initialValue += el.checked ? 1 : 0;
                if (i !== locks.length - 1) initialValue += '|';
            });
            hiddenInput.set({
                'value': initialValue
            });
            elems.addEvent('click', function (event) {
                this.select();
            });
            elems.addEvent('keyup', function () {
                YJSerialize.assembleVariable(elems, locks, hiddenInput, this);
            });
            locks.addEvent('click', function () {
                YJSerialize.assembleVariable(elems, locks, hiddenInput, false);
            });
        })
        YJSerialize.resetValues();
    },
    assembleVariable: function (elems, locks, hiddenInput, elem) {
        initialValue = '';
        if (elem) {
            var val = elem.get('value').toFloat().round(2);
            var v = val ? val : '0';
            elem.set({
                'value': v
            });
            YJSerialize.verifyInput(elems, elem, locks);
        }
        elems.each(function (el, i) {
            initialValue += el.get('value').toFloat().round(2);
             
            initialValue += '|';
        });
        locks.each(function (el, i) {
            initialValue += el.checked ? 1 : 0;
            if (i !== locks.length - 1) initialValue += '|';
        });
        hiddenInput.set({
            'value': initialValue
        });
    },
    verifyInput: function (elems, el, locks) {
        var elementValue = el.get('value').toInt();
        if (elementValue > 100) {
            el.set({
                'value': 100
            });
            elementValue = 100;
        }
        var locked = [];
        locks.each(function (e, i) {
            if (e.checked) locked.include(i);
        })
        var elIndex = elems.indexOf(el);
        var s = 0;
        var eVals = [];
        var eKeys = [];
        elems.each(function (e, i) {
            if (i == elIndex) return;
            var stopped = false;
            locked.each(function (e) {
                if (i == e) {
                    elementValue += elems[e].get('value').toFloat().round(2);
                    stopped = true;
                }
            })
            if (stopped) return;
            var v = e.get('value').toFloat().round(2);
            eVals[eVals.length] = v;
            eKeys.include(i);
            s += v;
        });
        if (elementValue > 100) {
            elementValue -= el.get('value');
            el.set({
                'value': '0'
            });
        }
        if (s + elementValue > 100) {
            var exceed = 100 - elementValue;
            eKeys.each(function (real, key) {
                var r = s == 0 ? 0 : eVals[key] / s;
                var newSize = exceed * r;
                elems[real].set({
                    'value': newSize.toFloat().round(2)
                });
            });
        };
    },
    resetValues: function (hiddenInput) {
        $$('a.YJSG_reset-values').addEvent('click', function (event) {
            new Event(event).preventDefault();
            var values = this.getProperty('rel').split('|');
            var elemsCSS = this.getProperty('id');
            $$('input.' + elemsCSS + '[type=text]').each(function (el, i) {
                el.set({
                    'value': values[i]
                });
            });
            $$('input.' + elemsCSS + '[type=checkbox]').each(function (el, i) {
                el.set({
                    'checked': ''
                });
                el.getParent().getParent().removeClass('locked').addClass('unlocked');
                el.getParent().removeClass('selected');
            });
            $$('input.' + elemsCSS + '[type=hidden]').set({
                'value': this.getProperty('rel')
            });
        });
    }
}
window.addEvent('domready', YJSerialize.start);
//YJSG LOGO
window.addEvent('domready', function () {
	

    fireEvent('change');

    function add_image() {
        var sitepath = $('YJSG_site_path').get('value');
        var template_path = $('YJSG_template_path').get('value');
        var split_color = $('yjsg_get_styles').getSelected().get("value")[0].split("|");
        var current_style = split_color[0];
        var slikica_src = $('jform_params_logo_image').get('value');
        if (slikica_src) {
            $('show_logo').set('src', sitepath + slikica_src);
            $('prev_logo').set('href', sitepath + slikica_src);
        } else {
            $('show_logo').set('src', template_path + '/images/' + current_style + '/logo.png');
            $('prev_logo').set('href', template_path + '/images/' + current_style + '/logo.png');
        }
         
        $('yjsg_get_styles').addEvent('change', function (el) {
            var split_value = this.get('value').split("|");
            var c = split_value[0];
            $('show_logo').set('src', template_path + '/images/' + c + '/logo.png');
            $('prev_logo').set('href', template_path + '/images/' + c + '/logo.png');
        });
    }

    function set_info() {
        var img = new Image();
        var slikica_src = $('show_logo').get('src') + '?' + Math.random();
        img.src = slikica_src;
        img.onload = function () {
            var img_real_width = this.width;
            var img_real_height = this.height;
            $('image_dimensions').set('html', 'This image is <b>' + this.width + 'px wide</b> and <b>' + this.height + 'px high.</b> Click on image for full preview.');
        }
    }

    function get_set_all_info() {
        var img = new Image();
        var slikica_src = $('show_logo').get('src') + '?' + Math.random();
        img.src = slikica_src;
        img.onload = function () {
            var img_real_width = this.width;
            var img_real_height = this.height;
            $('image_dimensions').set('html', 'This image is <b>' + this.width + 'px wide</b> and <b>' + this.height + 'px high.</b> Click on image for full preview.');
            $$('#logo_height,#logo_width').set('tween', {
                duration: 2000
            });
            $('logo_height').set('value', img_real_height + 'px').highlight('#D5EEFF');
            $('logo_width').set('value', img_real_width + 'px').highlight('#D5EEFF');
        }
    }

    function set_dim() {
        $('add_dimensions').addEvent('click', function () {
            get_set_all_info();
        });
    }
    add_image();
    set_info();
    set_dim();
    $('jform_params_logo_image').addEvent('change', function () {
        add_image();
        set_info();
    });
    $('clear_logo').addEvent('click', function () {
        add_image();
        get_set_all_info();
    });
	
            var split_default_value = $('yjsg_get_styles').get('value').split("|");
            var style_name = split_default_value[0];
            var style_color = $('def_link_color').get('value').replace("#", "");
            $('yjsg_get_styles').getSelected()[0].set('value', style_name + '|' + style_color);
            $$('.show_ranibow_in').setStyle("background-color", '#' + style_color);
            $('def_link_color').set('defaultValue', '#' + style_color);	
 
	
	
});
/*jshint eqnull:true */
/*!
 * jQuery Cookie Plugin v1.2
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function ($, document, undefined) {
    var pluses = /\+/g;

    function raw(s) {
        return s;
    }

    function decoded(s) {
        return decodeURIComponent(s.replace(pluses, ' '));
    }
    var config = $.cookie = function (key, value, options) {
        // write
        if (value !== undefined) {
            options = $.extend({}, config.defaults, options);
            if (value === null) {
                options.expires = -1;
            }
            if (typeof options.expires === 'number') {
                var days = options.expires,
                    t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }
            value = config.json ? JSON.stringify(value) : String(value);
            return (document.cookie = [
                encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
                ].join(''));
        }
        // read
        var decode = config.raw ? raw : decoded;
        var cookies = document.cookie.split('; ');
        for (var i = 0, parts;
        (parts = cookies[i] && cookies[i].split('=')); i++) {
            if (decode(parts.shift()) === key) {
                var cookie = decode(parts.join('='));
                return config.json ? JSON.parse(cookie) : cookie;
            }
        }
        return null;
    };
    config.defaults = {};
    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== null) {
            $.cookie(key, null, options);
            return true;
        }
        return false;
    };
})(jQuery, document);
(function ($) {
    $(document).ready(function () {
        $('a[data-toggle="tab"]').on('shown', function (e) {
            //save the latest tab using a cookie:
            $.cookie('last_tab', $(e.target).attr('href'));
        });
        //activate latest tab, if it exists:
        var lastTab = $.cookie('last_tab');
        if (lastTab) {
            $('ul.nav-tabs').children().removeClass('active');
            $('a[href=' + lastTab + ']').parents('li:first').addClass('active');
            $('div.tab-content').children().removeClass('active');
            $(lastTab).addClass('active');
        }
        /* last accordion */
        $('.accordion-body').on('shown', function (e) {
            var getlink = this.get('id');
            /* save last accordion */
            $.cookie('last_accordion', getlink);
        });
        var lastAcc = $.cookie('last_accordion');
        if (lastAcc) {
            $('a[data-toggle="collapse"]').addClass('collapsed');
            $('.accordion-body').removeClass('in').height('0px');
            $('a[href="#' + lastAcc + '"]').removeClass('collapsed');
            $('#' + lastAcc).addClass('in').height('auto');
        }
 
    });
	

	
})(jQuery)