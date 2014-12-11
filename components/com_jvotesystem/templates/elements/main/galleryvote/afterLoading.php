<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

//Plugins laden
$this->lib->plugin("colorbox"); 
$this->lib->plugin("form");

//BBCode deaktivieren.. Bildtitel nur ohne BBCode möglich
$galleryVoteBBCode = $this->vbparams->get("activate_bbcode");
$this->vbparams->set("global", "activate_bbcode", false);
$this->vbparams->set("global", "general_published_bbcode", true);

//Farbe einfügen
$color = $data->box->tmpl_galleryvote_main_color;
$this->document->addStyleDeclaration("#jvs-{$this->uniqid} .mainRGB, #jvs-{$this->uniqid} a.mainRGB { background-color: $color !important; } #jvs-{$this->uniqid} .rank + .count_hover:before { border-color: transparent $color transparent transparent; }");
$color = $data->box->tmpl_galleryvote_font_color;
$this->document->addStyleDeclaration("#jvs-{$this->uniqid} .topbox, #jvs-{$this->uniqid} .topbox .question, #jvs-{$this->uniqid} .endbox, #jvs-{$this->uniqid} .answer .votingbutton, #jvs-{$this->uniqid} .rank, #jvs-{$this->uniqid} .count_hover, #jvs-{$this->uniqid} .newanswerbox .label_for_newanswer, #jvs-{$this->uniqid} .newanswerbox input.button { color: $color !important; }");
?>