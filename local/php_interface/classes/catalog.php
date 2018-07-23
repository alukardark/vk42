<?php

use Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;

class CCatalogExt
{

    private static $arSizeProps       = array(
        SHIRINA => "Ширина",
        VYSOTA  => "Высота",
        DIAMETR => "Диаметр",
    );
    private static $arTXProps         = array(
        "vendor"       => "Марка",
        "model"        => "Модель",
        "year"         => "Год",
        "modification" => "Модификация",
    );
    //допустимые свойства фильтра + $arSizeProps + $arTXProps
    private static $arAviableProps    = array(
        "PRICE", "PRESET", "QUANTITY",
        "MARKA", "SHIPY", "KAMERA", "OS_PRIMENENIYA",
        SALE, SALE_DAY, HIT, BONUS, SEZON, AKTSIYA,
        SM_PROIZVODITEL, SM_VYAZKOST, SM_TIP, SM_NAZNACHENIEDV,
        SM_NAZNACHENIE, SM_VIDMASLA, OZH_TIP, OZH_TSVET,
        AKB_PROIZVODITEL, AKB_EMKOST, AKB_POLYARNOST,
        AKB_DLINA, AKB_SHIRINA, AKB_VYSOTA,
        DISKI_MARKA, DIAMETRDISKA, KREPLENIEDISKA, VYLET, DIA, SHIRINADISKA,
        "TUNING", RUN_FLAT, BREND_SOPUTSTVUYUSHCHIETOVARY, BREND_AVTOLAMPY,
        BREND_FILTRY, BREND_SHCHETKISTEKLOOCHISTITELEY, BREND_TORMOZNYEKOLODKI,
        BREND_SVECHI, BREND_AVTOKOSMETIKA
    );
    //свойства, являбщиеся чекбоксами с множественным выбором
    private static $arCheckBoxesMulti = array(
        "MARKA", "PRESET", "QUANTITY", AKTSIYA,
        SALE, SALE_DAY, HIT, BONUS,
        SM_PROIZVODITEL, SM_NAZNACHENIEDV,
        OZH_TIP, OZH_TSVET,
        SM_VYAZKOST, SM_TIP, SM_NAZNACHENIE, SM_VIDMASLA,
        AKB_PROIZVODITEL, AKB_POLYARNOST,
        DISKI_MARKA, DIA,
        KREPLENIEDISKA, DIAMETRDISKA, RUN_FLAT, BREND_SOPUTSTVUYUSHCHIETOVARY, BREND_AVTOLAMPY,
        BREND_FILTRY, BREND_SHCHETKISTEKLOOCHISTITELEY, BREND_TORMOZNYEKOLODKI,
        BREND_SVECHI, BREND_AVTOKOSMETIKA
    );
    //свойства, являбщиеся range-slider
    private static $arRangeProps      = array(
        "PRICE", AKB_EMKOST, AKB_DLINA, AKB_SHIRINA, AKB_VYSOTA,
        VYLET, SHIRINADISKA
    );

    /**
     * возвращает массив кодов свойств каталога, отсортированных в нужном порядке
     */
    public static function getPropertiesCodes($IBLOCK_ID)
    {
        $props = array();

        if ($IBLOCK_ID == TIRES_IB)
        {
            $props = array(
                SHIRINA, VYSOTA, DIAMETR, "MARKA", "MODEL", SEZON, "SHIPY", "NAGRUZKA",
                "MAKSIMALNAYA_SKOROST", "KAMERA", "TIP_AVTOMOBILYA", "OS_PRIMENENIYA", "CML2_ARTICLE", RUN_FLAT
            );
        }
        elseif ($IBLOCK_ID == OILS_IB)
        {
            $props = array(
                SM_TIP, SM_NAZNACHENIE, SM_PROIZVODITEL, SM_VYAZKOST, SM_API, SM_ACEA, OBYEM,
                SM_NAZNACHENIEDV, SM_VIDMASLA, OZH_TIP, OZH_TSVET, "CML2_ARTICLE",
            );
        }
        elseif ($IBLOCK_ID == AKB_IB)
        {
            $props = array(
                AKB_EMKOST, "PUSKOVOY_TOK", AKB_POLYARNOST, "TIP_KLEMM", AKB_PROIZVODITEL,
                AKB_DLINA, AKB_SHIRINA, AKB_VYSOTA, "CML2_ARTICLE",
            );
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $props = array(
                DISKI_MARKA, DIAMETRDISKA, KREPLENIEDISKA, VYLET, DIA, SHIRINADISKA,
            );
        }

        return $props;
    }

    public static function getSettings()
    {
        $arOnpageVariants = array(12, 24, 48, 72, 96);

        $arSortVariants = array(
            "PROPERTY_" . SORTPROP . ";DESC" => "по популярности",
            CATALOG_PRICE . ";ASC"           => "сначала дешевые",
            CATALOG_PRICE . ";DESC"          => "сначала дорогие",
            "NAME;ASC"                       => "по наименованию",
        );

        return array(
            'ONPAGE_DEAULT'   => $arOnpageVariants[1],
            'ONPAGE_VARIANTS' => $arOnpageVariants,
            'SORT_DEFAULT'    => key($arSortVariants),
            'SORT_VARIANTS'   => $arSortVariants,
        );
    }

    public static function getParams($IBLOCK_ID)
    {
        $arSettings = self::getSettings();

        //set sorting && onpage items
        $iOnPageDefault   = $iOnPage          = $arSettings['ONPAGE_DEAULT'];
        $sSortCodeDefault = $sSortCode        = $arSettings['SORT_DEFAULT'];

        if (!empty($_SESSION['section_onpage'])) $iOnPage   = $_SESSION['section_onpage'];
        if (!empty($_SESSION['section_sort'])) $sSortCode = $_SESSION['section_sort'];

        if (isPost(true))
        {
            if (!empty($_POST['PARAMS']['onpage'])) $iOnPage   = $_POST['PARAMS']['onpage'];
            if (!empty($_POST['PARAMS']['sort'])) $sSortCode = $_POST['PARAMS']['sort'];
        }

        if (!in_array($iOnPage, $arSettings['ONPAGE_VARIANTS'])) $iOnPage   = $iOnPageDefault;
        if (!array_key_exists($sSortCode, $arSettings['SORT_VARIANTS'])) $sSortCode = $sSortCodeDefault;

        $_SESSION['section_onpage'] = $iOnPage;
        $_SESSION['section_sort']   = $sSortCode;

        $arSortParams = explode(";", $sSortCode);
        $sSortField   = $arSortParams[0];
        $sSortOrder   = $arSortParams[1];

        if ($IBLOCK_ID == TIRES_IB)
        {
            $SEF_FOLDER = PATH_CATALOG;
        }
        elseif ($IBLOCK_ID == OILS_IB)
        {
            $SEF_FOLDER = PATH_OILS;
        }
        elseif ($IBLOCK_ID == AKB_IB)
        {
            $SEF_FOLDER = PATH_AKB;
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $SEF_FOLDER = PATH_DISCS;
        }
        elseif ($IBLOCK_ID == MISC_IB)
        {
            $SEF_FOLDER = PATH_MISC;
        }

        return array(
            'IBLOCK_ID'                        => $IBLOCK_ID,
            'SEF_FOLDER'                       => $SEF_FOLDER,
            'PAGE_ELEMENT_COUNT'               => $iOnPage,
            'ELEMENT_SORT_FIELD'               => $sSortField,
            'ELEMENT_SORT_ORDER'               => $sSortOrder,
            "ELEMENT_SORT_FIELD2"              => "SHOW_COUNTER",
            "ELEMENT_SORT_ORDER2"              => "DESC",
            'DETAIL_PROPERTY_CODE'             => \CCatalogExt::getPropertiesCodes($IBLOCK_ID),
            'HIDDEN_DISCOUNTS_IBLOCK_ID'       => \WS_PSettings::getFieldValue("HIDDEN_DISCOUNTS_IBLOCK_ID", false),
            //
            "IBLOCK_TYPE"                      => "catalog",
            "SECTION_USER_FIELDS"              => array("UF_SEO_TEXT"),
            //"SHOW_ALL_WO_SECTION"              => "Y",
            "TEMPLATE_THEME"                   => "",
            "HIDE_NOT_AVAILABLE"               => "L",
            "BASKET_URL"                       => PATH_BASKET,
            "USE_FILTER"                       => "Y",
            "FILTER_NAME"                      => "arTiresFilter",
            "ACTION_VARIABLE"                  => "action",
            "PRODUCT_ID_VARIABLE"              => "id",
            "SECTION_ID_VARIABLE"              => "SECTION_ID",
            "PRODUCT_QUANTITY_VARIABLE"        => "quantity",
            "PRODUCT_PROPS_VARIABLE"           => "prop",
            "AJAX_MODE"                        => "N",
            "AJAX_OPTION_JUMP"                 => "N",
            "AJAX_OPTION_STYLE"                => "N",
            "AJAX_OPTION_HISTORY"              => "N",
            "CACHE_TYPE"                       => "A",
            "CACHE_TIME"                       => "36000000",
            "CACHE_FILTER"                     => "Y",
            "CACHE_GROUPS"                     => "N",
            "SET_TITLE"                        => "Y",
            "ADD_SECTION_CHAIN"                => "N",
            "ADD_ELEMENT_CHAIN"                => "Y",
            "SET_STATUS_404"                   => "Y",
            "DETAIL_DISPLAY_NAME"              => "N",
            "USE_ELEMENT_COUNTER"              => "Y",
            "FILTER_VIEW_MODE"                 => "HORIZONTAL",
            "FILTER_FIELD_CODE"                => array(),
            "FILTER_PROPERTY_CODE"             => array(),
            "FILTER_PRICE_CODE"                => array(CATALOG_PRICE_NAME, RETAIL_PRICE_NAME),
            "PRICE_CODE"                       => array(CATALOG_PRICE_NAME, RETAIL_PRICE_NAME),
            "FILTER_OFFERS_FIELD_CODE"         => array(),
            "FILTER_OFFERS_PROPERTY_CODE"      => array(),
            "USE_REVIEW"                       => "N",
            "MESSAGES_PER_PAGE"                => "10",
            "USE_CAPTCHA"                      => "N",
            "FORUM_ID"                         => "1",
            "URL_TEMPLATES_READ"               => "",
            "SHOW_LINK_TO_FORUM"               => "N",
            "USE_COMPARE"                      => "N",
            "USE_PRICE_COUNT"                  => "N",
            "SHOW_PRICE_COUNT"                 => "1",
            "PRICE_VAT_INCLUDE"                => "Y",
            "PRICE_VAT_SHOW_VALUE"             => "Y",
            "PRODUCT_PROPERTIES"               => array(),
            "USE_PRODUCT_QUANTITY"             => "Y",
            "CONVERT_CURRENCY"                 => "Y",
            "QUANTITY_FLOAT"                   => "N",
            "OFFERS_CART_PROPERTIES"           => array(),
            "SHOW_TOP_ELEMENTS"                => "N",
            "SECTION_COUNT_ELEMENTS"           => "N",
            "SECTION_TOP_DEPTH"                => "2",
            "SECTIONS_VIEW_MODE"               => "LINE",
            "SECTIONS_SHOW_PARENT_NAME"        => "N",
            "LINE_ELEMENT_COUNT"               => "3",
            "LIST_PROPERTY_CODE"               => array(),
            "INCLUDE_SUBSECTIONS"              => "Y",
            "LIST_META_KEYWORDS"               => "-",
            "LIST_META_DESCRIPTION"            => "-",
            "LIST_BROWSER_TITLE"               => "-",
            "LIST_OFFERS_FIELD_CODE"           => array(),
            "LIST_OFFERS_PROPERTY_CODE"        => array(),
            "LIST_OFFERS_LIMIT"                => "0",
            "SECTION_BACKGROUND_IMAGE"         => "-",
            "DETAIL_META_KEYWORDS"             => "-",
            "DETAIL_META_DESCRIPTION"          => "-",
            "DETAIL_BROWSER_TITLE"             => "",
            "DETAIL_OFFERS_FIELD_CODE"         => array(),
            "DETAIL_OFFERS_PROPERTY_CODE"      => array(),
            "DETAIL_BACKGROUND_IMAGE"          => "-",
            "LINK_IBLOCK_TYPE"                 => "",
            "LINK_IBLOCK_ID"                   => "",
            "LINK_PROPERTY_SID"                => "",
            "LINK_ELEMENTS_URL"                => "",
            "USE_ALSO_BUY"                     => "N",
            "ALSO_BUY_ELEMENT_COUNT"           => "4",
            "ALSO_BUY_MIN_BUYES"               => "1",
            "OFFERS_SORT_FIELD"                => "sort",
            "OFFERS_SORT_ORDER"                => "desc",
            "OFFERS_SORT_FIELD2"               => "id",
            "OFFERS_SORT_ORDER2"               => "desc",
            "PAGER_TEMPLATE"                   => "catalog",
            "DISPLAY_TOP_PAGER"                => "Y",
            "DISPLAY_BOTTOM_PAGER"             => "Y",
            "PAGER_TITLE"                      => "Товары",
            "PAGER_SHOW_ALWAYS"                => "Y",
            "PAGER_DESC_NUMBERING"             => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME"  => "36000000",
            "PAGER_SHOW_ALL"                   => "Y",
            "ADD_PICT_PROP"                    => "MORE_PHOTO",
            "LABEL_PROP"                       => "-",
            "PRODUCT_DISPLAY_MODE"             => "Y",
            "OFFER_ADD_PICT_PROP"              => "MORE_PHOTO",
            "OFFER_TREE_PROPS"                 => "",
            "SHOW_DISCOUNT_PERCENT"            => "Y",
            "SHOW_OLD_PRICE"                   => "Y",
            "MESS_BTN_BUY"                     => "Купить",
            "MESS_BTN_ADD_TO_BASKET"           => "В корзину",
            "MESS_BTN_COMPARE"                 => "Сравнение",
            "MESS_BTN_DETAIL"                  => "Подробнее",
            "MESS_NOT_AVAILABLE"               => "Нет в наличии",
            "DETAIL_USE_VOTE_RATING"           => "N",
            "DETAIL_VOTE_DISPLAY_AS_RATING"    => "rating",
            "DETAIL_USE_COMMENTS"              => "N",
            "DETAIL_BLOG_USE"                  => "N",
            "DETAIL_VK_USE"                    => "N",
            "DETAIL_FB_USE"                    => "N",
            "AJAX_OPTION_ADDITIONAL"           => "",
            "USE_STORE"                        => "Y",
            "FIELDS"                           => array(
                0 => "TITLE",
                1 => "ADDRESS",
                2 => "PHONE",
                3 => "SCHEDULE",
                4 => "EMAIL",
                5 => "DESCRIPTION",
                6 => "COORDINATES",
            ),
            "USE_MIN_AMOUNT"                   => "N",
            "STORE_PATH"                       => "/store/#store_id#",
            "MAIN_TITLE"                       => "Наличие на складах",
            "MIN_AMOUNT"                       => "10",
            "DETAIL_BRAND_USE"                 => "N",
            "DETAIL_BRAND_PROP_CODE"           => "",
            "SIDEBAR_SECTION_SHOW"             => "N",
            "SIDEBAR_DETAIL_SHOW"              => "N",
            "SIDEBAR_PATH"                     => "",
            "COMPONENT_TEMPLATE"               => ".default",
            "COMMON_SHOW_CLOSE_POPUP"          => "Y",
            "DETAIL_SHOW_MAX_QUANTITY"         => "Y",
            "SET_LAST_MODIFIED"                => "Y",
            "ADD_SECTIONS_CHAIN"               => "Y",
            "USE_SALE_BESTSELLERS"             => "Y",
            "INSTANT_RELOAD"                   => "N",
            "CURRENCY_ID"                      => "RUB",
            "ADD_PROPERTIES_TO_BASKET"         => "Y",
            "PARTIAL_PRODUCT_PROPERTIES"       => "N",
            "USE_COMMON_SETTINGS_BASKET_POPUP" => "Y",
            "COMMON_ADD_TO_BASKET_ACTION"      => "BUY",
            "TOP_ADD_TO_BASKET_ACTION"         => "ADD",
            "SECTION_ADD_TO_BASKET_ACTION"     => "ADD",
            "DETAIL_ADD_TO_BASKET_ACTION"      => array(),
            "DETAIL_SHOW_BASIS_PRICE"          => "Y",
            "DETAIL_SET_CANONICAL_URL"         => "Y",
            "DETAIL_CHECK_SECTION_ID_VARIABLE" => "Y",
            "SHOW_DEACTIVATED"                 => "N",
            "DETAIL_DETAIL_PICTURE_MODE"       => "",
            "DETAIL_ADD_DETAIL_TO_SLIDER"      => "Y",
            "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "S",
            "USE_GIFTS_DETAIL"                 => "N",
            "USE_GIFTS_SECTION"                => "N",
            "USE_GIFTS_MAIN_PR_SECTION_LIST"   => "N",
            "STORES"                           => array(),
            "USER_FIELDS"                      => array(),
            "SHOW_EMPTY_STORE"                 => "N",
            "SHOW_GENERAL_STORE_INFORMATION"   => "N",
            "USE_BIG_DATA"                     => "N",
            "PAGER_BASE_LINK_ENABLE"           => "N",
            "SHOW_404"                         => "Y",
            "MESSAGE_404"                      => "",
            "DISABLE_INIT_JS_IN_COMPONENT"     => "Y",
            "DETAIL_SET_VIEWED_IN_COMPONENT"   => "N",
            "USE_MAIN_ELEMENT_SECTION"         => "Y",
            "DETAIL_STRICT_SECTION_CHECK"      => "Y",
            "SEF_MODE"                         => "Y",
            "SEF_URL_TEMPLATES"                => array(
                "sections"     => "",
                "section"      => "#SECTION_CODE#/",
                "element"      => "#SECTION_CODE#/#ELEMENT_CODE#/",
                "compare"      => "compare/",
                "smart_filter" => "#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/",
            )
        );
    }

