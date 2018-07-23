<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;
if (!$USER->IsAuthorized())
{
    LocalRedirect(PATH_AUTH);
    die;
}

$USER_ID      = $USER->GetID();
$USER_PROFILE = \CUserExt::getProfile();

if (empty($USER_PROFILE))
{
    //создаем профиль покупателя
    $arFields = array(
        "NAME"           => $USER->GetFullName(),
        "USER_ID"        => $USER_ID,
        "PERSON_TYPE_ID" => FIZ_LICO
    );
    \CSaleOrderUserProps::Add($arFields);

    $USER_PROFILE = \CUserExt::getProfile();
}

$subscribes     = explode(";", $USER_PROFILE["PROPS_VALUE"]["SUBSCRIBE"]["VALUE"]);
$subscribeSms   = in_array("SMS", $subscribes);
$subscribeEmail = in_array("EMAIL", $subscribes);

$PERSON_TYPE_ID = $USER_PROFILE["PERSON_TYPE_ID"];

$QUESTIONS = \CUserExt::getUserProps($PERSON_TYPE_ID) +
        array(
            "PASSWORD"  => array(
                "NAME"        => "PASSWORD",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
            "PASSWORD2" => array(
                "NAME"        => "PASSWORD2",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Повторите пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
);

foreach ($QUESTIONS as $code => &$arQuestion)
{
    $arQuestion["VALUE"] = $USER_PROFILE["PROPS_VALUE"][$code]["VALUE"];
}


$arResult["QUESTIONS"]       = $QUESTIONS;
$arResult["SUBSCRIBE_SMS"]   = $subscribeSms;
$arResult["SUBSCRIBE_EMAIL"] = $subscribeEmail;
