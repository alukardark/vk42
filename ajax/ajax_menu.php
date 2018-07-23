<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("action");

if (!$isPost)
{
    json_result(false, "invalid request");
}

if (empty($sAction))
{
    json_result(false, "invalid action");
}

if ($sAction == 'set_city')
{
    $sCityKey = \Axi::updateCityKey($request->getPost("cityKey"));
    json_result(true, array('cityKey' => $sCityKey));
}


json_result(false, "unknown error");
