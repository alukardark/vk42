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

$MAIN_ACTION = null;
if (!isPost() && $arResult["NavPageNomer"] == 1)
{
    //получим главную акцию
    $arFilter  = Array("IBLOCK_ID" => ACTIONS_IB, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "=PROPERTY_PROMO_VALUE" => "Да");
    $obList    = \CIBlockElement::GetList(array(), $arFilter);
    if ($arElement = $obList->GetNextElement(true, false))
    {

        $MAIN_ACTION               = $arElement->GetFields() + array("MAIN_ACTION" => "Y");
        $MAIN_ACTION["PROPERTIES"] = $arElement->GetProperties();

        array_unshift($arResult["ITEMS"], $MAIN_ACTION);
    }
}

foreach ($arResult["ITEMS"] as &$arItem)
{
    $isMainAction = $arItem['PROPERTIES']['PROMO']['VALUE_XML_ID'];

    if ($isMainAction == "Y")
    {
        $arItem['PREVIEW_RESIZED'] = \CPic::getPreviewSrc($arItem, 750, 375, BX_RESIZE_IMAGE_EXACT, false, 100);
    }
    else
    {
        $arItem['PREVIEW_RESIZED'] = \CPic::getPreviewSrc($arItem, 720, 330, BX_RESIZE_IMAGE_EXACT, false, 100);
    }
}
?>