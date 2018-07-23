<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
$USER_ID = $USER->GetID();

if (empty($USER_ID))
{
    LocalRedirect(PATH_AUTH);
    die;
}

$assets = Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/personal.js");

$APPLICATION->SetTitle("Личные данные");
?>

<div class="row">
    <?
    $APPLICATION->IncludeComponent("bitrix:main.profile", "", Array(
        "USER_PROPERTY_NAME"  => "",
        "SET_TITLE"           => "N",
        "AJAX_MODE"           => "N",
        "USER_PROPERTY"       => Array(),
        "SEND_INFO"           => "Y",
        "CHECK_RIGHTS"        => "Y",
        "AJAX_OPTION_JUMP"    => "N",
        "AJAX_OPTION_STYLE"   => "N",
        "AJAX_OPTION_HISTORY" => "N"
            )
    );
    ?>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>