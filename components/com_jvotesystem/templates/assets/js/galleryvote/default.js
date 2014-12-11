/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
* @projectsite www.joomess.de/projects/jvotesystem
* @authors Johannes Meßmer, Andreas Fischer
* @copyright (C) 2010 - 2012 Johannes Meßmer
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

function jVS_constructor_galleryvote () {

	var me = {}, $ = jQuery;
	
	me.construct = function(el) { el = $(el);
		me.constructjlib_colorbox(el);
		
		$(document).bind('jlibbox_load', function(){
		    var a = $.jlib_colorbox.element().closest('.answer');
		    var jlibbox = $('#jlib_colorbox #jlibboxContent');
		    if(a.closest('.jvs-galleryvote')[0] === el[0]) { 
		    	jlibbox.find('.jvs-galleryvote.boxToolbar').hide();
		    } else {
		    	jlibbox.find('.jvs-galleryvote.boxToolbar').remove();
		    }
		});
		
		$(document).bind('jlibbox_complete', function(){
		    var a = $.jlib_colorbox.element().closest('.answer');
		    var jlibbox = $('#jlib_colorbox #jlibboxContent');
		    if(a.closest('.jvs-galleryvote')[0] === el[0]) { 
		    	jlibbox.find('.jvs-galleryvote.boxToolbar').remove();
		    	jlibbox.append('<div class="jvs-galleryvote boxToolbar"> <div class="rank"></div> <div class="count_hover"></div> <div class="votingbutton">+</div> </div>');
		    	jlibbox.jvslistencore();
		    	jlibbox.jvslisten();
		    	
		    	//Toolbar
		    	var toolbar = jlibbox.find('.jvs-galleryvote.boxToolbar');
		    	if(a.find('.rank').length > 0) toolbar.find('.rank').html(a.find('.rank').html()).addClass("mainRGB"); else toolbar.find('.rank').remove();
		    	if(a.find('.count_hover').length > 0) toolbar.find('.count_hover').html(a.find('.count_hover').html()).addClass("mainRGB"); else toolbar.find('.count_hover').hide();
		    	toolbar.find('.votingbutton').click(function() { a.find('.votingbutton').trigger('click'); }).attr("data-jvs_tooltip", "").attr("data-jvs_tooltip", a.find('.votingbutton').data("jvs_tooltip")).addClass("mainRGB");
		    	toolbar.find('.votingbutton').addClass(a.find('.votingbutton').hasClass("vote") ? "vote" : "novote");
		    	toolbar.append(a.find('.vote-down').clone());
		    	toolbar.find('.vote-down.reset').mouseenter(function() { $(this).addClass("resetvotes"); $(this).find(".operator").html("–"); })
		    	toolbar.find('.vote-down.reset').mouseleave(function() { $(this).removeClass("resetvotes"); $(this).find(".operator").html("+"); })
		    	toolbar.find('.vote-down.reset').click(function() { a.find('.vote-down').trigger('click'); })
		    	
		    	//Comments
		    	$('body').css('overflow', 'hidden');
		    	var c_box = $('body').find('.jvs-galleryvote.comments');
		    	if($('body').find('.jvs-galleryvote.comments').length == 0) {
		    		var c_box = $('<div class="jvotesystem jvs-galleryvote comments answer" data-template="galleryvote"> </div>');
		    		$('#jlib_colorbox').after(c_box);
		    		c_box.jvslistencore();
		    		c_box.jvslisten(true);
		    	}
	    		c_box.fadeIn();
	    		c_box.attr("data-box", a.dp('box'));
	    		c_box.attr("data-a", a.dp('a'));
	    		c_box.spin();
	    		jVS.commentgo(a.find('.votingbutton'), 1);
		    	
		    	toolbar.fadeIn();
		    }
		});
		
		$(document).bind('jlibbox_closed', function(){
			$('body').css('overflow', 'auto');
			var jlibbox = $('#jlib_colorbox #jlibboxContent');
			jlibbox.find('.jvs-galleryvote.boxToolbar').remove();
			$('body .jvs-galleryvote.comments').fadeOut("fast");
		});
		
		
		el.on("change, keypress, keyup", ".newanswerbox #answertext", function(e) { if($(this).val() != "") el.find(".newanswerbox #answerimg").removeAttr("disabled"); else el.find(".newanswerbox #answerimg").attr("disabled", "disabled"); });
	
		//Check for current answer -> open popup
		var id = parseInt(el.dp("box"));
		if(jVS.conf.polls[id].cur_answer != null) {
			var aEl = el.find(".answer[data-a=" + jVS.conf.polls[id].cur_answer + "]");
			aEl.find(".vote_thumb_link").trigger("click");
		}
	}
	
	me.commentGo = function(anbox, box, answer, j) { 
		var cbox = $('body .jvs-galleryvote.comments');
		if(cbox.children('.commentbox').length > 0) {
			cbox.find('.commentbox, .newcommentbox').fadeOut("fast", function() { 
				var code = $(j.code);
				if(j.firstLoad) {
					code.hide();
					cbox.html(code);
					var newc = cbox.find('.newcommentbox').clone(true, true);
					cbox.find('.newcommentbox').remove();
					cbox.prepend(newc);
				} else cbox.find('.commentbox').hide().html(code);
				cbox.find('.commentpage').css({'position': 'relative', 'left':'auto', 'width':'auto'});
				cbox.find('.commentbox, .newcommentbox').fadeIn("fast");
			});
		} else {
			cbox.html(j.code);
			var newc = cbox.find('.newcommentbox').clone(true, true);
			cbox.find('.newcommentbox').remove();
			cbox.prepend(newc);
		}
		cbox.spin(false);
		
		return true;
	}
	
	me.constructjlib_colorbox = function(el) {
		$.jlib_colorbox.close();
		el.find(".answer .vote_thumb_link").jlib_colorbox({
			rel:"votesystem" + el.dp("box"),
			title: function(){
			    var text = $(this).closest('.answer').find(".toolbar .text");
			    return text.html();
			},
			maxWidth: "95%",
			maxHeight: "99%",
			fixed: true
		});
	}
	
	me.handleConstructAnswer = function() {
		return true;
	}
	
	me.voteChanged = function(el) {
		var a = $.jlib_colorbox.element().closest('.answer'); 
		var toolbar = $('#jlib_colorbox #jlibboxContent .jvs-galleryvote.boxToolbar');
		if(a.closest('.jvs-galleryvote')[0] === el.closest('.jvs-galleryvote')[0] && toolbar.length > 0) { 
			toolbar.find('.rank').html(a.find('.rank').html())
			toolbar.find('.count_hover').html(a.find('.count_hover').html());
			toolbar.find('.vote-down').html(a.find('.vote-down').html());
			toolbar.find('.votingbutton').removeClass("vote").removeClass("novote").addClass(a.find('.votingbutton').hasClass("vote") ? "vote" : "novote");
			if(parseInt(toolbar.find('.vote-down .ownvotes').html()) > 0) toolbar.find('.vote-down').removeClass("resetvotes").fadeIn(); else toolbar.find('.vote-down').hide();
		}
	}
	
	me.barChart = function(boxobj, values, labels, ids, box, code, count, colors) {
		$.jlib_colorbox.close();
		
		var frame = $('<div class="jsbarchart"></div>').appendTo(boxobj.find(".pagebox").html(''));
		var total = 0, max = 0, state = true;
		
		for (var i = 0, ii = values.length; i < ii; i++) {
			total += values[i];
			if (values[i] > max)  max = values[i];
		}
		
		var page = boxobj.data("lastviewpage");
		var startCount = count;
		if(page) startCount = count*page;
		for (var i = 0, ii = values.length; i < ii; i++) {
			var wrap = $('<div class="barbox" style="display:'+(i > (startCount-1) ? 'none':'block')+'"></div>');
			$('<div class="small_thumb"><img src="' + jVS.conf.site + 'images/jvotesystem/' + ids[i] + '/small.jpg" /></div>').appendTo(wrap);
			var container = $('<div class="container_bar"></div>').html('<div class="label_holder"><span class="bar_label">'+labels[i]+' </span><span class="bar_percent">0%</span></div>');
			var bars = $('<div class="bar"></div>')
				.html('<a style="background-color:'+(colors[i]||'#000000')+';"></a>')
				.appendTo(container);
			if (isNaN(values[i]/max) === false && values[i] > 0) {
				bars.delay(500).animate({width: values[i]/max*100+"%"}, {easing: "linear", duration: "slow", step: function (now, fx) {$(fx.elem).prev().children("span:last").html('('+jVS.round(now/total*max,0)+'%)')}});
			}
			container.appendTo(wrap);
			wrap.appendTo(frame);
		}
		
		frame.after(code);
		
		frame.next().find("a.scaling").click(function() {
			var bars = frame.find("div.barbox .container_bar .bar");
			for (var i = 0, ii = values.length; i < ii; i++) {
				if ($(bars[i]).width() === 0 ) continue;
				if (state == false) {
				$(bars[i]).stop(true).animate({width: values[i]/max*100+"%"}, "slow");
				} else if (state == true) {
				$(bars[i]).stop(true).animate({width: values[i]/total*100+"%"}, "slow");
				}
			}
			(state == false) ? $(this).html(jVS.jLang.relative) : $(this).html(jVS.jLang.absolute);
			(state == false) ? state = true : state = false;
		});
		
		if (values.length > (count-1)) {
			frame.next().on("click","a.showall",function() {
				$("div.barbox:not(:visible):lt(" + count + ")",frame).slideDown("slow");//LOL
				if ($("div.barbox:not(:visible)",frame).length === 0) $(this).attr("class","hideall").text(jVS.jLang.hideall);
			}).on("click", "a.hideall", function() {
				$("div.barbox:gt(" + (count-1) + ")",frame).slideUp("slow");
				$(this).attr("class","showall").text($(code).find("a.showall").dp("short") ? jVS.jLang.load_next_short : jVS.jLang.load_next.replace("%STEPS%", count));
			});
		}
		
		return true;
	}
	
	me.addanswer = function(el, data) {
		if ($.browser.msie  && parseInt($.browser.version, 10) <= 8) {
			alert( 'Sorry, the upload of pictures does not work in IE 8 or older. :/ \nYou need to update your browser!' );
			return true;
		}
		
		var boxobj = $(el).closest("div.jvotesystem");
		var answer = boxobj.find(".newanswerbox #answertext").val(); 
		var obj = boxobj.find(".newanswerbox #answerimg");
		
		if(data == undefined) {
			if ( jVS.removebb(answer) == "" || obj.val() == "") { 
				jVS.error(jVS.jLang.noemptyordefault); 
				return true;
			}
			
			var def = {"task": "addAnswer", "only_page":1, "lang": jVS.conf.lang, "box":boxobj.dp('box'), "answer":answer, "template": jVS.gettemplate(boxobj)};
			def[jVS.conf.token]=1;
		} else def = data;		
		
		jVS.load(boxobj.dp('box'));
		boxobj.find('.newanswerbox form').ajaxForm().ajaxSubmit({
			url: jVS.conf.site+"components/com_jvotesystem/ajax.php",
			async: false,
			type: "POST",
			dataType: "json",
			data: def,
			success: function(j){
				jVS.done(boxobj.dp('box'));
				if(j.error && j.captcha == 0) {
					jVS.info({'type':'error','title':jVS.jLang.error, 'message':j.error, 'button':['OK','Cancel'], callback: 
						function() {
							jVS.showcaptcha({"captcha_callback": function(c_data) {
								$.extend(def, c_data);
								me.addanswer(el, def);
							}});
						}
					});
				} else if(j.captcha == 0) {
					jVS.showcaptcha({"captcha_callback": function(c_data) {
						$.extend(def, c_data);
						me.addanswer(el, def);
					}});
				} else {
					boxobj.find(".newanswerbox #answertext").val("").trigger("keypress");
					obj.val("");
					jVS.answeradded(j, boxobj);
				}
			}
		});
		
		return true;
	}
	
	me.goLoading = function(el) {
		me.constructjlib_colorbox(el);
	}
	
	me.votesResetted = function(anbox, vote, countInt, leftInt) {
		$.jlib_colorbox.close();		
	}
	
	me.showAnswerTooltip = function(anbox, tooltip) {
		if($('#jlib_colorbox').is(":visible")) {
			var votingbutton = $('#jlib_colorbox #jlibboxContent .jvs-galleryvote.boxToolbar .votingbutton');
			votingbutton.attr("data-jvs_tooltip", tooltip);
			jVS.tip.show(votingbutton, '<div class="contentbox">' + tooltip + '</div>');
			
			return true;
		}
	}

	return me;
}

jVS.galleryvote = jVS_constructor_galleryvote();