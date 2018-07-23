<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$context = \Bitrix\Main\Application::getInstance()->getContext();
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

if ($sAction == 'SET_FILTER_TYPE')
{
    global $APPLICATION;
    $APPLICATION->set_cookie("FILTER_TYPE", $request->getPost("FILTER_TYPE"));

    json_result(true, null);
}

if ($sAction == 'HIDE_NOTE')
{
    global $APPLICATION;
    $APPLICATION->set_cookie("USER_READED_NOTE", 1, time() + 60 * 60 * 12);

    json_result(true, null);
}

if ($sAction == 'HIDE_ATTENTION')
{
    global $APPLICATION;
    $APPLICATION->set_cookie("USER_READED_ATTENTION", 1, time() + 60 * 60 * 24 * 365);

    json_result(true, null);
}

if ($sAction == 'SET_BACK_URL')
{
    global $APPLICATION;
    $BACK_URL = $request->getPost("BACK_URL");

    $APPLICATION->set_cookie("BACK_URL", $BACK_URL, time() + 60 * 15);

    json_result(true, null);
}

if ($sAction == "CHECK_UNIQUE")
{
    global $USER;

    $USER_ID = $USER->GetId();

    $message = null;
    $type    = $request->getPost("TYPE");
    $value   = $request->getPost("VALUE");

    $result = true;

    if (!empty($value) && $value != "+7 (___) ___-__-__")
    {
        if ($type == "phone")
        {
            $result = \CUserExt::isUniquePhone($value, $USER_ID, true);
        }
        elseif ($type == "email")
        {
            $result = \CUserExt::isUniqueEmail($value, $USER_ID, true);
        }
    }

    json_result(true, array("unique" => $result));
}

json_result(false, array('alert' => "Неизвестная ошибка"));