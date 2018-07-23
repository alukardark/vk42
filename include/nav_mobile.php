<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$curAlias = \Axi::getAlias();
?>

<nav id="nav-mobile" class="nav-mobile hidden-lg-up">
    <div id="nav-mobile-content" class="nav-mobile-content opened">
        <div class="nav-mobile-header">
            <figure class="nav-mobile-close" onclick="Menu.navMobileClose(this)">
                <i class="ion-android-close"></i>
            </figure>
            <div id="search-panel-m" class="search-panel-m"></div>
        </div>

        <nav id="nav-personal-m" class="nav-personal-m"></nav>
        <? if (0 && $curAlias != "index-page"): ?><nav id="nav-sections-m" class="nav-sections-m"></nav><? endif; ?>
        <nav id="nav-bottom-m" class="nav-bottom-m">
            <?
            $APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
                "ROOT_MENU_TYPE"        => "mobile",
                "MAX_LEVEL"             => "2",
                "USE_EXT"               => "N",
                "ALLOW_MULTI_SELECT"    => "N",
                "MENU_CACHE_TYPE"       => "A",
                "MENU_CACHE_TIME"       => "36000000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "CACHE_SELECTED_ITEMS"  => "Y",
                "MENU_CACHE_GET_VARS"   => array(),
                "DELAY"                 => "N",
                    )
            );
            ?>
        </nav>
        <div id="socnets-m" class="socnets-m"></div>
    </div>

    <div id="nav-mobile-inner" class="nav-mobile-inner" style="color: #fff;z-index: 55;">
        <button class="nav-mobile-inner-close" onclick="Menu.navMobileInnerClose()"><span>Назад</span></button>
        <div class="nav-mobile-inner-title"><span></span></div>
        <ul class="nav-mobile-inner-content"></ul>
    </div>
</nav>
