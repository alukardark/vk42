<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/catalog.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/catalog.css");