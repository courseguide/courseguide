/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//fgnass.github.com/spin.js#v1.2.4 //modified
(function(a,b,c){function g(a,c){var d=b.createElement(a||"div"),e;for(e in c)d[e]=c[e];return d}function h(a){for(var b=1,c=arguments.length;b<c;b++)a.appendChild(arguments[b]);return a}function j(a,b,c,d){var g=["opacity",b,~~(a*100),c,d].join("-"),h=.01+c/d*100,j=Math.max(1-(1-a)/b*(100-h),a),k=f.substring(0,f.indexOf("Animation")).toLowerCase(),l=k&&"-"+k+"-"||"";return e[g]||(i.insertRule("@"+l+"keyframes "+g+"{"+"0%{opacity:"+j+"}"+h+"%{opacity:"+a+"}"+(h+.01)+"%{opacity:1}"+(h+b)%100+"%{opacity:"+a+"}"+"100%{opacity:"+j+"}"+"}",0),e[g]=1),g}function k(a,b){var e=a.style,f,g;if(e[b]!==c)return b;b=b.charAt(0).toUpperCase()+b.slice(1);for(g=0;g<d.length;g++){f=d[g]+b;if(e[f]!==c)return f}}function l(a,b){for(var c in b)a.style[k(a,c)||c]=b[c];return a}function m(a){for(var b=1;b<arguments.length;b++){var d=arguments[b];for(var e in d)a[e]===c&&(a[e]=d[e])}return a}function n(a){var b={x:a.offsetLeft,y:a.offsetTop};while(a=a.offsetParent)b.x+=a.offsetLeft,b.y+=a.offsetTop;return b}var d=["webkit","Moz","ms","O"],e={},f,i=function(){var a=g("style");return h(b.getElementsByTagName("head")[0],a),a.sheet||a.styleSheet}(),o={lines:12,length:7,width:5,radius:10,color:"#000",speed:1,trail:100,opacity:.25,fps:20,zIndex:2e9,className:"spinner",top:"auto",left:"auto"},p=function q(a){if(!this.spin)return new q(a);this.opts=m(a||{},q.defaults,o)};p.defaults={},p.prototype={spin:function(a){this.stop();var b=this,c=b.opts,d=b.el=l(g(0,{className:c.className}),{position:"relative",zIndex:c.zIndex}),e=c.radius+c.length+c.width,h,i;a&&(a.insertBefore(d,a.firstChild||null),i=n(a),h=n(d),l(d,{left:(c.left=="auto"?i.x-h.x+(a.offsetWidth>>1):c.left+e)+"px",top:(c.top=="auto"?i.y-h.y+(a.offsetHeight>>1):c.top+e)+"px"})),d.setAttribute("aria-role","progressbar"),b.lines(d,b.opts);if(!f){var j=0,k=c.fps,m=k/c.speed,o=(1-c.opacity)/(m*c.trail/100),p=m/c.lines;!function q(){j++;for(var a=c.lines;a;a--){var e=Math.max(1-(j+a*p)%m*o,c.opacity);b.opacity(d,c.lines-a,e,c)}b.timeout=b.el&&setTimeout(q,~~(1e3/k))}()}return b},stop:function(){var a=this.el;return a&&(clearTimeout(this.timeout),a.parentNode&&a.parentNode.removeChild(a),this.el=c),this},lines:function(a,b){function e(a,d){return l(g(),{position:"absolute",width:b.length+b.width+"px",height:b.width+"px",background:a,boxShadow:d,transformOrigin:"left",transform:"rotate("+~~(360/b.lines*c)+"deg) translate("+b.radius+"px"+",0)",borderRadius:(b.width>>1)+"px"})}var c=0,d;for(;c<b.lines;c++)d=l(g(),{position:"absolute",top:1+~(b.width/2)+"px",transform:b.hwaccel?"translate3d(0,0,0)":"",opacity:b.opacity,animation:f&&j(b.opacity,b.trail,c,b.lines)+" "+1/b.speed+"s linear infinite"}),b.shadow&&h(d,l(e("#000","0 0 4px #000"),{top:"2px"})),h(a,h(d,e(b.color,"0 0 1px rgba(0,0,0,.1)")));return a},opacity:function(a,b,c){b<a.childNodes.length&&(a.childNodes[b].style.opacity=c)}},!function(){var a=l(g("group"),{behavior:"url(#default#VML)"}),b;if(!k(a,"transform")&&a.adj){for(b=4;b--;)i.addRule(["group","roundrect","fill","stroke"][b],"behavior:url(#default#VML)");p.prototype.lines=function(a,b){function e(){return l(g("group",{coordsize:d+" "+d,coordorigin:-c+" "+ -c}),{width:d,height:d})}function k(a,d,f){h(i,h(l(e(),{rotation:360/b.lines*a+"deg",left:~~d}),h(l(g("roundrect",{arcsize:1}),{width:c,height:b.width,left:b.radius,top:-b.width>>1,filter:f}),g("fill",{color:b.color,opacity:b.opacity}),g("stroke",{opacity:0}))))}var c=b.length+b.width,d=2*c,f=-(b.width+b.length)*2+"px",i=l(e(),{position:"absolute",top:f,left:f}),j;if(b.shadow)for(j=1;j<=b.lines;j++)k(j,-2,"progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");for(j=1;j<=b.lines;j++)k(j);return h(a,i)},p.prototype.opacity=function(a,b,c,d){var e=a.firstChild;d=d.shadow&&d.lines||0,e&&b+d<e.childNodes.length&&(e=e.childNodes[b+d],e=e&&e.firstChild,e=e&&e.firstChild,e&&(e.opacity=c))}}else f=k(a,"animation")}(),a.jVS_Spinner=p})
(window,document);

jQuery(document).ready(function($){
	//Votelinks Ajax einschalten, vermeidet das ganze onlick Spaghetti

	$('.jvotesystem').jvslisten();
	$(document).jvslistencore();

	$("#adminForm").on("click",	".bbcodeToolbar img[data-insert]",	function (e) {jVS.insertcode(this);});
	$("#adminForm").on("click",	".bbcodeToolbar img[data-bbcode]",	function (e) {jVS.insertbbcode(this);});
	
	if(($('.jvotesystem').length > 0 && $('.jvotesystem').dp("box")) || $('.update_timestamp.jvs').length > 0) {
		jVS.conf.time = parseInt(jVS.conf.time);
		jVS.updateTimestamps();
		jVS.conf.timeInterval = window.setInterval(function() { jVS.conf.time += 10; jVS.updateTimestamps(); }, 10000);
	}
});

