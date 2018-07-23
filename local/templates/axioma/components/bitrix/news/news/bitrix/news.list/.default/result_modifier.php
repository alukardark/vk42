<?

/*
  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение
 * из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obNavResult                = $arResult['NAV_RESULT'];
$arResult["NavNum"]         = $obNavResult->NavNum;
$arResult["NavPageCount"]   = $obNavResult->NavPageCount;
$arResult["NavPageNomer"]   = $obNavResult->NavPageNomer;
$arResult["NavPageSize"]    = $obNavResult->NavPageSize;
$arResult["NavRecordCount"] = $obNavResult->NavRecordCount;
$arResult["MORE_COUNT"]     = $arResult["NavRecordCount"] - $arResult["NavPageNomer"] * $arResult["NavPageSize"];

$arResult["TAGS_URL"] = urlencode(json_encode(array("TAGS" => $arParams['TAGS_SELECTED'])));

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_common/news.list/";
$cacheID   = "news.list" . serialize($arParams['TAGS_SELECTED']) . serialize($arResult["ITEMS"]);

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arTags"]) && isset($vars["arItemTags"]))
    {
        $arTags     = $vars["arTags"];
        $arItemTags = $vars["arItemTags"];
        $lifeTime   = 0;
    }
}

if ($lifeTime > 0)
{
    $arTags = array();
    if (count($arParams['TAGS_SELECTED']))
    {
        $arOrder  = array("SORT" => "ASC", "ID" => "DESC");
        $arFilter = array("IBLOCK_ID" => NEWS_TAGS_IB, "ACTIVE" => "Y", "CODE" => $arParams['TAGS_SELECTED']);
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE");
        $obList   = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($arFetch  = $obList->Fetch())
        {
            $arTags[] = $arFetch;
        }
    }

    $arItemTags = array();
    foreach ($arResult["ITEMS"] as $arItem)
    {
        $arItem['TAGS'] = array();

        $arOrder  = array("SORT" => "ASC", "ID" => "DESC");
        $arFilter = array("IBLOCK_ID" => NEWS_TAGS_IB, "ACTIVE" => "Y", "ID" => $arItem['PROPERTIES']["TAGS"]["VALUE"]);
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE");
        $obList   = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($arFetch  = $obList->Fetch())
        {
            $arItemTags[$arItem["ID"]][] = $arFetch;
        }
    }

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arTags"     => $arTags,
        "arItemTags" => $arItemTags,
    ));
}


foreach ($arResult["ITEMS"] as &$arItem)
{
    $arItem['PREVIEW_RESIZED'] = \CPic::getPreviewSrc($arItem, 750, 370, BX_RESIZE_IMAGE_EXACT, false, 100);
    $arItem['TAGS']            = $arItemTags[$arItem["ID"]];
}
$arResult['TAGS'] = $arTags;
?>