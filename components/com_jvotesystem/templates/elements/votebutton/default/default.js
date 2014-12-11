/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if(window.jVSEmbedC == undefined) {
	var jVSEmbedC = function () {
		var me = {}, ajaxqueue = [], reqcount = 0;
		var $ = null;
		
		me.loaded = function(lib) {
			if(lib != undefined) $ = lib;
			
			$(document).ready(function() {
				me.tooltip = me.lang.tooltip;
				
				/*
				 * jQuery postMessage - v0.5 - 9/11/2009
				 * http://benalman.com/projects/jquery-postmessage-plugin/
				 * 
				 * Copyright (c) 2009 "Cowboy" Ben Alman
				 * Dual licensed under the MIT and GPL licenses.
				 * http://benalman.com/about/license/
				 */
				(function($){var g,d,j=1,a,b=this,f=!1,h="postMessage",e="addEventListener",c,i=b[h]&&!$.browser.opera;$[h]=function(k,l,m){if(!l){return}k=typeof k==="string"?k:$.param(k);m=m||parent;if(i){m[h](k,l.replace(/([^:]+:\/\/[^\/]+).*/,"$1"))}else{if(l){m.location=l.replace(/#.*$/,"")+"#"+(+new Date)+(j++)+"&"+k}}};$.receiveMessage=c=function(l,m,k){if(i){if(l){a&&c();a=function(n){if((typeof m==="string"&&n.origin!==m)||($.isFunction(m)&&m(n.origin)===f)){return f}l(n)}}if(b[e]){b[l?e:"removeEventListener"]("message",a,f)}else{b[l?"attachEvent":"detachEvent"]("onmessage",a)}}else{g&&clearInterval(g);g=null;if(l){k=typeof m==="number"?m:typeof k==="number"?k:100;g=setInterval(function(){var o=document.location.hash,n=/^#?\d+&/;if(o!==d&&n.test(o)){d=o;l({data:o.replace(n,"")})}},k)}}}})($);
				
				(function($){var escapeable=/["\\\x00-\x1f\x7f-\x9f]/g,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"};$.toJSON=typeof JSON==="object"&&JSON.stringify?JSON.stringify:function(a){if(a===null){return"null"}var b=typeof a;if(b==="undefined"){return undefined}if(b==="number"||b==="boolean"){return""+a}if(b==="string"){return $.quoteString(a)}if(b==="object"){if(typeof a.toJSON==="function"){return $.toJSON(a.toJSON())}if(a.constructor===Date){var c=a.getUTCMonth()+1,d=a.getUTCDate(),e=a.getUTCFullYear(),f=a.getUTCHours(),g=a.getUTCMinutes(),h=a.getUTCSeconds(),i=a.getUTCMilliseconds();if(c<10){c="0"+c}if(d<10){d="0"+d}if(f<10){f="0"+f}if(g<10){g="0"+g}if(h<10){h="0"+h}if(i<100){i="0"+i}if(i<10){i="0"+i}return'"'+e+"-"+c+"-"+d+"T"+f+":"+g+":"+h+"."+i+'Z"'}if(a.constructor===Array){var j=[];for(var k=0;k<a.length;k++){j.push($.toJSON(a[k])||"null")}return"["+j.join(",")+"]"}var l,m,n=[];for(var o in a){b=typeof o;if(b==="number"){l='"'+o+'"'}else if(b==="string"){l=$.quoteString(o)}else{continue}b=typeof a[o];if(b==="function"||b==="undefined"){continue}m=$.toJSON(a[o]);n.push(l+":"+m)}return"{"+n.join(",")+"}"}};$.evalJSON=typeof JSON==="object"&&JSON.parse?JSON.parse:function(src){return eval("("+src+")")};$.secureEvalJSON=typeof JSON==="object"&&JSON.parse?JSON.parse:function(src){var filtered=src.replace(/\\["\\\/bfnrtu]/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"");if(/^[\],:{}\s]*$/.test(filtered)){return eval("("+src+")")}else{throw new SyntaxError("Error parsing JSON, source is not valid.")}};$.quoteString=function(a){if(a.match(escapeable)){return'"'+a.replace(escapeable,function(a){var b=meta[a];if(typeof b==="string"){return b}b=a.charCodeAt();return"\\u00"+Math.floor(b/16).toString(16)+(b%16).toString(16)})+'"'}return'"'+a+'"'}})($)
				
				me.conf.ref = decodeURIComponent( document.location.hash.replace( /^#/, '' ) );
				
				$(".button").click(function(e) { me.vote(); e.preventDefault();});
				$(".button").mouseenter(function(e) { 	
					if(!$(this).hasClass("error")) me.send({"task": "tooltip", "msg": me.tooltip, "tip_id": 12});
				});
				$(".button").mouseleave(function(e) { me.send({"task": "hideTooltip", "tip_id": 12}); });
				//$(".button").width($(".button").width());
			});
		}
		
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

		me.req = function(data, callback) {
			var def = {"paramView": "button", "lang":me.conf.lang, "admin": 0};
			def[me.conf.token] = 1;
			
			$.extend(def,data);
			
			me.ajax({
				url: me.conf.root + "components/com_jvotesystem/ajax.php",
				cache: false,
				type: "POST",
				data: def,
				dataType: "json",
				success: function(json)	{ callback(json);}
			});
		}
		
		me.send = function(data) { 
			data = $.extend(data, {"id": me.conf.id});
		
			var str = $.toJSON(data);
			$.postMessage(
			  str,
			  me.conf.ref,
			  parent
			);
		}
		
		me.changeVote = function(count) {
			me.conf.votes = parseInt(me.conf.votes) + count; 
			me.conf.uservotes = parseInt(me.conf.uservotes) + count;
			me.conf.allowed_votes = parseInt(me.conf.allowed_votes) - count;
				
			$(".vote_count").html(me.conf.votes);
			if(me.conf.uservotes > 0) {
				$(".button").addClass("voted").html("+" + me.conf.uservotes);
				
				if(me.conf.uservotes == me.conf.max_votes) $(".button").addClass("error");
			} else {
				$(".button").removeClass("voted").html(me.lang.vote);
			}
			
		}
		
		me.vote = function() { 
			if(me.conf.error) {
				me.send({"task": "tooltip", "msg": me.lang.error, "type": "error"});
			} else if(me.conf.allowed_votes > 0 && me.conf.uservotes < me.conf.max_votes) {
				me.changeVote(1);
				
				me.req( {"task":"vote", "box": me.conf.pid, "answer": me.conf.id} , function(d) {
					if(d.captcha == 0) {
						//Redirect to page -> no captcha
						parent.location.href = d.poll_link;
						
						me.changeVote(-1);
					} else if(d.erfolg == 1) {
						me.tooltip = d.tooltip;
						if(me.conf.allowed_votes == 0)
							me.send({"task": "tooltip", "msg": me.lang.thankyou_message});
						else
							me.send({"task": "tooltip", "msg": me.tooltip});
					} else {
						me.send({"task": "tooltip", "msg": d.error, "type": "error"});
						
						me.changeVote(-1);
					}
				});
			} else if(me.conf.allowed_votes == 0) {
				me.send({"task": "tooltip", "msg": me.lang.no_votes_left, "type": "error"});
			} else if(me.conf.uservotes == me.conf.max_votes) {
				me.send({"task": "tooltip", "msg": me.lang.no_votes_answer_left, "type": "error"});
			}
		}
		
		return me;
	}

	var jVSEmbed = new jVSEmbedC();
}