<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Сеть сервис-центров «Континент шин» предоставляет услуги шиномонтажа, ремонта ходовой части, замены масел, ремонта тормозной системы, обслуживание аккумуляторов и многие другие");
$APPLICATION->SetPageProperty("title", "Услуги - Автоcервис-центр «Континент шин» в Кемерово и Новокузнецке");
$APPLICATION->SetTitle("Услуги");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/services.css");
?>

<?
$APPLICATION->IncludeComponent(
    "bitrix:news", "uslugi", Array(
    "CURRENT_CITY_NAME"               => \Axi::getCityName(),
    "IBLOCK_ID"                       => USLUGI_IB,
    "NEWS_COUNT"                      => "99",
    "ADD_ELEMENT_CHAIN"               => "Y",
    "ADD_SECTIONS_CHAIN"              => "Y",
    "AJAX_MODE"                       => "N",
    "AJAX_OPTION_ADDITIONAL"          => "",
    "AJAX_OPTION_HISTORY"             => "N",
    "AJAX_OPTION_JUMP"                => "N",
    "AJAX_OPTION_STYLE"               => "Y",
    "BROWSER_TITLE"                   => "-",
    "CACHE_FILTER"                    => "Y",
    "CACHE_GROUPS"                    => "N",
    "CACHE_TIME"                      => "36000000",
    "CACHE_TYPE"                      => "A",
    "CHECK_DATES"                     => "Y",
    "DETAIL_ACTIVE_DATE_FORMAT"       => "j F Y",
    "DETAIL_DISPLAY_BOTTOM_PAGER"     => "N",
    "DETAIL_DISPLAY_TOP_PAGER"        => "N",
    "DETAIL_FIELD_CODE"               => array("", ""),
    "DETAIL_PAGER_SHOW_ALL"           => "N",
    "DETAIL_PAGER_TEMPLATE"           => "",
    "DETAIL_PAGER_TITLE"              => "Страница",
    "DETAIL_PROPERTY_CODE"            => array("PRICE", "IS_PRICE_EXACT", "STORES"),
    "DETAIL_SET_CANONICAL_URL"        => "N",
    "DISPLAY_BOTTOM_PAGER"            => "Y",
    "DISPLAY_NAME"                    => "Y",
    "DISPLAY_TOP_PAGER"               => "N",
    "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
    "IBLOCK_TYPE"                     => "services",
    "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
    "LIST_ACTIVE_DATE_FORMAT"         => "j F Y",
    "LIST_FIELD_CODE"                 => array("", ""),
    "LIST_PROPERTY_CODE"              => array("PRICE", "IS_PRICE_EXACT", "STORES"),
    "MESSAGE_404"                     => "Услуга не найдена",
    "META_DESCRIPTION"                => "-",
    "META_KEYWORDS"                   => "-",
    "PAGER_BASE_LINK_ENABLE"          => "N",
    "PAGER_DESC_NUMBERING"            => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL"                  => "N",
    "PAGER_SHOW_ALWAYS"               => "N",
    "PAGER_TEMPLATE"                  => "catalog",
    "PAGER_TITLE"                     => "Услуги",
    "PREVIEW_TRUNCATE_LEN"            => "300",
    "SEF_FOLDER"                      => "/uslugi/",
    "SEF_MODE"                        => "Y",
    "SEF_URL_TEMPLATES"               => Array(
        "detail"  => "#SECTION_CODE#/#ELEMENT_CODE#/",
        "news"    => "",
        "section" => "#SECTION_CODE#/"
    ),
    "SET_LAST_MODIFIED"               => "Y",
    "SET_STATUS_404"                  => "N",
    "SET_TITLE"                       => "Y",
    "SHOW_404"                        => "N",
    "SORT_BY1"                        => "SORT",
    "SORT_BY2"                        => "ACTIVE_FROM",
    "SORT_ORDER1"                     => "ASC",
    "SORT_ORDER2"                     => "DESC",
    "USE_CATEGORIES"                  => "N",
    "USE_FILTER"                      => "N",
    "FILTER_NAME"                     => "arUslugiFilter",
    "USE_PERMISSIONS"                 => "N",
    "USE_RATING"                      => "N",
    "USE_RSS"                         => "N",
    "USE_SEARCH"                      => "N"
    )
);
?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>