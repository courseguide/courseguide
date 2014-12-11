/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
* @projectsite www.joomess.de/projects/jvotesystem
* @authors Johannes Meßmer, Andreas Fischer
* @copyright (C) 2010 - 2012 Johannes Meßmer
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

function jVS_constructor_easyquestion () {

	var me = {}, $ = jQuery;
	
	me.construct = function(el) { el = $(el);
		
	}	
	
	me.barChart = function(boxobj, values, labels, ids, box, code, count, colors) {
		var el = boxobj;
		var link = $('<span class="barchart_link">(<span>' + jVS.translateStr("result") + '</span>)</span>').appendTo(el.find(".pagebox").html(''));
		var frame = $('<div class="jsbarchart">');
		var total = 0, max = 0;
		
		for (var i = 0, ii = values.length; i < ii; i++) {
			total += values[i];
			if (values[i] > max)  max = values[i];
		}
		
		for (var i = 0, ii = values.length; i < ii; i++) {
			
			var wrap = $('<div class="barbox"></div>').appendTo(frame);
			$('<p><span>'+labels[i]+' </span><span>('+'0%)</span></p><div class="barholder"></div>').appendTo(wrap);
			var bars = $('<div>')
				.html('<a style="background-color:'+(colors[i]||'#000000')+';"><span>' + values[i] + '</span></a>')
				.appendTo(wrap.find('.barholder'));
			if (isNaN(values[i]/max) === false && values[i] > 0) {
				var run = function(el,val) {
					el.width(0).animate(
						{
							width: val/max*100+"%"
						}, 
						{
							easing: "linear", 
							duration: "slow", 
							step: function (now, fx) {
								$(fx.elem).parent().prev().children("span:last").html('('+jVS.round(now/total*max,0)+'%)');
							}
						}
					); 
					if(val/max*100 >= 10) el.find('span').delay(400).fadeIn();
				};
				bars.on("run", {value: values[i]}, function(e) {run($(this),e.data.value);});
			}
		}
		//Tooltip mit der Auswertung anzeigen
		link.on("mouseenter mouseleave", function (e) { e.type[5]=="l" ? window.clearTimeout(jVS.timeout) : me.loadChart($(this), frame.clone(true));});//Clonen, sonst sonst hat die Variable frame nach dem ersten laden keine events mehr!
		
		return true;
	}
	
	me.loadChart = function(el, frame) {
		jVS.timeout = setTimeout(function(){
			jVS.tip.show(el, frame, false, "jvs-easyquestion");
			jVS.tip.el.find(".barbox div").trigger("run");
		},200);
	}

	return me;
}

jVS.easyquestion = jVS_constructor_easyquestion();