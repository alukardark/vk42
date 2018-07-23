<?

/*
  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение
 * из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult['DETAIL_RESIZED'] = \CPic::getDetailSrc($arResult, 750, 370, BX_RESIZE_IMAGE_EXACT);

$arResult['ICON_RESIZED'] = false;
if (!empty($arResult['PROPERTIES']['ICON']['VALUE']))
{
    $arResult['ICON_RESIZED'] = \CPic::getResized($arResult['PROPERTIES']['ICON']['VALUE'], 150, 38, BX_RESIZE_IMAGE_EXACT);
}

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_common/news.detail/";
$cacheID   = "news.detail" . serialize($arResult['PROPERTIES']["TAGS"]["VALUE"]) . $arResult['ID'] . $arResult['ACTIVE_FROM'] . serialize($_SESSION['TAGS_SELECTED_IDS']);

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arTags"]) && isset($vars["NEXT_NEWS"]))
    {
        $arTags    = $vars["arTags"];
        $NEXT_NEWS = $vars["NEXT_NEWS"];
        $lifeTime  = 0;
    }
}

if ($lifeTime > 0)
{
    $arTags = array();

    $arOrder  = array("SORT" => "ASC", "ID" => "DESC");
    $arFilter = array("IBLOCK_ID" => NEWS_TAGS_IB, "ACTIVE" => "Y", "ID" => $arResult['PROPERTIES']["TAGS"]["VALUE"]);
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE");
    $obList   = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
    while ($arFetch  = $obList->Fetch())
    {
        $arTags[] = $arFetch;
    }

    //get next news
    $arOrder  = array("ACTIVE_FROM" => "DESC", "SORT" => "ASC");
    $arFilter = array(
        "IBLOCK_ID"         => NEWS_IB,
        "ACTIVE"            => "Y",
        "!ID"               => $arResult['ID'],
        "<DATE_ACTIVE_FROM" => $arResult['ACTIVE_FROM'],
        "PROPERTY_TAGS"     => $_SESSION['TAGS_SELECTED_IDS'],
    );
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE");

    $obList    = \CIBlockElement::GetList($arOrder, $arFilter, false, array("nTopCount" => 1), $arSelect);
    $NEXT_NEWS = $obList->Fetch();

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arTags"    => $arTags,
        "NEXT_NEWS" => $NEXT_NEWS,
    ));
}

$arResult['TAGS']      = $arTags;
$arResult['NEXT_NEWS'] = $NEXT_NEWS;
?>