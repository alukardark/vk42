<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Application;

global $USER;

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

function getNewUserData($USER_ID)
{

    $USER_NFO     = \CUserExt::getById($USER_ID);
    $USER_PROFILE = \CUserExt::getProfile($USER_ID);

    return array(
        //"ID"             => $USER_NFO["XML_ID"],
        "XML_ID"         => $USER_NFO["XML_ID"],
        "PHONE"          => empty($USER_NFO["PERSONAL_MOBILE"]) ? \CUserExt::getPhone() : $USER_NFO["PERSONAL_MOBILE"],
        "SUBSCRIBE"      => $USER_PROFILE["PROPS_VALUE"]["SUBSCRIBE"]["VALUE"],
        "PERSON_TYPE_ID" => $USER_PROFILE["PERSON_TYPE_ID"] == FIZ_LICO ? 0 : 1,
        "FIO"            => $USER_PROFILE["PROPS_VALUE"]["FIO"]["VALUE"],
        "CITY"           => $USER_NFO["PERSONAL_CITY"],
        "BDAY"           => $USER_NFO["PERSONAL_BIRTHDAY"],
    );
}

if ($sAction == "SEND_SMS")
{
    if (!$USER->IsAuthorized())
    {
        json_result(false, array('message' => "Ошибка авторизации"));
    }

    $USER_ID = $USER->GetId();

    $FIO           = explode(" ", trim($VALUES['FIO']));
    $sUserLastName = "";
    $sUserFullName = "";

    foreach ($FIO as $k => $FIO_PART)
    {
        $sUserFullName .= $FIO_PART . " ";

        if ($k == 0)
        {
            $sUserFisrtName = $FIO_PART;
            continue;
        }

        $sUserLastName .= $FIO_PART . " ";
    }

    $sUserLastName = rtrim($sUserLastName, " ");
    $sUserFullName = rtrim($sUserFullName, " ");

    $sPhone = trim($VALUES['PHONE']);

    //printra($USER_ID);
    //if (!\CUserExt::isUniquePhone($sPhone, $USER_ID))
    {
        //json_result(false, array('message' => 'Телефон ' . $sPhone . ' уже зарегистрирован'));
    }

    //save user in bitrix
    $arUserFields = array(
        "NAME"              => $sUserFisrtName,
        "LAST_NAME"         => $sUserLastName,
        "PERSONAL_PHONE"    => $sPhone,
        "PERSONAL_CITY"     => $VALUES["CITY"],
        "PERSONAL_BIRTHDAY" => $VALUES["BDAY"],
    );

    $USER->Update($USER_ID, $arUserFields);
    $UserUpdateError = $USER->LAST_ERROR;

    if (!empty($UserUpdateError))
    {
        json_result(false, array('message' => $UserUpdateError));
    }

    //save user profile
    $USER_PROFILE    = \CUserExt::getProfile($USER_ID);
    $PERSON_TYPE_ID  = $USER_PROFILE["PERSON_TYPE_ID"];
    $PROPS           = \CUserExt::getUserProps($PERSON_TYPE_ID);
    $arProfileFields = array();
    foreach ($PROPS as $code => $prop)
    {
        if (isset($VALUES[$code])) $arProfileFields[$prop['ID']] = $VALUES[$code];
    }

    $SUBSCRIBE_PROP_ID                   = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "SUBSCRIBE");
    $arProfileFields[$SUBSCRIBE_PROP_ID] = $USER_PROFILE["PROPS_VALUE"]['SUBSCRIBE']["VALUE"];

    $resSaveProfile = \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROFILE["ID"], $sUserFullName, $PERSON_TYPE_ID, $arProfileFields, $arResult);

    if (empty($resSaveProfile))
    {
        json_result(false, array('message' => "Не удалось сохранить профиль покупателя"));
    }

    //save user in 1c
    $USER_NFO     = \CUserExt::getById($USER_ID);
    $USER_PROFILE = \CUserExt::getProfile($USER_ID);

    $arDataSaveUser = getNewUserData($USER_ID);

    $replaySaveUser = \CURL::getReplay("newUser", $arDataSaveUser, true, false, true, false, "Exchange");

    $resultSaveUser = (bool) $replaySaveUser["RESULT"];

    if ($resultSaveUser)
    {
        $XML_ID_1C = $resultSaveUser["XML_ID"];

        //если полученный из 1С XML_ID отлючается от того, 
        //что есть у юзера на сайте - обновим XML_ID на сайте
        if ($XML_ID_1C != $USER_NFO["XML_ID"])
        {
            $arUserFields = array(
                "XML_ID" => $XML_ID_1C,
            );

            $USER->Update($USER_ID, $arUserFields);
            $UserUpdateXmlId = $USER->LAST_ERROR;

            if (!empty($UserUpdateXmlId))
            {
                json_result(false, array('message' => $UserUpdateXmlId));
            }
        }

        //активируем карту
        $arDataSendSms = array(
            "PHONE" => $USER_NFO["PERSONAL_MOBILE"],
            "CARD"  => $VALUES["CARD"],
        );

        $replaySendSms = \CURL::getReplay("SendSMS", $arDataSendSms, true, false, true, false, "Card");

        $resultSendSms = (bool) $replaySendSms["RESULT"];
        $errorSendSms  = (string) $replaySendSms["Error"];

        if (!$resultSendSms)
        {
            if (empty($errorSendSms)) $errorSendSms = "Произошла ошибка";
            json_result(false, array('message' => $errorSendSms));
        }
        else
        {
            json_result(true);
        }
    }

    json_result(false, array('message' => "Не удалось сохранить данные"));
}


if ($sAction == "ACTIVATE")
{
    if (!$USER->IsAuthorized())
    {
        json_result(false, array('message' => "Ошибка авторизации"));
    }

    $USER_ID = $USER->GetId();

    $service = "Card";
    $sMethod = "Activate";

    $arData = array(
        "CODE" => $VALUES["CODE"],
        "CARD" => $VALUES["CARD"],
    );

    $arData += getNewUserData($USER_ID);

    $replay = \CURL::getReplay($sMethod, $arData, true, false, true, false, $service);

    $result = (bool) $replay["RESULT"];
    $error  = (string) $replay["Error"];

    if (!$result)
    {
        if (empty($error)) $error = "Произошла ошибка";
        json_result(false, array('message' => $error));
    }
    else
    {
        //прикрепляем карту
        $USER_NFO = \CUserExt::getById($USER_ID);

        $arData   = array(
            "XML_ID" => $USER_NFO["XML_ID"],
        );
        $ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);

//        $arUserFields                    = array();
//        $arUserFields["UF_CARD_TYPE"]    = $ar1CUser["TYPE_OF_CARD"];
//        $arUserFields["UF_CARD_NUMBER"]  = cutAllButNumbers($ar1CUser["CARD"]);
//        $arUserFields["UF_CARD_BALANCE"] = (int) $ar1CUser["BONUSES"];

        $arUserFields = array(
            "UF_CARD_TYPE"    => $ar1CUser["TYPE_OF_CARD"],
            "UF_CARD_NUMBER"  => cutAllButNumbers($ar1CUser["CARD"]),
            "UF_CARD_BALANCE" => (int) $ar1CUser["BONUSES"],
        );

        setUF("USER", $USER_ID, $arUserFields);

        //$USER->Update($USER_ID, $arUserFields);

        json_result(true);
    }
}


json_result(false, array('message' => "<p>Неизвестная ошибка</p>"));
