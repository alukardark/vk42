<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if ($USER->IsAdmin())
{
    echo '<h1>Testing</h1><hr>';

    $service = "Exchange";


    //$sMethod = "getUserByCard";
//    $arData = array(
//        "CARD"  => "9154",
//        "FIO"   => "Топорков Евгений",
//        "PHONE" => "79234780000",
//    );

    $sMethod = "getUserDataByXmlId";
    $arData = array(
        "XML_ID" => "0ba55e6b-2a57-11df-9812-001d7d4808c5",
    );

    $res = \CURL::getReplay($sMethod, $arData, true, true, true, false, $service, true);

    printra($res);
}
