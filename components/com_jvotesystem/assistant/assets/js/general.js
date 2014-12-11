/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
jQuery(document).ready(function($){
	$('table.params tr.area td.field textarea').focusin(function() {
		$(this).parent().find("div.bbcodeToolbar").stop(true, true).fadeIn();
	}).blur(function() {
		$(this).parent().find("div.bbcodeToolbar").stop(true, true).fadeOut();
	});
	
	$('table.overview td.content').hover(function() {
		$(this).find("table.overview").stop(true, true).fadeIn();
	}, function() {
		$(this).find("table.overview").stop(true, true).hide();
	});
	
	$('table.params tr td.field.state a').click(function() {
		var el = $(this).parent().find("input");
		var value = el.val();
		
		if(value == 1) {
			el.val(0);
			$(this).removeClass("state-published");
			$(this).addClass("state-unpublished");
		} else if(value == 0) {
			el.val(1);
			$(this).removeClass("state-unpublished");
			$(this).addClass("state-published");
		} else if(value == -1) {
			$(this).closest(".area.answer.trash").fadeOut("slow");
		}
	});
	
	$('table.params tr.area.answer td.field textarea').blur(function() {
		if($(this).parent().parent().hasClass("trash") == false) {
			if($(this).val() == "") {
				$(this).parent().parent().addClass("trash");
				$(this).parent().parent().find("td.field.state a").parent().find('input').val(-1);
			}
		} else {
			if($(this).val() != "") {
				$(this).parent().parent().removeClass("trash");
				$(this).parent().parent().find("td.field.state a").parent().find('input').val(1);
			}
		}
	});
	
	$('table.params tr.area.answer.new td.field textarea').click(function() {
		if($(this).parent().parent().hasClass("new")) {
			$(this).val("");
			$(this).parent().parent().removeClass("new");
			$(this).parent().find('input').val(0);
			addNewAnswer();
		}
	});
	
	addNewAnswer();
	
	$('table.params td.field textarea').prettyComments({
		animate: true,
		animationSpeed: 'normal',
		maxHeight: 500 
	});		
	
	$('#adminForm table.params tr.required td.field input').blur(function() {
		removeWarning($(this));
	});
	$('#adminForm table.params tr.required td.field textarea').blur(function() {
		removeWarning($(this));
	});
	
	$('#adminForm table.params #template_config').change(function() {
		$('#adminForm .config-page').slideUp("fast");;
		$('#adminForm .config-' + $(this).val()).slideDown("fast");
	});
});

function addNewAnswer() {
	var el = jQuery('table.params tr.area.answer.new.template').clone(true);
	el.appendTo('table.params.answers');
	el.hide();
	el.find("textarea").css("height", 25);
	el.removeClass("template");
	el.find(".field.color input").val( jVS.colors[jVS.cur_color % jVS.colors.length] );
	jVS.cur_color++;
	el.find(".field.color input").miniColors(); 
	el.stop(true, true).fadeIn();
}

function removeWarning(el) {
	if(el.parent().parent().hasClass("warning")) {
		if(el.val() != "") {
			el.parent().parent().removeClass("warning");
		}
	}
}

function onSubmitForm() {
	var els = jQuery('#adminForm table.params tr.required');
	var out = true;
	
	els.each(function() {
		var el = jQuery(this);
		
		if(el.hasClass("text")) {
			var param = el.find("td.field input");
		} else if(el.hasClass("area")) {
			var param = el.find("td.field textarea");
		}
		
		if(param != undefined) {
			if(param.val() == "") {
				el.addClass("warning");
				param.focus();
				out = false;
				
				if(firstel == undefined) {
					var firstel = param;
					firstel.focus();
					
					showMessage(TRANSLATION_FILLREQUIREDINPUTS, "error");
				}
			}
		}
	});
	
	return out;
}

var needReload = false;
function actionAssistant(action, basepath) {
	if (action == 'save') {
		if(onSubmitForm() == false) return false;
		jQuery("#task").val("savepoll");
		showLoading();
		jQuery.post(basepath + "/index.php", jQuery("#adminForm").serialize(),
			function( data ) {
				var vars = handle_get_toArray(data);
			
				hideLoading();
				if(vars["msg_style"] != undefined)
					showMessage(vars["msg"], vars["msg_style"]);
				else
					showMessage(vars["msg"]);
				needReload = true;
				
				//Umfragen-ID setzen
				jQuery("#pollid").val(vars["id"]);
				
				//Alias setzen
				jQuery("#alias").val(vars["alias"]);
				
				//Neuen Antworten IDs zuweisen
				if(vars["newids"] != "") {
					var els = jQuery('table.params tr.area.answer td.field.answer input');
					var answers = new Array();
					var i = 0;
					els.each(function() {
						if(jQuery(this).val() == 0) {
							answers[i] = jQuery(this);
							i++;
						}
					});
					
					var ids = vars["newids"].split(",");
					for(var i = 0; i < ids.length; i++) {
						if(ids[i] != "") {
							answers[i].val(ids[i]);
						}
					}
				}
				
				//Antworten entfernen...
				if(vars["removedids"] != "") {
					var els = jQuery('table.params tr.area.answer.trash td.field.answer input');
					var answers = new Array();
					els.each(function() {
						answers[jQuery(this).val()] = jQuery(this).parent().parent();
					});
					
					var ids = vars["removedids"].split(",");
					for(var i = 0; i < ids.length; i++) {
						if(ids[i] != "") {
							answers[ids[i]].fadeOut().delay(1000).remove();
						}
					}
				}
				
				if(vars["redirect"] != undefined)
					parent.window.location.href = vars["redirect"];
			}
		);
	}
}

function showLoading() {
	jQuery( "#overlay" ).show();
}

function hideLoading() {
	jQuery( "#overlay" ).hide();
}

function showMessage(msg, style) {
	if(style == undefined) var style = "info";
	if(style == "info") {
		jQuery( "#message" ).removeClass("error");
		jQuery( "#message" ).addClass("info");
	} else if(style == "error") {
		jQuery( "#message" ).removeClass("info");
		jQuery( "#message" ).addClass("error");
	}

	jQuery( "#message" ).empty().append( msg );
	jQuery( "#message" ).stop(true, true).fadeIn().delay(3000).fadeOut();
}

function loadChart(basepath, divID, id, infa) {
	var link = 'components/com_jvotesystem/assistant/index.php?view=ajax&task=chart&tmpl=smallblue&interface=' + infa + '&box=' + id;
	
	jQuery.get(basepath + link, {},
		function( data ) {
			var vars = handle_get_toArray(data);
		
			var params;
			var flashVars = {
				settings_file: vars['xmlpath'],
				chart_data: vars['code']
			};
			
			swfobject.embedSWF(basepath + "/components/com_jvotesystem/assets/charts/ampie.swf", divID, "100%", "400", "8.0.0", basepath + "/components/com_jvotesystem/assets/charts/expressInstall.swf", flashVars, params);
		}
	);
}

function handle_get_toArray(text) {
	var vars = text.split('&');
	var result = new Array();

	for(i = 0; i < vars.length; i++){
		var varSplit = vars[i].split('=');
		if(varSplit[1] != undefined) {
			varSplit[1] = decodeURIComponent((varSplit[1]).replace(/\+/g, '%20'));
			result[varSplit[0]] = varSplit[1];
		}
	}
	
	return result;
}

function loadView(v, width, height) {
	parent.resizeSqueezeBox(width, height);
	showLoading();
	location.href = 'index.php?view=' + v;
}