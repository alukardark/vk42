<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Оформление заказа | Сервис-центры «ВК» — шины, масла, технические жидкости, обслуживание автомобиля");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/order.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/kabinet.css");

$APPLICATION->SetTitle("Оформление заказа");
?>

<?
$APPLICATION->IncludeComponent(
        "bitrix:sale.order.ajax", "", array(
    "ADDITIONAL_PICT_PROP_11"        => "-",
    "ALLOW_APPEND_ORDER"             => "Y",
    "ALLOW_AUTO_REGISTER"            => "Y",
    "ALLOW_NEW_PROFILE"              => "N",
    "ALLOW_USER_PROFILES"            => "N",
    "BASKET_IMAGES_SCALING"          => "standard",
    "BASKET_POSITION"                => "after",
    "COMPATIBLE_MODE"                => "Y",
    "DELIVERIES_PER_PAGE"            => "50",
    "DELIVERY_FADE_EXTRA_SERVICES"   => "N",
    "DELIVERY_NO_AJAX"               => "N",
    "DELIVERY_NO_SESSION"            => "Y",
    "DELIVERY_TO_PAYSYSTEM"          => "d2p",
    "DISABLE_BASKET_REDIRECT"        => "N",
    "ONLY_FULL_PAY_FROM_ACCOUNT"     => "N",
    "PATH_TO_AUTH"                   => PATH_AUTH,
    "PATH_TO_BASKET"                 => PATH_BASKET,
    "PATH_TO_PAYMENT"                => PATH_PAYMENT,
    "PATH_TO_PERSONAL"               => PATH_PERSONAL,
    "PAY_FROM_ACCOUNT"               => "N",
    "PAY_SYSTEMS_PER_PAGE"           => "50",
    "PICKUPS_PER_PAGE"               => "50",
    "PRODUCT_COLUMNS_HIDDEN"         => array(),
    "PRODUCT_COLUMNS_VISIBLE"        => array(),
    "PROPS_FADE_LIST_1"              => array(),
    "PROPS_FADE_LIST_2"              => array(),
    "SEND_NEW_USER_NOTIFY"           => "Y",
    "SERVICES_IMAGES_SCALING"        => "standard",
    "SET_TITLE"                      => "Y",
    "SHOW_BASKET_HEADERS"            => "N",
    "SHOW_COUPONS_BASKET"            => "N",
    "SHOW_COUPONS_DELIVERY"          => "N",
    "SHOW_COUPONS_PAY_SYSTEM"        => "N",
    "SHOW_DELIVERY_INFO_NAME"        => "Y",
    "SHOW_DELIVERY_LIST_NAMES"       => "Y",
    "SHOW_DELIVERY_PARENT_NAMES"     => "Y",
    "SHOW_MAP_IN_PROPS"              => "N",
    "SHOW_NEAREST_PICKUP"            => "N",
    "SHOW_NOT_CALCULATED_DELIVERIES" => "N",
    "SHOW_ORDER_BUTTON"              => "always",
    "SHOW_PAY_SYSTEM_INFO_NAME"      => "Y",
    "SHOW_PAY_SYSTEM_LIST_NAMES"     => "Y",
    "SHOW_STORES_IMAGES"             => "N",
    "SHOW_TOTAL_ORDER_BUTTON"        => "N",
    "SKIP_USELESS_BLOCK"             => "N",
    "TEMPLATE_LOCATION"              => "popup",
    "TEMPLATE_THEME"                 => "site",
    "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
    "USE_CUSTOM_ERROR_MESSAGES"      => "N",
    "USE_CUSTOM_MAIN_MESSAGES"       => "N",
    "USE_PRELOAD"                    => "N",
    "USE_PREPAYMENT"                 => "N",
    "USE_YM_GOALS"                   => "N",
    "COMPONENT_TEMPLATE"             => "",
    "SPOT_LOCATION_BY_GEOIP"         => "N",
    "SHOW_VAT_PRICE"                 => "Y",
    "USER_CONSENT"                   => "N",
    "USER_CONSENT_ID"                => "0",
    "USER_CONSENT_IS_CHECKED"        => "N",
    "USER_CONSENT_IS_LOADED"         => "N",
    "ACTION_VARIABLE"                => "soa-action",
    "ADDITIONAL_PICT_PROP_21"        => "-",
    "ADDITIONAL_PICT_PROP_26"        => "-",
    "ADDITIONAL_PICT_PROP_27"        => "-"
        ), false
);
?>

<div class="backlink">
    <? if (strlen($request->get('ORDER_ID')) > 0): ?>
        <a href="<?= PATH_CATALOG ?>" title="Вернуться в каталог">
            <i class="ion-ios-arrow-back"></i><span>Вернуться в каталог</span>
        </a>
    <? else: ?>
        <a href="<?= PATH_BASKET ?>" title="Вернуться к корзине">
            <i class="ion-ios-arrow-back"></i><span>Вернуться к корзине</span>
        </a>
    <? endif; ?>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>