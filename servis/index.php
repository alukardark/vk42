<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Контакты сервисных центров сети автосервисов «Континент шин» в Кемерово и Новокузнецке");
$APPLICATION->SetPageProperty("title", "Контакты - Автоcервис-центр «Континент шин» в Кемерово и Новокузнецке");
$APPLICATION->SetTitle("Контакты");

// Получим название текущего города
$arCities    = \Axi::getCities();
$currCityKey = \Axi::getCityKey();
$curCityName = \Axi::getCityName();

if (!array_key_exists($currCityKey, $arCities))
{
    $currCityKey = \Axi::getDefaultCityKey();
    $curCityName = $arCities[$currCityKey];
}

// Формируем массив объектов складов для показа на карте
$arStores = \CCatalogExt::getStores(null, true);

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/services.css");
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/actions.css");
?>

<div class="services services-wrap">

    <div class="services-buttons">
        <? foreach ($arCities as $cityKey => $cityName): ?>
            <? $active = $cityKey == $currCityKey ? 'active' : '' ?>
            <button
                onclick="Services.setCity(this)"
                data-city="<?= $cityName ?>"
                class="<?= $active ?>"><span><?= mb_ucfirst($cityName) ?></span>
            </button>
        <? endforeach ?>
    </div>

    <div class="services-phone-note">
        <div class="services-phone-note-inner">
            <div class="phone-note"><? \Axi::GT("phone"); ?></div>
        </div>
    </div>

    <?
    $arI = array();
    foreach ($arStores as $storeName => $arStore):
        $ID       = $arStore["ID"];
        $TITLE    = $arStore["TITLE"];
        $PHONE    = $arStore["PHONE"];
        $SCHEDULE = $arStore["SCHEDULE"];
        $ADDRESS  = $arStore["ADDRESS"];
        $CITY     = $arStore["UF_STORE_CITY"];
        $PHONE2   = $arStore["UF_STORE_PHONE2"];

        if (!empty($arStore['IMAGE_ID']))
        {
            $IMAGE     = \CPic::getResized($arStore['IMAGE_ID'], 120, 120, BX_RESIZE_IMAGE_EXACT);
            $IMAGE_BIG = \CFile::GetPath($arStore['IMAGE_ID']);
        }
        else
        {
            $IMAGE_BIG = "";
        }

        $arI[$CITY][] = $ID;
        ?>
        <div class="services-row <?= $CITY == $curCityName ? "active" : "" ?>" data-city="<?= $CITY ?>">
            <div class="services-row-content row">

                <div class="row services-row-wrap services-row-wrap-first col-3 col-lg-4 col-md-6 col-sm-24">
                    <div class="row services-row-wrap-content">
                        <div class="services-row-cell services-row-image col-24">
                            <div class="services-row-cell-content" data-fancybox="" data-src="<?= $IMAGE_BIG ?>">
                                <img src="<?= $IMAGE ?>" alt="Изображение СЦ <?= htmlspecialcharsbx($TITLE) ?>" />
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row services-row-wrap services-row-wrap-center col-15 col-lg-16 col-md-18 col-sm-24">
                    <div class="row services-row-wrap-content">
                        <div class="services-row-cell services-row-title col-9 col-lg-24">
                            <div class="services-row-cell-content">
                                <span><strong><?= $TITLE ?></strong></span>
                                <span><?= $ADDRESS ?></span>
                            </div>
                        </div>

                        <div class="services-row-cell services-row-phone col-7 col-lg-24">
                            <div class="services-row-cell-content">
                                <? if (!empty($PHONE) || !empty($PHONE2)): ?>
                                    <? if (!empty($PHONE)): ?>
                                        <div class="services-row-cell-content-phone">
                                            <i class="ion-ios-telephone"></i>
                                            <span><?= $PHONE ?></span>
                                            <? if (!empty($PHONE2)): ?>
                                                <span class="phdescr">магазин, сервис</span>
                                            <? endif; ?>
                                        </div>
                                    <? endif; ?>
                                    <? if (!empty($PHONE2)): ?>
                                        <div class="services-row-cell-content-phone">
                                            <i class="ion-ios-telephone"></i>
                                            <span><?= $PHONE2 ?></span>
                                            <span class="phdescr">автомойка</span>
                                        </div>
                                    <? endif; ?>

                                <? else: ?>
                                    <span>Не указано</span>
                                <? endif; ?>
                            </div>
                        </div>

                        <div class="services-row-cell services-row-schedule col-8 col-lg-24">
                            <div class="services-row-cell-content">
                                <? if (!empty($SCHEDULE)): ?>
                                    <i class="ion-clock"></i>
                                    <span><?= $SCHEDULE ?></span>
                                <? else: ?>
                                    <span>Не указано</span>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row services-row-wrap services-row-wrap-last col-6 col-lg-4 col-md-18 offset-md-6 col-sm-24 offset-sm-0">
                    <div class="row services-row-wrap-content">
                        <div class="services-row-cell services-row-maplink col-12 col-lg-24 col-md-12">
                            <div class="services-row-cell-content">
                                <i class="ion-ios-location-outline ion-bold"></i>
                                <button onclick="Services.openBaloon(<?= count($arI[$CITY]) - 1 ?>)">На карте</button>
                            </div>
                        </div>

                        <div class="services-row-cell services-row-orderbutton col-12 col-lg-24 col-md-12">
                            <div class="services-row-cell-content">
                                <button>
                                    Услуги
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    <? endforeach; ?>

    <div id="map"></div>
    <? \Axi::GF("map_data"); ?>
    <script async="async">
        yml.readyGo(function () {
            map_data.initStoresMap('<?= $curCityName ?>');
        }, true, null, 'map');
    </script>

    <div class="actions actions-wrap urinfo">
        <div class="actions-detail urinfo-detail">
            <div class="actions-detail-title urinfo-detail-title"><? \Axi::GT("urinfo/title", "urinfo title"); ?></div>
            <div class="actions-detail-text urinfo-detail-text section ve">
                <? \Axi::GT("urinfo/content", "юр. инфо content", false, "html"); ?>
            </div>
        </div>
    </div>

</div>




<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>