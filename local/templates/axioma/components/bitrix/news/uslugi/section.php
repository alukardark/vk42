<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$SECTION_CODE = $arResult['VARIABLES']['SECTION_CODE'];
$arSection    = \CServicesExt::getCategory($SECTION_CODE);

$arSection['DETAIL_RESIZED'] = \CPic::getDetailSrc($arSection, 350, 400, BX_RESIZE_IMAGE_EXACT, false, 95);

$arStoresGroupedByCity  = array();
$arStoresGroupedByXMLID = array();
$arI                    = array();

$arStores = \CCatalogExt::getStores(null, true);

foreach ($arStores as $arStore)
{
    $arStoresGroupedByCity[$arStore['UF_STORE_CITY']][] = $arStore;
    $arStoresGroupedByXMLID[$arStore['XML_ID']]         = $arStore;
    $arI[$arStore["UF_STORE_CITY"]][]                   = $arStore["ID"];
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
$PHONE         = $arStoreFirst["PHONE"];
$SCHEDULE      = $arStoreFirst["SCHEDULE"];
$GPS_N         = $arStoreFirst["GPS_N"];
$GPS_S         = $arStoreFirst["GPS_S"];
//$STORE_IDENT   = 1;
?>

<div class="uslugi uslugi-wrap uslugi-cards-wrap">
    <div class="uslugi-inner">
        <div class="uslugi-description row">
            <h2 class="uslugi-description-title"><?= $arSection["NAME"] ?></h2>
            <div class="uslugi-description-text"><?= $arSection["DESCRIPTION"] ?></div>
            <figure class="uslugi-description-picture">
                <img src="<?= $arSection['DETAIL_RESIZED'] ?>" alt="<?= $arSection["NAME"] ?>" />
            </figure>
        </div>

        <div class="uslugi-stores">
            <div class="uslugi-stores-fakeselect row" id="uslugi-stores-fakeselect">
                <button
                    data-mode="ALL"
                    onclick="Services.setMode('ALL');"
                    class="uslugi-stores-fakeselect-button col-8 col-lg-24 <?= $mode == "ALL" ? "selected" : "" ?>"
                    title="Показать полный перечень услуг"
                    >
                    <i></i><span>Показать полный перечень услуг</span>
                </button>
                <button
                    data-mode="STORE"
                    onclick="Services.setMode('STORE');"
                    class="uslugi-stores-fakeselect-button col-8 col-lg-12 col-md-24 <?= $mode != "ALL" ? "selected" : "" ?>"
                    title="Показать услуги сервис-центра"
                    >
                    <i></i><span>Показать услуги сервис-центра:</span>
                </button>

                <div class="uslugi-stores-fakeselect-current col-8 col-lg-12 col-md-24">
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

                <div class="uslugi-stores-info">
                    <div class="uslugi-stores-info-content row">
                        <div class="uslugi-stores-info-content-block uslugi-stores-info-content-shedule col-10 col-sm-24">
                            <i class="ion-clock"></i>
                            <? if (!empty($SCHEDULE)): ?>
                                <span><?= $SCHEDULE ?></span>
                            <? else: ?>
                                <span>Не указано</span>
                            <? endif; ?>
                        </div>

                        <div class="uslugi-stores-info-content-block uslugi-stores-info-content-phone col-9 col-sm-24">
                            <i class="ion-ios-telephone"></i>
                            <? if (!empty($PHONE)): ?>
                                <span class="phone"><?= $PHONE ?></span>
                            <? else: ?>
                                <span>Не указано</span>
                            <? endif; ?>
                        </div>

                        <div class="uslugi-stores-info-content-block uslugi-stores-info-content-maplink col-5 col-sm-24">
                            <i class="ion-ios-location-outline ion-bold"></i>
                            <? if (!empty($GPS_N) && !empty($GPS_S)): ?>
                                <button
                                    data-map-popup="Y"
                                    data-src="#map"
                                    data-store-xml_id="<?= $arStoreFirst["XML_ID"] ?>"
                                    data-store-city="<?= $arStoreFirst["UF_STORE_CITY"] ?>"
                                    >На карте</button>
                                <? else: ?>

                            <? endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?
        $APPLICATION->IncludeComponent(
                "bitrix:news.list", "", Array(
            "CURRENT_CITY_NAME"               => $arParams["CURRENT_CITY_NAME"],
            "selectedCookieStoreXMLID"        => $selectedCookieStoreXMLID,
            "IBLOCK_TYPE"                     => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"                       => $arParams["IBLOCK_ID"],
            "NEWS_COUNT"                      => $arParams["NEWS_COUNT"],
            "SORT_BY1"                        => $arParams["SORT_BY1"],
            "SORT_ORDER1"                     => $arParams["SORT_ORDER1"],
            "SORT_BY2"                        => $arParams["SORT_BY2"],
            "SORT_ORDER2"                     => $arParams["SORT_ORDER2"],
            "FIELD_CODE"                      => $arParams["LIST_FIELD_CODE"],
            "PROPERTY_CODE"                   => $arParams["LIST_PROPERTY_CODE"],
            "DISPLAY_PANEL"                   => $arParams["DISPLAY_PANEL"],
            "SET_TITLE"                       => $arParams["SET_TITLE"],
            "SET_LAST_MODIFIED"               => $arParams["SET_LAST_MODIFIED"],
            "MESSAGE_404"                     => $arParams["MESSAGE_404"],
            "SET_STATUS_404"                  => $arParams["SET_STATUS_404"],
            "SHOW_404"                        => $arParams["SHOW_404"],
            "FILE_404"                        => $arParams["FILE_404"],
            "INCLUDE_IBLOCK_INTO_CHAIN"       => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
            "ADD_SECTIONS_CHAIN"              => $arParams["ADD_SECTIONS_CHAIN"],
            "CACHE_TYPE"                      => $arParams["CACHE_TYPE"],
            "CACHE_TIME"                      => $arParams["CACHE_TIME"],
            "CACHE_FILTER"                    => $arParams["CACHE_FILTER"],
            "CACHE_GROUPS"                    => $arParams["CACHE_GROUPS"],
            "DISPLAY_TOP_PAGER"               => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER"            => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE"                     => $arParams["PAGER_TITLE"],
            "PAGER_TEMPLATE"                  => $arParams["PAGER_TEMPLATE"],
            "PAGER_SHOW_ALWAYS"               => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_DESC_NUMBERING"            => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL"                  => $arParams["PAGER_SHOW_ALL"],
            "PAGER_BASE_LINK_ENABLE"          => $arParams["PAGER_BASE_LINK_ENABLE"],
            "PAGER_BASE_LINK"                 => $arParams["PAGER_BASE_LINK"],
            "PAGER_PARAMS_NAME"               => $arParams["PAGER_PARAMS_NAME"],
            "DISPLAY_DATE"                    => $arParams["DISPLAY_DATE"],
            "DISPLAY_NAME"                    => "Y",
            "DISPLAY_PICTURE"                 => $arParams["DISPLAY_PICTURE"],
            "DISPLAY_PREVIEW_TEXT"            => $arParams["DISPLAY_PREVIEW_TEXT"],
            "PREVIEW_TRUNCATE_LEN"            => $arParams["PREVIEW_TRUNCATE_LEN"],
            "ACTIVE_DATE_FORMAT"              => $arParams["LIST_ACTIVE_DATE_FORMAT"],
            "USE_PERMISSIONS"                 => $arParams["USE_PERMISSIONS"],
            "GROUP_PERMISSIONS"               => $arParams["GROUP_PERMISSIONS"],
            "FILTER_NAME"                     => $arParams["FILTER_NAME"],
            "HIDE_LINK_WHEN_NO_DETAIL"        => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
            "CHECK_DATES"                     => $arParams["CHECK_DATES"],
            "PARENT_SECTION"                  => $arResult["VARIABLES"]["SECTION_ID"],
            "PARENT_SECTION_CODE"             => $arResult["VARIABLES"]["SECTION_CODE"],
            "DETAIL_URL"                      => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
            "SECTION_URL"                     => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "IBLOCK_URL"                      => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
                ), $component
        );
        ?>
        <div id="uslugi-wait"><figure><i></i></figure></div>
    </div>
</div>

<div class="backlink uslugi-backlink backlink-likebrowser">
    <a href="<?= $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"] ?>" title="Назад ко всем услугам">
        <i class="ion-ios-arrow-back"></i><span>Назад ко всем услугам</span>
    </a>
</div>

<? \Axi::GF("map_data"); ?>