(function($){
	//Z?hler rauf
	$.fn.incr = function (e) {
		return this.text( function (i, txt) {
		return parseInt(txt, 10) + (e || 1);
	});}
	
	//Z?hler runter
	$.fn.decr = function (e) {
		return this.text( function (i, txt) {
		return parseInt(txt, 10) - (e || 1);
	});}
	
	//Zahl ausgeben
	$.fn.intg = function () {
		return parseInt(this.html());
	}
	
	//DataParent (gets data value of parent (if exisiting)
	$.fn.dp = function (e) {	
		if ($(this).attr("data-"+e) != undefined) {
			return $(this).attr("data-"+e);
		} else {
			return $(this).closest("[data-"+e+"]").attr("data-"+e) || false;
		}
	}
	//Loading Spinner
	$.fn.spin = function(opts) {
		this.each(function() {
			var $this = $(this),data = $this.data();

			if (data.spinner) {
			  data.spinner.stop();
			  delete data.spinner;
			}

			if (opts !== false) {
			  data.spinner = new jVS_Spinner($.extend({color: $this.css('color')},{lines:14,length:17,width:19,radius:40,trail:91,speed:1.7,zIndex:9998}, opts)).spin(this);
			}
		});
		return this;
	}
	
	$.fn.smallspin = function(opts) {
		this.each(function() {
			var $this = $(this),data = $this.data();

			if (data.spinner) {
			  data.spinner.stop();
			  delete data.spinner;
			}

			if (opts !== false) {
			  data.spinner = new jVS_Spinner($.extend({color: $this.css('color')},{lines:10,length:0,width:4,radius:5,trail:60,speed:1.0,zIndex:99999}, opts)).spin(this);
			}
		});
		return this;
	}
	
	$.fn.range = function() {
		var that = this[0];
		var start;
		var end;
		var text;
		var laenge;
		var totext = that.value;
		var totlen = that.value.length;
		if ( document.selection != undefined ) { //IE-Voodoo!
			var range = document.selection.createRange();
			text = range.text;
			laenge = range.text.length;
			var stored = range.duplicate();
			stored.moveToElementText(that);
			stored.setEndPoint('EndToEnd', range );
			start = stored.text.length - laenge;
			end = start + laenge;
		} else if (document.getSelection != undefined) {
			start = that.selectionStart;
			end = that.selectionEnd;
			laenge = end-start;
			text = that.value.substring(start, end);
		} else {
			alert("Your Browser doesn't support text selections! STOP!");
		}
		return {'start':start,'end':end,'text':text,'len':laenge,'totlen':totlen,'totext':totext};
	}
	$.fn.setrange = function(start,end) {
		var that = this[0];
		this.focus();
		if ( document.selection != undefined ) { //IE-Voodoo!
			var range = document.selection.createRange();
			range.moveToElementText(that);
			range.moveStart('character', start);
			range.moveEnd('character', - that.value.length + end);
			range.select();
		} else if (document.getSelection != undefined) {
			that.selectionStart = start;
			that.selectionEnd = end;
		} 
	}
	$.fn.insertcode = function(obj, bbc, input) {
		input = input || "";
		bbc = bbc || ["",""];
		this.focus(); //Important!
		this[0].value = obj.totext.substring(0,obj.start) + bbc[0] + ( input || obj.text ) + bbc[1] + obj.totext.substring(obj.end,obj.totlen);
		if (bbc[0] === "") obj.end = obj.start;
		this.setrange( obj.start + bbc[0].length + input.length , obj.end + bbc[0].length+input.length );
		this.trigger("keyup");
	}
	$.fn.totalOuterHeight = function(chk) {
		var total = 0;
		that = chk ? this.filter(":visible") : this;
		that.each(function(i, el) {
			total += $(el).outerHeight(true);
		});
		return total;
	};
	
	$.fn.jvslistencore = function() {
		$(this).on("mouseenter mouseleave",".jvs_avatar[data-u], .jvotesystem [data-u], #jvotesystem [data-u]",function (e) { e.type[5]=="l" ? window.clearTimeout(jVS.timeout) : jVS.loadUserInfo($(this));});
		$(this).on("mouseenter mouseleave",".jvotesystem [data-cid], #jvotesystem [data-cid]",function (e) { e.type[5]=="l" ? window.clearTimeout(jVS.timeout) : jVS.loadCategoryInfo($(this));});
		$(this).on("mouseenter mouseleave","[data-jvs_tooltip]",function (e) { e.type[5]=="l" ? window.clearTimeout(jVS.timeout) : jVS.loadTooltip($(this));});
		$(this).on("click",	".jvotesystem .showMoreText, .jvs_showMoreText", function (e) {jVS.showMoreText($(this));e.preventDefault();});
	}
	
	$.fn.jvslisten = function(forceLoad) {
		if (this.length === 0) return;
		
		$.each(this, function(key, vs){
			vs = $(vs);
			if(!vs.dp("listened") && (vs.dp("box") || forceLoad === true)) {
				vs.attr("data-listened", "1");
				
				vs.on("click",	"a.vote",							function (e, par) { if(vs.dp("mode") == "facebookVoting" && par == undefined) { console.log("Nur via FB")  } else jVS.vote(this);												e.preventDefault();});
				vs.on("click",	"a.novote",							function (e) {																e.preventDefault();});
				vs.on("click",	".answericons a.trash",			function (e) {jVS.removeanswer(this);										e.preventDefault();});
				vs.on("click",	".comment a.trash",					function (e) {jVS.removecomment(this);										e.preventDefault();});
				vs.on("click",	".answericons a.report",			function (e) {jVS.reportanswer(this);										e.preventDefault();});
				vs.on("click",	".comment a.report",				function (e) {jVS.reportcomment(this);										e.preventDefault();});
				vs.on("click",	".answericons a.state",			function (e) {jVS.changeanswerstate(this);									e.preventDefault();});
				vs.on("click",	".comment a.state",					function (e) {jVS.changecommentstate(this);									e.preventDefault();});
				vs.on("click",	"a.userlist",						function (e) {jVS.loaduserlist(this);										e.preventDefault();});
				vs.on("click",	"a.comment",						function (e) {jVS.commentopenclose(this);									e.preventDefault();});
				vs.on("submit",	".newanswerbox form",				function (e) {jVS.addanswer(this);											e.preventDefault();});
				vs.on("submit",	".newcommentbox form",				function (e) {jVS.addcomment(this);											e.preventDefault();});
				vs.on("click",	".bbcodeToolbar img[data-insert]",	function (e) {jVS.insertcode(this);											});
				vs.on("click",	".bbcodeToolbar img[data-bbcode]",	function (e) {jVS.insertbbcode(this);										});
				vs.on("click",	"input[name=reset]",				function (e) {$(this).closest('form').find('textarea').trigger('text'); e.preventDefault(); });
				vs.on("click",	"a.barchart.inactive,a.piechart.inactive",			function (e) {jVS.loadcharts(this);											});
				vs.on("click",	"a.barchart.active,a.piechart.active",function (e) {jVS.leavecharts(this);										});
				vs.on("click",	".commentsnavi a[data-p]",			function (e) {jVS.commentgo($(this));e.preventDefault();});
				vs.on("click",	".answersnavi a[data-p]",			function (e) {jVS.go($(this));e.preventDefault();});

				vs.on("mouseenter",	".vote-down.reset",					function (e) {$(this).addClass("resetvotes"); $(this).find(".operator").html("–"); });
				vs.on("mouseleave",	".vote-down.reset",					function (e) {$(this).removeClass("resetvotes"); $(this).find(".operator").html("+");});
				vs.on("click",		".vote-down.reset",					function (e) {jVS.resetvotes(this);	});
				vs.one("change",	"input[type=radio]",					function (e) {jVS.getlang(this,function(el) {jVS.votebutton(el);});});
								
				vs.on({
					text: 	function() {
								if(!jVS.trigger(this, "onTextareaText", "bug", $(this))) {
									$(this).val($(this).data("start")).data("stored","").trigger('keyup');
									$(this).closest('form').find(".text, .comment-text").siblings(":not([data-nohide=true])").hide();
								}
							},
					focus: 	function() {
								if($(this).hasClass("needLogin")) {
									
								} else {
									if (!$(this).data("stored")) {
										$(this).val('').data("stored","stored");
									}
									$(this).closest('form').find("div").fadeIn();
								}
							},
					blur: 	function() {
								if ($(this).val()=='') {
									var cur = $(this);
									jVS.textareaResetTimer = window.setTimeout(function() {
										/*cur.val(cur.data("start")).data("stored","");
										cur.closest('form').find(".text, .comment-text").siblings(":not([data-nohide=true])").hide();*/
										cur.trigger("text");
									}, 200);
								}
							},
					keyup: 	function() {
								jVS.resizetextarea(this);
							}
				}, ".newanswerbox textarea, .newcommentbox textarea");
				vs.on("click", ".needLogin", function() {jVS.error(jVS.translateStr("NeedToLogin"));});
				
				/*if(vs.dp("mode") == "facebookVoting") {
					vs.on("click", "", function() {
						if(!jVS.conf.fb.valid)
							window.open(jVS.conf.fb.loginUrl, "jVSFBLogin", "width=640,height=300,left=100,top=200");
					});
					vs.on("mouseenter mouseleave","a.vote, a.novote",function (e) { e.type[5]=="l" ? window.clearTimeout(jVS.timeout) : jVS.loadFBVoteButton($(this));});
				}*/
				
				vs.find(".searchfield input").val('');
				vs.on("keyup keydown", ".searchfield input", function(e) { jVS.searchInput($(this), (e.which == 13)); })
			
				if(!forceLoad && !jVS.trigger(vs, 'handleConstructAnswer')) {
					var id = parseInt(vs.dp("box"));
					if(jVS.conf.polls[id].cur_answer != null) 
						window.setTimeout(function() {
							jVS.scrollto(vs.find('.answer[data-a=' + jVS.conf.polls[id].cur_answer + ']'));
						}, 300);
				}
				
				if(!forceLoad) jVS.trigger(vs, "construct", vs);
			} else if(!vs.dp("listened")) {
				vs.on('click', 'p.task_msg', function() {
					var el = $(this).next('div');
					if(el.is(':visible')) el.slideUp();
					else el.slideDown();
				});
			}			
		});
		
		return this;
	}
})(jQuery);

