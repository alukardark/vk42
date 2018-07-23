<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Новости сети автосервисов «Континент шин»");
$APPLICATION->SetPageProperty("title", "Новости - Автоcервис-центр «Континент шин» в Кемерово и Новокузнецке");
$APPLICATION->SetTitle("Новости");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/actions.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/actions.js");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$arTagsRequest = array(); //список переданных в POST или GET тегов


$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_common/news.arTagsList/";
$cacheID   = "arTagsList";

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arTagsList"]))
    {
        $arTagsList = $vars["arTagsList"];
        $lifeTime   = 0;
    }
}

if ($lifeTime > 0)
{
    $arTagsList = array(); //список всех доступных тегов

    $arOrder  = array("SORT" => "ASC", "ID" => "DESC");
    $arFilter = array("IBLOCK_ID" => NEWS_TAGS_IB, "ACTIVE" => "Y");
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE");
    $obList   = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
    while ($arFetch  = $obList->Fetch())
    {
        $arTagsList[$arFetch["CODE"]] = $arFetch;
    }

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arTagsList" => $arTagsList,
    ));
}


if (!isPost())
{
    if (!empty($request['TAGS']))
    {
        $arTagsRequest = $request->getQuery("TAGS");
    }

    if (empty($arTagsRequest))
    {
        $arTagsRequest = $_SESSION['TAGS_SELECTED'];
    }
}

if (isPost("get_list"))
{
    if (isset($request['TAGS']))
    {
        $arTagsRequest = $request->getPost("TAGS");
    }

    if (isset($request['SHOWMORE']))
    {
        $arTagsRequest = $_SESSION['TAGS_SELECTED'];
    }

    if (empty($arTagsRequest))
    {
        unset($_SESSION['TAGS_SELECTED']);
        unset($_SESSION['TAGS_SELECTED_IDS']);
    }
}

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_common/news.arTagsSelected/";
$cacheID   = "arTagsSelected" . serialize($arTagsRequest);

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arTagsSelected"]) && isset($vars["arTagsSelectedIds"]))
    {
        $arTagsSelected    = $vars["arTagsSelected"];
        $arTagsSelectedIds = $vars["arTagsSelectedIds"];
        $lifeTime          = 0;
    }
}

if ($lifeTime > 0)
{
    $arTagsSelected    = array(); //список выбранных юзером тегов (отфильтрованных)
    $arTagsSelectedIds = array();

    if (!empty($arTagsRequest))
    {
        $arOrder  = array("SORT" => "ASC", "ID" => "DESC");
        $arFilter = array("IBLOCK_ID" => NEWS_TAGS_IB, "ACTIVE" => "Y", "CODE" => $arTagsRequest);
        $arSelect = array("ID", "IBLOCK_ID", "CODE");


        $obList  = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($arFetch = $obList->Fetch())
        {
            $arTagsSelectedIds[] = $arFetch['ID'];
            $arTagsSelected[]    = $arFetch['CODE'];
        }
    }

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arTagsSelectedIds" => $arTagsSelectedIds,
        "arTagsSelected"    => $arTagsSelected,
    ));
}

if (!empty($arTagsRequest))
{
    $_SESSION['TAGS_SELECTED']     = $arTagsSelected;
    $_SESSION['TAGS_SELECTED_IDS'] = $arTagsSelectedIds;

    global $arNewssFilter;
    $arNewssFilter = array("PROPERTY_TAGS" => $arTagsSelectedIds);
}
?>

<?

$APPLICATION->IncludeComponent(
        "bitrix:news", "news", Array(
    "TAGS_LIST"                       => $arTagsList,
    "TAGS_SELECTED"                   => $arTagsSelected,
    "IBLOCK_ID"                       => NEWS_IB,
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
    "DETAIL_PROPERTY_CODE"            => array("TAGS"),
    "DETAIL_SET_CANONICAL_URL"        => "N",
    "DISPLAY_BOTTOM_PAGER"            => "Y",
    "DISPLAY_NAME"                    => "Y",
    "DISPLAY_TOP_PAGER"               => "N",
    "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
    "IBLOCK_TYPE"                     => "news",
    "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
    "LIST_ACTIVE_DATE_FORMAT"         => "j F Y",
    "LIST_FIELD_CODE"                 => array(),
    "LIST_PROPERTY_CODE"              => array("TAGS"),
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
    "PREVIEW_TRUNCATE_LEN"            => "150",
    "SEF_FOLDER"                      => "/novosti/",
    "SEF_MODE"                        => "Y",
    "SEF_URL_TEMPLATES"               => Array(
        "section" => "",
        "detail"  => "#ELEMENT_CODE#/",
        "news"    => "",
    ),
    "SET_LAST_MODIFIED"               => "Y",
    "SET_STATUS_404"                  => "N",
    "SET_TITLE"                       => "Y",
    "SHOW_404"                        => "N",
    "SORT_BY1"                        => "ACTIVE_FROM",
    "SORT_BY2"                        => "SORT",
    "SORT_ORDER1"                     => "DESC",
    "SORT_ORDER2"                     => "ASC",
    "USE_CATEGORIES"                  => "N",
    "USE_FILTER"                      => "Y",
    "FILTER_NAME"                     => "arNewssFilter",
    "USE_PERMISSIONS"                 => "N",
    "USE_RATING"                      => "N",
    "USE_RSS"                         => "N",
    "USE_SEARCH"                      => "N"
        ), false, array("HIDE_ICONS" => "Y")
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>