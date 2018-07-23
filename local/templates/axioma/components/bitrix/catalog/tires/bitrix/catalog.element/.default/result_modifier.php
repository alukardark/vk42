<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $USER;

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();
$isPost  = $request->isPost();

/**
 * получаем из путя типа "https://cs42.ru/katalog/hankok/185_65_r15_hankook_winter_w419_i_pike_rs_xl_92t_ship/?k=k"
 * папку второго уровня. Вданном примере это "hankok"
 */
$_REQUEST_URI  = $server->get("REQUEST_URI");
$arRequest     = parse_url($_REQUEST_URI);
$_REQUEST_PATH = $arRequest["path"];

$realDetailUrl = \CCatalogExt::getProductUrl($arResult);

if ($realDetailUrl != $_REQUEST_PATH)
{
    LocalRedirect($realDetailUrl, false, '301 Moved permanently');
    exit;
}

$arResult['USER_CITY']              = $arParams['USER_CITY'];
$arResult['USER_CITY_KEY']          = $arParams['USER_CITY_KEY'];
$arResult['BASKET_RECORD_QUANTITY'] = $arParams['BASKET_RECORD_QUANTITY'];
$arResult['DELIVERY_QUANTITY']      = $arParams['DELIVERY_QUANTITY'];
$arResult['DELIVERY_DATE_PRINT']    = $arParams['DELIVERY_DATE_PRINT'];
$arResult['STORES_AMOUNT']          = $arParams['STORES_AMOUNT'];

$arResult['DETAIL_BIG']     = \CPic::getDetailSrc($arResult, 1920, 1920, BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, 95, true);
$arResult['DETAIL_RESIZED'] = \CPic::getDetailSrc($arResult, 500, 500, BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false, 85, true);

//отсортируем свойства в нужном порядке
$arDisplayPropsCodes = \CCatalogExt::getPropertiesCodes($arResult['IBLOCK_ID']);
$arDisplayProps      = $arResult['DISPLAY_PROPERTIES'];

$arResult['DISPLAY_PROPERTIES'] = array();

foreach ($arDisplayPropsCodes as $sPropertyCode)
{
    if (empty($arDisplayProps[$sPropertyCode])) continue;

    $arResult['DISPLAY_PROPERTIES'][$sPropertyCode] = $arDisplayProps[$sPropertyCode];
}

foreach ($arResult['DISPLAY_PROPERTIES'] as &$arProperty)
{
    if ($arProperty['NAME'] == "Объём")
    {
        $arProperty['DISPLAY_VALUE'] .= " л.";
    }

    $arProperty['NAME'] = str_replace(array("СМ_", "ОЖ_", "АКБ_"), "", $arProperty['NAME']);
    $arProperty['NAME'] = str_replace("ВидМасла", "Вид", $arProperty['NAME']);
    $arProperty['NAME'] = str_replace("Диски_Марка", "Марка", $arProperty['NAME']);
    $arProperty['NAME'] = str_replace("КреплениеДиска", "Крепление (PCD)", $arProperty['NAME']);
    $arProperty['NAME'] = str_replace("ШиринаОбода", "Ширина обода", $arProperty['NAME']);
    $arProperty['NAME'] = str_replace("DIA", "Центральное отверстие (DIA)", $arProperty['NAME']);
}

if ($USER->IsAdmin() && $_REQUEST['allprops'] == 'allprops')
{
    $arResult['DISPLAY_PROPERTIES'] = $arResult['PROPERTIES'];

    usort($arResult['DISPLAY_PROPERTIES'], function($a, $b) {
        return $a['NAME'] > $b['NAME'];
    });
}

$PATH = $arResult["SECTION"]["SECTION_PAGE_URL"] . $arResult["CODE"] . "/";

$CANONICAL_PAGE_URL              = SITE_PROTOCOL . SITE_URL . $PATH;
$arResult['CANONICAL_PAGE_URL']  = $CANONICAL_PAGE_URL;
$arResult['~CANONICAL_PAGE_URL'] = $CANONICAL_PAGE_URL;

\CCatalogExt::setName($arResult);

$arResult['NAME'] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"];
