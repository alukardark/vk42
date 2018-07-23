<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("ACTION");

if (!$isPost)
{
    json_result(false, "invalid request");
}

if (empty($sAction))
{
    json_result(false, "invalid action");
}

if ($sAction == "get_filter_car")
{
    json_result(true, \CCatalogExt::setCarFilter($request->getPost("FILTER"), $request->getPost("IB"), $request->getPost("SC")));
}

json_result(false, "unknown error");
