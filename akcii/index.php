<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Скидки и акции сети автосервисов «Континент шин»");
$APPLICATION->SetPageProperty("title", "Акции и скидки - Автоcервис-центр «Континент шин» в Кемерово и Новокузнецке");
$APPLICATION->SetTitle("Акции");
?>

<?

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/catalog.css");
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/actions.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/catalog.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/actions.js");

global $arActionsFilter;
$arActionsFilter = array("!PROPERTY_PROMO_VALUE" => "Да"); //исключая главную акцию
?>

<?

$APPLICATION->IncludeComponent(
	"bitrix:news", 
	"actions", 
	array(
		"IBLOCK_ID" => ACTIONS_IB,
		"NEWS_COUNT" => "10",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "PROMO",
			2 => "REF_ACTION",
			3 => "ICON",
			4 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_TYPE" => "actions",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "j F Y",
		"LIST_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "",
			1 => "PROMO",
			2 => "REF_ACTION",
			3 => "ICON",
			4 => "",
		),
		"MESSAGE_404" => "Акция не найдена",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "catalog",
		"PAGER_TITLE" => "Акции",
		"PREVIEW_TRUNCATE_LEN" => "300",
		"SEF_FOLDER" => "/akcii/",
		"SEF_MODE" => "Y",
		"SET_LAST_MODIFIED" => "Y",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SORT_BY2" => "ACTIVE_FROM",
		"SORT_BY1" => "SORT",
		"SORT_ORDER2" => "DESC",
		"SORT_ORDER1" => "ASC",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "Y",
		"FILTER_NAME" => "arActionsFilter",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N",
		"COMPONENT_TEMPLATE" => "actions",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"STRICT_SECTION_CHECK" => "N",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>