<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("ACTION");

if (!$isPost)
{
    json_result(false, array('message' => "<p>Ошибочный запрос</p>"));
}

if (empty($sAction))
{
    json_result(false, array('message' => "<p>Неверное действие</p>"));
}

parse_str($request->getPost("VALUES"), $VALUES);

if (!vk_check_bitrix_sessid($VALUES))
{
    json_result(false, array('message' => '<p>Session error</p>'));
}


if ($sAction == "REGISTER")
{
    $result = \CUserExt::register($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        global $APPLICATION;
        $redirect = PATH_PERSONAL;

        $BACK_URL = $APPLICATION->get_cookie("BACK_URL");
        if (!empty($BACK_URL))
        {
            $redirect = urldecode($BACK_URL);
            $APPLICATION->set_cookie("BACK_URL", null);
        }

        json_result(true, array('redirect' => $redirect));
    }
}

if ($sAction == "AUTH" && !$VALUES['BYCARD'])
{
    $result = \CUserExt::auth($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        global $APPLICATION;
        $redirect = PATH_PERSONAL;

        $BACK_URL = $APPLICATION->get_cookie("BACK_URL");
        if (!empty($BACK_URL))
        {
            $redirect = urldecode($BACK_URL);
            $APPLICATION->set_cookie("BACK_URL", null);
        }

        json_result(true, array('redirect' => $redirect));
    }
}

if ($sAction == "AUTH" && $VALUES['BYCARD'])
{
    $result = \CUserExt::authByCard($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        json_result(true, array('redirect' => $result['redirect']));
    }
}

if ($sAction == "RECOVERY")
{
    $result = \CUserExt::recovery($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        json_result(true, array('message' => $result['message']));
    }
}

if ($sAction == "CHANGE")
{
    $result = \CUserExt::change($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        
        json_result(true, array('message' => $result['message'], 'redirect' => PATH_PERSONAL));
    }
}

if ($sAction == "SAVE_USER")
{
    $result = \CUserExt::saveUser($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        json_result(true, array('message' => $result['message'], 'redirect' => PATH_PERSONAL));
    }
}

if ($sAction == "ADDCARD")
{
    $result = \CUserExt::addCard($VALUES);

    if (!$result['success'])
    {
        json_result(false, array('message' => $result['message']));
    }
    else
    {
        json_result(true, array('redirect' => $result['redirect']));
    }
}

json_result(false, array('message' => "<p>Неизвестная ошибка</p>"));