function jVS_constructor () {

	var me = {}, //Init Object!
		reqcount = 0, //Number of running AJAX-Requests
		$ = jQuery, //Alias the jQuery-Object
		ajaxerror = '<div style="font-size:15px;font-weight:bold;color:#f00;text-align:center;line-height:30px;padding: 0 20px;border:4px solid #f00;background:#000;">jVoteSystem Debug: AJAX failed!<br/>Deleting remaining requests!</div>',
		toomany = '<div style="font-size:15px;font-weight:bold;color:#f00;text-align:center;line-height:30px;padding: 0 20px;border:4px solid #f00;background:#000;">jVoteSystem Debug: Too many requests!<br/>Deleting remaining requests!</div>',
		ajaxqueue = [];
		
	me.conf;
	me.jLang;
	me.loadedCount = 0;
	me.ajax = function (options) {
	
		if (options == undefined) {
			ajaxqueue = [];
			return;
		}
		if (ajaxqueue.length > 10) {
			me.ajax();
			me.error(toomany);
			return;
		}
		var oldcomplete = options.complete;
		options.complete = function (request, status)
		{
			ajaxqueue.shift ();
			if (oldcomplete) oldcomplete(request, status);
			if (ajaxqueue.length > 0) $.ajax (ajaxqueue[0]);
		};
		ajaxqueue.push (options);
		if (ajaxqueue.length === 1) $.ajax (options);
	}

	me.req = function(data, callback, cerror, longtime) {
		if(longtime == undefined) var longtime = false;
		if(!longtime) me.load(data.box);
		me.getlang();
		
		var def = {"paramView":$("[data-box="+data.box+"]").data("view"), "admin": (me.conf.admin) ? 1 : 0, "lang": me.conf.lang};
		def[me.conf.token]=1;
		
		$.extend(def,data);
		
		var ax = longtime ? $ : me;
		
		ax.ajax({
			url: me.conf.site+"components/com_jvotesystem/ajax.php",
			cache: false,
			type: "POST",
			data: def,
			dataType: "json",
			complete: function() 	{ if(!longtime) me.done(data.box);},
			success: function(json)	{
				if(json == undefined) {
					if(cerror != undefined) cerror(); 
					return;
				}
				
				if(json.cur_time != undefined) {
					window.clearInterval(me.conf.timeInterval);
					me.conf.time = json.cur_time;
					me.conf.timeInterval = window.setInterval(function() { me.conf.time += 10; me.updateTimestamps(); }, 10000);
				}
				
				callback(json);
			},
			error: function()		{ if(cerror != undefined) cerror(); /*me.ajax();me.error();*/}
		});
	}
	
	me.convertToHex = function(rgb) {
		if(rgb == "transparent" || rgb == "rgba(0, 0, 0, 0)") return "transparent"; 
		if(rgb.substr(0, 1) == "#") return rgb;
		rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		 return "#" +
		  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
		  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
		  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
	}
	
	me.isExecutionTimeSet = function(callback) {
		if(!me.conf.executionTime) {
			me.conf.executionTime = 60; //Start with 60 seconds
			var check = function() {
				jVS.req({"task": "testMaxExecutionTime", "stime": me.conf.executionTime}, function(result) {
					if(result.success) callback(me.conf.executionTime - 4);
					else callback(false);
				}, function() {
					me.conf.executionTime -= 5;
					if(me.conf.executionTime < 5) callback(false);
					else check();
				}, true);
			}
			check();
		} else callback(me.conf.executionTime);
	}
	
	me.trigger = function(el, fn /*, args */) {
		try {
			var args = Array.prototype.slice.call(arguments).slice(2);//Array.prototype.slice.call(arguments).splice(2);
			var context = window["jVS"][me.gettemplate(el)];
			if(context == undefined) return false;
			var namespaces = fn.split(".");
			var func = namespaces.pop();
			for(var i = 0; i < namespaces.length; i++) {
				context = context[namespaces[i]];
			}
			if($.isFunction(context[func])) {
				return context[func].apply(this, args);
			}
		} catch(err) {
			console.log(err);
			return false;
		}
		return false;
	}
	
	me.getlang = function(el,fn) {
		if (me.jLang == undefined) { //load language variables once!
		
			var def = {"task": "getlang", "lang": me.conf.lang};
			def[me.conf.token]=1;
			
			me.ajax({
				url: me.conf.site+"components/com_jvotesystem/ajax.php",
				cache: true,
				async: false,
				type: "POST",
				dataType: "json",
				data: def,
				success: function(json){
							me.jLang = json;
							if ($.isFunction(fn)) fn(el);
						}
			});
		} else {
			if ($.isFunction(fn)) fn(el);
		}
	}
	
	me.translateStr = function(str) {
		me.getlang();
		$.each(me.jLang, function(key,item){ 
			if(key.toLowerCase() == str.toLowerCase()) { str = item; return false; }
		});
		return str;
	}
	
	me.zebraDialogBox;
	me.info = function(data) {
		me.getlang();
		me.tip.el.hide();
		if(data.buttons) for(i = 0; i < data.buttons.length; i++) data.buttons[i] = me.translateStr(data.buttons[i]);
		me.zebraDialogBox = new $.Zebra_Dialog(data.message, {
			'type':     data.type,
			'title':    data.title,
			'overlay_opacity': 0.1,
			'buttons':  data.buttons,
			'onClose':  data.callback,
			'position': data.position,
			'auto_close': data.autoclose,
			'modal': data.modal, 
			//'overlay_close' : false,
			'width': data.width
		});
		return me.zebraDialogBox;
	}
	
	me.notify = function(data) {
		var el = $("div.ZebraDialog:not(:has(div.jvotesystem))").last();//LOL
		var translate = parseInt($(el).css('top'))+$(el).height() || 0;
		me.info({'type':'confirmation','title':false, 'message':data, 'buttons':false, 'position':['right - 20', 'top + ' + ( 20 + translate )], 'autoclose':2000, 'modal':false});
	}
	
	me.error = function(data) {
		me.info({'type':data?'error':false,'title':false, 'message':data||ajaxerror, 'button':['OK']});
	}
	
	//hilfsfunktion zum zeigen/verstecken des loading-gifs
	me.load = function (box) { 
		me.loadedCount++;
		if(me.loadedCount == 1) {
			$("[data-box="+box+"]").spin();
			$("body").addClass("jvs-loading");
		}
	}

	me.done = function (box) {
		me.loadedCount--;
		if(me.loadedCount == 0) {
			$("[data-box="+box+"]").spin(false);
			$("body").removeClass("jvs-loading");
		}
	}
	
	me.lock = function (el) {
		/*var elem = $(el).closest("div.jvotesystem");
		
		if (elem.length !== 1) {
			console.log("me.lock: number of elements: " + elem.length);
			return true;
		}
		
		if (elem.data('locked') !== 1) {
			elem.data('locked',1);
			return false;
		} else {
			console.log("Locked!");
			return true;
		}*/ //RETHINK
	}
	
	me.unlock = function (el) {
		/*var elem = $(el).closest("div.jvotesystem");
		
		if (elem.length !== 1) {
			console.log("me.lock: number of elements: " + elem.length);
			return true;
		}
		
		elem.removeData('locked');*/ //RETHINK
	}
	
	me.gettemplate = function (el) {
		var elem = $(el).closest(".jvotesystem").first();
		if(elem.dp('template')) return elem.dp('template');
		return elem.attr('class').replace(/^.*jvs-(\w+)/g,"$1");
	}
	
	me.round = function (num, dec) {
		if (isNaN(num)) return 0;
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}
	
	me.jsbarchart = function(boxEl, values, labels, ids, box, code, count, colors) { 
		me.getlang();
		boxEl.find('.newanswerbox, .makenew').hide();
		boxEl.find('.barchart').addClass("active").removeClass("inactive");
		code = decodeURIComponent(code.replace(/\+/g, '%20'));
		
		if(!me.trigger(boxEl, "barChart", boxEl, values, labels, ids, box, code, count, colors)) {
			var frame = $('<div class="jsbarchart"></div>').appendTo(boxEl.find(".pagebox").html(''));
			var total = 0, max = 0, state = true;
			
			for (var i = 0, ii = values.length; i < ii; i++) {
				total += values[i];
				if (values[i] > max)  max = values[i];
			}
			
			var page = boxEl.dp("lastviewpage");
			var startCount = count;
			if(page >= 2) startCount = count*2;
			for (var i = 0, ii = values.length; i < ii; i++) {
				var wrap = $('<div class="barbox" style="display:'+(i > (startCount-1) ? 'none':'block')+'"></div>');
				$('<p><span>'+labels[i]+' </span><span>('/*+me.round((values[i]/total)*100,2)*/+'0%)</span><div class="barholder"></div></p>').appendTo(wrap);
				var bars = $('<div>')
					.html('<a style="background-color:'+(colors[i]||'#000000')+';"><span>' + values[i] + '</span></a>')
					.appendTo(wrap.find('.barholder'));
				if (isNaN(values[i]/max) === false && values[i] > 0) {
					bars.delay(500).animate({width: values[i]/max*100+"%"}, {easing: "linear", duration: "slow", step: function (now, fx) {$(fx.elem).parent().prev().children("span:last").html('('+me.round(now/total*max,0)+'%)')}});
					if(values[i]/max*100 >= 10) bars.find('span').delay(800).fadeIn();
				}
				$(wrap).appendTo(frame);
			}
			
			frame.after(code);
			
			frame.next().find("a.scaling").click(function() {
				var bars = frame.find("div .barholder div");
				for (var i = 0, ii = values.length; i < ii; i++) {
					if ($(bars[i]).width() === 0 ) continue;
					if (state == false) {
					$(bars[i]).animate({width: values[i]/max*100+"%"}, "slow");
					} else if (state == true) {
					$(bars[i]).animate({width: values[i]/total*100+"%"}, "slow");
					}
				}
				(state == false) ? $(this).html(me.jLang.relative) : $(this).html(me.jLang.absolute);
				(state == false) ? state = true : state = false;
			});
			
			if (values.length > (count-1)) {
				frame.next().on("click","a.showall",function() {
					$("div.barbox:not(:visible):lt(" + count + ")",frame).slideDown("slow");//LOL
					if ($("div.barbox:not(:visible)",frame).length === 0) $(this).attr("class","hideall").text(me.jLang.hideall);
				}).on("click", "a.hideall", function() {
					$("div.barbox:gt(" + (count-1) + ")",frame).slideUp("slow");
					$(this).attr("class","showall").text($(code).find("a.showall").dp("short") ? me.jLang.load_next_short : me.jLang.load_next.replace("%STEPS%", count));
				});
			}
		}
	}
	
	me.googleapiloaded = false;
	me.googleapiloading = false;
	me.jspiechart = function(boxEl, values, labels, id, colors) {
		boxEl.find('.newanswerbox, .makenew').hide();
		boxEl.find('.piechart').addClass("active").removeClass("inactive");
		var frame = $('<div class="jspiechart" id="jvs_piechart_' + boxEl.dp('uniqid') + '"></div>').appendTo(boxEl.find(".pagebox").html(''));
		var width = frame.width();
		var height = width * (400/725);
		
		frame.animate({"height": height});
		
		frame.bind("googleLoaded", function() { 
			var data = new google.visualization.DataTable();
			data.addColumn("string", "Answer");
			data.addColumn("number", "Votes");
			
			for (var i = 0, ii = values.length; i < ii; i++) {
				data.addRow([labels[i],values[i]]);
			}
			
			var ready = false;
			
			var options = {	"width":"100%",
							"height":height,
							chartArea:{width:"95%",height:"80%"}, 
							backgroundColor: me.convertToHex(boxEl.css("backgroundColor")), 
							legend: {
								textStyle: { 
									color: me.convertToHex(frame.closest(".jvotesystem").css("color"))
								}
							},
							"colors": colors
						};
			var drawChart = function() { ready = false; var chart = new google.visualization.PieChart($("#jvs_piechart_"+boxEl.dp('uniqid'))[0]); google.visualization.events.addListener(chart, 'ready', function() { ready = true; }); chart.draw(data, options); };
			var resizeTimer; $(window).resize(function () { if(!ready) return; window.clearTimeout(resizeTimer); $("#jvs_piechart_"+boxEl.dp('uniqid')).html(""); resizeTimer = window.setTimeout(function() {drawChart();}, 200); });
			drawChart();
			
			$(this).unbind("googleLoaded");
		});
		
		if(!me.googleapiloaded && !me.googleapiloading) {
			me.googleapiloading = true;
			
			var script = document.createElement('script');
			script.src = 'http://www.google.com/jsapi?callback=jVS.googleLoadCallback';
			script.type = 'text/javascript';
			document.getElementsByTagName('head')[0].appendChild(script);
		}
		
		if(me.googleapiloaded && !me.googleapiloading) frame.trigger("googleLoaded");
	};
	
	me.googleLoadCallback = function() {
		me.googleapiloading = false;
		me.googleapiloaded = true;
		google.load("visualization", "1", {packages:["corechart"],callback: function() {$(".jvotesystem .jspiechart").trigger("googleLoaded");}});
	}
	
	//Diagramm-Daten laden
	me.loadcharts = function(el) {
		var box = $(el).dp("box");
		var boxEl = $(el).closest('.jvotesystem');
		
		if($(el).hasClass("chart"))
			var mode = $(el).hasClass("piechart") ? "pie" : "bar";
		else {
			var mode = boxEl.dp("chart");
			if(!mode) mode = "bar";
			el = boxEl.find(".chart." + mode + "chart");
			boxEl.find(".chart").fadeIn("slow");
		}
		if(!$(el).hasClass("active")) {
			me.req({"task":"loadCharts", "box":box, "mode":mode, "template": me.gettemplate(boxEl)},
				function(j){
					me.tip.el.hide(); //Fix: Tooltip ausblenden
					if(j.erfolg) {
						if(j.allowed) {
							if (el != undefined ) {
								$(el).siblings().addClass("inactive").removeClass("active");
								$(el).removeClass("inactive").addClass("active"); 
								if($(el).closest('.jvotesystem').length > 0)
									if ($(el).closest('.jvotesystem').offset().top < $(window).scrollTop()) me.scrollto($(el).closest('.jvotesystem'));
							}
							if (j.mode == 'pie') me.jspiechart(boxEl, j.values, j.answers, j.box, j.colors);
							else if (j.mode == 'bar') me.jsbarchart(boxEl, j.values, j.answers, j.ids, j.box,j.code,j.count, j.colors);
						} else {
							me.error("InfoLeave");
							me.leavecharts(el);
						}
					} else {
						me.error(j.error);
					}
				}
			);
		} else {
			me.error("Info");
			me.leavecharts(el);
		}
	}
	
	me.leavecharts = function(el) {
		var elem = $(el);
		var box = elem.dp("box");
		var boxobj = elem.closest(".jvotesystem");
		
		if(!me.trigger(el, "leavecharts", elem, box, boxobj)) {
			if (elem != undefined && elem.data != undefined ){
				elem.addClass("inactive").removeClass("active");
			}
			$("div[data-box="+box+"] div.newanswerbox, div[data-box="+box+"] .makenew").fadeIn("slow");
			me.go(boxobj, boxobj.dp("lastviewpage"),0);
		}
	}
	me.vote = function (el) {
		me.trigger(el, "beforeVoting");
		
		var anbox = $(el).closest(".answer");
		
		var box = anbox.dp('box')
		var answer = anbox.dp('a');
		var template = me.gettemplate(anbox);
		
		var count = anbox.find(".votecount");
		var counttext = anbox.find("p.votecounttext");
		var left = anbox.closest(".jvotesystem").find("span.count");
		
		if ( left.intg() <= 0) return false;
		
		//Felder schon mal rauf bzw. runterz?hlen
		count.incr();
		left.decr();
		
		me.req(
			{"task":"vote", "box":box, "answer":answer,'template':template}, function(d) {me.voted(d,el);}
		);
	}
	
	me.voted = function(j, el) { 
		var anbox = $(el).closest(".answer");
		
		var box = anbox.dp('box')
		var answer = anbox.dp('a');
		var template = me.gettemplate(anbox);
		
		var count = anbox.find("span.votecount");
		var counttext = anbox.find("p.votecounttext");
		var left = anbox.closest(".jvotesystem").find("span.count");

		var vote = anbox.find("a.vote");
		
		if(me.trigger(el, "voteResponse", anbox, box, answer, j)) return;		
		
		if(j.error && j.captcha == 0) {
			me.info({'type':'error','title':me.jLang.error, 'message':j.error, 'button':['OK','Cancel'], callback: 
				function() {
					me.showcaptcha({"task":"vote", "box":box, "answer":answer, "exec":function(d) {me.voted(d,el);}});
				}
			});
		} else if(j.captcha == 0) {
			me.showcaptcha({"task":"vote", "box":box, "answer":answer, "exec":function(d) {me.voted(d,el);}});
			
			count.decr();
			left.incr();
		} else if(j.erfolg == 1) {
			if (j.disableanswer) {
				$(vote).attr({"data-disabled":1});
				vote.addClass("novote");
				vote.removeClass("vote");
			}
			
			if (j.thankyou_title) {
				me.info({'type':'information','title':j.thankyou_title, 'message':j.thankyou_message, 'button':['OK']});//Wenn aktiviert, danke-meldung
				vote.addClass("novote");
				vote.removeClass("vote");
			} else if(j.voted_message) {
				me.notify(j.voted_message);
			}
			
			if (j.make_userlist && me.gettemplate(el) !== "module" && anbox.find('div.count a.userlist').length === 0) {
				$('<a class="icon userlist"></a>').hide().prependTo(anbox.find('div.count')).fadeIn("slow");
			}
			
			if (j.userVotes > 0) {
				anbox.find(".ownvotes").html(j.userVotes);
				anbox.find(".vote-down:hidden").fadeIn();
			}
			
			if (j.totalVotes === 1) {
				counttext.html(me.jLang.votessingular);
			} else {
				counttext.html(me.jLang.votesplural);
			}

			if (j.goto_chart) {
				me.loadcharts(el);//Wenn aktivert, zu Diagramm gehen..
			} else if (j.goto_box) {
				me.go(anbox,j.page);
			} else if (j.goto_page) {
				location.href = j.redirect_page;
			} else {
				//Tooltip aktualisieren
				vote.attr("data-jvs_tooltip", j.tooltip);
				if(!me.trigger(el, "showAnswerTooltip", anbox, j.tooltip))
					me.tip.show(vote, '<div class="contentbox">' + j.tooltip + '</div>');
			}

			//TotalVotes aktualisieren -nur wenn count nicht h?her
			if(count.length !== 0 && j.totalVotes && count.intg() < j.totalVotes) {
				count.html(j.totalVotes);
			}
			if (j.leftVotes === 0) {
				anbox.closest(".jvotesystem").find("a.vote").attr("class", "novote");
				anbox.closest(".jvotesystem").find(".votebutton").hide();
			}
			if(j.leftVotes && left.intg() > j.leftVotes) left.html(j.leftVotes);
		} else {
			me.error(j.error);
			
			count.decr();
			left.incr();
			
			if(j.leftVotes === 0) me.go(anbox);//maybe?
				
			//TotalVotes aktualisieren
			if(count.length !== 0 && j.totalVotes) count.html(j.totalVotes);
		}
		
		me.trigger(el, "voteChanged", anbox);
	}
	
	me.votebutton = function (el) {
		me.getlang();
		
		var boxobj = $(el).closest(".jvotesystem");
		var pagebox = boxobj.find(".pagebox");
		var votebutton = $("<div class=\"votebutton\"><div>"+me.jLang.vote+"</div></div>").click(function() {me.vote(boxobj.find("input:checked"))});
		votebutton.insertAfter(pagebox).slideDown("slow");
	}

	me.resetvotes = function(el) {
		var anbox = $(el).closest(".answer");
		
		var box = anbox.dp('box')
		var answer = anbox.dp('a');
		
		var count = anbox.find("span.votecount");
		var left = anbox.closest(".jvotesystem").find("span.count");
		var ownvotes = $(el).find(".ownvotes").intg();
		
		var steps = parseInt($(el).dp('steps'));
		if(!steps) steps = ownvotes;

		var vote = anbox.find("a.votingbutton");
		
		//Alles zählen & Bubble entfernen
		ownvotes = ownvotes - steps; if(ownvotes < 0) return;
		var countInt = count.intg() - steps;
		count.html(countInt);
		var leftInt = left.intg();
		if(left.length == 0) leftInt = 0;
		left.html(leftInt + steps);
		
		if(ownvotes == 0) $(el).fadeOut();
		$(el).find(".ownvotes").html(ownvotes);
		
		//Endbox Count einblenden
		anbox.closest('.jvotesystem').find('.leftMessage .leftStateMsg').hide();	
		anbox.closest('.jvotesystem').find('.leftMessage .leftCount').show();	
		
		me.req(
				{"task":"resetvotes", "box":box, "answer":answer, "reset": steps},
				function(j){
					if(j.erfolg === 1) {
						vote.attr("data-jvs_tooltip", '<div class="contentbox">' + j.tooltip + '</div>');
						if(me.trigger(el, "votesResetted", anbox, vote, countInt, leftInt)) return;	
						
						vote.removeClass("novote");
						vote.addClass("vote");
						
						if(countInt == 0) anbox.find(".icon.userlist").fadeOut("slow", function() {$(this).remove();});
						
						if(leftInt == 0) me.go(anbox, anbox.dp("lastviewpage"));
						
						me.trigger(el, "voteChanged", anbox);
					} else {
						me.error(j.error);
					}
				}
			);
	}
	//Seite vorw?rts
	me.go = function (anbox, page, currentpage, aid) {
		me.getlang();
		var boxobj = anbox.closest("div.jvotesystem");
		if (me.lock(boxobj)) return;
		var box = anbox.dp("box");
		page = anbox.data("p") || page;
		
		var pagebox = boxobj.find("div.pagebox");
		var pageobj = pagebox.find("div[data-p]").last();
		var template = me.gettemplate(boxobj);
		currentpage = currentpage != undefined ? currentpage : pageobj.data("p") || boxobj.dp("lastviewpage");
		
		if(!me.trigger(anbox, "go", boxobj, box, page, pagebox, pageobj, template, currentpage)) {
			var search = boxobj.find('.searchfield input').val();
			if(search == (me.jLang.search+"...")) search = "";
			me.req({"task":"answers", "box":box, "page":page, "currentPage":currentpage,'template':template, 'q': search, 'aid': aid},function(j){
				me.unlock(boxobj);
				if(j.erfolg === 1) {
					
					boxobj.attr("data-lastviewpage", j.page);
					if(j.page === j.currentPage || pagebox.find("div.jsbarchart,div.jspiechart").length > 0 || pagebox.children().length > 1) {
						
						var oldp = pagebox.children();
						var newp = pagebox.prepend(j.code).find("div[data-p]");
						
						var after = function() {
							pagebox.height("auto");
						}

						pagebox.animate({height: newp.height()}, "slow", function() {after();});
						oldp.remove();
						newp.hide().css({'position':'relative'}).fadeIn("slow");						
					} else {
						var oldp = pageobj;

						pagebox.css("overflow", "hidden");
						pagebox.prepend(j.code);
						
						var newp = oldp.prev();
						var width = oldp.width();
						var height = newp.height();
						var direction = (j.currentPage > j.page) ? -1 : 1;
						
						//Neue Seite - left anpassen
						newp.css('left',width*direction);

						//Hintergrundbox festsetzen und anpassen
						pagebox.animate({height: height}, 800);
						
						//Alte Seite festsetzen und rausfahren
						oldp.animate({
							height: height,
							left: -width*direction
						}, 800);
						
						//Neue Seite reinfahren
						newp.animate({left: 0}, 810, function() {
							oldp.remove();
							newp.css({'position':'relative','height':'auto'});
							pagebox.height("auto");
							pagebox.css("overflow", "visible");
						});
					}
					
					//Scrollen
					if(j.scrollToAnswer) me.scrollto( pagebox.find('.answer[data-a=' + j.scrollToAnswer + ']') );
					else if (pagebox.offset().top < $(window).scrollTop()) me.scrollto(pagebox);
					
					me.trigger(boxobj, "goLoading", pagebox);
					if(j.banner_code != undefined) me.loadBannerScript(box, j.banner_code);
				} else {
					me.error(j.error);
				}
			});
		}		
	}
	//Antwort hinzuf?gen
	me.addanswer = function (el) {
		me.getlang();
		
		if(!me.trigger(el, "addanswer", el)) {
			var boxobj = $(el).closest("div.jvotesystem");
			var answer = boxobj.find(".newanswerbox textarea").val();
			if ( answer === boxobj.find(".newanswerbox textarea").data('start') || me.removebb(answer) === "") {me.error(me.jLang.noemptyordefault);return;}
			fixboxobj = $("div.jvotesystem[data-box="+boxobj.dp('box')+"]").first();//for module
			me.lock(fixboxobj);
			me.req({"task":"addAnswer", "box":boxobj.dp('box'), "answer":answer, "template": me.gettemplate(boxobj)},function(j){me.answeradded(j,fixboxobj);});
		}
	}
	
	me.removebb = function (string) {
		var ar = string.split('');
		var check = false;
		var out = '';
		for( var k=0; k<ar.length; k++ ) {
			if (ar[k] === '[') check=true;
			else if (ar[k] === ']') check=false;
			else if (check === false) out += ar[k];
		}
		return out.replace(/^\s+|\s+$/g,"");
	}

	me.answeradded = function (j,boxobj) {
		if(j.error && j.captcha == 0) {
			me.info({'type':'error','title':me.jLang.error, 'message':j.error, 'button':['OK','Cancel'], callback: 
				function() {
					me.showcaptcha({"task":"addAnswer", "box":j.box, "answer":j.text, "exec":function(d) {me.answeradded(d,boxobj);}});
				}
			});
		} else if(j.captcha == 0) {
			me.showcaptcha({"task":"addAnswer", "box":j.box, "answer":j.text, "exec":function(d) {me.answeradded(d,boxobj);}});
		} else if(j.erfolg == 1) { 
			me.trigger(boxobj, "answerAdded");
			
			boxobj.find('.searchfield input').val('');
			me.go(boxobj, j.newPage, undefined, j.answer);
			boxobj.find("span.count").html(j.leftVotes);
			$("[data-box="+j.box+"] .newanswerbox input[name=reset]").trigger('click');
			me.notify(j.success);			
		} else {
			me.error(j.error);
		}
		
		if(j.newSearchKey != undefined) 
			me.search(boxobj, j.newSearchKey);
	}
	
	me.showcaptcha = function (data) {
		me.getlang();
		code = me.jLang.captchaLoading;
		me.info({'type':false,'title':me.jLang.captchaEnterCode, 'message':'<div id="captchahere">'+code+'</div>', 'buttons':['OK','Cancel'], 'width':'339', 
			'callback':function (e) { me.enteredCaptcha(e, data); }});
		$('#recaptcha_response_field').focus();
		showRecaptcha('captchahere', 'red', function() { $('#recaptcha_response_field').keypress(function(e){ if(e.which == 13){ me.enteredCaptcha(me.jLang.ok, data); me.zebraDialogBox.close(); } });});
	}
	
	me.enteredCaptcha = function(e, data) { 
		if (e != me.jLang.ok) return;
		var captcha = $('#recaptcha_response_field').val() || 0; //0 bei leerem captcha-feld gibts sonst js error
		var challenge = $('#recaptcha_challenge_field').val();
		
		if(data.captcha_callback != undefined) {
			data.captcha_callback( {'captcha':captcha,'recaptcha_challenge_field':challenge} );
		} else {
			var exec = data.exec;
			delete data.exec;
			$.extend(data,{'captcha':captcha,'recaptcha_challenge_field':challenge});
			me.req(data,function(j){exec(j);});
		}
	}
	
	//Antwort entfernen
	me.removeanswer = function(el) {
		me.getlang();
		var anbox = $(el).closest("div.answer").css("background-color","#FFE4E1");//Hardcoded CSS
		var boxobj = anbox.closest("div.jvotesystem");
		var count = boxobj.find("span.count");
		me.info({'type':'question','title':me.jLang.titleQuestion, 'message':me.jLang.qremoveanswer, 'buttons':['Yes', 'No'],'callback':function (e) {
			if (e == me.translateStr('Yes')) {
				me.req({"task":"removeAnswer", "box":anbox.dp('box'), "answer":anbox.dp('a'), "template":me.gettemplate(el)},
					function(j){
						if(j.erfolg==1) {
							me.notify(j.success);
							count.html(j.leftVotes);
							
							if(j.leftVotes > 0) {
								//Endbox Count einblenden
								anbox.closest('.jvotesystem').find('.leftMessage .leftStateMsg').hide();	
								anbox.closest('.jvotesystem').find('.leftMessage .leftCount').show();	
							}
							
							if (j.leftVotes > 0 ) boxobj.find("a.novote:not([data-disabled])").addClass("class", "vote").removeClass("class", "novote");

							if( boxobj.find("div[data-a]").length > 1) {
								anbox.animate({height: 0}, "slow", function() {
									//Ränge updaten
									if(anbox.find('.rank').text() != '#')
										$(el).closest('.pagebox').find('.answer .rank').each(function() {
											if($(this).intg() >= anbox.find('.rank').intg()) $(this).decr();		
										});
									anbox.remove();
									boxobj.data("answerremoved",'true');
								});
							} else  me.go(boxobj.find("div[data-p]"));
						} else {
							me.error(j.error);
							anbox.css("background-color","transparent");
						}
					}
				);
			} else {
				anbox.css("background-color","transparent");
			}
		}});
	}

	//Ver?ffentlichungsstatus ?ndern
	me.changeanswerstate = function(el) {
		var anbox = $(el).closest("div.answer");
		me.req({"task":"changePublishStateAnswer", "box":anbox.dp('box'), "answer":anbox.dp('a')},
			function(j){
				if(j.erfolg) {
					me.notify(j.success);
					anbox.find("a.state:first").toggleClass("published unpublished");
					
					//Ränge updaten
					$(el).closest('.pagebox').find('.answer .rank').each(function() {
						if(j.state == 'published') {
							if($(this).intg() >= j.rank) $(this).incr();
						} else {
							if($(this).intg() >= anbox.find('.rank').intg()) $(this).decr();
						}						
					});
					anbox.find('.rank').text(j.rank);
					
					anbox
						.find('a.vote, a.novote')
						.removeClass('vote, novote')
						.addClass(j.votingAllowed ? "vote" : "novote")
						.attr("data-jvs_tooltip", j.tooltip);
				} else {
					me.error(j.error);
				}
			}
		);
	}

	//Antwort reporten
	me.reportanswer = function (el) {
		me.getlang();
		var anbox = $(el).closest("div.answer");
		me.info({'type':'question','title':me.jLang.titleQuestion, 'message':me.jLang.qreportanswer + '<br /> ' + me.jLang.reportAddMessage  +'<textarea id="jVS-report-message" cols=30 rows=2></textarea>', 'buttons':['Send', 'Cancel'],'callback':function (e) {
			if (e == me.translateStr('Send')) {
				me.req({"task":"reportAnswer", "box":anbox.dp('box'), "answer":anbox.dp('a'), "reportMessage":$("#jVS-report-message").val()},
					function(j){
						if(j.erfolg) {
							me.notify(j.success);
							$(el).remove();
						} else {
							me.error(j.error);
						}
					}
				);
			}
		}});
	}

	me.loaduserlist = function (el) {
		var anbox = $(el).closest("div.answer");
		me.req({"task":"loadUserList", "box":anbox.dp('box'), "answer":anbox.dp('a')},
			function(j){
				if(j.erfolg) {
					me.info({'type':false,'title':j.title, 'message':j.code, 'button':['OK'], 'width':460});
				} else {
					me.error(j.error);
				}
			}
		);
	}
	
	me.resizetextarea = function (t) {
		a = t.value.split("\n");
		b=1;
		for (var i = 0; i < a.length; i++) {
			if (a[i].length >= t.cols) b+= Math.floor(a[i].length/t.cols);
		}
		b+= a.length;
		t.rows = b-1;
	}
	
	me.commentopenclose = function(el) {
		var commentbox = $(el).closest("div.answer").find("div.comments");
		if(commentbox.is(":empty")) {
			me.commentgo($(el));
		} else if (commentbox.height() == 0) {
			commentbox.animate({height: commentbox.find("div.commentbox,div.makenew,div.newcommentbox").totalOuterHeight(true)}, "slow", function() {commentbox.height("auto")});
		} else {
			commentbox.animate({height: 0}, "slow");
		}
	}
	
	//Seite vorw?rts
	me.commentgo = function(el, page) {
		var box = $(el).dp('box');
		var answer = $(el).dp('a');
		page = $(el).data("p") || page;

		var anbox = $(el).closest(".answer"); 
		var currentpage = anbox.find("div[data-cp]").data("cp") - 0 || 0; 
		var template = me.gettemplate(anbox); 
		if (me.lock(el)) return;
		me.req(
			{"task":"loadComments", "box":box,"answer":answer,"page":page,"currentpage":currentpage, "template":template},
			function(j){
				me.unlock(el);
				if(j.erfolg === 1) {
					if(!me.trigger(el, "commentGo", anbox, box, answer, j)) {
						var com = anbox.find("div.comments");
						if (currentpage === 0) {
							if(!me.trigger(el, "commentGoFirstLoaded", anbox, box, answer, j)) {
								com.height(0).prepend(j.code).animate({height: com.find(".newcommentbox,[data-cp]").totalOuterHeight(true) }, "slow", function() {com.height("auto");});
							}
						} else {
							var cbox = com.find(".commentbox").prepend(j.code),
								pages = cbox.find("[data-cp]"),
								oldp = pages.last(),
								newp = pages.first();
							cbox.animate({height: newp.height()}, "slow", function() {cbox.height("auto");});
							oldp.remove();
							newp.hide().css({'left':0, 'position':'relative'}).fadeIn("slow");
						}
					}
				} else {
					me.error(j.error);
				}
			}
		);
	}
	
	//Kommentar hinzuf?gen
	me.addcomment = function (el) {
		var anbox = $(el).closest("div.answer");
		var comment = anbox.find(".newcommentbox textarea").val();
		if ( comment === anbox.find(".newcommentbox textarea").data('start') || me.removebb(comment) === "") {me.error(me.jLang.noemptyordefault);return;}
		if (me.lock(el)) return;
		me.req({"task":"addComment", "box":anbox.dp('box'), "answer":anbox.dp('a'), "comment":comment},function(j){me.commentadded(j,anbox);});
	}

	me.commentadded = function (j,anbox) {
		if(j.error && j.captcha == 0) {
			me.info({'type':'error','title':'get.title', 'message':j.error, 'button':['OK','Cancel'], callback: 
				function() {
					me.showcaptcha({"task":"addComment", "box":j.box, "answer":j.answer, "comment":j.comment, "exec":function(d) {me.commentadded(d,anbox);}});
				}
			});
		} else if(j.captcha == 0) {
			me.showcaptcha({"task":"addComment", "box":j.box, "answer":j.answer, "comment":j.comment, "exec":function(d) {me.commentadded(d,anbox);}});
		} else if(j.erfolg == 1) {
			me.unlock(anbox);
			me.commentgo(anbox, j.page);//if(j.page != undefined) 
			anbox.find(".newcommentbox textarea").trigger('text');//Reset comment box
			$("[data-box="+j.box+"] [data-a="+j.answer+"] a.comment span").incr();//works for module, too!
			me.notify(j.success);
		} else {
			me.unlock(anbox);
			me.error(j.error);
		}
	}
	
	//Kommentar entfernen
	me.removecomment = function(el) {
		me.getlang();
		var combox = $(el).closest("[data-c]").css("background-color","#FFE4E1");
		me.info({'type':'question','title':me.jLang.titleQuestion, 'message':me.jLang.qremovecomment, 'buttons':['Yes', 'No'],'callback':function (e) {//hardcoded lang
			if (e == me.translateStr('Yes')) {
				me.req({"task":"removeComment", "box":combox.dp('box'), "comment":combox.dp('c')},
					function(j){
						if(j.erfolg==1) {
							me.notify(j.success);
							$("[data-box="+j.box+"] [data-a="+j.answer+"] a.comment span").decr();//works for module, too!
							if(combox.siblings("div[data-c]").length > 0) {
								combox.animate({height: 0}, "slow", function() {combox.remove();});
							} else { 
								me.commentgo(el, $(el).dp("cp"));
							}
						} else {
							me.error(j.error);
							combox.css("background-color","transparent");
						}
					}
				);
			} else {
				combox.css("background-color","transparent");
			}
		}});
	}
	
	//Ver?ffentlichungsstatus ?ndern
	me.changecommentstate = function(el) {
		var combox = $(el).closest("[data-c]");
		me.req({"task":"changePublishStateComment", "box":combox.dp('box'), "comment":combox.dp('c')},
			function(j){
				if(j.erfolg) {
					me.notify(j.success);
					combox.find("a.state").toggleClass("published unpublished");
				} else {
					me.error(j.error);
				}
			}
		);
	}

	//Kommentar reporten
	me.reportcomment = function (el) {
		me.getlang();
		var combox = $(el).closest("[data-c]");
		me.info({'type':'question','title':me.jLang.titleQuestion, 'message':me.jLang.qreportcomment+ '<br /> ' + me.jLang.reportAddMessage +'<textarea id="jVS-report-message" cols=30 rows=2></textarea>', 'buttons':['Send', 'Cancel'],'callback':function (e) {
			if (e == me.translateStr('Send')) {
				me.req({"task":"reportComment", "box":combox.dp('box'), "comment":combox.dp('c'), "reportMessage":$("#jVS-report-message").val()},
					function(j){
						if(j.erfolg) {
							me.notify(j.success);
							$(el).remove();
						} else {
							me.error(j.error);
						}
					}
				);
			}
		}});
	}
	
	me.textareaResetTimer = null;
	
	me.insertcode = function(el) { window.clearTimeout(me.textareaResetTimer);
		var code = $(el).data("insert");
		var elem = $(el).parent().parent().find("textarea");
		elem.insertcode(elem.focus().range(),null,code);
	}
	me.insertbbcode = function(el) { window.clearTimeout(me.textareaResetTimer);
		var code = $(el).data("bbcode");
		var elem = $(el).parent().parent().find("textarea");
		var title = $(el).data("bbinfo");
		var tinfo = elem.focus().range();
		bb = code.split("{value}");
		if ( tinfo.len === 0) {
			var message = '<textarea id="jVS-bbcode" cols=38 rows=1></textarea>';
			if ( bb[0] === "[url]" ) {
				message = 'Title (optional):<textarea id="jVS-bbcode-urltitle" cols=38 rows=1></textarea><br /><br />URL:<textarea id="jVS-bbcode" cols=38 rows=1></textarea>';
			}
			me.info({'type':false,'title':title, 'message': message, 'callback':function(e) { 
				if (e == 'Ok') {
					var titel = $('#jVS-bbcode-urltitle').val() || "";
					var input = $('#jVS-bbcode').val();
					if ( titel ) {
						input = titel;
						titel = "=" + $('#jVS-bbcode').val();
					}
					if ( bb[0] === "[url]" ) bb[0] = bb[0].substring(0,bb[0].length - 1) + titel + bb[0].substring(bb[0].length - 1,bb[0].length);
					elem.insertcode(tinfo,bb,input);
				}
			}});
			$('#jVS-bbcode').focus();
		} else {
			elem.insertcode(tinfo,bb)
		}
	}

	me.scrollto = function(elem) {
		$('html, body').animate({scrollTop: $(elem).offset().top - 50}, 500);
	}
	
	me.showMoreText = function(e) {
		e.hide();
		var dataE = decodeURIComponent((e.find("span:first").html()).replace(/\+/g, '%20'));
		e.parent().html(e.parent().html() + dataE);
	}
	
	//UserInfo
	me.userInfos = new Array();
	me.loadUserInfo = function(el) {
		me.timeout = setTimeout(function(){
			var id = el.dp("u");
			if(me.userInfos[id] == undefined) {
				me.tip.show(el, " ", true);
				//Daten laden
				me.req({"task":"loadUserTooltip", "uid": id}, function(j) {
					me.tip.show(el, me.userInfos[id] = j.html);
				});
			} else {
				me.tip.show(el, me.userInfos[id]);
			}
		},200);
	}
	
	//CategoryInfo
	me.categoryInfos = new Array();
	me.loadCategoryInfo = function(el) {
		me.timeout = setTimeout(function(){
			var id = el.dp("cid");
			if(me.categoryInfos[id] == undefined) {
				me.tip.show(el, " ", true);
				//Daten laden
				me.req({"task":"loadCategoryTooltip", "cid": id}, function(j) {
					me.tip.show(el, me.categoryInfos[id] = j.html);
				});
			} else {
				me.tip.show(el, me.categoryInfos[id]);
			}
		},200);
	}
	
	//Small-Tooltip
	me.loadTooltip = function(el) {
		me.timeout = setTimeout(function(){
			var data = el.attr("data-jvs_tooltip");
			
			if(data) {
				var container = $("<div>").attr("class", "contentbox");
				container.html(decodeURIComponent(data.replace(/\+/g, '%20')));
				me.tip.show(el, container);
			}
		},200);
	}
	
	//FacebookVoting
	/*me.loadFBVoteButton = function(el) {
		me.timeout = setTimeout(function(){
			var data = parseInt(el.dp("a"));
			var oldId = parseInt(me.tip.el.find(".facebookVoteButton").dp("aid")); 
			
			if(oldId != data) {
				var link = (jVS.conf.fb.like_url).replace("999999999", data);
				var src = jVS.conf.fb.html.replace("DUMMY_URL_REPLACE", link);
				var html = $(src);
				me.tip.show(el, html);
				
				FB.XFBML.parse();
			} else me.tip.show(el, false);
		},200);
	}
	
	me.FBLikeEvent = function(type, url) {
		var parts = url.split("&");
		
		
		if(parts[parts.length-3].substr(5) == "poll") {
			var aid = parts[parts.length-1].substr(4);
			
			if(type == "vote") {
				var el = $(".jvotesystem [data-a=" + aid + "] .votingbutton");
				el.trigger("click", true);
			} else {
				var el = $(".jvotesystem [data-a=" + aid + "] .vote-down").addClass("reset");
				el.trigger("click");
				el.removeClass("reset");
			}
		}
	}*/
	
	//Timestamps
	me.updateTimestamps = function() {
		me.getlang();
		
		$('#jvotesystem .update_timestamp[data-time]:visible, .jvotesystem .update_timestamp[data-time]:visible, .jvs.update_timestamp[data-time]:visible').each(function() {
			var ts = parseInt($(this).dp("time"));
			
			var diff = me.conf.time - ts;
			var text = "";
			
			if(diff < 60) {
				count = diff;
				text = (count == 1) ? me.jLang.second : me.jLang.seconds;
			} else if(diff < 60*60) {
				count = Math.round(diff/60);
				text = (count == 1) ? me.jLang.minute : me.jLang.minutes;
			} else if(diff < 60*60*24) {
				count = Math.round(diff/60/60);
				text = (count == 1) ? me.jLang.hour : me.jLang.hours;
			} else if(diff < 60*60*24*7) {
				count = Math.floor(diff/60/60/24);
				text = (count == 1) ? me.jLang.day : me.jLang.days;
			} else if(diff < 60*60*24*30) {
				count = Math.floor(diff/60/60/24/7);
				text = (count == 1) ? me.jLang.week : me.jLang.weeks;
			} else if(diff < 60*60*24*365) {
				count = Math.floor(diff/60/60/24/30);
				text = (count == 1) ? me.jLang.month : me.jLang.months;
			} else {
				count = Math.floor(diff/60/60/24/365);
				text = (count == 1) ? me.jLang.year : me.jLang.years;
			}
			
			var newContent = count + " " + text;
			if($(this).html() != newContent) {
				$(this).fadeOut("fast", function() { 
					$(this).html(newContent);
					$(this).parent().find('span').fadeIn("fast"); 
				});
			}
		});
	}
	
	//Toolbars
	me.removePoll = function(box) {
		me.getlang();
		me.info({'type':'question','title':me.jLang.titleQuestion, 'message':me.jLang.qremovepoll, 'buttons':['Yes', 'No'],'callback':function (e) {
			if (e == me.translateStr('Yes')) {
				me.req({"task":"removePoll", "box":box}, function(j) {
					if(j.erfolg) {
						var el = $("[data-box="+box+"]");
						var toolbar = $("[data-linkedto="+box+"]").slideUp("slow", function() {$(this).remove();});
						
						el.slideUp("slow", function() {
							el.html(j.code);
							el.slideDown("slow");
						});
					} else {
						me.error(j.error);
					}
				});
			}
		}});
	}
	
	me.editPollState = function(poll, state) {
		me.req({"task":"editPollState", "poll": poll, "state" : state}, function(j) {
			if(j.erfolg) {
				document.location.reload(); //Neuladen
			} else {
				me.error(j.error);
			}
		});
	}
	
	me.loadSqueezebox = function(el, link, width, height, closeable) {
		if(width == undefined) var width = 820;
		if(height == undefined) var height = 650;
		if(closeable == undefined) var closeable = false; 
		if(me.conf.legacy) {
			if(el == false) el = $('<div>').hide().appendTo('body');
			if(closeable) $(el).attr('rel', "{handler: 'iframe', closeBtn: true, closable: true, size: {x: " + width + ", y: " + height + "}}").attr('href',link);
			else $(el).attr('rel', "{handler: 'iframe', closeBtn: false, closable: false, size: {x: " + width + ", y: " + height + "}, onOpen: function(){jQuery('#sbox-btn-close').css('visibility', 'hidden');jQuery('object').hide();}, onClose: function() {jQuery('#sbox-btn-close').css('visibility', 'visible');jQuery('object').show();}}").attr('href',link);
			SqueezeBox.fromElement(el);
		} else { 
			if(closeable) SqueezeBox.open(link, {handler: 'iframe', size: {x: width, y: height}, closeBtn: true, closable: true, onOpen: function(){$('#sbox-btn-close').css('visibility', 'visible');$('object').show();}});	
			else SqueezeBox.open(link, {handler: 'iframe', closeBtn: false, closable: false, size: {x: width, y: height}, onOpen: function(){$('#sbox-btn-close').css('visibility', 'hidden');$('object').hide();}, onClose: function() {$('#sbox-btn-close').css('visibility', 'visible');$('object').show();}});	
		}
	}
	
	me.loadBannerScript = function(box, code) {
		var src = "http://pagead2.googlesyndication.com/pagead/show_ads.js";
		if(box == undefined) {
			document.write(
				unescape("%3Cscript%20type%3D%22text%2Fjavascript%22%20src%3D%22")+
					src +
				unescape("%22%3E%3C%2Fscript%3E")
			);
		} else {
			var el = $("[data-box="+box+"] .jvsbanner .bannercode");
			el.html("");
			domWrite(
				el.get(0),
				src,
				function(){
					eval(code);
				}
			);
		}
	}
	
	me.searchInputTimeout;
	me.searchInput = function(el, nowait) {
		if(me.searchInputTimeout != undefined) window.clearTimeout(me.searchInputTimeout);
		
		me.searchInputTimeout = window.setTimeout(function() {
			me.search(el);
		}, nowait ? 20 : 400);	
		
	}
	
	me.search = function(el, str) {
		if(str != undefined) el.closest('.jvotesystem').find('.searchfield input').val(str);
		me.go(el);
	}

	me.submitTask = function(el, task_group, group, action, id) {
		me.req({"task": "submitTask", "task_group": task_group, "group": group, "action": action, "id": id}, function(j) {
			if(j.success) {
				el = $(el).parent();
				if(el.hasClass('task_data') && el.prev().hasClass('task_head') && el.next().length == 0) {
					el.prev().slideUp(function() {
						$(this).remove();
					});
				}
				if(el.hasClass('task_head')) {
					el.slideUp(function() {
						$(this).remove();
					})
					el = el.nextUntil('.task_head');
				}
				el.slideUp(function() {
					$(this).remove();
				});
				el.parent().parent().prev('p.task_msg').find('.count').decr();
			}
		});
	}
	
	//Tooltip
	me.tooltip_constructor = function() {
		var tip = {};
		
		//options
		tip.hideDelay = 500;
		
		//load
		$(document).ready(function(){
			if(tip.config == undefined) {
				tip.config = {};
				tip.config.alwaysTop = false;
			}
			
			//Neues Element erstellen
			var jvs = $("<div class='jvotesystem vstooltip'>");
			//Bubble-Element
			tip.el = $('<table class="vsbubble"><tbody><tr><td class="vscorner vstopleft"></td><td class="vstop"><div></div></td><td class="vscorner vstopright"></td></tr><tr><td class="vsleft"></td><td class="vscontent">'
						+'</td><td class="vsright"></td></tr><tr><td class="vscorner vsbottomleft"></td><td class="vsbottom"><div></div></td><td class="vscorner vsbottomright"></td></tr></tbody></table>')
						.hide();
			
			//Element anfügen
			$("body").append(jvs.append(tip.el));
			
			tip.el.on("mouseenter", function() {
				tip.el.stop(true,true).one("mouseleave",function(){
					tip.el.delay(tip.hideDelay).fadeOut("fast");
				});
			});
			
			$(document).click(function() { 
				tip.el.delay(tip.hideDelay).fadeOut("fast");
			});
		});
		
		//show
		tip.show = function(el, content, spin, classAttr) {
			if(el != undefined) {
				var coord = $(el).offset();
				var cont = tip.el.find("td.vscontent");
				
				//Content
				if(content != false || spin) cont.html(content);
				if(classAttr != undefined) cont.addClass(classAttr);
				
				var curTop = tip.el.offset().top; 
				var curLeft = tip.el.offset().left; 
				var shown = (tip.el.css("display") == "table" && tip.el.css("opacity") == 1); 
				
				tip.el.css({"top": -1000, "left": -1000}).show();
				
				//Top or Bottom?
				if(((coord.top - $(window).scrollTop()) < ($(window).height()/2)) && !tip.config.alwaysTop) {
					var topPos = coord.top + el.outerHeight(true) + 10;
					tip.el.addClass("posTop");
				} else {
					var topPos = coord.top - tip.el.outerHeight(true) - 10;
					tip.el.removeClass("posTop");
				}
				//Left or Right?
				if(coord.left < ($(window).width()/2)) {
					var leftPos = coord.left - 24;
					tip.el.addClass("posLeft");
				} else {
					var leftPos = coord.left - tip.el.outerWidth(true) + 34 + $(el).width()/2;
					tip.el.removeClass("posLeft");
				}
				
				if(!shown) tip.el.hide(); 
				else tip.el.show();
				//Show
				tip.el.css({"top": topPos, "left": leftPos}).stop(true,true);
				if(curTop != topPos || curLeft != leftPos || !shown)
					tip.el.fadeIn("fast", function() { $(this).css("opacity", 1);});
				
				//Spinner
				spin ? cont.smallspin() : cont.smallspin(false);
				
				//Hide
				el.one("mouseleave", function() { 
					tip.el.delay(tip.hideDelay).fadeOut("fast");
				});
			}
		}
		
		return tip;
	}
	
	me.tip = me.tooltip_constructor();
	
	/*me.validateFB = function() {
		jVS.conf.fb.valid = true;
		$(".jvotesystem .fblogin").fadeOut("slow", function() { $(this).remove(); })
	}*/
	
	return me;
}
var jVS=jVS_constructor();

//   //E-Mail Box
//   function jVoteSystemAskEmail(box) {
//   	me.info({
//   		'type':'question',
//   		'title':'Error', 
//   		'message': '<input type="text" value="Hallo" maxlength="250" size="32" id="jvoteemail" name="jvoteemail">', 
//   		'buttons':['Yes', 'No', 'Help'],
//   		'callback':  function(caption) {
//   			alert(caption + ' was clicked');
//   			alert(jQuery('#jvoteemail').val());
//   		},
//   		'position': ['right - 20', 'top + 20']
//   	});
//   }