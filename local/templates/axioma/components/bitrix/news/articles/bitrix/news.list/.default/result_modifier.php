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



foreach ($arResult["ITEMS"] as &$arItem)
{
    $arItem['PREVIEW_RESIZED'] = \CPic::getPreviewSrc($arItem, 720, 330, BX_RESIZE_IMAGE_EXACT, false, 100);
}

$arResult["ARTICLE_TITLE"] = '';

if (!empty($arResult['SECTION']['PATH']))
{
    $arPath                    = end($arResult['SECTION']['PATH']);
    $arResult["ARTICLE_TITLE"] = $arPath['NAME'];
}
?>