<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;

$assets   = \Bitrix\Main\Page\Asset::getInstance();
$curAlias = \Axi::getAlias();

\CJSCore::Init(array());
$assets->addJs("//code.jquery.com/jquery-2.2.4.min.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/common.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/app.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/search.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/menu.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/user.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/basket.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/services.js");

if (strstr($curAlias, 'inner-page'))
{
    $assets->addCss(SITE_TEMPLATE_PATH . "/styles/inner.css");
    $assets->addCss(SITE_TEMPLATE_PATH . "/styles/kabinet.css");
}

$assets->addCss("/custom.css");
?><!doctype html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>">
    <head>
        <title><?= SITE_TEST ? "TEST " : "" ?><? $APPLICATION->ShowTitle('title', true); ?></title>
        <link rel="shortcut icon" href="/<?= SITE_URL == "vk.axiomatest.ru" ? "favicon2" : "favicon" ?>.ico" type="image/x-icon" />
        <link rel="manifest" href="/manifest.json">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="author" content="web-studio «AXIOMA»" />
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="HandheldFriendly" content="true" />
        <meta name="MobileOptimized" content="width" />
        <meta name="yandex-verification" content="c5c589c7feb59748" />
        <meta name="google-site-verification" content="sDTmt59VUaWQJfS4uXhZCog6ysDXbbK4K_DV3wEnIZw" />

        <!--<link rel="alternate stylesheet" href="app.dark.css" title="Dark Theme">-->


        <script>
            var CITIES = <?= json_encode(\Axi::getCities()) ?>
        </script>

        <? $APPLICATION->ShowHead(true); ?>

        <!--[if IE]>
            <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js" data-skip-moving="true"></script>
            <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js" data-skip-moving="true"></script>
        <![endif]-->

        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({'gtm.start':
                            new Date().getTime(), event: 'gtm.js'});
                var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-PJRJW24');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PJRJW24"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <? /* if (0 && !$USER->IsAdmin() && (!$APPLICATION->get_cookie("USER_READED_NOTE") || SITE_URL == "vk.axiomatest.ru")): */ ?>
        <? if (0): ?>
            <div id="notification" class="<?= SITE_TEST ? "test" : "" ?>">
                <span><? \Axi::GT("notification"); ?></span>
                <i onclick="App.hideNote()" class="ion-ios-close-empty"></i>
            </div>
        <? endif; ?>

        <?
        if ($USER->IsAdmin()) :
            //\CCatalogExt::updateSortProp(DISCS_IB, true);
            /* $IBlockElement = new \CIBlockElement;

              $arCarSelect = Array("ID");
              $arCarFilter = Array("IBLOCK_ID" => DISCS_IB, ">PROPERTY_" . SORTPROP => 0);
              $obCarList   = \CIBlockElement::GetList(Array(), $arCarFilter, false, false, $arCarSelect);
              while ($arFetch     = $obCarList->Fetch())
              {
              \CIBlockElement::SetPropertyValuesEx($arFetch["ID"], false, array(SORTPROP => 0));
              } */
            ?>
            <div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
        <? endif; ?>


        <div id="body" class="body <?= $curAlias ?>">
            <div class="body-content">

                <? \Axi::GF("header"); ?>