<?
/*
BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение 
 * из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

foreach ($arResult["ITEMS"] as &$arItem)
{
    //$arItem['PREVIEW_RESIZED'] = CPic::getPreviewSrc($arItem, 750, 374, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
    //$arItem['DETAIL_RESIZED']  = CPic::getDetailSrc($arItem, 750, 374, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
}

$obNavResult                = $arResult['NAV_RESULT'];
$arResult["NavNum"]         = $obNavResult->NavNum;
$arResult["NavPageCount"]   = $obNavResult->NavPageCount;
$arResult["NavPageNomer"]   = $obNavResult->NavPageNomer;
$arResult["NavPageSize"]    = $obNavResult->NavPageSize;
$arResult["NavRecordCount"] = $obNavResult->NavRecordCount;
$arResult["MORE_COUNT"]     = $arResult["NavRecordCount"] - $arResult["NavPageNomer"] * $arResult["NavPageSize"];
?>