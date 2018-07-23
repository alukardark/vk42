<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$AUTH_TOKEN = md5('AUTH_TOKEN');
define('AUTH_TOKEN', $AUTH_TOKEN); //c6275ca833ac06c83926ccb00dff4c82

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$isPost  = $request->isPost();

$token    = (string) $request->get("token");
$ACTION   = (string) $request->get("ACTION");
$ORDER_ID = (int) $request->get("ORDER_ID");

if ($token != AUTH_TOKEN)
{
    json_result(false, array('error_message' => "bad token", 'server_time' => time()));
}
//elseif (!$isPost)
//{
//    json_result(false, array('error_message' => "bad request", 'server_time' => time()));
//}
elseif (empty($ACTION))
{
    json_result(false, array('error_message' => "bad ACTION", 'server_time' => time()));
}
elseif (empty($ORDER_ID))
{
    json_result(false, array('error_message' => "bad ORDER_ID", 'server_time' => time()));
}
else
{
    if ($ACTION == "SET_PROPERTY")
    {
        \COrderExt::serviceSetProperty();
    }
    elseif ($ACTION == "SET_PROPERTIES")
    {
        \COrderExt::serviceSetProperties();
    }
    elseif ($ACTION == "SET_STORE")
    {
        \COrderExt::serviceSetStore();
    }
    elseif ($ACTION == "SET_USER")
    {
        $test = array(
            "PERSON_TYPE_ID" => "1",
            "FIO"            => "FIO",
            "PHONE"          => "79236204880",
            "EMAIL"          => "coder12@web-axioma.ru",
            "DELIVERY_DATE"  => "55.55.5555",
        );
//        echo urlencode(serialize($test));
//        die;
        \COrderExt::serviceSetUser();
    }
}

json_result(false, array('error_message' => "unknown error", 'server_time' => time()));


