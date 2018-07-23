<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$_REQUEST["show_all"] = "Y";

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

$APPLICATION->SetTitle("Заказы");
?>

<div class="row">
    <div class="offset-4 col-16 offset-xxl-3 col-xxl-18 offset-xl-2 col-xl-20 offset-lg-1 col-lg-22">
        <?
        $APPLICATION->IncludeComponent("bitrix:sale.personal.order", "", Array(
            "STATUS_COLOR_N"                => "green",
            "STATUS_COLOR_P"                => "yellow",
            "STATUS_COLOR_F"                => "gray",
            "STATUS_COLOR_PSEUDO_CANCELLED" => "red",
            "SEF_MODE"                      => "Y",
            "ORDERS_PER_PAGE"               => 10,
            "PATH_TO_PAYMENT"               => PATH_PAYMENT,
            "PATH_TO_BASKET"                => PATH_BASKET,
            "SET_TITLE"                     => "N",
            "SAVE_IN_SESSION"               => "N",
            "NAV_TEMPLATE"                  => "catalog",
            "ACTIVE_DATE_FORMAT"            => "d.m.Y",
            "PROP_1"                        => Array(),
            "PROP_2"                        => Array(),
            "CACHE_TYPE"                    => "A",
            "CACHE_TIME"                    => "3600",
            "CACHE_GROUPS"                  => "Y",
            "CUSTOM_SELECT_PROPS"           => "",
            "HISTORIC_STATUSES"             => "",
            "SEF_FOLDER"                    => PATH_PERSONAL_ORDERS,
            "SEF_URL_TEMPLATES"             => Array(
                "list"   => "list/",
                "detail" => "detail/#ID#/",
                "cancel" => "cancel/#ID#/"
            ),
            "VARIABLE_ALIASES"              => Array(
                "list"   => Array(),
                "detail" => Array(
                    "ID" => "ID"
                ),
                "cancel" => Array(
                    "ID" => "ID"
                ),
            )
                )
        );
        ?>
    </div>
</div>

<div class="backlink">
    <a href="<?= PATH_PERSONAL ?>" title="Назад в кабинет">
        <i class="ion-ios-arrow-back"></i><span>Назад в кабинет</span>
    </a>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>