<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
global $APPLICATION;

$DEFAULT_CITY = 'Kemerovo';

$arAllowedCities = array(
    'Kemerovo'    => "Кемерово",
    'Novokuzneck' => "Новокузнецк",
);

$userCityKey = $APPLICATION->get_cookie("USER_CITY");

if (empty($userCityKey) || !array_key_exists($userCityKey, $arAllowedCities))
{
    $userCityKey = $DEFAULT_CITY;
}

$CURRENT_CITY = $arAllowedCities[$userCityKey];

if (isPost())
{
    $APPLICATION->RestartBuffer();
}
?>

<?
$APPLICATION->IncludeComponent(
        "bitrix:form.result.new", "service_entry", Array(
    "CACHE_TIME"             => "360000",
    "CACHE_TYPE"             => "N",
    "CHAIN_ITEM_LINK"        => "",
    "CHAIN_ITEM_TEXT"        => "",
    "EDIT_URL"               => "",
    "IGNORE_CUSTOM_TEMPLATE" => "Y",
    "LIST_URL"               => "",
    "SEF_MODE"               => "N",
    "SUCCESS_URL"            => "",
    "USE_EXTENDED_ERRORS"    => "Y",
    "SHOW_LIST_PAGE"         => "N",
    "SHOW_EDIT_PAGE"         => "N",
    "CURRENT_CITY"           => $CURRENT_CITY,
    "VARIABLE_ALIASES"       => Array("RESULT_ID" => "RESULT_ID", "WEB_FORM_ID" => "WEB_FORM_ID"),
    "WEB_FORM_ID"            => FORM_SERVICE_ENTRY
        )
);
?>

<div class="form-se__map">
    map here
</div>

<?
if (isPost())
{
    die;
}
?>