    public static function getTree($IBLOCK_ID)
    {
        $arSort   = array("LEFT_MARGIN" => "ASC");
        $arFilter = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "<=DEPTH_LEVEL" => 2);
        $arSelect = array("ID", "NAME", "ACTIVE", "SECTION_PAGE_URL", "DEPTH_LEVEL");

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_catalog/getTree/";
        $cacheID   = "getTree" . $IBLOCK_ID;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arTree"]))
            {
                $arTree   = $vars["arTree"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $obList    = \CIBlockSection::GetList($arSort, $arFilter, true, $arSelect);
            while ($arSection = $obList->Fetch())
            {
                $arTree[] = $arSection;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arTree" => $arTree,
            ));
        }

        return $arTree;
    }

    /**
     * свойства ИБ автомобилей. Порядок важен.
     * @return array
     */
    public static function getTXProps()
    {
        return self::$arTXProps;
    }

    /**
     * получаем значения выбранных юзером свойств фильтра по автомобилю (выпадашки)
     * @return type
     */
    public static function getTXLists($IBLOCK_ID, $SECTION_CODE)
    {
        $arResult        = array();
        $arPropFilter    = array();
        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_filter/getTXLists/";
        $cacheID   = "getTXLists" . $IBLOCK_ID . $SECTION_CODE;
        foreach (self::$arTXProps as $property => $title)
        {
            $cacheID .= $property . $arSectionFilter[$property];
        }

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]))
            {
                $arResult = $vars["arResult"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            foreach (self::$arTXProps as $property => $title)
            {
                $arResult[$property] = array();

                $value = $arSectionFilter[$property]; //выбранное юзером значение текущего свояйства

                $arFilter = array("IBLOCK_ID" => TX_CARS_IB, "ACTIVE" => "Y") + $arPropFilter;
                if ($property == 'year')
                {
                    $arGroupBy  = array("PROPERTY_beginyear", "PROPERTY_endyear");
                    $arTmpYears = array();
                }
                else
                {
                    $arGroupBy = array("PROPERTY_" . $property);
                }

                $obList  = \CIBlockElement::GetList(array(), $arFilter, $arGroupBy);
                while ($arFetch = $obList->Fetch())
                {
                    if ($property == 'year')
                    {
                        for ($i = $arFetch["PROPERTY_BEGINYEAR_VALUE"]; $i <= $arFetch["PROPERTY_ENDYEAR_VALUE"]; $i++)
                        {
                            if (!in_array($i, $arTmpYears) && !empty($i))
                            {
                                $arTmpYears[]          = $i;
                                $arResult[$property][] = array(
                                    'VALUE'    => $i,
                                    'SELECTED' => $i == $value
                                );
                            }
                        }
                    }
                    else
                    {
                        $prop_value = $arFetch['PROPERTY_' . strtoupper($property) . '_VALUE'];

                        if (!empty($prop_value))
                        {
                            $arResult[$property][] = array(
                                'VALUE'    => $prop_value,
                                'SELECTED' => $prop_value == $value
                            );
                        }
                    }
                }

                if (!empty($value))
                {
                    if ($property == "year")
                    {
                        $arPropFilter += array(
                            "LOGIC"                => "AND",
                            "<=PROPERTY_beginyear" => $value,
                            ">=PROPERTY_endyear"   => $value,
                        );
                    }
                    else
                    {
                        $arPropFilter += array("PROPERTY_" . $property => $value);
                    }
                }

                if (empty($value))
                {
                    break;
                }
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult" => $arResult,
            ));
        }

        return $arResult;
    }

    public static function setFilters($IBLOCK_ID, $SECTION_CODE)
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $isPost  = $request->isPost();

        if ($request['FILTER']["CLEAR_BEFORE"] == "1")
        {
            self::clearFilter($IBLOCK_ID, $SECTION_CODE);
        }

        //запрос на сброс фильтров
        if ($request['FILTER']["CLEAR_ALL"] == "1")
        {
            self::clearFilter($IBLOCK_ID, $SECTION_CODE);
            return;
        }
        elseif ($request['FILTER']["CLEAR_CAR"] == "1")
        {
            self::clearCarFilter($IBLOCK_ID, $SECTION_CODE);
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'vendor');
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'model');
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'year');
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'modification');
        }
        elseif ($request['FILTER']["CLEAR_SIZE"] == "1")
        {
            if ($IBLOCK_ID == TIRES_IB)
            {
                self::clearSizeFilter($IBLOCK_ID, $SECTION_CODE);
                //unset($arFilter[SHIRINA]);
                //unset($arFilter[VYSOTA]);
                //unset($arFilter[DIAMETR]);
            }
            elseif ($IBLOCK_ID == DISCS_IB)
            {
                //unset($arFilter[DIAMETRDISKA]);
                //unset($arFilter[KREPLENIEDISKA]);
            }
        }
        elseif ($request['FILTER']["CLEAR_PROPERTY"] == "1")
        {
            $property      = $request['FILTER_PROPERTY'];
            $propertyValue = $request['FILTER_PROPERTY_VALUE'];

            if ($property == "modification")
            {
                self::clearCarFilter($IBLOCK_ID, $SECTION_CODE);
                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'vendor');
                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'model');
                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'year');
                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'modification');
            }
            elseif (in_array($property, array(
                        "MARKA", "PRESET", SM_VYAZKOST,
                        SM_TIP, SM_NAZNACHENIE, SM_VIDMASLA,
                        OZH_TIP, OZH_TSVET, SM_NAZNACHENIEDV,
                        DISKI_MARKA, DIA,
                    )))
            {
                $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

                if (($key = array_search($propertyValue, $arSectionFilter[$property])) !== false)
                {
                    \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $property, $key);
                }
            }
            else
            {
                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $property);
            }
        }

        $arFilter = array();
        $mode     = 'update';

        //$request['FILTER'] - может быть одно конкретное свойство (например при установке какого-либо чекбокса)
        //а может быть и множество свойств (например переход по ссылке или фильтр по авто)
        if ($isPost && !empty($request['FILTER']))
        {
            //ajax-запрос. в $request['FILTER'] содержится не весь фильтр.
            $arFilter = $request['FILTER'];
            $mode     = 'update';
        }
        elseif (!$isPost && !empty($request['FILTER']))
        {
            //переход по ссылке. в $request['FILTER'] содержится весь фильтр.
            $arFilter = $request['FILTER'];
            $mode     = 'replace';
        }

        if (!empty($arFilter['modification']) || $request['FILTER_SET'] == 'car' || $request['FILTER_SET'] == 'discs_car')
        {
            if (empty($arFilter['TUNING']))
            {
                self::clearSizeFilter($IBLOCK_ID, $SECTION_CODE);

                if ($IBLOCK_ID == TIRES_IB)
                {
                    unset($arFilter[SHIRINA]);
                    unset($arFilter[VYSOTA]);
                    unset($arFilter[DIAMETR]);
                }
                elseif ($IBLOCK_ID == DISCS_IB)
                {
                    //unset($arFilter[DIAMETRDISKA]);
                    //unset($arFilter[KREPLENIEDISKA]);
                }
            }
        }

        if (!empty($arFilter[SHIRINA]) || !empty($arFilter[VYSOTA]) || !empty($arFilter[DIAMETR]) || !empty($arFilter[DIAMETRDISKA]) || !empty($arFilter[KREPLENIEDISKA]) || $request['FILTER_SET'] == 'size' || $request['FILTER_SET'] == 'discs_size')
        {
            if (empty($arFilter['TUNING']))
            {
                if ($IBLOCK_ID == TIRES_IB)
                {
                    self::clearCarFilter($IBLOCK_ID, $SECTION_CODE);

                    unset($arFilter['vendor']);
                    unset($arFilter['model']);
                    unset($arFilter['year']);
                    unset($arFilter['modification']);
                }
            }
        }

        //printra($arFilter);
        $arResult = self::setResultFilter($IBLOCK_ID, $SECTION_CODE, $arFilter, $mode);

        return $arResult;
    }

    public static function setResultFilter($IBLOCK_ID, $SECTION_CODE, $arFilter, $mode)
    {
        $arApiFilter   = array();
        $arHumanFilter = $mode == 'update' ? \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE) : array();

        //printra($arFilter);
        if (empty($arFilter['TUNING']))
        {
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE);
        }

        unset($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]);

        //все доступные свойства
        $allAviableProps = array_merge(self::$arAviableProps, array_keys(self::$arSizeProps), array_keys(self::$arTXProps));

        //очистка фильтра от левака
        foreach ($arFilter as $property => &$arFilterItem)
        {
            if (is_array($arFilterItem))
            {
                foreach ($arFilterItem as &$itemValue)
                {
                    if (!is_array($itemValue)) $itemValue = urldecode($itemValue);
                }
            }
            else
            {
                $arFilterItem = urldecode($arFilterItem);
            }

            if (!in_array($property, $allAviableProps)) continue;

            if (in_array($property, self::$arCheckBoxesMulti) && !is_array($arFilterItem))
            {
                //$arFilterItem должно быть array. Если нет - исправим это
                if (!is_array($arHumanFilter[$property]))
                {
                    $arHumanFilter[$property] = array();
                }

                if (($key = array_search($arFilterItem, $arHumanFilter[$property])) !== false)
                {
                    unset($arHumanFilter[$property][$key]);
                }
                else
                {
                    $arHumanFilter[$property][] = $arFilterItem;
                }
            }
            else
            {
                $arHumanFilter[$property] = $arFilterItem;
            }
            if (empty($arHumanFilter[$property])) unset($arHumanFilter[$property]);
        }

        self::setCarFilter($arHumanFilter, $IBLOCK_ID, $SECTION_CODE);

        //формируем $arApiFilter. записываем $arHumanFilter в сессию
        foreach ($arHumanFilter as $property => $arFilterItem)
        {
            if (in_array($property, array_keys(self::$arTXProps)))
            {
                //фильтр по авто создадим отдельно
                continue;
            }
            elseif (in_array($property, self::$arRangeProps))
            {
                if ($property == "PRICE") $code = CATALOG_PRICE;
                elseif ($property == SHIRINADISKA) $code = "PROPERTY_" . SHIRINADISKA . "_VALUE";
                elseif ($property == VYLET) $code = "PROPERTY_" . VYLET . "_VALUE";
                else $code = "PROPERTY_" . $property;

                //$arFilterItem - array
                $koef = $property == "PRICE" ? MAX_DISCOUNT : 0;

                if ($property == SHIRINADISKA)
                {
                    $arApiFilter[] = array(
                        $code => $arFilterItem["TO"],
                    );
                }
                else
                {
                    $arApiFilter[] = array(
                        "LOGIC"      => "AND",
                        ">=" . $code => floor($arFilterItem["FROM"] / (1 + $koef)),
                        "<=" . $code => ceil($arFilterItem["TO"] / (1 - $koef)),
                    );
                }
            }
            elseif ($property == "TUNING")
            {
                continue;
            }
            elseif ($property == "QUANTITY")
            {
                $arApiFilter[] = array(">CATALOG_QUANTITY" => "4");
            }
            elseif ($property == "PRESET")
            {
                //$arFilterItem - array
                $arPresetSizes = array();

                if ($IBLOCK_ID == TIRES_IB)
                {
                    foreach ($arFilterItem as $preset)
                    {
                        $matches = array();
                        preg_match_all('/(?P<d>R[0-9]+) (?P<w>[0-9]+)\/(?P<h>[0-9]+)/', $preset, $matches);

                        //свойства инфоблока каталога товаров (шин)
                        $prop_w = "=PROPERTY_" . SHIRINA . "_VALUE";
                        $prop_h = "=PROPERTY_" . VYSOTA . "_VALUE";
                        $prop_d = "=PROPERTY_" . DIAMETR . "_VALUE";

                        $arPresetSizes[] = array($prop_w => $matches["w"][0], $prop_h => $matches["h"][0], $prop_d => $matches["d"][0]);
                        $arPresetSizes[] = array($prop_w => $matches["w"][0], $prop_h => $matches["h"][0], $prop_d => $matches["d"][0] . "C");
                    }

                    $arPreset = array("LOGIC" => "OR");
                    foreach ($arPresetSizes as $arSize)
                    {
                        $arPreset[] = $arSize;
                    }
                }
                elseif ($IBLOCK_ID == DISCS_IB)
                {
                    foreach ($arFilterItem as $preset)
                    {
                        $arPresetSizes[] = \CFilterExt::parseDiskPreset($preset);
                    }

                    $arPreset = array("LOGIC" => "OR");
                    foreach ($arPresetSizes as $arSize)
                    {
                        $arPreset[] = $arSize;
                    }
                    //printra($arPreset);
                }

                $arApiFilter[] = $arPreset;
            }
            elseif (in_array($property, array(AKTSIYA)))
            {
                $propertyKey   = "PROPERTY_" . $property;
                $arApiFilter[] = array($propertyKey => $arFilterItem);
            }
            elseif ($property == "OS_PRIMENENIYA")
            {
                $propertyKey = "PROPERTY_" . $property . "_VALUE";

                if ($arFilterItem == "steer_trail")
                {
                    $arFilterItem = array("рулевая ось", "прицеп ось");
                }

                $arApiFilter[] = array($propertyKey => $arFilterItem);
            }
            elseif ($property == SM_NAZNACHENIEDV)
            {
                $propertyKey = "PROPERTY_" . $property . "_VALUE";

                if (is_array($arFilterItem))
                {
                    $filterDv = array();

                    foreach ($arFilterItem as $subFilterItem)
                    {
                        if ($subFilterItem == "Для бензиновых двигателей")
                        {
                            $filterDv[] = "Для бензиновых двигателей";
                            $filterDv[] = "Универсальное";
                        }
                        elseif ($subFilterItem == "Для дизельных двигателей")
                        {
                            $filterDv[] = "Для дизельных двигателей";
                            $filterDv[] = "Универсальное";
                        }
                        else
                        {
                            $filterDv[] = $subFilterItem;
                        }
                    }
                    $arApiFilter[] = array($propertyKey => $filterDv);
                }
            }
            else
            {
                $propertyKey   = "PROPERTY_" . $property . "_VALUE";
                $arApiFilter[] = array($propertyKey => $arFilterItem);
            }
        }

        if (!empty($_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE]))
        {
            $arApiFilter[] = $_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE];
        }

        \CFilterExt::setFilterSession($IBLOCK_ID, $SECTION_CODE, $arHumanFilter);

        return $arApiFilter;
    }

    private static function addFilterLabel($property, $label, $value = false, $all = false)
    {
        if (!$all)
        {
            return '<button '
                    . ' title="Сбросить фильтр"'
                    . ' data-item-property="' . $property . '"'
                    . ' data-item-value="' . $value . '"'
                    . ' onclick="Filter.clearFilterItem(this)"'
                    . '>'
                    . '<span>' . $label . '</span><i class="ion-ios-close-empty"></i></button>';
        }
        else
        {
            return '<button '
                    . ' title="Сбросить все фильтры"'
                    . ' onclick="Filter.clear(this)"'
                    . '>'
                    . '<span class="bold">Очистить все</span><i class="ion-ios-close-empty bold"></i></button>';
        }
    }

    public static function getFilterLabels($IBLOCK_ID, $SECTION_CODE)
    {
        $res           = array();
        $arHumanFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arHumanFilter as $property => $arFilterItem)
        {
            if (in_array($property, array_keys(self::$arTXProps)))
            {
                if ($property != 'modification') continue;

                $label = $arHumanFilter['vendor'] . ' '
                        . $arHumanFilter['model'] . ' '
                        . $arHumanFilter['year'] . 'г. '
                        . $arHumanFilter['modification'];

                $res[] = self::addFilterLabel($property, $label);
            }
            elseif (in_array($property, array_keys(self::$arSizeProps)) && in_array($SECTION_CODE, array(LEGKOVYE, GRUZOVYE, MOTO)))
            {
                if (is_array($arFilterItem))
                {
                    $labelValue = "Диаметр: ";
                    foreach ($arFilterItem as $key => $subItemValue)
                    {
                        $labelValue .= "&nbsp;&nbsp;" . $subItemValue . ";";
                    }
                    $res[] = self::addFilterLabel($property, rtrim($labelValue, ";"));
                }
                else
                {
                    $label = self::$arSizeProps[$property] . ': ' . $arFilterItem;
                    $res[] = self::addFilterLabel($property, $label);
                }
            }
            elseif (in_array($property, self::$arRangeProps))
            {
                switch ($property)
                {
                    case "PRICE": $labelStart = "Цена ";
                        $labelEnd   = "";
                        break;
                    case AKB_DLINA: $labelStart = "Длина ";
                        $labelEnd   = " мм";
                        break;
                    case AKB_SHIRINA: $labelStart = "Ширина ";
                        $labelEnd   = " мм";
                        break;
                    case AKB_VYSOTA: $labelStart = "Высота ";
                        $labelEnd   = " мм";
                        break;
                    case AKB_EMKOST: $labelStart = "Емкость ";
                        $labelEnd   = " А·ч";
                        break;
                    case SHIRINADISKA: $labelStart = "Ширина ";
                        $labelEnd   = "";
                        break;
                    case VYLET: $labelStart = "Вылет ";
                        $labelEnd   = "";
                        break;
                    default: $labelStart = "";
                        $labelEnd   = "";
                        break;
                }

                $label = $labelStart;

                if ($property == "PRICE")
                {
                    if ($arFilterItem['FROM'] != $arFilterItem['TO'])
                    {
                        $label .= 'от ' . printPrice($arFilterItem['FROM']) . ' до ' . printPrice($arFilterItem['TO']);
                    }
                    else
                    {
                        $label .= printPrice($arFilterItem['FROM']);
                    }
                }
                else
                {
                    if ($arFilterItem['FROM'] != $arFilterItem['TO'])
                    {
                        $label .= 'от ' . $arFilterItem['FROM'] . ' до ' . $arFilterItem['TO'];
                    }
                    else
                    {
                        $label .= $arFilterItem['FROM'];
                    }
                }

                $label .= $labelEnd;

                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == SALE)
            {
                $label = 'Распродажа';
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == RUN_FLAT)
            {
                $label = 'RunFlat';
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == "TUNING")
            {
                $label = 'Тюнинг: Да';
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == BONUS)
            {
                $label = 'Бонус';
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == HIT)
            {
                $label = 'Хит продаж';
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == AKTSIYA)
            {
                $HBElement = getHLEelementByXML_ID(ACTIONS_HB, $arFilterItem);
                $label     = $HBElement['UF_NAME'];
                $res[]     = self::addFilterLabel($property, $label);
            }
            elseif ($property == "QUANTITY")
            {
                $label = "от 4 шт.";
                $res[] = self::addFilterLabel($property, $label);
            }
            elseif ($property == "OS_PRIMENENIYA")
            {
                $label = $arFilterItem == 'steer_trail' ? "рулевая / прицепная ось" : $arFilterItem;
                $res[] = self::addFilterLabel($property, mb_ucfirst($label));
            }
            elseif ($property == SM_NAZNACHENIEDV)
            {
                foreach ($arFilterItem as $key => $subItemValue)
                {
                    $label = $subItemValue == 'universe' ? "Универсальное" : $subItemValue;
                    $res[] = self::addFilterLabel($property, $label, $label);
                }
            }
            else
            {
                if (is_array($arFilterItem))
                {
                    if ($property == KREPLENIEDISKA && in_array($property, self::$arCheckBoxesMulti))
                    {
                        $labelValue = "Крепление диска: ";
                        foreach ($arFilterItem as $key => $subItemValue)
                        {
                            $labelValue .= "&nbsp;&nbsp;" . $subItemValue . ";";
                        }
                        $res[] = self::addFilterLabel($property, rtrim($labelValue, ";"));
                    }
                    else
                    {
                        foreach ($arFilterItem as $key => $subItemValue)
                        {
                            if ($property == "PRESET" && strstr($subItemValue, "|"))
                            {
                                $arPresetParts   = explode("|", $subItemValue);
                                $subItemValueKey = $arPresetParts[1];
                            }
                            else
                            {
                                $subItemValueKey = $subItemValue;
                            }
                            $res[] = self::addFilterLabel($property, $subItemValueKey, $subItemValue);
                        }
                    }
                }
                else
                {
                    $res[] = self::addFilterLabel($property, $arFilterItem);
                }
            }
        }

        if (count($res) > 1)
        {
            $res[] = self::addFilterLabel(0, 0, 0, 1);
        }
        return implode(false, $res);
    }

    public static function getActiveFilterTab($IBLOCK_ID, $SECTION_CODE)
    {
        global $APPLICATION;
        $cookie_filter_type = $APPLICATION->get_cookie("FILTER_TYPE");

        $arFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        if ($IBLOCK_ID == TIRES_IB)
        {
            if (!empty($arFilter[SHIRINA]) || !empty($arFilter[VYSOTA]) || !empty($arFilter[DIAMETR]))
            {
                return 'size';
            }
            elseif (!empty($_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE]))
            {
                return 'car';
            }
            elseif ($cookie_filter_type == "size")
            {
                return 'size';
            }
            elseif ($cookie_filter_type == "car")
            {
                return 'car';
            }


            return 'car';
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            if (!empty($arFilter[KREPLENIEDISKA]) || !empty($arFilter[DIAMETRDISKA]))
            {
                return 'discs_size';
            }
            elseif (!empty($_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE]))
            {
                return 'discs_car';
            }
            elseif ($cookie_filter_type == "discs_size")
            {
                return 'discs_size';
            }
            elseif ($cookie_filter_type == "discs_car")
            {
                return 'discs_car';
            }


            return 'discs_car';
        }
    }

    /**
     * записывает в сессию два массива: 1 - фильтр со всеми доступными значениями ширины, высота и диматера,
     * 2 - сгруппированные уникальные комбинации ширины/высоты/диметра
     * 
     * @param array $arFilter массив вида array('vendor' => 'BMW', 'model' => 'M5', ...)
     * @return string
     */
    public static function setCarFilter($arFilter, $IBLOCK_ID, $SECTION_CODE)
    {
        unset($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]);

        $arProperties = array_keys(self::$arTXProps);

        //получаем из $arFilter только нужные нам свойств и формируем $arCarFilter для запроса к API
        //перебираем свойства ИБ автомобилей.
        //Как только попадется свойство, для которого юзер не выбрал значение - значит оно и есть, то
        //для которого надо получить список и остановиться
        $arCarFilter  = array("IBLOCK_ID" => TX_CARS_IB, "ACTIVE" => "Y"/* , "TUNING" => $arFilter["TUNING"] */);
        $nextProperty = false;

        foreach ($arProperties as $property)
        {
            $val = $arFilter[$property];

            if (empty($val) || !empty($nextProperty))
            {
                if (empty($nextProperty)) $nextProperty = $property;

                \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $property);
                continue;
            }

            if ($property == "year")
            {
                $arCarFilter[] = array(
                    "LOGIC"                => "AND",
                    "<=PROPERTY_beginyear" => trim($val),
                    ">=PROPERTY_endyear"   => trim($val),
                );
            }
            else
            {
                $arCarFilter["PROPERTY_" . $property] = trim($val);
            }

            \CFilterExt::setFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $property, $val);
        }

        if (!empty($nextProperty))
        {
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'PRESET');

            if ($nextProperty == 'year')
            {
                $arGroupBy = array("PROPERTY_beginyear", "PROPERTY_endyear");
            }
            else
            {
                $arGroupBy = array("PROPERTY_" . $nextProperty);
            }

            //пытаемся получить данные из кеша
            $obCache   = \Bitrix\Main\Data\Cache::createInstance();
            $lifeTime  = strtotime("1day", 0);
            $cachePath = "/ccache_filter/setCarFilter1/";
            $cacheID   = "setCarFilter1" . $nextProperty . serialize($arCarFilter) . serialize($arGroupBy);

            if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
            {
                $vars = $obCache->GetVars();
                if (isset($vars["arResult"]))
                {
                    $arResult = $vars["arResult"];
                    //$lifeTime = 0;
                }
            }

            if ($lifeTime > 0)
            {
                $arResult  = array();
                $obCarList = \CIBlockElement::GetList(array(), $arCarFilter, $arGroupBy);
                while ($arCarItem = $obCarList->GetNext(false, false))
                {
                    if ($nextProperty == 'year')
                    {
                        for ($i = $arCarItem["PROPERTY_BEGINYEAR_VALUE"]; $i <= $arCarItem["PROPERTY_ENDYEAR_VALUE"]; $i++)
                        {
                            if (!in_array($i, $arResult) && !empty($i))
                            {
                                $arResult[] = $i;
                            }
                        }
                    }
                    else
                    {
                        $value = $arCarItem["PROPERTY_" . strtoupper($nextProperty) . "_VALUE"];

                        if (!in_array($value, $arResult) && !empty($value))
                        {
                            $arResult[] = $value;
                        }
                    }
                }

                //кешируем
                $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
                $obCache->EndDataCache(array(
                    "arResult" => $arResult,
                ));
            }

            return array("refresh" => "N", "property" => $nextProperty, "items" => $arResult);
        }
        else
        {
            if ($IBLOCK_ID == TIRES_IB)
            {
                list($arResult, $arPresets) = self::getTiresPresets($IBLOCK_ID, $arCarFilter);
            }
            elseif ($IBLOCK_ID == DISCS_IB)
            {
                list($arResult, $arPresets) = self::getDiscsPresets($IBLOCK_ID, $arCarFilter, $arFilter["TUNING"]);
            }

            if (empty($arFilter['TUNING']))
            {
                self::clearSizeFilter($IBLOCK_ID, $SECTION_CODE);
            }

            $_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE]  = $arResult; //фильтр для api со всеми знакчениями размеров
            $_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE] = $arPresets; //массив пресетов, готовый для использования в умном фильтре

            $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);
            return array("refresh" => "Y", "property" => null, "items" => array(), "FILTER" => json_encode($arSectionFilter));
        }
    }

    public static function clearFilter($IBLOCK_ID, $SECTION_CODE)
    {
        global $APPLICATION;
        $APPLICATION->set_cookie("FILTER_TYPE", NULL);
        self::clearCarFilter($IBLOCK_ID, $SECTION_CODE);
        self::clearSizeFilter($IBLOCK_ID, $SECTION_CODE);
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE);
    }

    public static function clearSizeFilter($IBLOCK_ID, $SECTION_CODE)
    {
        if ($IBLOCK_ID == TIRES_IB)
        {
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, SHIRINA);
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, VYSOTA);
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, DIAMETR);
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, DIAMETRDISKA);
            \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, KREPLENIEDISKA);
        }
    }

    public static function clearCarFilter($IBLOCK_ID, $SECTION_CODE)
    {
        unset($_SESSION['FILTER_BY_CAR'][$IBLOCK_ID][$SECTION_CODE]);
        unset($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]);

        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'vendor');
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'PRESET');
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'TUNING');
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'model');
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'year');
        \CFilterExt::unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, 'modification');
    }

    public static function getFilterUrl($IBLOCK_ID, $SECTION_CODE)
    {
        $arResult = null;

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        if (!empty($arSectionFilter))
        {
            $arResult['FILTER'] = $arSectionFilter;
        }

        return urlencode(json_encode($arResult));
    }

    private static function getTiresPresets($IBLOCK_ID, $arCarFilter)
    {
        //пытаемся получить данные из кеша
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_filter/getTiresPresets/";
        $cacheID   = "getTiresPresets" . $IBLOCK_ID . serialize($arCarFilter);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]) && isset($vars["arPresets"]))
            {
                $arResult  = $vars["arResult"];
                $arPresets = $vars["arPresets"];
                $lifeTime  = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arModificationIds = array();
            $arSizes           = array();
            $arPresets         = array();

            $obCarList = \CIBlockElement::GetList(array(), $arCarFilter, false, false, array("ID"));
            while ($arCarItem = $obCarList->Fetch())
            {
                $arModificationIds[] = $arCarItem['ID'];
            }

            //свойства инфоблока базы автомобилей и шин
            $arSelect = array(
                "PROPERTY_front_width",
                "PROPERTY_front_profile",
                "PROPERTY_front_diameter",
                "PROPERTY_back_width",
                "PROPERTY_back_profile",
                "PROPERTY_back_diameter",
            );

            //свойства инфоблока каталога товаров (шин)
            $prop_w = "=PROPERTY_" . SHIRINA . "_VALUE";
            $prop_h = "=PROPERTY_" . VYSOTA . "_VALUE";
            $prop_d = "=PROPERTY_" . DIAMETR . "_VALUE";

            $arTireFilter = array("IBLOCK_ID" => TX_TYRES_IB, "ACTIVE" => "Y", "PROPERTY_carmodel_link" => $arModificationIds);

            $obTireList = \CIBlockElement::GetList(Array(), $arTireFilter, false, false, $arSelect);
            while ($arTireItem = $obTireList->Fetch())
            {
                if (!empty($arTireItem['PROPERTY_FRONT_WIDTH_VALUE']))
                {
                    $arFrontSize = array(
                        $prop_w => $arTireItem['PROPERTY_FRONT_WIDTH_VALUE'],
                        $prop_h => $arTireItem['PROPERTY_FRONT_PROFILE_VALUE'],
                        $prop_d => "R" . ceil($arTireItem['PROPERTY_FRONT_DIAMETER_VALUE']),
                    );

                    $arFrontSizeC = array(
                        $prop_w => $arTireItem['PROPERTY_FRONT_WIDTH_VALUE'],
                        $prop_h => $arTireItem['PROPERTY_FRONT_PROFILE_VALUE'],
                        $prop_d => "R" . ceil($arTireItem['PROPERTY_FRONT_DIAMETER_VALUE']) . "C",
                    );

                    $sPreset = "R" . ceil($arTireItem['PROPERTY_FRONT_DIAMETER_VALUE'])
                            . " " . $arTireItem['PROPERTY_FRONT_WIDTH_VALUE']
                            . "/" . $arTireItem['PROPERTY_FRONT_PROFILE_VALUE'];

                    if (!array_key_exists($sPreset, $arPresets))
                    {
                        $arPresets[$sPreset] = array('value' => $sPreset, 'title' => $sPreset);
                    }
                }

                if (!empty($arTireItem['PROPERTY_BACK_WIDTH_VALUE']))
                {
                    $arBackSize = array(
                        $prop_w => $arTireItem['PROPERTY_BACK_WIDTH_VALUE'],
                        $prop_h => $arTireItem['PROPERTY_BACK_PROFILE_VALUE'],
                        $prop_d => "R" . ceil($arTireItem['PROPERTY_BACK_DIAMETER_VALUE']),
                    );

                    $arBackSizeC = array(
                        $prop_w => $arTireItem['PROPERTY_BACK_WIDTH_VALUE'],
                        $prop_h => $arTireItem['PROPERTY_BACK_PROFILE_VALUE'],
                        $prop_d => "R" . ceil($arTireItem['PROPERTY_BACK_DIAMETER_VALUE']) . "C",
                    );

                    $sPreset = "R" . ceil($arTireItem['PROPERTY_BACK_DIAMETER_VALUE'])
                            . " " . $arTireItem['PROPERTY_BACK_WIDTH_VALUE']
                            . "/" . $arTireItem['PROPERTY_BACK_PROFILE_VALUE'];

                    if (!array_key_exists($sPreset, $arPresets))
                    {
                        $arPresets[$sPreset] = array('value' => $sPreset, 'title' => $sPreset);
                    }
                }

                if (!empty($arFrontSize) && !in_array($arFrontSize, $arSizes)) $arSizes[] = $arFrontSize;
                if (!empty($arBackSize) && !in_array($arBackSize, $arSizes)) $arSizes[] = $arBackSize;

                if (!empty($arFrontSizeC) && !in_array($arFrontSizeC, $arSizes)) $arSizes[] = $arFrontSizeC;
                if (!empty($arBackSizeC) && !in_array($arBackSizeC, $arSizes)) $arSizes[] = $arBackSizeC;
            }

            $arResult = array("LOGIC" => "OR");
            foreach ($arSizes as $arSize)
            {
                $arResult[] = $arSize;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult"  => $arResult,
                "arPresets" => $arPresets,
            ));
        }

        return array($arResult, $arPresets);
    }

    private static function getDiscsPresets($IBLOCK_ID, $arCarFilter, $isTuning = false)
    {
        //$isTuning = (bool) $arCarFilter["TUNING"];
        //пытаемся получить данные из кеша
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_filter/getDiscsPresets/";
        $cacheID   = "getDiscsPresets" . $IBLOCK_ID . serialize($arCarFilter) . $isTuning;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]) && isset($vars["arPresets"]))
            {
                $arResult  = $vars["arResult"];
                $arPresets = $vars["arPresets"];
                $lifeTime  = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arModificationIds = array();
            $arSingleProps     = array(); //набор параметров , привязанный к конкретной модификации
            $arSizes           = array();
            $arPresets         = array();

            $arSelectCarProps = array(
                "ID",
                "PROPERTY_pcd",
                "PROPERTY_dia",
                "PROPERTY_diamax",
                "PROPERTY_counthole",
            );

            //получаем массив модификаций
            $obCarList = \CIBlockElement::GetList(array(), $arCarFilter, false, false, $arSelectCarProps);
            while ($arCarItem = $obCarList->Fetch())
            {
                $arModificationIds[]             = $arCarItem['ID'];
                $arSingleProps[$arCarItem['ID']] = $arCarItem;
            }

            //свойства инфоблока базы автомобилей и шин
            $arSelect = array(
                "PROPERTY_carmodel_link",
                "PROPERTY_type",
                "PROPERTY_oem",
                "PROPERTY_diameter",
                "PROPERTY_et_from",
                "PROPERTY_et_to",
                "PROPERTY_width",
            );

            //свойства инфоблока каталога товаров (диски)
            $prop_k = "PROPERTY_" . KREPLENIEDISKA . "_VALUE";
            $prop_w = "=PROPERTY_" . SHIRINADISKA . "_VALUE";
            $prop_d = "=PROPERTY_" . DIAMETRDISKA . "_VALUE";
            $prop_v = "PROPERTY_" . VYLET . "_VALUE";
            $prop_o = "PROPERTY_" . DIA . "_VALUE";

            $arTireFilter = array("IBLOCK_ID" => TX_DISKS_IB, "ACTIVE" => "Y", "PROPERTY_carmodel_link" => $arModificationIds);

            $obTireList = \CIBlockElement::GetList(Array(), $arTireFilter, false, false, $arSelect);
            while ($arTireItem = $obTireList->Fetch())
            {
                $arSingleProp = $arSingleProps[$arTireItem["PROPERTY_CARMODEL_LINK_VALUE"]];

                $TYPE     = ceil($arTireItem['PROPERTY_TYPE_VALUE']);
                $WIDTH    = /* ceil */($arTireItem['PROPERTY_WIDTH_VALUE']);
                $DIAMETER = ceil($arTireItem['PROPERTY_DIAMETER_VALUE']);

                $COUNTHOLE = ceil($arSingleProp['PROPERTY_COUNTHOLE_VALUE']);
                $PCD       = floor($arSingleProp['PROPERTY_PCD_VALUE']);
                $DIA       = round($arSingleProp['PROPERTY_DIA_VALUE'], 1);
                $DIAMAX    = round($arSingleProp['PROPERTY_DIAMAX_VALUE'], 1);

                if ($WIDTH == ceil($WIDTH))
                {
                    $WIDTH = ceil($WIDTH);
                }

                $arPCD = \CFilterExt::getDiscksPcd($COUNTHOLE, $PCD);

                if ($isTuning)
                {
                    $ET_FROM = ceil($arTireItem['PROPERTY_ET_FROM_VALUE']) - 5;
                    $ET_TO   = ceil($arTireItem['PROPERTY_ET_TO_VALUE']) + 5;
                    $arDIA   = \CFilterExt::getDiscksDia($DIA, $DIAMAX, 3000, true);
                }
                else
                {
                    $ET_FROM = ceil($arTireItem['PROPERTY_ET_FROM_VALUE']);
                    $ET_TO   = ceil($arTireItem['PROPERTY_ET_TO_VALUE']);
                    $arDIA   = \CFilterExt::getDiscksDia($DIA, $DIAMAX);
                }

                $arSize = array(
                    $prop_d => "R" . $DIAMETER,
                    $prop_w => array(
                        $WIDTH,
                        number_format($WIDTH, 1, '.', ' '),
                        $WIDTH . ".0",
                        str_replace(".", ",", $WIDTH),
                    ),
                    array(
                        "LOGIC"        => "AND",
                        ">=" . $prop_v => $ET_FROM,
                        "<=" . $prop_v => $ET_TO
                    ),
                    $prop_k => $arPCD,
                    $prop_o => $arDIA,
                );

                if (!in_array($arSize, $arSizes)) $arSizes[] = $arSize;

                $arPresetStrings = array();
                $sPreset         = $WIDTH . "Jx" . $DIAMETER . " " . $COUNTHOLE . "x" . $PCD;

                if ($ET_FROM == $ET_TO && $DIA == $DIAMAX)
                {
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . " d" . $DIA;
                }
                elseif ($ET_FROM == $ET_TO && $DIA != $DIAMAX)
                {
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . " d" . $DIA;
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . " d" . $DIAMAX;
                }
                elseif ($ET_FROM != $ET_TO && $DIA == $DIAMAX)
                {
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . ".." . $ET_TO . " d" . $DIA;
                }
                elseif ($ET_FROM != $ET_TO && $DIA != $DIAMAX)
                {
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . ".." . $ET_TO . " d" . $DIA;
                    $arPresetStrings[] = $sPreset . " ET" . $ET_FROM . ".." . $ET_TO . " d" . $DIAMAX;
                }

                foreach ($arPresetStrings as $sPresetString)
                {
                    if (!array_key_exists($sPresetString, $arPresets))
                    {
                        $arPresets[$TYPE . "|" . $sPresetString] = array(
                            'value' => $TYPE . "|" . $sPresetString,
                            'title' => $sPresetString
                        );
                    }
                }
            }

            $arResult = array("LOGIC" => "OR");
            foreach ($arSizes as $arSize)
            {
                $arResult[] = $arSize;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult"  => $arResult,
                "arPresets" => $arPresets,
            ));
        }

        return array($arResult, $arPresets);
    }

    /**
     * получает массив ID складов по их XML_ID
     * @param array $arXML_IDs массив XML_ID нужных складов
     */
    public static function getStoresByXML_ID($arXML_IDs)
    {
        $arStoreItems = array();
        $arFilter     = array("XML_ID" => $arXML_IDs);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_stores/getStoresByXML_ID/";
        $cacheID   = "getStoresByXML_ID" . serialize($arFilter);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arStoreItems"]))
            {
                $arStoreItems = $vars["arStoreItems"];
                //$lifeTime     = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $obList  = \CCatalogStore::GetList(array(), $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                $arStoreItems[$arFetch['XML_ID']]                  = $arFetch;
                $arStoreItems[$arFetch['XML_ID']]["UF_STORE_CITY"] = getUF("CAT_STORE", $arFetch['ID'], "UF_STORE_CITY");
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arStoreItems" => $arStoreItems,
            ));
        }

        return $arStoreItems;
    }

    /**
     * получает ID склада по XML_ID без кеширования 
     * @param type $XML_ID
     * @return type
     */
    public static function getStoreByXML_ID($XML_ID)
    {
        $arFilter = array("XML_ID" => $XML_ID);

        $obList  = \CCatalogStore::GetList(array(), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch;
        }

        return false;
    }

    /**
     * обновляет инфу (название, описание и т.п.) по складам по запросу в 1С
     */
    public static function updateStoresInfo()
    {
        $arStoresUnsorted = \CURL::getReplay("Store", array('XML_ID' => 'All'));
        $images_path      = "stores_images";

        if (count($arStoresUnsorted) < 1)
        {
            AddMessage2Log('ABORT', "", 6);
            return false;
        }

        //AddMessage2Log('get ' . count($arStoresUnsorted) . ' records', "", 2);

        $arStores = array();
        foreach ($arStoresUnsorted['Stores'] as $arStore)
        {
            $arStores[$arStore['XML_ID']] = $arStore['data'];
        }

        $arStoreItems = self::getStoresByXML_ID(array_keys($arStores));

        unset($arStore);

        foreach ($arStoreItems as $XML_ID => $arStoreItem)
        {
            $arStore = $arStores[$XML_ID];

            $NAME           = $arStore['name'] == "Оптовый склад" ? "Склад" : $arStore['name'];
            $ISSUING_CENTER = $arStore['ТочкаВыдачи'] == "Да" ? "Y" : "N";
            $SERVICE_CENTER = $arStore['СервисныйЦентр'] == "Да" ? true : false;
            $IMAGE_ID       = null;

            if (!empty($arStore['Изображение']))
            {
                //update store picture
                $oldPicture   = $arStoreItem['IMAGE_ID'];
                $fileName     = 'store' . $arStoreItem['ID'] . '.png';
                $base64String = $arStore['Изображение'];

                $IMAGE_ID = base64ToFile($base64String, $images_path, $fileName, $oldPicture);
            }

            $arFields = Array(
                "XML_ID"          => $XML_ID,
                "TITLE"           => $NAME,
                "ADDRESS"         => $arStore['АдресТсц'],
                "PHONE"           => $arStore['Телефон'],
                "SCHEDULE"        => $arStore['ГрафикРаботы'],
                "GPS_N"           => $arStore['Широта'],
                "GPS_S"           => $arStore['Долгота'],
                "ISSUING_CENTER"  => $ISSUING_CENTER, //пункт выдачи (Y/N);
                "SHIPPING_CENTER" => $ISSUING_CENTER, //для отгрузки (Y/N).
                "IMAGE_ID"        => $IMAGE_ID,
                "ACTIVE"          => 'Y',
                "DESCRIPTION"     => '',
            );

            $arUserFields = array(
                "UF_STORE_CITY"        => $arStore['Город'],
                "UF_STORE_PHONE2"      => $arStore['Телефонавтомойка'],
                "UF_IS_SERVICE_CENTER" => $SERVICE_CENTER,
                "UF_EMAIL"             => ToLower($arStore['E-Mail']),
            );

            \CCatalogStore::Update($arStoreItem['ID'], $arFields);
            setUF("CAT_STORE", $arStoreItem['ID'], $arUserFields);
        }

        clearCache("/ccache_stores/");
    }

    /**
     * Получает список складов из 1С, и если какого-либо склада на сайте нет - создает его
     */
    public static function actualizeStoresList()
    {
        $arStoresUnsorted = \CURL::getReplay("Store", array('XML_ID' => 'All'));

        foreach ($arStoresUnsorted['Stores'] as $arStore)
        {
            $arFilter = array("XML_ID" => $arStore["XML_ID"]);

            $obList  = \CCatalogStore::GetList(array(), $arFilter);
            if (!$arFetch = $obList->Fetch())
            {
                $NAME           = $arStore["data"]['name'] == "Оптовый склад" ? "Склад" : $arStore["data"]['name'];
                $ISSUING_CENTER = $arStore["data"]['ТочкаВыдачи'] == "Да" ? "Y" : "N";
                $SERVICE_CENTER = $arStore["data"]['СервисныйЦентр'] == "Да" ? true : false;
                $IMAGE_ID       = null;

                $arFields = Array(
                    "XML_ID"          => $arStore["XML_ID"],
                    "TITLE"           => $NAME,
                    "ADDRESS"         => $arStore["data"]['АдресТсц'],
                    "PHONE"           => $arStore["data"]['Телефон'],
                    "SCHEDULE"        => $arStore["data"]['ГрафикРаботы'],
                    "GPS_N"           => $arStore["data"]['Широта'],
                    "GPS_S"           => $arStore["data"]['Долгота'],
                    "ISSUING_CENTER"  => $ISSUING_CENTER, //пункт выдачи (Y/N);
                    "SHIPPING_CENTER" => $ISSUING_CENTER, //для отгрузки (Y/N).
                    "IMAGE_ID"        => $IMAGE_ID,
                    "ACTIVE"          => 'Y',
                    "DESCRIPTION"     => '',
                );

                $arUserFields = array(
                    "UF_STORE_CITY"        => $arStore["data"]['Город'],
                    "UF_IS_SERVICE_CENTER" => $SERVICE_CENTER
                );

                $arStoreId = \CCatalogStore::Add($arFields);
                setUF("CAT_STORE", $arStoreId, $arUserFields);
            }
        }

        clearCache("/ccache_stores/");
    }

    /**
     * Обновляет изображения брендов с помощью запроса в 1С
     */
    public static function updateBrandsPictures()
    {
        $arGroups = \CURL::getReplay("Group", array('XML_ID' => 'All'), false, false, true);

        if (empty($arGroups))
        {
            return;
        }

        $arBrands = array();
        foreach ($arGroups['Group'] as $arGroup)
        {
            $arBrands[$arGroup['XML_ID']] = $arGroup;
        }

        $arXML_IDs = array_keys($arBrands);

        $obIBlockSection = new \CIBlockSection;

        $arFilter = Array(/* 'IBLOCK_ID' => TIRES_IB, */ 'XML_ID' => $arXML_IDs);
        $obList   = \CIBlockSection::GetList(array(), $arFilter, false, array("ID", "XML_ID", "PICTURE"));
        while ($arFetch  = $obList->Fetch())
        {

            $ID         = $arFetch['ID'];
            $XML_ID     = $arFetch['XML_ID'];
            $oldPicture = $arFetch['PICTURE'];

            $fileName     = $arBrands[$XML_ID]['data']['name'] . '.png';
            $base64String = $arBrands[$XML_ID]['data']['picture'];

            $fileId = base64ToFile($base64String, "tires_brands", $fileName, $oldPicture);
            $obIBlockSection->Update($ID, array("PICTURE" => \CFile::MakeFileArray($fileId)));
        }

        $dir = $_SERVER["DOCUMENT_ROOT"] . '/upload/tires_brands/';
        clearPath($dir);

        clearCache("/ccache_catalog/arBrandImages/");
    }

    /**
     * обновляет остатки товара с $XML_ID по запросу в 1С
     * @param type $XML_ID
     * @return boolean
     */
    public static function updateProductStoreAmount($XML_ID = false, $ID = false)
    {
        if (SITE_TEST) return false;
        if (empty($XML_ID) && empty($ID)) return false;
        elseif (!empty($XML_ID)) $arFilter = array("XML_ID" => $XML_ID);
        else $arFilter = array("ID" => $ID);

        //get product ID by its XML_ID
        $obList = \CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID", "XML_ID"));
        if ($arItem = $obList->Fetch())
        {
            $XML_ID     = $arItem['XML_ID'];
            $iProductId = $arItem['ID'];
        }

        //get amount data by CURL from 1C
        $arCurlData = \CURL::getReplay("Info", array('XML_ID' => $XML_ID));

        $arStoreRecords = array();
        $obList         = \CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $iProductId));
        while ($arFetch        = $obList->Fetch())
        {
            $arStoreRecords[$arFetch['ID']] = $arFetch;
        }

        $arStoreRecordsNew = array();
        $obList            = \CCatalogStore::GetList(array(), array("XML_ID" => array_keys($arCurlData)));
        while ($arFetch           = $obList->Fetch())
        {
            $arStoreRecordsNew[$arFetch['ID']] = $arFetch + array("NEW_AMOUNT" => 0 + $arCurlData[$arFetch['XML_ID']]);
        }

        $quantity = 0;
        foreach ($arStoreRecords as $iStoreRecoredId => $arStoreRecord)
        {
            $iStoreId = $arStoreRecord['STORE_ID'];

            //если по продукту пришла инфа - обновляем. Иначе - обнуляем
            if ($arStoreRecordsNew[$iStoreId]['NEW_AMOUNT'] > 0)
            {

                $iStoreAmount = $arStoreRecordsNew[$iStoreId]['NEW_AMOUNT'];
            }
            else
            {
                //$iStoreAmount = $arStoreRecord['AMOUNT'];
                $iStoreAmount = 0;
            }

            $quantity += $iStoreAmount;

            $arFields = Array(
                "PRODUCT_ID" => $iProductId,
                "STORE_ID"   => $iStoreId,
                "AMOUNT"     => $iStoreAmount,
            );

            \CCatalogStoreProduct::UpdateFromForm($arFields);
        }

        clearCache("/ccache_stores/");
    }

    /**
     * получает массив складов из БД
     * 
     * @param string $CITY город
     * @param bool $SERVICE_CENTER является ли сервисным центром (null|true|false)
     * @param string $ISSUING_CENTER является ли пунктом выдачи (null|"Y" | "N")
     * @return mixed
     */
    public static function getStores($CITY = null, $SERVICE_CENTER = null, $ISSUING_CENTER = null, $ids = null)
    {
        $arFilterCity = $CITY === null ? array() : array('UF_STORE_CITY' => $CITY);
        $arFilterServ = $SERVICE_CENTER === null ? array() : array('UF_IS_SERVICE_CENTER' => $SERVICE_CENTER);
        $arFilterIssu = $ISSUING_CENTER === null ? array() : array('ISSUING_CENTER' => $ISSUING_CENTER);
        $arFilterIds  = $ids === null ? array() : array('ID' => $ids);

        $arFilter = array("ACTIVE" => "Y") + $arFilterCity + $arFilterServ + $arFilterIssu + $arFilterIds;
        $arSort   = array("SORT" => "ASC");

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_stores/getStores/";
        $cacheID   = "getStores" . serialize($arFilter) . serialize($arSort);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arStores"]))
            {
                $arStores = $vars["arStores"];
                //$lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arStores     = array();
            $obStoreList  = \CCatalogStore::GetList($arSort, $arFilter);
            while ($arStoreFetch = $obStoreList->Fetch())
            {
                $UF_STORE_CITY        = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_STORE_CITY");
                $UF_STORE_PHONE2      = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_STORE_PHONE2");
                $UF_IS_SERVICE_CENTER = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_IS_SERVICE_CENTER");

                if (!empty($CITY) && $CITY != $UF_STORE_CITY) continue;

                $arStores[$arStoreFetch['TITLE']] = $arStoreFetch +
                        array('UF_STORE_CITY' => $UF_STORE_CITY) +
                        array('UF_STORE_PHONE2' => $UF_STORE_PHONE2) +
                        array('UF_IS_SERVICE_CENTER' => $UF_IS_SERVICE_CENTER);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arStores" => $arStores,
            ));
        }

        return $arStores;
    }

    public static function getTSC()
    {
        $arFilter = array("ACTIVE" => "Y", "UF_IS_SERVICE_CENTER" => "Y");
        $arSort   = array("SORT" => "ASC");

        $arStores     = array();
        $obStoreList  = \CCatalogStore::GetList($arSort, $arFilter);
        while ($arStoreFetch = $obStoreList->Fetch())
        {
            $UF_STORE_CITY        = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_STORE_CITY");
            $UF_STORE_PHONE2      = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_STORE_PHONE2");
            $UF_IS_SERVICE_CENTER = getUF("CAT_STORE", $arStoreFetch['ID'], "UF_IS_SERVICE_CENTER");

            $arStores[$arStoreFetch['XML_ID']] = $arStoreFetch +
                    array('UF_STORE_CITY' => $UF_STORE_CITY) +
                    array('UF_STORE_PHONE2' => $UF_STORE_PHONE2) +
                    array('UF_IS_SERVICE_CENTER' => $UF_IS_SERVICE_CENTER);
        }

        return $arStores;
    }

    /**
     * Получаем массив объектов складов для показа на карте
     */
    public static function getStoresJS()
    {
        $arStores = self::getStores(null, true);

        $arStoresJS = array();
        foreach ($arStores as $item)
        {
            if (!($item['UF_STORE_CITY'] && $item['GPS_N'] && $item['GPS_S']))
            {
                continue;
            }

            $arStoresJS[ToLower($item['UF_STORE_CITY'])][] = array(
                'coords'   => array($item['GPS_N'], $item['GPS_S']), // [широта, долгота]
                'title'    => $item['TITLE'],
                'address'  => $item['ADDRESS'],
                'schedule' => $item['SCHEDULE'],
                'phone'    => $item['PHONE'],
                'phone2'   => $item['UF_STORE_PHONE2'],
                'XML_ID'   => $item['XML_ID'],
                'button'   => false
            );
        }

        return $arStoresJS;
    }

    public static function getProductDeliveryMinDate($iProductId, $needAmount, $curCityKey, $print, $curCityName)
    {
        $allStores = self::getStores($curCityName, true, "Y");

        $minDays = null;

        foreach ($allStores as $store)
        {
            $STORE_ID = $store['ID'];

            $days = self::getProductDeliveryDate($iProductId, $needAmount, $curCityKey, false, $STORE_ID);

            if ($days === null) continue;

            if ($minDays === null || $days < $minDays)
            {
                $minDays = $days;
            }
        }

        if ($minDays === null)
        {
            return null;
        }

        global $USER;
        $NEWYEAR_DELIVERY      = /* $USER->IsAdmin() && */ \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY", false);
        $NEWYEAR_DELIVERY_TEXT = \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY_TEXT", "уточните у менеджера");

        if ($print != false)
        {
            if ($NEWYEAR_DELIVERY && $minDays > 0)
            {
                return $NEWYEAR_DELIVERY_TEXT;
            }
            else
            {
                return $minDays . " " . wordPlural($minDays, array("день", "дня", "дней"));
            }
        }

        return $minDays;
    }

    /**
     * вычисляет срок доставки для конкретного товара
     * 
     * @param int $iProductId ID товара
     * @param int $needAmount необходимое количество
     * @param string $curCityKey код города
     * @param bool $print 
     * @param mixed $STORE_ID false или ID склада
     * @return int
     */
    public static function getProductDeliveryDate($iProductId, $needAmount, $curCityKey, $print = false, $STORE_ID = false)
    {
        $iDays = null;

        //список складов. 
        //Нам надо определить ID скалда оптового и ID склада под заказ
        $STORES_OPT   = array(); //оптовый склад
        $STORES_ZAKAZ = array(); //склад под заказ
        $STORES_KEM   = array(); //Точк выдачи Кемерово (кроме под заказ и кроме опт)
        $STORES_NVK   = array(); //Точк выдачи Новокузнецка (кроме под заказ и кроме опт)

        $allStores = self::getStores();

        foreach ($allStores as $key => $arStore)
        {
            if ($key == "Оптовый склад")
            {
                $STORES_OPT[] = $arStore['ID'];
            }
            elseif ($key == "Склад")
            {
                $STORES_OPT[] = $arStore['ID'];
            }
            elseif ($key == "Под заказ")
            {
                $STORES_ZAKAZ[] = $arStore['ID'];
            }
            elseif ($arStore['UF_STORE_CITY'] == "Кемерово")
            {
                $STORES_KEM[] = $arStore['ID'];
            }
            elseif ($arStore['UF_STORE_CITY'] == "Новокузнецк")
            {
                $STORES_NVK[] = $arStore['ID'];
            }
        }


        //кол-во товара на складе оптовом
        $amountOpt = self::getProductAmountInStores($iProductId, $STORES_OPT);


        //кол-во товара в выбранной точке выдачи
        if ($iDays === null && !empty($STORE_ID))
        {
            $amountStore = self::getProductAmountInStores($iProductId, array($STORE_ID));

            //есть нужное кол-во в ТВ
            if ($amountStore >= $needAmount)
            {
                $iDays = 0;
            }
            //на оптовом есть хотя бы 1 шт
            elseif ($amountOpt >= 1 && $amountStore >= ($needAmount - $amountOpt))
            {
                $iDays = $curCityKey == "Kemerovo" ? 3 : 4;
            }
        }

        //нужное кол-во товара есть на складе оптовом
        if ($iDays === null && $amountOpt >= $needAmount)
        {
            $iDays = $curCityKey == "Kemerovo" ? 3 : 4;
        }

        //если город Кемерово или Новокузнецк, то проверим нгаличие во всех складах текущего города
        if ($iDays === null && ($curCityKey == "Kemerovo" || $curCityKey == "Novokuzneck"))
        {
            $STORES_CITY = $curCityKey == "Kemerovo" ? $STORES_KEM : $STORES_NVK;
            $amountCity  = self::getProductAmountInStores($iProductId, $STORES_CITY);

            if ($amountCity >= $needAmount)
            {
                $iDays = $curCityKey == "Kemerovo" ? 3 : 2;
            }
        }

        //проверим наличие в любых точках выдачи и складах любых городов (кроме "под заказ")
        if ($iDays === null)
        {
            $STORES_ANY = array_merge($STORES_OPT, $STORES_KEM, $STORES_NVK);
            $amountAny  = self::getProductAmountInStores($iProductId, $STORES_ANY);

            if ($amountAny >= $needAmount)
            {
                $iDays = 4;
            }
        }

        //проверим наличие под заказ
        if ($iDays === null)
        {
            $STORES_ALL = array_merge($STORES_ZAKAZ, $STORES_OPT, $STORES_KEM, $STORES_NVK);
            $amountAll  = self::getProductAmountInStores($iProductId, $STORES_ALL);

            if ($amountAll >= $needAmount)
            {
                $arFilter = array('ID' => $iProductId);
                $sSelect  = $curCityKey == "Kemerovo" ? 'PROPERTY_SROK_DOSTAVKI' : 'PROPERTY_SROK_DOSTAVKI_NOVOKUZNETSK';
                $obList   = \CIBlockElement::GetList(array(), $arFilter, false, false, array($sSelect));
                while ($arFetch  = $obList->Fetch())
                {
                    $srok  = (int) $arFetch[$sSelect . '_VALUE'];
                    $iDays = !empty($srok) ? $srok : 4;
                }
            }
        }




        //кол-во товара на складе оптовом
        /* $amountOpt = self::getProductAmountInStores($iProductId, $STORES_OPT);
          if ($amountOpt >= $needAmount)
          {
          $iDays = $curCityKey == "Kemerovo" ? 3 : 4;
          }

          //кол-во товара в выбранной точке выдачи
          if ($iDays === null && !empty($STORE_ID))
          {
          $amountStore = self::getProductAmountInStores($iProductId, array($STORE_ID));

          //на оптовом есть хотя бы 1 шт
          if ($amountOpt >= 1 && $amountStore >= ($needAmount - $amountOpt))
          {
          $iDays = $curCityKey == "Kemerovo" ? 3 : 4;
          }
          elseif ($amountStore >= $needAmount)
          {
          $iDays = 0;
          }
          }

          //если город Кемерово или Новокузнецк, то проверим нгаличие во всех складах текущего города
          if ($iDays === null && ($curCityKey == "Kemerovo" || $curCityKey == "Novokuzneck"))
          {
          $STORES_CITY = $curCityKey == "Kemerovo" ? $STORES_KEM : $STORES_NVK;
          $amountCity  = self::getProductAmountInStores($iProductId, $STORES_CITY);

          if ($amountCity >= $needAmount)
          {
          $iDays = $curCityKey == "Kemerovo" ? 3 : 2;
          }
          }

          //проверим наличие в любых точках выдачи и складах любых городов (кроме "под заказ")
          if ($iDays === null)
          {
          $STORES_ANY = array_merge($STORES_OPT, $STORES_KEM, $STORES_NVK);
          $amountAny  = self::getProductAmountInStores($iProductId, $STORES_ANY);

          if ($amountAny >= $needAmount)
          {
          $iDays = 4;
          }
          }

          //проверим наличие под заказ
          if ($iDays === null)
          {
          $STORES_ALL = array_merge($STORES_ZAKAZ, $STORES_OPT, $STORES_KEM, $STORES_NVK);
          $amountAll  = self::getProductAmountInStores($iProductId, $STORES_ALL);

          if ($amountAll >= $needAmount)
          {
          $arFilter = array('ID' => $iProductId);
          $sSelect  = $curCityKey == "Kemerovo" ? 'PROPERTY_SROK_DOSTAVKI' : 'PROPERTY_SROK_DOSTAVKI_NOVOKUZNETSK';
          $obList   = \CIBlockElement::GetList(array(), $arFilter, false, false, array($sSelect));
          while ($arFetch  = $obList->Fetch())
          {
          $srok  = (int) $arFetch[$sSelect . '_VALUE'];
          $iDays = !empty($srok) ? $srok : 4;
          }
          }
          }
         */
        if ($iDays === null)
        {
            return null;
            //$iDays = 10;
        }

        global $USER;
        $NEWYEAR_DELIVERY      = /* $USER->IsAdmin() && */ \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY", false);
        $NEWYEAR_DELIVERY_TEXT = \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY_TEXT", "уточните у менеджера");

        if ($print != false)
        {
            if ($NEWYEAR_DELIVERY && $iDays > 0)
            {
                return $NEWYEAR_DELIVERY_TEXT;
            }
            else
            {
                return $iDays . " " . wordPlural($iDays, array("день", "дня", "дней"));
            }
        }

        return $iDays;
    }

    /**
     * получает общее количество товара в указанных складах
     * @param int $iProductId
     * @param array $arStoresIDs
     * @return int
     */
    public static function getProductAmountInStores($iProductId, $arStoresIDs = array())
    {
        $totalAmount = 0;
        $arFilter    = array('PRODUCT_ID' => $iProductId, 'STORE_ID' => $arStoresIDs);
        $obList      = \CCatalogStoreProduct::GetList(array(), $arFilter);
        while ($arFetch     = $obList->Fetch())
        {
            $totalAmount += $arFetch['AMOUNT'];
        }

        return SITE_TEST ? 99 : $totalAmount;
    }

    public static function getProductsAmountInStores($arProductsId, $arStoresIDs = array())
    {
        $arResult = array();

        $arFilter = array('PRODUCT_ID' => $arProductsId, 'STORE_ID' => $arStoresIDs);
        $obList   = \CCatalogStoreProduct::GetList(array(), $arFilter);
        while ($arFetch  = $obList->Fetch())
        {
            $arResult[$arFetch['PRODUCT_ID']] += $arFetch['AMOUNT'];
        }

        return $arResult;
    }

    /**
     * Возвращает массив - Информацию о товаре по указанным складам
     * @param $iProductId
     * @param array $arStoresIDs
     * @return array
     */
    public static function getProductAmountByStores($iProductId, $arStoresIDs = array())
    {
        $arResult = array();
        $arFilter = array('PRODUCT_ID' => $iProductId, 'STORE_ID' => $arStoresIDs);
        $obList   = \CCatalogStoreProduct::GetList(array(), $arFilter);
        while ($arFetch  = $obList->Fetch())
        {
            $arResult[$arFetch['STORE_NAME']] = $arFetch;
        }

        return $arResult;
    }

    /**
     * Из-за ебучей структуры разделов
     * Возвращает "правильный" путь до товара. чтобы хлебные крошки были не длинными
     * /katalog/legkovye/205_55_r16_nordman_5_xl_94t_ship/ - правильно
     * /katalog/nordman/205_55_r16_nordman_5_xl_94t_ship/ - не айс
     * @param $arFields
     * @return null|string|string[]
     */
    public static function getProductUrl($arFields)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("10day", 0);
        $cachePath = "/ccache_catalog/getProductUrl/";
        $cacheID   = "getProductUrl" .
                $arFields["DETAIL_PAGE_URL"] .
                $arFields["LIST_PAGE_URL"] .
                $arFields["CODE"] .
                $arFields["IBLOCK_ID"] .
                $arFields["IBLOCK_SECTION_ID"]
        ;

        $sProductUrl = '';
        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["sProductUrl"]))
            {
                $sProductUrl = $vars["sProductUrl"];
                $lifeTime    = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arPaths = array(PATH_CATALOG, PATH_OILS, PATH_AKB, PATH_DISCS, PATH_MISC);

            $DETAIL_PAGE_URL = $arFields['DETAIL_PAGE_URL'];
            $LIST_PAGE_URL   = $arFields['LIST_PAGE_URL'];

            $obNavChain = \CIBlockSection::GetNavChain($arFields['IBLOCK_ID'], $arFields['IBLOCK_SECTION_ID']);
            while ($arFetch    = $obNavChain->Fetch())
            {
                if ($arFetch['DEPTH_LEVEL'] != 2) continue;

                $list_page = false;

                foreach ($arPaths as $path)
                {
                    if ($LIST_PAGE_URL == $path)
                    {
                        $list_page = $path;
                        break;
                    }

                    if (!$list_page)
                    {
                        if (strstr($DETAIL_PAGE_URL, $path))
                        {
                            $list_page = $path . $LIST_PAGE_URL;
                            break;
                        }
                    }
                }

                $sProductUrl = NormalizeLink($list_page . $arFetch['CODE'] . "/" . $arFields['CODE'] . "/");
                break;
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "sProductUrl" => $sProductUrl,
            ));
        }

        return $sProductUrl;
    }

    public static function getOilFilterParams($IBLOCK_ID, $SECTION_CODE)
    {
        //пытаемся получить данные из кеша
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_filter/getOilFilterParams/";
        $cacheID   = "getOilFilterParams" . $SECTION_CODE . OILS_IB;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arBlocks"]))
            {
                $arBlocks = $vars["arBlocks"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arSections = array(
                MASLA  => "Моторные масла",
                TRANSM => "Трансмиссионные масла",
                FLUIDS => "Технические жидкости",
            );

            $arBlocks = array(
                array(
                    "property" => 'SECTION_CODE',
                    "title"    => $arSections[$SECTION_CODE],
                    "items"    => $arSections,
                ),
            );

            switch ($SECTION_CODE)
            {
                case MASLA:
                    $arBlocks[] = array(
                        "property" => SM_NAZNACHENIEDV,
                        "title"    => "Назначение",
                    );
                    $arBlocks[] = array(
                        "property" => SM_VYAZKOST,
                        "title"    => "Вязкость",
                    );
                    $arBlocks[] = array(
                        "property" => SM_PROIZVODITEL,
                        "title"    => "Бренд",
                    );

                    break;

                case TRANSM:
                    $arBlocks[] = array(
                        "property" => SM_NAZNACHENIE,
                        "title"    => "Назначение", //акпп/вариатор/мкпп
                    );
                    $arBlocks[] = array(
                        "property" => SM_VYAZKOST,
                        "title"    => "Вязкость",
                    );
                    $arBlocks[] = array(
                        "property" => SM_PROIZVODITEL,
                        "title"    => "Бренд",
                    );
                    break;

                case FLUIDS:
                    $arBlocks[] = array(
                        "property" => SM_VIDMASLA,
                        "title"    => "Вид",
                    );
                    $arBlocks[] = array(
                        "property" => SM_PROIZVODITEL,
                        "title"    => "Бренд",
                    );
                    break;

                default:
                    break;
            }

            $arFilter = Array(
                "IBLOCK_ID"           => OILS_IB,
                "ACTIVE"              => "Y",
                "SECTION_CODE"        => $SECTION_CODE,
                'INCLUDE_SUBSECTIONS' => 'Y',
            );

            foreach ($arBlocks as $key => &$arBlock)
            {
                if ($arBlock["property"] == "SECTION_CODE" /* || $arBlock["property"] == SM_NAZNACHENIEDV */) continue;

                $arGroup = array();

                $obList = \CIBlockElement::GetList(Array(), $arFilter, array("PROPERTY_" . $arBlock['property']), false, false);
                while ($arItem = $obList->Fetch())
                {
                    $sValue = $arItem["PROPERTY_" . $arBlock['property'] . "_VALUE"];

                    if (!in_array($sValue, $arGroup)) $arGroup[$sValue] = $sValue;
                    else continue;
                }

                $arBlock['items'] = array('' => $arBlock['title']) + $arGroup;

                unset($arGroup);
                unset($obList);
                unset($arItem);
                unset($arBlock);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arBlocks" => $arBlocks,
            ));
        }

        return $arBlocks;
    }

    public static function getDiscsFilterParams($IBLOCK_ID, $SECTION_CODE)
    {
        //пытаемся получить данные из кеша
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_filter/getDiscsFilterParams/";
        $cacheID   = "getOilFilterParams" . $SECTION_CODE . DISCS_IB;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arBlocks"]))
            {
                $arBlocks = $vars["arBlocks"];
                //$lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arSections = array(
                DISCS_LIGHT => "Легкосплавные",
                DISCS_STEEL => "Стальные",
            );

            //$arBlocks = array();

            /* foreach ($arSections as $sectionKey => $sectionTitle)
              {
              $arBlocks[] = array(
              "property" => 'SECTION_CODE',
              "title"    => $sectionTitle,
              "value"    => $sectionKey,
              "items"    => $arSections,
              );
              } */

            $arBlocks = array(
                array(
                    "property" => 'SECTION_CODE',
                    "title"    => $arSections[$SECTION_CODE],
                    "value"    => $SECTION_CODE,
                    "items"    => $arSections,
                ),
            );

            $arBlocks[] = array(
                "property" => DIAMETRDISKA,
                "title"    => "Диаметр",
            );
            $arBlocks[] = array(
                "property" => KREPLENIEDISKA,
                "title"    => "Крепление",
            );

            $arFilter = Array(
                "IBLOCK_ID"           => DISCS_IB,
                "ACTIVE"              => "Y",
                "SECTION_CODE"        => $SECTION_CODE,
                'INCLUDE_SUBSECTIONS' => 'Y',
            );

            foreach ($arBlocks as $key => &$arBlock)
            {
                if ($arBlock["property"] == "SECTION_CODE") continue;

                $arGroup = array();

                $obList = \CIBlockElement::GetList(Array(), $arFilter, array("PROPERTY_" . $arBlock['property']), false, false);
                while ($arItem = $obList->Fetch())
                {
                    $sValue = $arItem["PROPERTY_" . $arBlock['property'] . "_VALUE"];

                    if (!in_array($sValue, $arGroup)) $arGroup[$sValue] = $sValue;
                    else continue;
                }

                $arBlock['items'] = array('' => $arBlock['title']) + $arGroup;

                unset($arGroup);
                unset($obList);
                unset($arItem);
                unset($arBlock);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arBlocks" => $arBlocks,
            ));
        }

        return $arBlocks;
    }

    /**
     * 
     * @param bool $clearAll флаг очистки индекса сортировки вообще у всех товаров перед переиндексацией
     */
    public static function updateSortProp($IBLOCK_ID = DISCS_IB, $clearAll = false)
    {
        //сброии сортировку для товаров без картинок
        $arSelect = Array("ID");
        $arFilter = Array(
            "IBLOCK_ID"             => $IBLOCK_ID,
            ">PROPERTY_" . SORTPROP => 0,
        );

        if (!$clearAll)
        {
            $arFilter['DETAIL_PICTURE'] = false;
        }

        $obList  = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($arFetch = $obList->Fetch())
        {
            \CIBlockElement::SetPropertyValuesEx($arFetch["ID"], false, array(SORTPROP => 0));
        }

        //переиндексация 
        if ($IBLOCK_ID == DISCS_IB)
        {
            \CCatalogExt::reindexDisks();
        }
    }

    public static function reindexDisks()
    {
        $IBLOCK_ID = DISCS_IB;

        $arSelect = Array("ID", "CODE");
        $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID);

        $arSections = array();
        $obList     = \CIBlockSection::GetList(Array(), $arFilter, false, $arSelect);
        while ($arFetch    = $obList->Fetch())
        {
            if (!in_array($arFetch["CODE"], array(DISCS_LIGHT, DISCS_STEEL))) continue;
            $arSections[$arFetch["CODE"]] = $arFetch["ID"];
        }

        foreach ($arSections as $SECTION_CODE => $SECTION_ID)
        {
            if ($SECTION_CODE == DISCS_LIGHT)
            {
                $presets = array(
                    '5000' => array(
                        DIAMETRDISKA   => 'R15',
                        KREPLENIEDISKA => '4*100',
                    ),
                    '4000' => array(
                        DIAMETRDISKA   => 'R14',
                        KREPLENIEDISKA => '4*98',
                    ),
                    '3000' => array(
                        DIAMETRDISKA   => 'R14',
                        KREPLENIEDISKA => '4*100',
                    ),
                    '2000' => array(
                        DIAMETRDISKA   => 'R16',
                        KREPLENIEDISKA => '5*114',
                    ),
                    '1000' => array(
                        DIAMETRDISKA   => 'R17',
                        KREPLENIEDISKA => '5*114',
                    ),
                );

                $arSelect = Array("ID", "PROPERTY_" . DIAMETRDISKA, "PROPERTY_" . KREPLENIEDISKA);
                $arFilter = Array(
                    "ACTIVE"            => "Y",
                    ">CATALOG_QUANTITY" => "0",
                    "IBLOCK_ID"         => $IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $SECTION_ID,
                    "!DETAIL_PICTURE"   => false, //наличие картинки обязательно
                );

                $arFilter['PRESETS'] = array(
                    "LOGIC" => "OR"
                );

                foreach ($presets as $sort => $preset)
                {
                    $arFilter['PRESETS'][$sort] = array();

                    foreach ($preset as $prop_code => $prop_value)
                    {
                        $arFilter['PRESETS'][$sort]["PROPERTY_" . $prop_code . "_VALUE"] = $prop_value;
                    }
                }

                $arElements = array();
                $obList     = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while ($arFetch    = $obList->Fetch())
                {
                    $prest = array(
                        DIAMETRDISKA   => $arFetch["PROPERTY_" . DIAMETRDISKA . "_VALUE"],
                        KREPLENIEDISKA => $arFetch["PROPERTY_" . KREPLENIEDISKA . "_VALUE"],
                    );

                    $propSortValue = (int) array_search($prest, $presets);

                    if (!empty($propSortValue))
                    {
                        \CIBlockElement::SetPropertyValuesEx($arFetch["ID"], false, array(SORTPROP => $propSortValue));
                    }
                }
            }
            elseif ($SECTION_CODE == DISCS_STEEL)
            {
                $arSelect = Array("ID");
                $arFilter = Array(
                    "ACTIVE"            => "Y",
                    ">CATALOG_QUANTITY" => "0",
                    "IBLOCK_ID"         => $IBLOCK_ID,
                    "IBLOCK_SECTION_ID" => $SECTION_ID,
                    "!DETAIL_PICTURE"   => false, //наличие картинки обязательно
                );

                $arElements = array();
                $obList     = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                while ($arFetch    = $obList->Fetch())
                {
                    \CIBlockElement::SetPropertyValuesEx($arFetch["ID"], false, array(SORTPROP => 1000));
                }
            }
        }
    }

    public static function setDiscSortPropValue(&$arFields)
    {
        $defaultSort = 1000;

        $IBLOCK_SECTION_ID = $arFields["IBLOCK_SECTION_ID"];
        $IBLOCK_ID         = $arFields["IBLOCK_ID"];

        $obSECTION = \CIBlockSection::GetByID($IBLOCK_SECTION_ID);
        if ($SECTION   = $obSECTION->Fetch())
        {
            $SECTION_CODE = $SECTION["CODE"];
        }
        else
        {
            return;
        }

        //при выгрузке из 1С индекс не "0", "n0"
        $index = $_REQUEST['mode'] == 'import' ? "n0" : "0";

        if ($_REQUEST['mode'] != 'import' || $_REQUEST['type'] != 'catalog' || $IBLOCK_ID != DISCS_IB)
        {
            return;
        }

        if ($arFields['ACTIVE'] != "Y")
        {
            return;
        }

        if (empty($arFields['DETAIL_PICTURE']))
        {
            return;
        }
        else
        {
            $PICTURE = $arFields['DETAIL_PICTURE'];
            if (!empty($PICTURE['old_file']))
            {
                
            }
            else
            {
                if (!empty($PICTURE['error']))
                {
                    return;
                }
            }
        }

        if ($SECTION_CODE == DISCS_LIGHT)
        {
            //это те свойства, значения которых будут влиять на сортировку
            $arNeededProps = array(
                DIAMETRDISKA,
                KREPLENIEDISKA,
            );

            $propSortId = null;

            $presets = array(
                '5000' => array(
                    DIAMETRDISKA   => 'R15',
                    KREPLENIEDISKA => '4*100',
                ),
                '4000' => array(
                    DIAMETRDISKA   => 'R14',
                    KREPLENIEDISKA => '4*98',
                ),
                '3000' => array(
                    DIAMETRDISKA   => 'R14',
                    KREPLENIEDISKA => '4*100',
                ),
                '2000' => array(
                    DIAMETRDISKA   => 'R16',
                    KREPLENIEDISKA => '5*114',
                ),
                '1000' => array(
                    DIAMETRDISKA   => 'R17',
                    KREPLENIEDISKA => '5*114',
                ),
            );

            $arProps    = array();
            $arPropsIds = array();
            $obList     = \CIBlockProperty::GetList(array(), array("IBLOCK_ID" => DISCS_IB));
            while ($arFetch    = $obList->Fetch())
            {
                if (in_array($arFetch["CODE"], $arNeededProps))
                {
                    $arProps[$arFetch["ID"]] = $arFetch;
                    $arPropsIds[]            = $arFetch["ID"];

                    $obPropList  = \CIBlockProperty::GetPropertyEnum($arFetch["ID"]);
                    while ($arPropFetch = $obPropList->Fetch())
                    {
                        $arProps[$arFetch["ID"]]["VARIANTS"][$arPropFetch["ID"]] = $arPropFetch["VALUE"];
                    }
                }

                if ($arFetch["CODE"] == SORTPROP)
                {
                    $propSortId = $arFetch["ID"];
                }
            }

            if (empty($propSortId) || !empty($arFields["PROPERTY_VALUES"][$propSortId]['n0']['VALUE']))
            {
                return;
            }

            $PROPERTY_VALUES = $arFields["PROPERTY_VALUES"];

            $preset = array();

            foreach ($PROPERTY_VALUES as $PROPERTY_ID => $PROPERTY_VALUE)
            {
                if (!in_array($PROPERTY_ID, $arPropsIds)) continue;

                $PROP = $arProps[$PROPERTY_ID];

                if (!empty($PROPERTY_VALUE[$index]["VALUE"]))
                {
                    //ID варианта значения
                    $VARIANT_ID = $PROPERTY_VALUE[$index]["VALUE"];

                    //собственно значение
                    $VARIANT_VALUE = $PROP["VARIANTS"][$VARIANT_ID];

                    $preset[$PROP['CODE']] = $VARIANT_VALUE;
                }
            }

            $propSortValue = (int) array_search($preset, $presets);
        }
        else
        {
            $propSortValue = $defaultSort;
        }

        if (!empty($propSortValue))
        {
            $arFields["PROPERTY_VALUES"][$propSortId]['n0']['VALUE'] = $propSortValue;
        }
        else
        {
            $arFields["PROPERTY_VALUES"][$propSortId]['n0']['VALUE'] = 0;
        }
    }

    public static function getDiskName($w, $d, $hc, $hd, $dia, $et)
    {
        return $w . "x" . $d . "/" . $hc . "x" . $hd . " " . "D" . $dia . " " . "ET" . $et;
    }

    public static function getName($arFields, $ucFirst = true)
    {
        $res = "";

        $IBLOCK_ID    = $arFields["IBLOCK_ID"];
        $SECTION_CODE = $arFields["SECTION"]["CODE"];
        $PROPS        = $arFields["PROPERTIES"];

        if ($IBLOCK_ID == TIRES_IB)
        {
            $res = " ";

            $SEZON   = $PROPS[SEZON]["VALUE"];
            $SHIPY   = $PROPS["SHIPY"]["VALUE"];
            $KAMERA  = $PROPS["KAMERA"]["VALUE"];
            $MARKA   = $PROPS["MARKA"]["VALUE"];
            $MODEL   = $PROPS["MODEL"]["VALUE"];
            $SHIRINA = $PROPS[SHIRINA]["VALUE"];
            $VYSOTA  = $PROPS[VYSOTA]["VALUE"];
            $DIAMETR = $PROPS[DIAMETR]["VALUE"];

            if ($SEZON == SUMMER) $res .= " летние";
            elseif ($SEZON == WINTER) $res .= "зимние";

            if ($SHIPY == "Шипы") $res .= " шипованные";
            elseif ($SEZON == "Нешип") $res .= " нешипованные";

            if ($KAMERA == "Камерная") $res .= " камерные";

            if ($SECTION_CODE == LEGKOVYE) $res .= " легковые";
            elseif ($SECTION_CODE == GRUZOVYE) $res .= " грузовые";
            elseif ($SECTION_CODE == MOTO) $res .= " мото";

            $res .= " шины";

            if (!empty($MARKA)) $res .= " " . $MARKA;
            if (!empty($MODEL)) $res .= " " . $MODEL;
            if (!empty($DIAMETR)) $res .= " R" . str_replace("R", "", $DIAMETR);
            if (!empty($SHIRINA)) $res .= " " . $SHIRINA;
            if (!empty($VYSOTA)) $res .= "/" . $VYSOTA;
        }
        elseif ($IBLOCK_ID == OILS_IB)
        {
            $res = " ";

            $SM_MARKA         = $PROPS[SM_MARKA]["VALUE"];
            $SM_VIDMASLA      = $PROPS[SM_VIDMASLA]["VALUE"];
            $SM_NAZNACHENIE   = $PROPS[SM_NAZNACHENIE]["VALUE"];
            $SM_PROIZVODITEL  = $PROPS[SM_PROIZVODITEL]["VALUE"];
            $SM_NAZNACHENIEDV = $PROPS[SM_NAZNACHENIEDV]["VALUE"];
            $SM_TIP           = $PROPS[SM_TIP]["VALUE"];
            $OZH_TIP          = $PROPS[OZH_TIP]["VALUE"];
            $OZH_TSVET        = $PROPS[OZH_TSVET]["VALUE"];
            $SM_API           = $PROPS[SM_API]["VALUE"];
            $SM_ACEA          = $PROPS[SM_ACEA]["VALUE"];
            $OBYEM            = $PROPS[OBYEM]["VALUE"];
            $SM_VYAZKOST      = $PROPS[SM_VYAZKOST]["VALUE"];

            if ($SM_TIP == "Синтетическое") $res .= " синтетическое";
            elseif ($SM_TIP == "Полусинтетическое") $res .= " полусинтетическое";
            elseif ($SM_TIP == "Минеральное") $res .= " минеральное";

            if ($OZH_TSVET == "Красный") $res .= " красный";
            elseif ($OZH_TSVET == "Зеленый") $res .= " зеленый";
            elseif ($OZH_TSVET == "Синий") $res .= " синий";

            if ($OZH_TIP == "Антифриз") $res .= " антифриз";
            elseif ($OZH_TIP == "Тосол") $res .= " тосол";

            if ($SM_VIDMASLA == "Смазка") $res .= " смазку";
            elseif ($SM_VIDMASLA == "Гидравлическое масло") $res .= " масло";
            elseif ($SM_VIDMASLA == "Масло моторное") $res .= " масло";
            elseif ($SM_VIDMASLA == "д/зад.моста") $res .= " масло для заднего моста";
            elseif ($SM_VIDMASLA == "Компрессорное масло") $res .= " компрессорное масло";
            elseif ($SM_VIDMASLA == "Редукторное масло") $res .= " редукторное масло";
            elseif ($SM_VIDMASLA == "Дистиллированная вода") $res .= " дистиллированную воду";
            elseif ($SM_VIDMASLA == "Электролит") $res .= " электролит";
            elseif ($SM_VIDMASLA == "Индустриальное масло") $res .= " индустриальное масло";
            elseif ($SM_VIDMASLA == "Трансформаторное масло") $res .= " трансформаторное масло";
            elseif ($SM_VIDMASLA == "Тосол ОЖ40" && $OZH_TIP != "Тосол") $res .= " тосол";
            elseif ($SM_VIDMASLA == "Тосол" && $OZH_TIP != "Тосол") $res .= " тосол";
            elseif ($SM_VIDMASLA == "Тосол Морозоff" && $OZH_TIP != "Тосол") $res .= " тосол";
            elseif ($SM_VIDMASLA == "Тосол AGA" && $OZH_TIP != "Тосол") $res .= " тосол";
            elseif ($SM_VIDMASLA == "Антифриз" && $OZH_TIP != "Антифриз") $res .= " антифриз";
            elseif ($SM_VIDMASLA == "Антифриз AGA" && $OZH_TIP != "Антифриз") $res .= " антифриз";
            elseif ($SM_VIDMASLA == "Антифриз Shell" && $OZH_TIP != "Антифриз") $res .= " антифриз";
            elseif ($SM_VIDMASLA == "Антифриз AWM" && $OZH_TIP != "Антифриз") $res .= " антифриз";
            elseif ($SM_VIDMASLA == "Охлаждающая жидкость" && $OZH_TIP != "Антифриз" && $OZH_TIP != "Тосол")
                    $res .= " охлаждающую жидкость";
            elseif ($SM_VIDMASLA == "Стеклоомывающая жидкость") $res .= " стеклоомывающую жидкость";
            elseif ($SM_VIDMASLA == "Трансмиссионное масло") $res .= " трансмиссионное масло";
            elseif ($SM_VIDMASLA == "Промывочное масло") $res .= " промывочное масло";
            elseif ($SM_VIDMASLA == "Масло для мототехники") $res .= " масло для мототехники";
            elseif ($SM_VIDMASLA == "Масло для лодочных двигателей") $res .= " масло для лодочных двигателей";
            elseif ($SM_VIDMASLA == "Масла для ГУР") $res .= " масло для ГУР";
            elseif ($SM_VIDMASLA == "Незамерзающая жидкость") $res .= " незамерзающую жидкость";
            elseif ($SM_VIDMASLA == "Тормозная жидкость") $res .= " тормозную жидкость";

            if ($SM_NAZNACHENIE == "Вариатор") $res .= " для вариаторов";
            elseif ($SM_NAZNACHENIE == "АКПП") $res .= " для АКПП";
            elseif ($SM_NAZNACHENIE == "МКПП") $res .= " для МКПП";
            elseif ($SM_NAZNACHENIE == "ГУР" && $SM_VIDMASLA != "Масла для ГУР") $res .= " для ГУР";

            if ($SM_NAZNACHENIEDV == "Универсальное") $res .= " универсальное";
            elseif ($SM_NAZNACHENIEDV == "Для дизельных двигателей") $res .= " для дизельных двигателей";
            elseif ($SM_NAZNACHENIEDV == "Для бензиновых двигателей") $res .= " для бензиновых двигателей";

            if (!empty($SM_PROIZVODITEL)) $res .= " " . $SM_PROIZVODITEL;
            if (!empty($SM_MARKA)) $res .= " " . $SM_MARKA;
            if (!empty($SM_VYAZKOST)) $res .= " " . $SM_VYAZKOST;
            if (!empty($SM_API)) $res .= " " . $SM_API;
            if (!empty($SM_ACEA)) $res .= " " . $SM_ACEA;
            if (!empty($OBYEM)) $res .= " " . $OBYEM . " л.";
        }
        elseif ($IBLOCK_ID == AKB_IB)
        {
            $res = " ";

            $AKB_EMKOST       = $PROPS[AKB_EMKOST]["VALUE"];
            $AKB_POLYARNOST   = $PROPS[AKB_POLYARNOST]["VALUE"];
            $AKB_PROIZVODITEL = $PROPS[AKB_PROIZVODITEL]["VALUE"];
            $AKB_MODEL        = $PROPS[AKB_MODEL]["VALUE"];

            if ($SECTION_CODE == AKB_AVTO) $res .= " автомобильный";
            elseif ($SECTION_CODE == AKB_MOTO) $res .= " мото";

            $res .= " аккумулятор";

            if (!empty($AKB_PROIZVODITEL)) $res .= " " . $AKB_PROIZVODITEL;
            if (!empty($AKB_MODEL)) $res .= " " . $AKB_MODEL;
            if (!empty($AKB_EMKOST)) $res .= " " . $AKB_EMKOST . " А·ч";

            if ($AKB_POLYARNOST == "обратная") $res .= " обратная полярность";
            elseif ($AKB_POLYARNOST == "прямая") $res .= " прямая полярность";
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $res = " ";

            $DISKI_MARKA = $PROPS[DISKI_MARKA]["VALUE"];
            $DISKI_MODEL = $PROPS[DISKI_MODEL]["VALUE"];
            $DIAMETR     = $PROPS[DIAMETRDISKA]['VALUE'];
            $SHIRINA     = $PROPS[SHIRINA_DISKA]['VALUE'];
            $VYLET       = $PROPS[VYLET]['VALUE'];
            $KREPLENIE   = $PROPS[KREPLENIEDISKA]['VALUE'];
            $DIA         = $PROPS[DIA]['VALUE'];
            $COLOR       = $PROPS['TSVET']['VALUE'];

            $RADIUS = str_replace("R", "", $DIAMETR);

            $matches = array();
            preg_match_all('/(?P<hc>[0-9]{1,2})\*(?P<hd>[0-9]{1,3})(\/(?P<hd2>[0-9]+))?/', $KREPLENIE, $matches);

            $HOLES_DIAMETR = $matches["hd"][0];
            $HOLES_COUNT   = $matches["hc"][0];

            $DISK_NAME = \CCatalogExt::getDiskName($SHIRINA, $RADIUS, $HOLES_COUNT, $HOLES_DIAMETR, $DIA, $VYLET);

            if ($SECTION_CODE == DISCS_LIGHT) $res .= " легкосплавные";
            elseif ($SECTION_CODE == DISCS_STEEL) $res .= " стальные";

            $res .= " диски";

            if (!empty($DISKI_MARKA)) $res .= " " . $DISKI_MARKA;
            if (!empty($DISKI_MODEL)) $res .= " " . $DISKI_MODEL;
            if (!empty($DISK_NAME)) $res .= " " . $DISK_NAME;
            if (!empty($COLOR)) $res .= " " . $COLOR;
        }

        if (/* isAdmin() && */!empty($res))
        {
            $res = removeDuplicates($res);
            return $ucFirst ? mb_ucfirst(ltrim($res)) : ltrim($res);
        }

        return $arFields["NAME"];
    }

    public static function setName($arFields)
    {
        //if (isAdmin())
        {
            $ID             = $arFields["ID"];
            $OLD_META_TITLE = $arFields["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"];
            $OLD_PAGE_TITLE = $arFields["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"];

            $title = \CCatalogExt::getName($arFields, false);

            $ELEMENT_PAGE_TITLE = mb_ucfirst($title);
            $ELEMENT_META_TITLE = "Купить " . $title . " | Сервис-центры «Континент шин»";

            if ($OLD_META_TITLE != $ELEMENT_META_TITLE || $OLD_PAGE_TITLE != $ELEMENT_PAGE_TITLE)
            {
                $arItemElement = array(
                    "IPROPERTY_TEMPLATES" => array(
                        'ELEMENT_PAGE_TITLE' => $ELEMENT_PAGE_TITLE,
                        'ELEMENT_META_TITLE' => $ELEMENT_META_TITLE,
                    //'ELEMENT_META_DESCRIPTION' => $ELEMENT_META_TITLE,
                ));

                $obElement = new \CIBlockElement;
                $obElement->Update($ID, $arItemElement);
            }
        }
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get()
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init();
        }
        return self::$_instance;
    }

    private static function init()
    {
        
    }

}
