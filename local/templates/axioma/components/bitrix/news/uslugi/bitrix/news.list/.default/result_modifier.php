<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
$selectedCookieStoreXMLID = $APPLICATION->get_cookie("STORE_XML_ID");
$selectedCookieStoreData  = array();

if (!empty($selectedCookieStoreXMLID))
{
    $selectedCookieStoreData = \CServicesExt::getItems(array($selectedCookieStoreXMLID));
}

foreach ($arResult["ITEMS"] as &$arItem)
{
    $arItem['PREVIEW_RESIZED'] = \CPic::getPreviewSrc($arItem, 720, 330, BX_RESIZE_IMAGE_EXACT, false, 100);
    $arItem['ACTIVE']          = in_array($arItem["ID"], $selectedCookieStoreData["ELEMENTS"]) || empty($selectedCookieStoreData);
}
?>