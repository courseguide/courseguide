/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
* @projectsite www.joomess.de/projects/jvotesystem
* @authors Johannes Meßmer, Andreas Fischer
* @copyright (C) 2010 - 2012 Johannes Meßmer
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

function jVS_constructor_module () {

	var me = {}, $ = jQuery;
	
	me.construct = function(el) { el = $(el);
		el.on("click", ".makenew", function(e) {
			jVS.getlang(this,function(el) {me.addanswerspecial(el);});
		});
		
		el.on({
			mouseenter: function(e) {
				$(this).find(".answericons").stop(true, true).fadeIn("slow");e.preventDefault();
			},
			mouseleave: function(e) {
				$(this).find(".answericons").stop(true, true).fadeOut("slow");e.preventDefault();
			}
		}, "div.answerbox");
			
		el.on("click", ".answerlist", function(e) { jVS.leavecharts(el); });
			
		el.on("click",	"input[name=cancel]",	  function (e) { me.answerAddBox.close(); });
	}
	
	me.onTextareaText = function(el) { el = $(el);
		 if(el.attr("name") == "answer") return true;
	}
	
	me.answerAdded = function(el) {
		me.answerAddBox.close();
	}
	
	me.answerAddBox;
	me.commentGoFirstLoaded = function(anbox, box, answer, j) {
		jVS.info({'type':false,'title':jVS.translateStr("commentsofanswer")+": "+anbox.find(".answertext").text().substr(0, 25) + "..",'width':550, 'message':'<div class="jvotesystem jvs-module" data-box="'+box+'"><div class="answer" data-a="'+answer+'"><div class="comments" >'+j.code+'</div></div></div>', 'buttons':false});
		$(".ZebraDialog div.jvotesystem").jvslisten();
		
		return true;
	}
	
	me.addanswerspecial = function (el) {
		elem = $(el);
		if(!elem.hasClass("needLogin")) {
			me.answerAddBox = jVS.info({'type':false,'title':jVS.translateStr("newanswertovotebox")+": "+elem.closest("div.jvotesystem").find(".title").text(),'width':550, 'message':'<div class="jvotesystem jvs-module" data-box="'+elem.dp("box")+'"></div>', 'buttons':false,'callback':function(){$(".ZebraDialog .jvotesystem div.newanswerbox").detach().hide().insertAfter(".jvotesystem div.dummy").prev().remove();}});
			elem.next().after('<div class="dummy"></div>').detach().show().appendTo(".ZebraDialog div.jvotesystem");
			$(".ZebraDialog div.jvotesystem").jvslisten().find('textarea').focus();
		}
	}
	
	me.leavecharts = function(elem, box, boxobj) {
		jVS.go(boxobj, boxobj.data("lastviewpage"),0);
		
		$("div[data-box="+box+"] div.makenew").fadeIn("slow");
		
		return true;
	}
	
	me.go = function(boxobj, box, page, pagebox, pageobj, template, currentpage) { 
		if (page > 9) {jVS.error("TOO_MUCH_PAGES_FOR_MODULE");return true;};
		var removed = boxobj.data("answerremoved");
		var current = removed === 'true' ? 0 : currentpage;
		if (removed === 'true') {
			boxobj.data("answerremoved",'false');
			pagebox.html('');
		}
		
		while (page >= current) {
			current++;
			var setpage = current > page ? page : current;
			jVS.req({"task":"answers", "box":box, "page":setpage, "currentPage":current > page ? page : current - 1,'template':template},function(j){
				jVS.unlock(boxobj);
				if(j.erfolg === 1) {
					pageobj = pagebox.find("div[data-p]").last();//update pageobj!
					boxobj.data("lastviewpage",setpage);
					if(j.page === j.currentPage || pagebox.find("div.jsbarchart,div.jspiechart").length > 0) {
						if (pagebox.find("div.jsbarchart,div.jspiechart").length > 0) {
							var oldp = pagebox.children();
							var newp = pagebox.prepend(j.code).find("div[data-p]");
							
							var after = function() {
								pagebox.height("auto");
							}
								pagebox.animate({height: newp.height()}, "slow", after);
							oldp.remove();
							newp.hide().css({'position':'relative'}).fadeIn("slow");
							// pagebox.html('');
							// pagebox.append(j.code);
							// pagebox.find("div[data-p]").last().hide().css({'position':'relative','height':'auto'}).fadeIn("slow");
						} else {
							//pageobj.remove();
							pagebox.append(j.code);
							var pages = pagebox.find("div[data-p]"),
							oldp = pages.eq(-2),
							newp = pages.last();
							pagebox.animate({height: pages.not(oldp).totalOuterHeight()}, "slow", function() {pagebox.height("auto");});
							oldp.remove();
							newp.hide().css({'position':'relative'}).fadeIn("slow");
						}
					} else {
						if (j.currentPage === 0) {
							pagebox.append(j.code);
							pageobj = pagebox.find("div[data-p]").last();
							pageobj.hide().css({'position':'relative','height':'auto'}).slideDown("slow");
						} else {
							pagebox.append(j.code);
							pageobj.find("div.navi").slideUp("slow");
							pageobj.next().hide().css({'position':'relative','height':'auto'}).slideDown("slow");
						}
					}
				} else {
					jVS.error(j.error);
				}
			});
			if (page === current) {return true;}
		}
		
		if (page < currentpage) {
			pagebox.find("div[data-p]").slice(page-currentpage).slideUp("slow",function(){
			pagebox.find("div[data-p]").slice(page-currentpage).remove();
		});
			pagebox.find("div[data-p]").eq(page-currentpage-1).find("div.navi").slideDown("slow");
			jVS.unlock(boxobj);
		}
		return true;;
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

jVS.module = jVS_constructor_module();