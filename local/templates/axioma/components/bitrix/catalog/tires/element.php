<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();
$isPost  = $request->isPost();

$PRODUCT_ID = \CBasketExt::getProductIdByCode($arResult['VARIABLES']['ELEMENT_CODE']);

if (!isBot())
{
    \CCatalogExt::updateProductStoreAmount(false, $PRODUCT_ID);
}

$BASKET_DATA = \CBasketExt::getBasketNew();
$arRecord    = $BASKET_DATA["RECORDS"][$PRODUCT_ID];

$arParams['SHOW_BONUSES_INFO'] = \WS_PSettings::getFieldValue("SHOW_BONUSES_INFO", false);

$arParams['USER_CITY']     = \Axi::getCityName();
$arParams['USER_CITY_KEY'] = \Axi::getCityKey();
//$arParams['BASKET_RECORD'] = \CBasketExt::getRecord($PRODUCT_ID);

global $USER;
$NEWYEAR_DELIVERY      = /* $USER->IsAdmin() && */ \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY", false);
$NEWYEAR_DELIVERY_TEXT = \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY_TEXT", false);

$DELIVERY_DATE       = null;
$DELIVERY_DATE_PRINT = null;

if (!empty($arRecord))
{
    $arParams['BASKET_RECORD_QUANTITY'] = $arRecord["QUANTITY"];
    $arParams['DELIVERY_QUANTITY']      = $arRecord["QUANTITY"];
    $arParams['DELIVERY_DATE_PRINT']    = $arRecord["DELIVERY_DATE_PRINT"];
    $arParams['STORES_AMOUNT']          = $arRecord["MAX_QUANTITY"];

    if (isPost("get_detail_notes"))
    {
        $arParams['DELIVERY_QUANTITY'] += (int) $request->getPost("ADD_COUNT");
        $DELIVERY_DATE                 = \CCatalogExt::getProductDeliveryMinDate($PRODUCT_ID, $arParams['DELIVERY_QUANTITY'], $arParams['USER_CITY_KEY'], false, $arParams['USER_CITY']);
        $DELIVERY_DATE_PRINT           = \CCatalogExt::getProductDeliveryMinDate($PRODUCT_ID, $arParams['DELIVERY_QUANTITY'], $arParams['USER_CITY_KEY'], true, $arParams['USER_CITY']);
    }
}
else
{
    $arParams['BASKET_RECORD_QUANTITY'] = 0;
    $arParams['STORES_AMOUNT']          = \CCatalogExt::getProductAmountInStores($PRODUCT_ID);

    if (isPost("get_detail_notes"))
    {
        $arParams['DELIVERY_QUANTITY'] += (int) $request->getPost("ADD_COUNT");
    }
    else
    {
        $arParams['DELIVERY_QUANTITY'] = 1;
    }

    if ($arParams['DELIVERY_QUANTITY'] == 0) $arParams['DELIVERY_QUANTITY'] = 1;

    $DELIVERY_DATE       = \CCatalogExt::getProductDeliveryMinDate($PRODUCT_ID, $arParams['DELIVERY_QUANTITY'], $arParams['USER_CITY_KEY'], false, $arParams['USER_CITY']);
    $DELIVERY_DATE_PRINT = \CCatalogExt::getProductDeliveryMinDate($PRODUCT_ID, $arParams['DELIVERY_QUANTITY'], $arParams['USER_CITY_KEY'], true, $arParams['USER_CITY']);
}

if ($DELIVERY_DATE_PRINT !== null)
{
    $delivery_text = "Доставим " . $arParams['DELIVERY_QUANTITY'] . " шт. в " . $arParams['USER_CITY'] . ' <span id="delivery-date" class="red bold">';


    if ($DELIVERY_DATE === 0)
    {
        $delivery_text .= 'сегодня';
    }
    elseif ($NEWYEAR_DELIVERY && $DELIVERY_DATE > 0)
    {
        $delivery_text .= $DELIVERY_DATE_PRINT;
    }
    else
    {
        $delivery_text .= "за " . $DELIVERY_DATE_PRINT;
    }

    $delivery_text .= '</span>';

    $arParams['DELIVERY_DATE_PRINT'] = $delivery_text;
}
?>

