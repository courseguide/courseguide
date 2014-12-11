/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if(window.jVSEmbedC == undefined) {
	var jVSEmbedC = function () {
		var me = {};
		var $ = null;
		var root = 'ROOT_PATH_PLACEHOLDER';
		
		me.init = function() {
			if(me.checkQuery("jQuery") || me.checkQuery("JMQuery")) me.loaded();
			else {
				var po = document.createElement('script');
				po.type = 'text/javascript'; 
				po.async = true;
				po.src =root + "components/com_jvotesystem/assets/js/jvs-embed-jquery.js";
				
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(po, s);
			}
		}
		
		me.checkQuery = function(name) {
			var lib = window[name];
			if(lib != undefined) {
				if(lib.fn.jquery != "1.8.3") return false;
				$ = lib;
				return true;
			} else return false;
		}
		
		me.loaded = function(lib) {
			if(lib != undefined) $ = lib;
			
			/*
			 * jQuery postMessage - v0.5 - 9/11/2009
			 * http://benalman.com/projects/jquery-postmessage-plugin/
			 * 
			 * Copyright (c) 2009 "Cowboy" Ben Alman
			 * Dual licensed under the MIT and GPL licenses.
			 * http://benalman.com/about/license/
			 */
			(function($){var g,d,j=1,a,b=this,f=!1,h="postMessage",e="addEventListener",c,i=b[h]&&!$.browser.opera;$[h]=function(k,l,m){if(!l){return}k=typeof k==="string"?k:$.param(k);m=m||parent;if(i){m[h](k,l.replace(/([^:]+:\/\/[^\/]+).*/,"$1"))}else{if(l){m.location=l.replace(/#.*$/,"")+"#"+(+new Date)+(j++)+"&"+k}}};$.receiveMessage=c=function(l,m,k){if(i){if(l){a&&c();a=function(n){if((typeof m==="string"&&n.origin!==m)||($.isFunction(m)&&m(n.origin)===f)){return f}l(n)}}if(b[e]){b[l?e:"removeEventListener"]("message",a,f)}else{b[l?"attachEvent":"detachEvent"]("onmessage",a)}}else{g&&clearInterval(g);g=null;if(l){k=typeof m==="number"?m:typeof k==="number"?k:100;g=setInterval(function(){var o=document.location.hash,n=/^#?\d+&/;if(o!==d&&n.test(o)){d=o;l({data:o.replace(n,"")})}},k)}}}})($);
			
			$(document).ready(function() {
				var buttons = $(".jvs-votebutton");
				
				if(buttons.length > 0) {
					buttons.each(function() {
						var el = $(this);
						el.css({"height": "30px", "width": "250px", "display": "inline-block", "text-indent": "0pt", "margin": 0, "padding": 0, "background": "none repeat scroll 0% 0% transparent;", "border-style": "none", "line-height": "normal", "font-size": "1px", "vertical-align": "baseline", "position": "relative"});
							
						var iframe = $('<iframe marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="vote" width="100%" scrolling="no" frameborder="0" vspace="0" tabindex="0">');
						
						var src = root + "components/com_jvotesystem/api.php?task=votebutton&id=" + el.data("id") + "&key=" + el.data("apikey") + "&lang=" + (el.data("lang") ? el.data("lang") : "en-GB");
						if(el.data("token") != "") src += "&" + el.data("token") + "=1";
						src += "&ref=" + escape(window.location.href) + '#' + encodeURIComponent( document.location.href );
						
						iframe.attr("src", src); 
						iframe.css({"position": "static", "left": "0pt", "top": "0pt", "width": "250px", "margin": 0, "border-style": "none", "visibility": "visible", "height": "30px"});
							
						el.append(iframe);
					});
					
					$("head").append('<link rel="stylesheet" href="' + root + 'components/com_jvotesystem/assets/css/embed/votebutton.css" type="text/css" />');
				}
			});
					
			$.receiveMessage(
				function(e){
					var j = $.parseJSON(e.data);
					switch(j.task) {
						case "tooltip":
							me.tooltip(j.id, j.msg, (j.tip_id != undefined) ? j.tip_id : 0, (j.type != undefined) ? j.type : "notice");
							break;
						case "hideTooltip":
							if(j.tip_id != undefined) $('.jvs-votebutton[data-id=' + j.id + '] .jvs-tooltip[data-tip_id=' + j.tip_id + ']').fadeOut();
							break;
					}
				}
			);
		}
		
		me.tooltip = function(elID, msg, id, type) {
			var el = $('.jvs-votebutton[data-id=' + elID + ']');
			
			var tooltip = el.find(".jvs-tooltip");
			if(tooltip.length == 0) { 
				tooltip = $("<div>");
				
				tooltip.click(function() {
					tooltip.fadeOut();
				});
				
				tooltip.mouseenter(function() {
					tooltip.stop(true, true).show();
				}).mouseleave(function() {
					tooltip.fadeOut();
				});
				
				el.append(tooltip);
			}
			tooltip.attr("class", "jvs-tooltip");
			tooltip.addClass("tool-" + type);
			
			tooltip.attr("data-tip_id", id);
			tooltip.html(msg);
			tooltip.stop(true, true).hide().fadeIn();
		}
		
		me.init();
		
		return me;
	}

	var jVSEmbed = new jVSEmbedC();
}