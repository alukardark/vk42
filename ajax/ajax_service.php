<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("ACTION");

if (!$isPost)
{
    json_result(false, array('alert' => "Ошибочный запрос"));
}

if (empty($sAction))
{
    json_result(false, array('alert' => "Неверное действие"));
}

if ($sAction == "set_store")
{
    global $APPLICATION;

    //все ТСЦ
    $arStores = \CCatalogExt::getStores(null, true);
    $arI      = array();
    foreach ($arStores as $arStore)
    {
        $arI[$arStore["UF_STORE_CITY"]][] = $arStore["ID"];
    }

    $STRORE_XML_ID = $request->getPost("STRORE_XML_ID");
    $result        = \CServicesExt::getItems(array($STRORE_XML_ID));

    //метод вернет массив, но там только один элемент должен быть
    $storesByXML_ID  = \CCatalogExt::getStoresByXML_ID(array($STRORE_XML_ID));
    $result['STORE'] = array_shift($storesByXML_ID);

    if (!empty($STRORE_XML_ID))
    {
        $APPLICATION->set_cookie("STORE_XML_ID", $STRORE_XML_ID, time() + 60 * 60 * 24 * 30);
    }
    else
    {
        $APPLICATION->set_cookie("STORE_XML_ID", null, time() + 60 * 60 * 24 * 30);
    }

    json_result(true, array('result' => $result));
}


if ($sAction == "GET_OPERATIONS")
{
    $CITY = $request->getPost("CITY");

    //массив категорий услуг
    $arOperations = \CServicesExt::getOperations($CITY);
    //$arTimes      = \CServicesExt::GetDateTime("52b8638a-5f0f-11e8-9b80-005056b77576", "20d2a91e-9a57-11e6-ad05-005056b77576");
    //группы и услуги
    $arGroups = array();
    $arItems  = array();
    foreach ($arOperations as $arOperation)
    {
        $group = $arOperation["Group"];

        $arGroups[]           = $group;
        $arItems[$group][]    = $arOperation['Operation'];
        $arServices[$group][] = $arOperation;
    }

    $arStores = \CCatalogExt::getTSC('Кемерово', true);

    $arResult = array(
        'OPERATIONS' => $arOperations,
        'GROUPS'     => array_unique($arGroups, SORT_LOCALE_STRING),
        'ITEMS'      => array_unique($arItems),
        'SERVICES'   => $arServices,
        'TSC'        => $arStores,
    );

    json_result(true, $arResult);
}

//json_result(true, $arResult);

json_result(false, array('alert' => "Неизвестная ошибка"));
