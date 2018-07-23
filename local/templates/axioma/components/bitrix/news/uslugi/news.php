<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$arStores               = \CCatalogExt::getStores(null, true);
$arStoresGroupedByCity  = array();
$arStoresGroupedByXMLID = array();

foreach ($arStores as $arStore)
{
    $arStoresGroupedByCity[$arStore['UF_STORE_CITY']][] = $arStore;
    $arStoresGroupedByXMLID[$arStore['XML_ID']]         = $arStore;
}

unset($arStores);
unset($arStore);

$arCities    = \Axi::getCities();
$currCityKey = \Axi::getCityKey();
$curCityName = \Axi::getCityName();

if (!array_key_exists($currCityKey, $arCities))
{
    $currCityKey = \Axi::getDefaultCityKey();
    $curCityName = $arCities[$currCityKey];
}

global $APPLICATION;
$selectedCookieStoreXMLID = $APPLICATION->get_cookie("STORE_XML_ID");

if (!empty($selectedCookieStoreXMLID))
{
    $arStoreFirst = $arStoresGroupedByXMLID[$selectedCookieStoreXMLID];
    $mode         = "STORE";
}
else
{
    $arStoreFirst = $arStoresGroupedByCity[$curCityName][0];
    $mode         = "ALL";
}

$selectedStore = $arStoreFirst['XML_ID'];
?>

<div class="uslugi uslugi-wrap">
    <div class="uslugi-inner">
        <div class="uslugi-stores uslugi-stores--list">

            <div class="uslugi-price">
                <div class="uslugi-price__content">
                    <i>PDF</i>
                    <a
                        href="/upload/medialibrary/fc9/Prays_list_na_uslugi_dlya_Sayta_s.pdf"
                        title='Скачать прайс-лист'
                        target="_blank"
                        >Просмотреть прайс</a>
                </div>
            </div>

            <div class="uslugi-stores-fakeselect row" id="uslugi-stores-fakeselect">
                <button
                    data-mode="ALL"
                    onclick="Services.setMode('ALL');"
                    class="uslugi-stores-fakeselect-button col-9 col-lg-24 <?= $mode == "ALL" ? "selected" : "" ?>"
                    title="Показать полный перечень услуг"
                    >
                    <i></i><span>Показать полный перечень услуг</span>
                </button>
                <button
                    data-mode="STORE"
                    onclick="Services.setMode('STORE');"
                    class="uslugi-stores-fakeselect-button col-9 col-lg-12 col-md-24 <?= $mode != "ALL" ? "selected" : "" ?>"
                    title="Показать услуги сервис-центра"
                    >
                    <i></i><span>Показать услуги сервис-центра:</span>
                </button>

                <div class="uslugi-stores-fakeselect-current col-6 col-lg-12 col-md-24">
                    <span id="uslugi-stores-current" onclick="Services.toggleStores(this);"><?= $arStoreFirst["TITLE"] ?></span>
                    <i onclick="Services.toggleStores(this);" class="ion-chevron-down"></i>

                    <ul class="uslugi-stores-fakeselect-list" id="uslugi-stores">
                        <? foreach ($arStoresGroupedByCity as $city => $arStores): ?>
                            <li data-no-js="1" class="li-title noselect"><?= $city ?></li>

                            <? foreach ($arStores as $arStore): ?>
                                <li
                                    data-store-xml-id="<?= $arStore['XML_ID'] ?>"
                                    class="noselect <?= $selectedStore == $arStore['XML_ID'] ? "selected" : "" ?>"
                                    onclick="Services.selectStore(this);"
                                    ><?= $arStore["TITLE"] ?>
                                </li>
                            <? endforeach; ?>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <?
        $APPLICATION->IncludeComponent("bitrix:catalog.section.list", "", Array(
            "CURRENT_CITY_NAME"        => $arParams["CURRENT_CITY_NAME"],
            "IBLOCK_TYPE"              => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"                => $arParams["IBLOCK_ID"],
            "SECTION_ID"               => $_REQUEST["SECTION_ID"],
            "selectedCookieStoreXMLID" => $selectedCookieStoreXMLID,
            "SECTION_CODE"             => "",
            "SECTION_URL"              => "",
            "COUNT_ELEMENTS"           => "Y",
            "TOP_DEPTH"                => "2",
            "SECTION_FIELDS"           => "",
            "SECTION_USER_FIELDS"      => "",
            "ADD_SECTIONS_CHAIN"       => "Y",
            "CACHE_TYPE"               => "A",
            "CACHE_TIME"               => "36000000",
            "CACHE_NOTES"              => "",
            "CACHE_GROUPS"             => "Y"
                ), $component
        );
        ?>

        <div id="uslugi-wait"><figure><i></i></figure></div>
    </div>
</div>