<div id="catalog-detail" class="catalog-detail row">
    <?
    $ElementID = $APPLICATION->IncludeComponent(
            "bitrix:catalog.element", "", array(
        "USE_DISCOUNT"                                 => USE_DISCOUNT,
        "GET_STORE_AMOUNT"                             => isPost("get_store_amount"),
        "SHOW_BONUSES_INFO"                            => $arParams['SHOW_BONUSES_INFO'],
        "HIDDEN_DISCOUNTS_IBLOCK_ID"                   => $arParams['HIDDEN_DISCOUNTS_IBLOCK_ID'],
        "DELIVERY_QUANTITY"                            => $arParams['DELIVERY_QUANTITY'],
        "DELIVERY_DATE_PRINT"                          => $arParams['DELIVERY_DATE_PRINT'],
        "USER_CITY"                                    => $arParams['USER_CITY'],
        "USER_CITY_KEY"                                => $arParams['USER_CITY_KEY'],
        "BASKET_RECORD_QUANTITY"                       => $arParams['BASKET_RECORD_QUANTITY'],
        "STORES_AMOUNT"                                => $arParams['STORES_AMOUNT'],
        "IBLOCK_TYPE"                                  => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID"                                    => $arParams["IBLOCK_ID"],
        "PROPERTY_CODE"                                => $arParams["DETAIL_PROPERTY_CODE"],
        "META_KEYWORDS"                                => $arParams["DETAIL_META_KEYWORDS"],
        "META_DESCRIPTION"                             => $arParams["DETAIL_META_DESCRIPTION"],
        "BROWSER_TITLE"                                => $arParams["DETAIL_BROWSER_TITLE"],
        "SET_CANONICAL_URL"                            => $arParams["DETAIL_SET_CANONICAL_URL"],
        "BASKET_URL"                                   => $arParams["BASKET_URL"],
        "ACTION_VARIABLE"                              => $arParams["ACTION_VARIABLE"],
        "PRODUCT_ID_VARIABLE"                          => $arParams["PRODUCT_ID_VARIABLE"],
        "SECTION_ID_VARIABLE"                          => $arParams["SECTION_ID_VARIABLE"],
        "CHECK_SECTION_ID_VARIABLE"                    => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ''),
        "PRODUCT_QUANTITY_VARIABLE"                    => $arParams["PRODUCT_QUANTITY_VARIABLE"],
        "PRODUCT_PROPS_VARIABLE"                       => $arParams["PRODUCT_PROPS_VARIABLE"],
        "CACHE_TYPE"                                   => $arParams["CACHE_TYPE"],
        "CACHE_TIME"                                   => $arParams["CACHE_TIME"],
        "CACHE_GROUPS"                                 => $arParams["CACHE_GROUPS"],
        "SET_TITLE"                                    => $arParams["SET_TITLE"],
        "SET_LAST_MODIFIED"                            => $arParams["SET_LAST_MODIFIED"],
        "MESSAGE_404"                                  => $arParams["MESSAGE_404"],
        "SET_STATUS_404"                               => $arParams["SET_STATUS_404"],
        "SHOW_404"                                     => $arParams["SHOW_404"],
        "FILE_404"                                     => $arParams["FILE_404"],
        "PRICE_CODE"                                   => $arParams["PRICE_CODE"],
        "USE_PRICE_COUNT"                              => $arParams["USE_PRICE_COUNT"],
        "SHOW_PRICE_COUNT"                             => $arParams["SHOW_PRICE_COUNT"],
        "PRICE_VAT_INCLUDE"                            => $arParams["PRICE_VAT_INCLUDE"],
        "PRICE_VAT_SHOW_VALUE"                         => $arParams["PRICE_VAT_SHOW_VALUE"],
        "USE_PRODUCT_QUANTITY"                         => $arParams['USE_PRODUCT_QUANTITY'],
        "PRODUCT_PROPERTIES"                           => $arParams["PRODUCT_PROPERTIES"],
        "ADD_PROPERTIES_TO_BASKET"                     => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
        "PARTIAL_PRODUCT_PROPERTIES"                   => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
        "LINK_IBLOCK_TYPE"                             => $arParams["LINK_IBLOCK_TYPE"],
        "LINK_IBLOCK_ID"                               => $arParams["LINK_IBLOCK_ID"],
        "LINK_PROPERTY_SID"                            => $arParams["LINK_PROPERTY_SID"],
        "LINK_ELEMENTS_URL"                            => $arParams["LINK_ELEMENTS_URL"],
        "OFFERS_CART_PROPERTIES"                       => $arParams["OFFERS_CART_PROPERTIES"],
        "OFFERS_FIELD_CODE"                            => $arParams["DETAIL_OFFERS_FIELD_CODE"],
        "OFFERS_PROPERTY_CODE"                         => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
        "OFFERS_SORT_FIELD"                            => $arParams["OFFERS_SORT_FIELD"],
        "OFFERS_SORT_ORDER"                            => $arParams["OFFERS_SORT_ORDER"],
        "OFFERS_SORT_FIELD2"                           => $arParams["OFFERS_SORT_FIELD2"],
        "OFFERS_SORT_ORDER2"                           => $arParams["OFFERS_SORT_ORDER2"],
        "ELEMENT_ID"                                   => $arResult["VARIABLES"]["ELEMENT_ID"],
        "ELEMENT_CODE"                                 => $arResult["VARIABLES"]["ELEMENT_CODE"],
        "SECTION_ID"                                   => $arResult["VARIABLES"]["SECTION_ID"],
        "SECTION_CODE"                                 => $arResult["VARIABLES"]["SECTION_CODE"],
        "SECTION_URL"                                  => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "DETAIL_URL"                                   => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
        'CONVERT_CURRENCY'                             => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID'                                  => $arParams['CURRENCY_ID'],
        'HIDE_NOT_AVAILABLE'                           => $arParams["HIDE_NOT_AVAILABLE"],
        'USE_ELEMENT_COUNTER'                          => $arParams['USE_ELEMENT_COUNTER'],
        'SHOW_DEACTIVATED'                             => $arParams['SHOW_DEACTIVATED'],
        "USE_MAIN_ELEMENT_SECTION"                     => $arParams["USE_MAIN_ELEMENT_SECTION"],
        "DETAIL_STRICT_SECTION_CHECK"                  => $arParams["DETAIL_STRICT_SECTION_CHECK"],
        'ADD_PICT_PROP'                                => $arParams['ADD_PICT_PROP'],
        'LABEL_PROP'                                   => $arParams['LABEL_PROP'],
        'OFFER_ADD_PICT_PROP'                          => $arParams['OFFER_ADD_PICT_PROP'],
        'OFFER_TREE_PROPS'                             => $arParams['OFFER_TREE_PROPS'],
        'PRODUCT_SUBSCRIPTION'                         => $arParams['PRODUCT_SUBSCRIPTION'],
        'SHOW_DISCOUNT_PERCENT'                        => $arParams['SHOW_DISCOUNT_PERCENT'],
        'SHOW_OLD_PRICE'                               => $arParams['SHOW_OLD_PRICE'],
        'SHOW_MAX_QUANTITY'                            => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
        'MESS_BTN_BUY'                                 => $arParams['MESS_BTN_BUY'],
        'MESS_BTN_ADD_TO_BASKET'                       => $arParams['MESS_BTN_ADD_TO_BASKET'],
        'MESS_BTN_SUBSCRIBE'                           => $arParams['MESS_BTN_SUBSCRIBE'],
        'MESS_BTN_COMPARE'                             => $arParams['MESS_BTN_COMPARE'],
        'MESS_NOT_AVAILABLE'                           => $arParams['MESS_NOT_AVAILABLE'],
        'USE_VOTE_RATING'                              => $arParams['DETAIL_USE_VOTE_RATING'],
        'VOTE_DISPLAY_AS_RATING'                       => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
        'USE_COMMENTS'                                 => $arParams['DETAIL_USE_COMMENTS'],
        'BLOG_USE'                                     => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
        'BLOG_URL'                                     => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
        'BLOG_EMAIL_NOTIFY'                            => (isset($arParams['DETAIL_BLOG_EMAIL_NOTIFY']) ? $arParams['DETAIL_BLOG_EMAIL_NOTIFY'] : ''),
        'VK_USE'                                       => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
        'VK_API_ID'                                    => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
        'FB_USE'                                       => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
        'FB_APP_ID'                                    => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
        'BRAND_USE'                                    => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
        'BRAND_PROP_CODE'                              => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
        'DISPLAY_NAME'                                 => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
        'ADD_DETAIL_TO_SLIDER'                         => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
        'TEMPLATE_THEME'                               => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
        "ADD_SECTIONS_CHAIN"                           => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
        "ADD_ELEMENT_CHAIN"                            => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
        "DISPLAY_PREVIEW_TEXT_MODE"                    => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
        "DETAIL_PICTURE_MODE"                          => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
        'ADD_TO_BASKET_ACTION'                         => $basketAction,
        'SHOW_CLOSE_POPUP'                             => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
        'DISPLAY_COMPARE'                              => (isset($arParams['USE_COMPARE']) ? $arParams['USE_COMPARE'] : ''),
        'COMPARE_PATH'                                 => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
        'SHOW_BASIS_PRICE'                             => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),
        'BACKGROUND_IMAGE'                             => (isset($arParams['DETAIL_BACKGROUND_IMAGE']) ? $arParams['DETAIL_BACKGROUND_IMAGE'] : ''),
        'DISABLE_INIT_JS_IN_COMPONENT'                 => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
        'SET_VIEWED_IN_COMPONENT'                      => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),
        "USE_GIFTS_DETAIL"                             => $arParams['USE_GIFTS_DETAIL'] ?: 'Y',
        "USE_GIFTS_MAIN_PR_SECTION_LIST"               => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] ?: 'Y',
        "GIFTS_SHOW_DISCOUNT_PERCENT"                  => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
        "GIFTS_SHOW_OLD_PRICE"                         => $arParams['GIFTS_SHOW_OLD_PRICE'],
        "GIFTS_DETAIL_PAGE_ELEMENT_COUNT"              => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        "GIFTS_DETAIL_HIDE_BLOCK_TITLE"                => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
        "GIFTS_DETAIL_TEXT_LABEL_GIFT"                 => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
        "GIFTS_DETAIL_BLOCK_TITLE"                     => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
        "GIFTS_SHOW_NAME"                              => $arParams['GIFTS_SHOW_NAME'],
        "GIFTS_SHOW_IMAGE"                             => $arParams['GIFTS_SHOW_IMAGE'],
        "GIFTS_MESS_BTN_BUY"                           => $arParams['GIFTS_MESS_BTN_BUY'],
        "GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
        "GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE"        => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
        "USE_STORE"                                    => $arParams['USE_STORE'],
        "STORE_PATH"                                   => $arParams['STORE_PATH'],
        "USE_MIN_AMOUNT"                               => $arParams['USE_MIN_AMOUNT'],
        "MIN_AMOUNT"                                   => $arParams['MIN_AMOUNT'],
        "STORES"                                       => $arParams['STORES'],
        "SHOW_EMPTY_STORE"                             => $arParams['SHOW_EMPTY_STORE'],
        "SHOW_GENERAL_STORE_INFORMATION"               => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
        "USER_FIELDS"                                  => $arParams['USER_FIELDS'],
        "FIELDS"                                       => $arParams['FIELDS'],
            ), $component, array("HIDE_ICONS" => "Y")
    );
    ?>

    <?
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_catalog/detail_text/";
    $cacheID   = "detail_text" . $ElementID;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arFetch"]))
        {
            $arFetch  = $vars["arFetch"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $arFilter = Array("ID" => $ElementID);
        $obList   = \CIBlockElement::GetList(Array(), $arFilter, false, false, array("XML_ID", "DETAIL_TEXT"));
        $arFetch  = $obList->Fetch();

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arFetch" => $arFetch,
        ));
    }

    $ElementXML_ID = $arFetch['XML_ID'];
    $sDetailText   = $arFetch['DETAIL_TEXT'];


    /**
     *  Занесём в переменные контент табов
     */
    //--------
    // Описание 
    //--------
    $description = $sDetailText;

    //--------
    // Отзывы 
    //--------
    $arParams["USE_COMMENTS"] = 'N'; // for test
    ob_start();
    /* $APPLICATION->IncludeComponent(
      "bitrix:catalog.comments", "", array(
      "ELEMENT_ID"       => $ElementID,
      "ELEMENT_CODE"     => "",
      "IBLOCK_ID"        => $arParams['IBLOCK_ID'],
      "SHOW_DEACTIVATED" => $arParams['SHOW_DEACTIVATED'],
      "URL_TO_COMMENT"   => "",
      "WIDTH"            => "",
      "COMMENTS_COUNT"   => "5",
      "BLOG_USE"         => $arParams['BLOG_USE'],
      "FB_USE"           => $arParams['FB_USE'],
      "FB_APP_ID"        => $arParams['FB_APP_ID'],
      "VK_USE"           => $arParams['VK_USE'],
      "VK_API_ID"        => $arParams['VK_API_ID'],
      "CACHE_TYPE"       => $arParams['CACHE_TYPE'],
      "CACHE_TIME"       => $arParams['CACHE_TIME'],
      'CACHE_GROUPS'     => $arParams['CACHE_GROUPS'],
      "BLOG_TITLE"       => "",
      "BLOG_URL"         => $arParams['BLOG_URL'],
      "PATH_TO_SMILE"    => "",
      "EMAIL_NOTIFY"     => $arParams['BLOG_EMAIL_NOTIFY'],
      "AJAX_POST"        => "Y",
      "SHOW_SPAM"        => "Y",
      "SHOW_RATING"      => "N",
      "FB_TITLE"         => "",
      "FB_USER_ADMIN_ID" => "",
      "FB_COLORSCHEME"   => "light",
      "FB_ORDER_BY"      => "reverse_time",
      "VK_TITLE"         => "",
      "TEMPLATE_THEME"   => $arParams['~TEMPLATE_THEME']
      ), $component, array("HIDE_ICONS" => "Y")
      ); */
    $reviews                  = ob_get_clean();


    //--------
    // Остатки
    //--------    
    //if ($isPost && $request->getPost("AJAX") == "Y" && $request->getPost("ACTION") == "get_store_amount")

    if (isPost("get_store_amount"))
    {
        \CCatalogExt::updateProductStoreAmount($ElementXML_ID);
        $APPLICATION->RestartBuffer();
    }
    else
    {
        ob_start();
    }

    $APPLICATION->IncludeFile("/include/catalog/stores.php", array('PRODUCT_ID' => $ElementID));

    // if ($isPost && $request->getPost("AJAX") == "Y" && $request->getPost("ACTION") == "get_store_amount")
    if (isPost("get_store_amount"))
    {
        die();
    }
    else
    {
        $stores = "<button
                        title='Обновить остатки'
                        class='catalog-detail-button'
                        data-xml-id='{$ElementXML_ID}'
                        onclick='Catalog.getStoreAmount(this)'
                        >
                        <i class='ion-ios-refresh-empty'></i>
                        <span>Обновить остатки</span>
                    </button>";
        $stores .= "<div class='stores'>";
        $stores .= ob_get_clean();
        $stores .= "</div>";
    }
    ?>



    <div class="catalog-detail-footer col-24 row">
        <ul class="catalog-detail-tabs js-detail-tabs noliststyle clearfix">
            <? if (!empty($sDetailText)): ?>
                <li>
                    <a href="#description" data-toggle="tab">
                        Описание
                        <span class="tab-state">
                            <i class="tab-state__plus ion-android-add"></i>
                            <i class="tab-state__minus ion-android-remove"></i>
                        </span>
                    </a>
                    <div class="catalog-detail-pane-mobile"><?= $description ?></div>
                </li>
            <? endif; ?>

            <? if ($arParams["USE_COMMENTS"] == "Y"): ?>
                <li>
                    <a href="#reviews" data-toggle="tab">Отзывы
                        <span class="reviews-count">14</span>
                        <span class="tab-state">
                            <i class="tab-state__plus ion-android-add"></i>
                            <i class="tab-state__minus ion-android-remove"></i>
                        </span>
                    </a>
                    <div class="catalog-detail-pane-mobile"><?= $reviews ?></div>
                </li>
            <? endif; ?>

            <? if ($arParams["USE_STORE"] == "Y"): ?>
                <li id="tab-stores">
                    <a href="#stores" data-toggle="tab">
                        Наличие в магазинах
                        <span class="tab-state">
                            <i class="tab-state__plus ion-android-add"></i>
                            <i class="tab-state__minus ion-android-remove"></i>
                        </span>
                    </a>
                    <div class="catalog-detail-pane-mobile"><?= $stores ?></div>
                </li>
            <? endif; ?>
        </ul>

        <div class="catalog-detail-content js-detail-content">
            <? if (!empty($sDetailText)): ?>
                <div class="catalog-detail-pane ve" id="description">
                    <?= $description ?>
                </div>
            <? endif; ?>

            <!--<div class="catalog-detail-pane" id="reviews">-->
            <? ?>
            <!--</div>-->

            <? if ($arParams["USE_COMMENTS"] == "Y"): ?>
                <div class="catalog-detail-pane" id="reviews">
                    <?= $reviews ?>
                </div>
            <? endif; ?>

            <? if ($arParams["USE_STORE"] == "Y"): ?>
                <div class="catalog-detail-pane" id="stores">
                    <?= $stores ?>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>

<? ?>
<div class="backlink backlink-likebrowser">
    <a href="<?= $arResult["FOLDER"] . $arResult["VARIABLES"]["SECTION_CODE"] ?>/" title="Назад к каталогу">
        <i class="ion-ios-arrow-back"></i><span>Назад к каталогу</span>
    </a>
</div>