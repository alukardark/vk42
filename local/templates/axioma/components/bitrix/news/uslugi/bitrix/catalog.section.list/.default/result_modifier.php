<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
$selectedCookieStoreXMLID = $APPLICATION->get_cookie("STORE_XML_ID");
$selectedCookieStoreData  = array();

if (!empty($selectedCookieStoreXMLID))
{
    $selectedCookieStoreData = \CServicesExt::getItems(array($selectedCookieStoreXMLID));
}

//var_dump($selectedCookieStoreData);
//die;

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_services/arSectionElements/";
$cacheID   = "arSectionElements" . $arSection['ID'] . $arSection['NAME'];

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arSectionElements"]))
    {
        $arSectionElements = $vars["arSectionElements"];
        $lifeTime          = 0;
    }
}

if ($lifeTime > 0)
{
    $arSectionElements = array();

    foreach ($arResult['SECTIONS'] as &$arSection)
    {
        $arSectionElements[$arSection['ID']] = array();

        //get service itens
        $arSort   = array("SORT" => "ASC");
        $arFilter = array("IBLOCK_ID" => USLUGI_IB, "ACTIVE" => "Y", "SECTION_ID" => $arSection['ID']);
        $arSelect = array("ID", "NAME");
        $obList   = \CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
        while ($arFetch  = $obList->Fetch())
        {
            if ($arFetch['NAME'] != $arSection['NAME'])
            {
                $arSectionElements[$arSection['ID']][] = array(
                    'ID'   => $arFetch['ID'],
                    'NAME' => $arFetch['NAME'],
                );
            }
        }
    }

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arSectionElements" => $arSectionElements,
    ));
}

foreach ($arResult['SECTIONS'] as &$arSection)
{
    $arSection['ACTIVE'] = in_array($arSection["ID"], $selectedCookieStoreData["SECTIONS"]) || empty($selectedCookieStoreData);

    $arSection['ELEMENTS'] = $arSectionElements[$arSection['ID']];

    foreach ($arSection['ELEMENTS'] as &$arElement)
    {
        $arElement['ACTIVE'] = in_array($arElement["ID"], $selectedCookieStoreData["ELEMENTS"]) || empty($selectedCookieStoreData);
    }

    //get service icon
    if (empty($arSection["PICTURE"]["SRC"]))
    {
        $arSection["PICTURE"]["SRC"] = $this->GetFolder() . "/icon.png";
    }
}
?>