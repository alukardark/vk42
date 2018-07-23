<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arSocnets = \Axi::getSocNets();
?>

<footer id="footer" class="footer">
    <div class="container-fluid">
        <div class="row footer-content">
            <div class="offset-4 offset-xxl-2 offset-xl-0 col-16 col-xxl-20 col-xl-24">
                <nav id="nav-bottom" class="nav-bottom clearfix container-fluid hidden-lg-down">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
                        "ROOT_MENU_TYPE"        => "bottom",
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

                <div class="footer-disclaimer container-fluid">
                    <div class="row">
                        <div class="footer-disclaimer-content">
                            <? \Axi::GT("footer/disclaimer", "disclaimer"); ?>
                        </div>
                    </div>
                </div>

                <div class="footer-contacts container-fluid">
                    <div class="row">
                        <div id="socnets" class="footer-contacts-socnets hidden-md-down">
                            <ul class="socnets-list noliststyle clearfix">
                                <li class="socnets-list-title"><? \Axi::GT("footer/socnets-list-title", "соцсети: заголовок"); ?></li>
                                <? foreach ($arSocnets as $sCode => $arItem): ?>
                                    <li class="socnets-list-item">
                                        <a
                                            href="<?= $arItem['LINK'] ?>"
                                            title="<?= $arItem['NAME'] ?>"
                                            target="_blank"
                                            ><? \Axi::GSVG($sCode) ?>
                                        </a>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                        <div class="footer-contacts-phone container-fluid">
                            <div class="row">
                                <div class="col-12 col-lg-14 col-md-12 col-sm-24">
                                    <div class="footer-contacts-phone-title"><? \Axi::GT("footer/contacts-phone-title", "телефон: заголовок"); ?></div>
                                    <div class="footer-contacts-phone-descr"><? \Axi::GT("footer/contacts-phone-descr", "телефон: описание"); ?></div>
                                </div>
                                <div class="col-12 col-lg-10 col-md-12 col-sm-24">
                                    <div class="footer-contacts-phone-number"><? \Axi::GT("footer/contacts-phone-number", "телефон: номер"); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-copyright container-fluid">
                    <div class="row">
                        <div class="footer-copyright-vk float-left">
                            <?
                            $start_year = 2016;
                            $curr_year  = date("Y");
                            $date_range = $start_year == $curr_year ? $curr_year : $start_year . " — " . $curr_year;
                            ?>
                            <a href="/" title="Главная страница">
                                <? \Axi::GT("footer/copyright-vk", "копирайт: вк"); ?> <?= $date_range ?>
                            </a>
                        </div>
                        <div class="footer-copyright-axioma float-right">
                            <a href="http://www.web-axioma.ru" target="_blank" title="Создание, продвижение, администрирование сайтов">
                                <? \Axi::GT("footer/copyright-axioma", "копирайт: акиома"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>