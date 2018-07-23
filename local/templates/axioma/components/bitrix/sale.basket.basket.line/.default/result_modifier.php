<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult["BASKET"] = false;

if (!isBot())
{
    $arResult["BASKET"] = \CBasketExt::getBasketNew();
}