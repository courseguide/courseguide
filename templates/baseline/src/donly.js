/*======================================================================*\
|| #################################################################### ||
|| # Copyright �2006-2009 Youjoomla.com. All Rights Reserved.           ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- THIS IS NOT FREE SOFTWARE ---------------- #      ||
|| # http:www.youjoomla.com | http:www.youjoomla.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
window.addEvent('load', function(){
	var dropholder = $('topmenu_holder');
	var activedropded = dropholder.getElement("li.active").hasClass("haschild");
	var getholders  = $('topmenu_holder').getStyle('height').toInt();
	var getdrops = $('topmenu_holder').getElement('ul.dropline').getStyle('height').toInt();
	var fullheight = getholders + getdrops;
	//console.log(fullheight);
	if (activedropded){
		dropholder.setStyle('height',fullheight);
	}
	$$('li.haschild').addEvent('mouseover', function() {
		dropholder.setStyle('height',fullheight);
	});
	if (!activedropded){
		$$('ul.level1,ul.menunavd').addEvent('mouseleave', function() {
			dropholder.setStyle('height',getholders);
		});
	}
	var mainList = $('horiznav').getElement('ul.menunavd');
	var firstRow = mainList.getChildren();
	firstRow.addEvent('mouseenter', function(){
		if(!activedropded){
			if( !this.hasClass('haschild'))
				dropholder.setStyle('height',getholders);
		}
	});
});