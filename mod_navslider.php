<?php
// Restrict access.
defined('_JEXEC') or die;
// Require helper.
require_once dirname(__FILE__) . '/helper.php';

// Fetch articles from database.
$articles = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias', 'state = 1', 0, 'publish_up DESC');

// Fetch categories & tags from database.
$categories = modNavSliderHelper::getCategories();
$tags = modNavSliderHelper::getTags();

// Load CSS & JS.
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'modules/'.$module->module . "/css/mod_navslider.css");
$document->addScript("https://storage.googleapis.com/code.getmdl.io/1.0.6/material.min.js");
$document->addStyleSheet("https://fonts.googleapis.com/icon?family=Material+Icons");
$document->addStyleSheet("//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css");
$document->addScript("//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js");
$document->addScript(JURI::base().'modules/'.$module->module . "/js/mod_navslider.js");
$document->addScript(JURI::base().'modules/'.$module->module . "/js/iscroll.js");

require JModuleHelper::getLayoutPath('mod_navslider');
?>