<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?

if (isPost())
{
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
}
?>

<?

$APPLICATION->IncludeComponent(
        "bitrix:form.result.new", "buy_one_click", Array(
    "CACHE_TIME"             => "360000",
    "CACHE_TYPE"             => "A",
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
    "VARIABLE_ALIASES"       => Array("RESULT_ID" => "RESULT_ID", "WEB_FORM_ID" => "WEB_FORM_ID"),
    "WEB_FORM_ID"            => FORM_SUPPORT
        )
);
?>

<?

if (isPost())
{
    die;
}
?>