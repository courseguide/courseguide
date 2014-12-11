/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
* @projectsite www.joomess.de/projects/jvotesystem
* @authors Johannes Meßmer, Andreas Fischer
* @copyright (C) 2010 - 2012 Johannes Meßmer
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

function jVS_constructor_darksimple () {

	var me = {}, $ = jQuery;
	
	me.construct = function(el) { el = $(el);
		el.on("mouseenter",	".answerbox",	function (e) { me.showAnswerOptions($(this)); } );
		el.on("mouseleave",	".answerbox",	function (e) { me.hideAnswerOptions($(this)); } );
	}
	
	me.answerOptionsTimeout;
	me.showAnswerOptions = function(el) {
		window.clearTimeout(me.answerOptionsTimeout);
		el.closest("[data-p]").find(".answericons").hide();
		var icons = el.find(".answericons"); 
		if((icons.find("span").children()).length > 0)
			me.answerOptionsTimeout = window.setTimeout(function() { icons.fadeIn("slow"); }, 100);
	}
	
	me.hideAnswerOptions = function(el) {
		window.clearTimeout(me.answerOptionsTimeout);
		el.find(".answericons").hide();
	}

	return me;
}

jVS.darksimple = jVS_constructor_darksimple();