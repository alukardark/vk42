<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//собираем ID складов
$arStoresIds = array();
$arResult['TOTAL_AMOUNT'] = 0;
//dmp($arResult,1,0);
foreach ($arResult["STORES"] as $pid => $arProperty)
{
    $arStoresIds[] = $arProperty['ID'];
    $arResult['TOTAL_AMOUNT'] += $arProperty["REAL_AMOUNT"];
}

$arResult["STORES_ORIGINAL"] = array();
$arFilter     = array("ID" => $arStoresIds);
$obList       = CCatalogStore::GetList(array(), $arFilter);
while ($arFetch      = $obList->Fetch())
{
    $arResult["STORES_ORIGINAL"][$arFetch['ID']] = $arFetch;
}

?>