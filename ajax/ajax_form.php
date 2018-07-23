<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("action");

$arClientsId = array(
    133, //ТК «РАТЭК»
    136, //ТК «Энергия»
    10, //ТК «Деловые линии»
);


if (!$isPost)
{
    json_result(false, array('alert' => "Ошибочный запрос"));
}

if (empty($sAction))
{
    json_result(false, array('alert' => "Неверное действие"));
}


if ($sAction == "get_buy_one_click_form")
{
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
    $APPLICATION->IncludeFile("/include/forms/form_buy_one_click.php");
    die;
}

if ($sAction == "get_help_akb_form")
{
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
    $APPLICATION->IncludeFile("/include/forms/form_help_akb.php");
    die;
}

if ($sAction == "get_delivery_calc_variants")
{
    $data = $request->getPost("data");
    parse_str($data, $params);

    $default_weight = 10;
    $default_volume = 0.1;

    if (isset($params["ANOTHER_CITY_PROP"])) {
        $toCity       = $params["ANOTHER_CITY_PROP"];
        $needDeliver  = (int) !empty($params["DELIVERY_TK_NEED_DELIVERY"]);
        $needLathing  = (int) !empty($params["DELIVERY_TK_LATHING"]);
        $needInsuring = (int) !empty($params["DELIVERY_TK_INSURING"]);
    } else {
        $toCity       = $params["form_text_36"];
        $needDeliver  = (int) !empty($params["form_checkbox_NEED_DELIVERY"][0]);
        $needLathing  = (int) !empty($params["form_checkbox_LATHING"][0]);
        $needInsuring = (int) !empty($params["form_checkbox_INSURING"][0]);
    }

    $cargoPrice = 0; //Стоимость груза. Параметр используется при расчете стоимости страхования.
    $quantities = array();
    $palletize  = array();
    $lathing    = array();
    $weights    = array();
    $volumes    = array();

    $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
    foreach ($basket as $basketItem)
    {
        $PRODUCT_ID = $basketItem->getProductId();
        $QUANTITY   = $basketItem->getQuantity();
        $cargoPrice += $basketItem->getPrice();

        $arFilter = ["ID" => $PRODUCT_ID];
        $arSelect = ["ID", "IBLOCK_ID", "PROPERTY_CML2_TRAITS"];

        $obList    = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        if ($obElement = $obList->GetNextElement())
        {
            $arProps     = $obElement->GetProperties();
            $CML2_TRAITS = $arProps["CML2_TRAITS"];

            $weight = null;
            $volume = null;

            foreach ($CML2_TRAITS["DESCRIPTION"] as $k => $descr)
            {
                if ($descr == "Вес") $weight = $CML2_TRAITS["VALUE"][$k];
                if ($descr == "Объем") $volume = $CML2_TRAITS["VALUE"][$k];
            }

            $quantities[] = $QUANTITY;
            $palletize[]  = 0;
            $lathing[]    = $needLathing;
            $weights[]    = empty($weight) ? $default_weight : $weight;
            $volumes[]    = empty($volume) ? $default_volume : $volume;
        }
    }

    if (empty($needInsuring))
    {
        $cargoPrice = 0;
    }

    if (empty($quantities))
    {
        $quantities[] = 1;
        $palletize[]  = 0;
        $lathing[]    = $needLathing;
        $weights[]    = $default_weight;
        $volumes[]    = $default_volume;
    }

    $sUrl       = "https://capi.sbl.su/calc/group";
    $arData     = array(
        "from-country"  => "RU",
        "from-city"     => "Кемерово",
        "to-city"       => $toCity,
        "to-country"    => "RU",
        "need-pickup"   => 0,
        "need-deliver"  => $needDeliver,
        "need-insuring" => $needInsuring,
        "cargo-price"   => $cargoPrice,
        "weights"       => $weights,
        "volumes"       => $volumes,
        "quantities"    => $quantities,
        "palletize"     => $palletize,
        "lathing"       => $lathing,
    );
    $arVarinats = \CURL::sendSbl($sUrl, $arData, false, true, false);

    $arResult = array();
    foreach ($arVarinats['result'] as $arVarinat)
    {
        if (in_array($arVarinat["client_id"], $arClientsId))
        {
            $arResult[] = $arVarinat;
        }
    }

    if (empty($arResult))
    {
        json_result(false, array('alert' => "Не найдено ни одного варианта"));
    }


    json_result(true, $arResult);
}

if ($sAction == "get_delivery_calc_destinations")
{
    $QUERY = $request->getPost("QUERY");

    $sUrl     = "https://capi.sbl.su/city/to-group";
    $arData   = array(
        "from-country" => "RU",
        "from-city"    => "Кемерово",
        "to-city-like" => ucfirst(strtolower($QUERY)),
        "limit"        => "10",
        "client-ids"   => $arClientsId,
    );
    $arCities = \CURL::sendSbl($sUrl, $arData, false, true, false);

    $arResult = array();
    foreach ($arCities["cities"] as $item)
    {
        $arResult[] = array(
            "value" => $item[2],
            "data"  => $item[0],
        );
    }

    json_result(true, $arResult);
}


//json_result(true, $arResult);

json_result(false, array('alert' => "Неизвестная ошибка"));
