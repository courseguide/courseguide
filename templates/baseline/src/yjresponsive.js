/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) since 2010  Youjoomla.com. All Rights Reserved.      ||
|| # Authors - Dragan Todorovic                                         ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
//window.addEventListener('load', function () {
window.addEvent('load', function () {
    if (window.getScrollSize().x < 980) {
        resize();
        size_menu();

        if ($('logo')) {
            logo_size();
        }
    }
});
window.addEvent('resize', function () {
    resize();
    size_menu();
    if ($('logo')) {
        logo_size();
    }
});
window.addEvent('domready', function () {
    showmenu();
    size_menu();
    if ($('logo')) {
        logo_size();
    }

});

function logo_size() {
    function roundNumber(num, dec) {
        var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
        return result;
    }
    var logo_out = roundNumber(logo_w / $('header').getSize().x * 100, 2);
    var grid_w = roundNumber(100 - logo_out, 2);
    if ($('logo')) {
        $('logo').setStyle('width', logo_out + '%');
    }
    if ($('yjsgheadergrid')) {
        $('yjsgheadergrid').setStyle('width', grid_w + '%');
    }
}

function size_menu() {

    if ($('mmenu_holder')) {
        if (Browser.name == 'ie' && (Browser.version == 7 || Browser.version == 8)) {
            var size = document.documentElement.clientWidth;
        } else {
            var size = window.getScrollSize().x;
        }

        var all_li = $$('ul.menunav li');

        if (all_li.length > 0) {
            var tops = all_li[0].getSiblings();
            var tops_count = tops.length + 1;
            var width = tops.getWidth().sum() + all_li[0].getWidth();
            var menu_width = $('topmenu_holder').getCoordinates().width;
        }

        if (size < 980) {
            $$('.top_menu,.top_menu_poz').removeClass('showmenu');
            $$('#mmenu_holder').setStyle('display', 'block');
        } else {
            $$('#mmenu_holder').setStyle('display', 'none');
            $$('.top_menu,.top_menu_poz').addClass('showmenu');
        }
        if (size > 980) {
            $$('#mmenu_holder').setStyle('display', 'none');
            $$('.top_menu,.top_menu_poz').removeClass('showmenu');
        }

    }
}

function resize() {

    var size = document.documentElement.clientWidth;

    if (size < 980) {
        $$('#logo').addClass('lfloat');
        $$('#topmenu_holder').addClass('dropped');
        $$('#header').addClass('dropped');
    } else {
        $$('#logo').removeClass('lfloat');
        $$('#topmenu_holder').removeClass('dropped');
        $$('#header').removeClass('dropped');

    }

}

function showmenu() {
    if ($('mmenu_holder')) {
        var select_holder = $$('#mmenu_holder');
        $$('#topmenu_holder').grab(select_holder[0], 'top');
        $$('#mmenu').addEvent('change', function () {
            window.location.href = this.get('value');
        });
    }
}