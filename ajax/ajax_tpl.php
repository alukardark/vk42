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


//json_result(true, $arResult);

json_result(false, array('alert' => "Неизвестная ошибка"));
