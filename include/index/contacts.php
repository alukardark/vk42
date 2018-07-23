<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Получим название текущего города
$curCity = ToLower(\Axi::getCityName(true));

// Формируем массив объектов складов для показа на карте
$arStoresJS = \CCatalogExt::getStoresJS();
?>

<section id="contacts" class="contacts">
    <div class="container-fluid">
        <div class="row">
            <div class="offset-4 offset-xxl-2 offset-xl-0 col-16 col-xxl-20 col-xl-24">
                <div class="row">
                    <div class="contacts-cities offset-13 col-11 offset-md-0 col-md-24">
                        <h2 class="contacts-cities-title"><? \Axi::GT("index/contacts-cities-title", "карта: заголовок"); ?></h2>
                        <div class="contacts-cities-descr"><? \Axi::GT("index/contacts-cities-descr", "карта: описание"); ?></div>
                        <div class="contacts-cities-slogan"><? \Axi::GT("index/contacts-cities-slogan", "карта: слоган"); ?></div>
                        <div class="contacts-cities-buttons clearfix">
                            <? foreach ($arStoresJS as $city => $stores): ?>
                                <? $active = $city == $curCity ? 'active' : '' ?>
                                <button data-city="<?= $city ?>" class="<?= $active ?>"><span><?= mb_ucfirst($city) ?></span></button>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="map"></div>
    <? \Axi::GF("map_data"); ?>
    <script async="async">
        yml.readyGo(function () {
            map_data.initStoresMap('<?= $curCity ?>');
        }, true, null, 'map');
    </script>
</section>