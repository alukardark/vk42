<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER, $APPLICATION;

$arCities        = \Axi::getAllCities();
$sCurrentCityKey = \Axi::getCityKey();
$bUserSetCityKey = \Axi::isUserSetCityKey();
$curAlias        = \Axi::getAlias();

$CARD = \COrderExt::getCard();

$CARD_TYPE    = $CARD["TYPE"];
$CARD_NUMBER  = $CARD["NUMBER"];
$CARD_BALANCE = $CARD["BALANCE"];

$CARD_BALANCE_NUMBER = number_format($CARD_BALANCE, 0, 0, " ");
$CARD_BALANCE_TEXT   = wordPlural($CARD_BALANCE, array("бонус", "бонуса", "бонусов"));
?>

<header id="header" class="header">
    <div class="container-fluid">
        <div class="row">
            <?
            $APPLICATION->IncludeComponent(
                    "bitrix:menu", "top_multilevel", array(
                "ROOT_MENU_TYPE"        => "top",
                "MAX_LEVEL"             => "2",
                "USE_EXT"               => "N",
                "ALLOW_MULTI_SELECT"    => "N",
                "MENU_CACHE_TYPE"       => "N",
                "MENU_CACHE_TIME"       => "36000000",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "CACHE_SELECTED_ITEMS"  => "Y",
                "MENU_CACHE_GET_VARS"   => array(
                ),
                "DELAY"                 => "N",
                "COMPONENT_TEMPLATE"    => "top_multilevel",
                "CHILD_MENU_TYPE"       => "submenu",
                "MENU_THEME"            => "site"
                    ), false
            );
            ?>

            <? if ($curAlias == 'index-page' && ERROR_404 != "Y"): ?>
                <?
                $APPLICATION->IncludeComponent("bitrix:menu", "sections", array(
                    "ROOT_MENU_TYPE"        => "sections",
                    "MAX_LEVEL"             => "1",
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
            <? endif; ?>

            <div class="header-burger hidden-lg-up" title="Меню" onclick="Menu.navMobileToggle(this)">
                <i></i>
                <span class="hidden-sm-down">Меню</span>
            </div>

            <div class="header-right row">
                <nav id="nav-personal" class="nav-personal">
                    <div id="nav-cities" class="nav-cities">
                        <div
                            class="nav-cities-current noselect clearfix"
                            data-city-key="<?= $sCurrentCityKey ?>"
                            title="Ваш город: <?= $arCities[$sCurrentCityKey] ?>"
                            onclick="Menu.navCitiesToggle(this)">
                            <span>
                                <?= $sCurrentCityKey != ANOTHER_CITY_CODE ? 'г. ' : ''; ?><?= $arCities[$sCurrentCityKey] ?>
                            </span>
                            <i class="ion-arrow-down-b"></i>
                            <i class="ion-arrow-up-b"></i>
                        </div>

                        <div id="nav-cities-question" class="nav-cities-question <?= $bUserSetCityKey ? "" : "opened" ?> ">
                            <span>Это ваш город?</span>
                            <div class="nav-cities-question-buttons">
                                <button onclick="Menu.navCitiesQuestionClose(this);Menu.setCity(null)">Да</button>
                                <button onclick="Menu.navCitiesQuestionClose(this);Menu.navCitiesOpen(this)">Нет</button>
                            </div>
                        </div>

                        <ul class="nav-cities-list noliststyle">
                            <? foreach ($arCities as $sCityKey => $sCityName): ?>
                                <li
                                    data-city-key="<?= $sCityKey ?>"
                                    onclick="Menu.setCity(this)"
                                >
                                    <?= $sCityKey != ANOTHER_CITY_CODE ? 'г. ' : ''; ?><?= $sCityName ?>
                                </li>
                                <? endforeach; ?>
                        </ul>
                    </div>

                    <? if (PERSONAL_ENABLE): ?>
                        <div id="nav-auth" class="nav-auth hidden-sm-down">
                            <figure class="nav-auth-icon"></figure>

                            <ul class="nav-auth-wrap noliststyle">
                                <? if (!$USER->IsAuthorized()): ?>
                                    <li><a href="<?= PATH_AUTH ?>" class="underline" title="Вход">Вход</a></li>
                                    <!--<li><a href="<?= PATH_REGISTER ?>" class="underline" title="Регистрация">Регистрация</a></li>-->
                                <? else: ?>
                                    <li><a href="<?= PATH_PERSONAL ?>" class="underline" title="Кабинет">Кабинет</a></li>
                                    <li class="hidden"><span class="underline noselect" onclick="User.toggleHeaderMenu(this);"><?= \CUserExt::getName(null, true) ?></span></li>
<!--                                    <li>
                                        <span class="hidden" title="Ваши бонусы">
                                            <?= $CARD_BALANCE_NUMBER ?> <?= $CARD_BALANCE_TEXT ?>
                                        </span>
                                    </li>-->

                                    <ul class="nav-auth-menu" id="nav-auth-menu">
                                        <li><a href="<?= PATH_PERSONAL ?>" title="Личный кабинет">Личный кабинет</a></li>
                                        <li><a href="<?= PATH_LOGOUT ?>" title="Выход">Выход</a></li>
                                    </ul>
                                <? endif; ?>
                            </ul>
                        </div>
                    <? endif; ?>
                </nav>

                <?
                $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "", Array(
                    "PATH_TO_BASKET"       => PATH_TO_BASKET,
                    "SHOW_NUM_PRODUCTS"    => "Y",
                    "SHOW_EMPTY_VALUES"    => "Y",
                    "SHOW_PERSONAL_LINK"   => "N",
                    "SHOW_TOTAL_PRICE"     => "Y",
                    "SHOW_PRODUCTS"        => "N",
                    "POSITION_FIXED"       => "N",
                    "HIDE_ON_BASKET_PAGES" => "N",
                        ), false, array("HIDE_ICONS" => "Y")
                );
                ?>

                <div class="nav-search hidden-lg-down" title="Поиск" onclick="Search.panel_open(this)">
                    <figure><i class="ion-search"></i></figure>
                </div>
            </div>
        </div>
    </div>
</header>



<div id="search-panel" class="search-panel">
    <figure class="search-panel-icon" onclick="Search.submit(this)" title="Искать"><i class="ion-search"></i></figure>
    <figure class="search-panel-close" onclick="Search.panel_close(this)" title="Закрыть"><i class="ion-android-close"></i></figure>
    <form class="search-panel-form" method="GET" action="/katalog/" onsubmit="yaCounter12153865.reachGoal('poisk');">
        <input
            class="search-panel-input"
            type="search"
            name="q"
            placeholder="Поиск"
            value="<?= !empty($_REQUEST['q']) ? htmlspecialchars($_REQUEST['q']) : "" ?>"
            />
        <input class="search-panel-submit" type="submit" value="Искать" />
    </form>
</div>

<?
if ($curAlias != 'index-page'):
    $src      = $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/images/bg/bg_inner.jpg";
    $iFileId  = \CPic::makeFile($src);
    $sFileSrc = \CPic::getResized($iFileId, 1900, 460, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
    ?>
    <div class="header-inner">
        <div class="header-inner-bg" style="background-image: url(<?= $sFileSrc ?>);"></div>

        <div class="header-inner-about row">
            <a class="header-inner-about-logo" href="/" title="<?= SITE_NAME ?>">
                <figure>
                    <img src="/images/logo_ct_inner.png" alt="<?= SITE_NAME ?>" title="<?= SITE_NAME ?>" />
                </figure>
            </a>
            <div class="header-inner-about-content">
                <div class="header-inner-about-title"><? \Axi::GT("index/promo-about-title", "промо: заголовок"); ?></div>
                <div class="header-inner-about-descr"><? \Axi::GT("index/promo-about-descr", "промо: описание"); ?></div>
            </div>
        </div>
        <!--<button class="header-inner-button" title="Записаться на сервис"><i></i><span>Записаться на сервис</span></button>-->

        <h1 class="header-inner-title">
            <? ShowCondTitle(); ?>
        </h1>

        <? if (empty($_REQUEST['q'])): ?>
            <?
            $START_FROM = 1;
            ?>
            <?
            $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(
                "START_FROM" => $START_FROM,
                    ), false, array("HIDE_ICONS" => "Y"));
            ?>

            <? $APPLICATION->ShowViewContent("catalog_sections_header"); ?>
        <? endif; ?>
    </div>
<? endif; ?>