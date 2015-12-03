<?php
// This file is the main entry point for the module. It will perform any necessary initialization routines, call helper routines to collect any necessary data, and include the template which will display the module output.
// Restrict access.
defined('_JEXEC') or die;
// Require helper.
require_once dirname(__FILE__) . '/helper.php';
// Fetch articles from database.
$articles = modNavSlider::getArticles();
// Load CSS.
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/'.$module->module . "/css/mod_navslider.css");
require JModuleHelper::getLayoutPath('mod_navslider');
?>