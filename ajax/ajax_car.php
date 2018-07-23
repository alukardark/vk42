<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (empty($_POST['action']))
{
    json_result(false, "invalid action");
}

$sAction = $_POST['action'];


if ($sAction == "get")
{
    if (empty($_POST['next_property']))
    {
        json_result(true, null);
    }

    json_result(true, get_list($_POST['filter'], $_POST['next_property']));
}

json_result(false, "unknown error");

function get_list($filter, $next_property)
{
    $arCarFilter = Array("IBLOCK_ID" => TX_CARS_IB, "ACTIVE" => "Y");

    if (!empty($filter) && is_array($filter))
    {
        foreach ($filter as $property => $value)
        {
            if (!empty($property) && !empty($value))
            {
                $arCarFilter["PROPERTY_" . $property] = $value;
            }
        }
    }

    $arResult  = array();
    $arGroupBy = array("PROPERTY_" . $next_property);
    $obCarList = CIBlockElement::GetList(Array(), $arCarFilter, $arGroupBy);
    while ($arCarItem = $obCarList->GetNext(false, false))
    {
        $arResult[] = $arCarItem["PROPERTY_" . strtoupper($next_property) . "_VALUE"];
    }

    return $arResult;
}
