<?php
// Restrict access.
defined('_JEXEC') or die;
// Require helper.
require_once dirname(__FILE__) . '/helper.php';
// Fetch articles from database.
$articles = modNavSlider::getArticles();
// Load CSS.
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/'.$module->module . "/css/mod_navslider.css");
// Load JS.
$document->addScript(JURI::base().'modules/'.$module->module . "/js/mod_navslider.js");
require JModuleHelper::getLayoutPath('mod_navslider');
?>