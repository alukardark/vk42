<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сеть сервис-центров «Континент шин» предлагает: купить шины, масла и аккумуляторы, а также пройти обслуживание своего автомобиля. Полный перечень услуг, гарантия качества.");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("title", "Статьи | Сервис-центры «Континент шин» — шины, масла, технические жидкости, обслуживание автомобиля");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/actions.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/actions.js");
?>

<?

$APPLICATION->IncludeComponent(
        "bitrix:news", "articles", Array(
    "IBLOCK_ID"                       => ARTICLES_IB,
    "NEWS_COUNT"                      => "10",
    "ADD_ELEMENT_CHAIN"               => "Y",
    "ADD_SECTIONS_CHAIN"              => "N",
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
    "DETAIL_FIELD_CODE"               => array(),
    "DETAIL_PAGER_SHOW_ALL"           => "N",
    "DETAIL_PAGER_TEMPLATE"           => "",
    "DETAIL_PAGER_TITLE"              => "Страница",
    "DETAIL_PROPERTY_CODE"            => array(),
    "DETAIL_SET_CANONICAL_URL"        => "N",
    "DISPLAY_BOTTOM_PAGER"            => "Y",
    "DISPLAY_NAME"                    => "Y",
    "DISPLAY_TOP_PAGER"               => "N",
    "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
    "IBLOCK_TYPE"                     => "news",
    "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
    "LIST_ACTIVE_DATE_FORMAT"         => "j F Y",
    "LIST_FIELD_CODE"                 => array(),
    "LIST_PROPERTY_CODE"              => array(),
    "MESSAGE_404"                     => "Новость не найдена",
    "META_DESCRIPTION"                => "-",
    "META_KEYWORDS"                   => "-",
    "PAGER_BASE_LINK_ENABLE"          => "N",
    "PAGER_DESC_NUMBERING"            => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL"                  => "N",
    "PAGER_SHOW_ALWAYS"               => "N",
    "PAGER_TEMPLATE"                  => "catalog",
    "PAGER_TITLE"                     => "Новости",
    "PREVIEW_TRUNCATE_LEN"            => "300",
    "SEF_FOLDER"                      => "/articles/",
    "SEF_MODE"                        => "Y",
    "SEF_URL_TEMPLATES"               => Array(
        "section" => "#SECTION_CODE#/",
        "detail"  => "#SECTION_CODE#/#ELEMENT_CODE#/",
        "news"    => "",
    ),
    "SET_LAST_MODIFIED"               => "Y",
    "SET_STATUS_404"                  => "N",
    "SET_TITLE"                       => "Y",
    "SHOW_404"                        => "N",
    "SORT_BY2"                        => "ACTIVE_FROM",
    "SORT_BY1"                        => "SORT",
    "SORT_ORDER2"                     => "DESC",
    "SORT_ORDER1"                     => "ASC",
    "USE_CATEGORIES"                  => "N",
    "USE_FILTER"                      => "N",
    "FILTER_NAME"                     => "arArticlesFilter",
    "USE_PERMISSIONS"                 => "N",
    "USE_RATING"                      => "N",
    "USE_RSS"                         => "N",
    "USE_SEARCH"                      => "N"
        ), false, array("HIDE_ICONS" => "Y")
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>