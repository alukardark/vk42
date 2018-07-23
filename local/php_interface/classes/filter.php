<?php

use Bitrix\Highloadblock as HL;

class CFilterExt
{

    public static function getFilterSession($IBLOCK_ID, $SECTION_CODE)
    {
        return isset($_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE]) ?
                $_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE] :
                array();
    }

    public static function setFilterSession($IBLOCK_ID, $SECTION_CODE, $data)
    {
        $_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE] = $data;
    }

    public static function setFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $key, $value)
    {
        $_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE][$key] = $value;
    }

    public static function getFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $key)
    {
        return $_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE][$key];
    }

    public static function issetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $key)
    {
        return isset($_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE][$key]);
    }

    public static function unsetFilterSessionKey($IBLOCK_ID, $SECTION_CODE, $key = false, $subkey = false)
    {
        if (empty($key)) unset($_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE]);
        elseif (empty($subkey)) unset($_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE][$key]);
        else unset($_SESSION['FILTER'][$IBLOCK_ID][$SECTION_CODE][$key][$subkey]);
    }

    public static function getActions()
    {
        $arFilter = array("IBLOCK_ID" => ACTIONS_IB, "ACTIVE" => "Y", "PROPERTY_SHOW_IN_SMART_VALUE" => "Да");
        $arSelect = array("ID", "PROPERTY_REF_ACTION");

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getActions/";
        $cacheID   = "getActions" . serialize($arFilter + $arSelect);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arActions"]))
            {
                $arActions = $vars["arActions"];
                $lifeTime  = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arActions = array();

            $hlblock = HL\HighloadBlockTable::getById(ACTIONS_HB)->fetch();
            $entity  = HL\HighloadBlockTable::compileEntity($hlblock)->getDataClass();

            $obList = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            while ($arItem = $obList->Fetch())
            {
                $rsData = $entity::getList(array(
                            'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
                            'limit'  => '1',
                            'filter' => array('UF_XML_ID' => $arItem["PROPERTY_REF_ACTION_VALUE"]),
                ));

                if ($arFields = $rsData->Fetch())
                {
                    //$iId    = $arFields["ID"];
                    $sValue = $arFields["UF_NAME"];
                    $sXMLId = $arFields["UF_XML_ID"];

                    $arActions[$sXMLId] = array('value' => $sXMLId, 'title' => $sValue);
                }
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arActions" => $arActions,
            ));
        }


        return $arActions;
    }

    public static function getListVariants($property, $IBLOCK_ID, $SECTION_CODE)
    {
        $arFilter                      = Array(
            "IBLOCK_ID"           => $IBLOCK_ID,
            "ACTIVE"              => "Y",
            //"SECTION_ACTIVE"        => "Y",
            //"SECTION_GLOBAL_ACTIVE" => "Y",
            //"CATALOG_AVAILABLE"     => "Y",
            //">CATALOG_QUANTITY"   => "0",
            "SECTION_CODE"        => $SECTION_CODE,
            'INCLUDE_SUBSECTIONS' => 'Y',
        );
        if (HIDE_NULL) $arFilter[">CATALOG_QUANTITY"] = "0";

        $arSelect = array("PROPERTY_" . $property);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getListVariants/";
        $cacheID   = "getListVariants" . serialize($arFilter + $arSelect);

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
            $arResult = array();

            $obList = \CIBlockElement::GetList(Array(), $arFilter, $arSelect, false, false);
            while ($arItem = $obList->Fetch())
            {
                $sValue = $arItem["PROPERTY_" . $property . "_VALUE"];
                $iId    = $arItem["PROPERTY_" . $property . "_ENUM_ID"];

                if (!empty($sValue) && !array_key_exists($iId, $arResult))
                {
                    $arResult[$iId] = array('value' => $sValue, 'title' => $sValue);
                }
                else continue;
            }

            if ($property == DISKI_MARKA)
            {
                //printrau($arResult);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult" => $arResult,
            ));
        }

        return $arResult;
    }

    public static function getDiscksDia($DIA, $DIAMAX, $steps = 1, $onlyToUp = false)
    {
        if (empty($DIAMAX)) $DIAMAX = $DIA;

        $arDIA = array();
        for ($i = 0; $i <= $steps; $i++)
        {
            if ($onlyToUp)
            {
                $arDIA[] = $DIA + $i / 10;
                $arDIA[] = $DIAMAX + $i / 10;
            }
            else
            {
                $arDIA[] = $DIA + $i / 10;
                $arDIA[] = $DIA - $i / 10;
                $arDIA[] = $DIAMAX + $i / 10;
                $arDIA[] = $DIAMAX - $i / 10;
            }
        }

        return array_unique($arDIA);
    }

    public static function getDiscksPcd($COUNTHOLE, $PCD)
    {
        $arPCD = array(
            $COUNTHOLE . "*" . $PCD . "%",
            $COUNTHOLE . "*%/" . $PCD,
        );

        return array_unique($arPCD);
    }

    public static function getRange($property, $IBLOCK_ID, $SECTION_CODE, $step = false, $notNull = false)
    {
        $code     = "PROPERTY_" . $property;
        $code_val = "PROPERTY_" . $property . "_VALUE";

        $arFilter = Array(
            "IBLOCK_ID"           => $IBLOCK_ID,
            "ACTIVE"              => "Y",
            "SECTION_CODE"        => $SECTION_CODE,
            'INCLUDE_SUBSECTIONS' => 'Y',
        );

        if ($notNull)
        {
            $arFilter["!" . $code] = false;
        }

        $arSelect = array("IBLOCK_ID", "ID", "PROPERTY_" . $property . "_ENUM_ID", $code_val);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getRange/";
        $cacheID   = "getRange" . serialize($arFilter + $arSelect);

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arRange"]))
            {
                $arRange  = $vars["arRange"];
                $lifeTime = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $arRange = array();

            //определим тип свойства
            $obList = \CIBlockProperty::GetList(array(), Array("IBLOCK_ID" => $IBLOCK_ID, "CODE" => $property));
            if ($arItem = $obList->Fetch())
            {
                //printrau($arItem);
                $PROPERTY_TYPE = $arItem["PROPERTY_TYPE"];
                $LIST_TYPE     = $arItem["LIST_TYPE"];
            }

            if ($PROPERTY_TYPE != "L" && $LIST_TYPE != "L")
            {
                $arSortDesc  = array($code => "DESC");
                $arSortAsc   = array($code => "ASC");
                $arNavParams = array("nPageSize" => 1);

                //MAX
                $obList = \CIBlockElement::GetList($arSortDesc, $arFilter, false, $arNavParams, $arSelect);
                if ($arItem = $obList->Fetch())
                {
                    $arRange['MAX'] = $arRange['TO']  = ceil($arItem[$code_val . "_VALUE"]);
                }

                //MIN
                $obList = \CIBlockElement::GetList($arSortAsc, $arFilter, false, $arNavParams, $arSelect);
                if ($arItem = $obList->Fetch())
                {
                    $arRange['MIN']  = $arRange['FROM'] = floor($arItem[$code_val . "_VALUE"]);
                }
            }
            else
            {
                $arDataCeils  = array();
                $arDataFloors = array();
                $obList       = \CIBlockElement::GetList(array(), $arFilter, array($code));
                while ($arItem       = $obList->Fetch())
                {
                    $VALUE          = $arItem[$code_val];
                    //if (!is_numeric($VALUE)) continue;
                    $arDataCeils[]  = ceil($VALUE);
                    $arDataFloors[] = floor($VALUE);
                }
                $arRange['MAX']  = $arRange['TO']   = max($arDataFloors);
                $arRange['MIN']  = $arRange['FROM'] = min($arDataCeils);
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arRange" => $arRange,
            ));
        }

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        if (!empty($arSectionFilter[$property]))
        {
            $arRange['FROM'] = $arSectionFilter[$property]["FROM"];
            $arRange['TO']   = $arSectionFilter[$property]["TO"];
        }

        $arRange["STEP"] = $step;

        return $arRange;
    }

    public static function getPriceRange($IBLOCK_ID, $SECTION_CODE)
    {
        $arSortDesc  = array(CATALOG_PRICE => "DESC");
        $arSortAsc   = array(CATALOG_PRICE => "ASC");
        $arNavParams = array("nPageSize" => 1);

        $arFilter                      = Array(
            "IBLOCK_ID"           => $IBLOCK_ID,
            "ACTIVE"              => "Y",
            "SECTION_CODE"        => $SECTION_CODE,
            'INCLUDE_SUBSECTIONS' => 'Y',
                //">CATALOG_QUANTITY"   => "0"
        );
        if (HIDE_NULL) $arFilter[">CATALOG_QUANTITY"] = "0";

        $arSelect = array(CATALOG_PRICE);

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getPriceRange/";
        $cacheID   = "getPriceRange" . serialize($arFilter + $arSelect) . MAX_DISCOUNT;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arPriceRange"]))
            {
                $arPriceRange = $vars["arPriceRange"];
                $lifeTime     = 0;
            }
        }

        if ($lifeTime > 0)
        {
            //MAX и MIN цена
            $arPriceRange = array();

            $obList = \CIBlockElement::GetList($arSortDesc, $arFilter, false, $arNavParams, $arSelect);
            if ($arItem = $obList->Fetch())
            {
                $arPriceRange['MAX'] = $arPriceRange['TO']  = ceil($arItem[CATALOG_PRICE] * (1 + MAX_DISCOUNT));
            }

            //MIN цена
            $obList = \CIBlockElement::GetList($arSortAsc, $arFilter, false, $arNavParams, $arSelect);
            if ($arItem = $obList->Fetch())
            {
                $arPriceRange['MIN']  = $arPriceRange['FROM'] = floor($arItem[CATALOG_PRICE] * (1 - MAX_DISCOUNT));
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arPriceRange" => $arPriceRange,
            ));
        }

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        if (!empty($arSectionFilter['PRICE']))
        {
            $arPriceRange['FROM'] = $arSectionFilter['PRICE']["FROM"];
            $arPriceRange['TO']   = $arSectionFilter['PRICE']["TO"];
        }

        return $arPriceRange;
    }

    public static function getSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arHeaderFilters = $arBottomFilters = $arRangeFilters = [];

        if ($IBLOCK_ID == TIRES_IB)
        {
            $arSmartFilter   = \CFilterExt::getTiresSmartFilter($IBLOCK_ID, $SECTION_CODE);
            $arHeaderFilters = \CFilterExt::getTiresHeadFilters($SECTION_CODE, $arSmartFilter);
            $arBottomFilters = \CFilterExt::getTiresBottomFilters($SECTION_CODE, $arSmartFilter);
        }
        elseif ($IBLOCK_ID == OILS_IB)
        {
            $arSmartFilter   = \CFilterExt::getOilsSmartFilter($IBLOCK_ID, $SECTION_CODE);
            $arHeaderFilters = \CFilterExt::getOilsHeadFilters($SECTION_CODE, $arSmartFilter);
            $arBottomFilters = \CFilterExt::getOilsBottomFilters($SECTION_CODE, $arSmartFilter);
        }
        elseif ($IBLOCK_ID == AKB_IB)
        {
            $arSmartFilter   = \CFilterExt::getAkbSmartFilter($IBLOCK_ID, $SECTION_CODE);
            $arHeaderFilters = \CFilterExt::getAkbHeadFilters($SECTION_CODE, $arSmartFilter);
            $arBottomFilters = \CFilterExt::getAkbBottomFilters($SECTION_CODE, $arSmartFilter);
            $arRangeFilters  = \CFilterExt::getAkbRangeFilters($SECTION_CODE, $arSmartFilter);
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $arSmartFilter   = \CFilterExt::getDiscsSmartFilter($IBLOCK_ID, $SECTION_CODE);
            $arHeaderFilters = \CFilterExt::getDiscsHeadFilters($SECTION_CODE, $arSmartFilter);
            $arBottomFilters = \CFilterExt::getDiscsBottomFilters($SECTION_CODE, $arSmartFilter);
            $arRangeFilters  = \CFilterExt::getDiscsRangeFilters($SECTION_CODE, $arSmartFilter);
        }
        elseif ($IBLOCK_ID == MISC_IB)
        {
            $arSmartFilter   = \CFilterExt::getMiscSmartFilter($IBLOCK_ID, $SECTION_CODE);
            $arHeaderFilters = \CFilterExt::getMiscHeadFilters($SECTION_CODE, $arSmartFilter);
        }

        return array($arHeaderFilters, $arBottomFilters, $arRangeFilters);
    }

    public static function getTiresSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arBrands  = self::getListVariants("MARKA", $IBLOCK_ID, $SECTION_CODE);
        $arActions = self::getActions();

        $arSeasons = array(
            array('value' => '', 'title' => 'Любой'),
            array('value' => 'Лето', 'title' => 'Лето'),
            array('value' => 'Зима', 'title' => 'Зима'),
        );

        $arSpikes = array(
            array('value' => '', 'title' => 'Все шины'),
            array('value' => 'Шипы', 'title' => 'Шипованные'),
            array('value' => 'Нешип', 'title' => 'Нешипованные'),
        );

        $arCamers = array(
            array('value' => '', 'title' => 'Все'),
            array('value' => 'Камерная', 'title' => 'С камерой'),
            array('value' => 'Бескамерная', 'title' => 'Без камеры'),
        );

        $arAxis = array(
            array('value' => '', 'title' => 'Любая'),
            array('value' => 'рулевая ось', 'title' => '<b>Рулевая</b><figure class="axis_steering"></figure>'),
            array('value' => 'ведущая ось', 'title' => '<b>Ведущая</b><figure class="axis_leading"></figure>'),
            array('value' => 'прицеп ось', 'title' => '<b>Прицепная</b><figure class="axis_trailing"></figure>'),
            array('value' => 'steer_trail', 'title' => '<b>Рулевая / Прицепная</b><figure class="axis_steering_trailing"></figure>'),
            array('value' => 'универсальная ось', 'title' => '<b>Универсальная</b><figure class="axis_universal"></figure>'),
        );

        $arSmartFilter = array(
            SEZON            => array(
                'title'    => 'Сезон',
                'property' => SEZON,
                'type'     => 'radio',
                'class'    => '',
                'selected' => '',
                'items'    => $arSeasons,
                'default'  => '',
            ),
            'SHIPY'          => array(
                'title'    => 'Наличие шипов',
                'property' => 'SHIPY',
                'type'     => 'radio',
                'class'    => '',
                'selected' => '',
                'items'    => $arSpikes,
                'default'  => '',
            ),
            'KAMERA'         => array(
                'title'    => 'Камера',
                'property' => 'KAMERA',
                'type'     => 'radio',
                'class'    => '',
                'selected' => '',
                'items'    => $arCamers,
                'default'  => '',
            ),
            'OS_PRIMENENIYA' => array(
                'title'    => 'Ось',
                'property' => 'OS_PRIMENENIYA',
                'type'     => 'radio',
                'class'    => 'smartfilter-block-axis',
                'selected' => '',
                'items'    => $arAxis,
                'default'  => '',
            ),
            'MARKA'          => array(
                'title'    => 'Бренд товара',
                'property' => 'MARKA',
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => $arBrands,
            ),
            RUN_FLAT         => array(
                'title'    => '',
                'property' => RUN_FLAT,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'RunFlat')),
            ),
            SALE             => array(
                'title'    => '',
                'property' => SALE,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Распродажа')),
            ),
            SALE_DAY         => array(
                'title'    => '',
                'property' => SALE_DAY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Цена дня')),
            ),
            HIT              => array(
                'title'    => '',
                'property' => HIT,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Хит продаж')),
            ),
            BONUS            => array(
                'title'    => '',
                'property' => BONUS,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Бонус')),
            ),
            AKTSIYA          => array(
                'title'    => 'Акция',
                'property' => AKTSIYA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => $arActions,
            ),
            'QUANTITY'       => array(
                'title'    => '',
                'property' => 'QUANTITY',
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'В наличии от 4 шт.')),
            ),
        );


        if (!empty($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]))
        {
            $arSmartFilter['PRESET'] = array(
                'title'    => 'Размер',
                'property' => 'PRESET',
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => $_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE],
            );
        }

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arSmartFilter as &$arBlock)
        {
            $filterProp = $arBlock['property'];

            if (!empty($arSectionFilter[$filterProp]))
            {
                $arBlock['selected'] = $arSectionFilter[$filterProp];
            }
        }

        return $arSmartFilter;
    }

    public static function getTiresHeadFilters($SECTION_CODE, $arSmartFilter)
    {
        $arHeaderFilters = array();
        if ($SECTION_CODE == LEGKOVYE)
        {
            $arHeaderFilters[] = $arSmartFilter[SEZON];

            if ($arSmartFilter[SEZON]['selected'] == "Зима" || empty($arSmartFilter[SEZON]['selected']))
            {
                $arHeaderFilters[] = $arSmartFilter['SHIPY'];
            }

            if (!empty($arSmartFilter['PRESET']))
            {
                $arHeaderFilters[] = $arSmartFilter['PRESET'];
            }

            $arHeaderFilters[] = $arSmartFilter['MARKA'];
        }
        elseif ($SECTION_CODE == GRUZOVYE)
        {
            $arHeaderFilters = array(
                $arSmartFilter['OS_PRIMENENIYA'],
                $arSmartFilter['KAMERA'],
                $arSmartFilter['MARKA'],
            );
        }
        elseif ($SECTION_CODE == MOTO)
        {
            $arHeaderFilters = array(
                $arSmartFilter['MARKA'],
            );
        }

        return $arHeaderFilters;
    }

    public static function getTiresBottomFilters($SECTION_CODE, $arSmartFilter)
    {
        $arBottomFilters = array(
            $arSmartFilter[RUN_FLAT],
            $arSmartFilter[SALE],
            $arSmartFilter[AKTSIYA],
            $arSmartFilter['QUANTITY'],
        );

        return $arBottomFilters;
    }

    public static function getOilsSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arActions = self::getActions();

        $arSmartFilter = array(
            SM_PROIZVODITEL  => array(
                'title'    => 'Бренд товара',
                'property' => SM_PROIZVODITEL,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_PROIZVODITEL, $IBLOCK_ID, $SECTION_CODE),
            ),
            SM_VYAZKOST      => array(
                'title'    => 'Вязкость',
                'property' => SM_VYAZKOST,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_VYAZKOST, $IBLOCK_ID, $SECTION_CODE),
            ),
            SM_TIP           => array(
                'title'    => 'Тип масла',
                'property' => SM_TIP,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_TIP, $IBLOCK_ID, $SECTION_CODE),
            ),
            SM_NAZNACHENIE   => array(
                'title'    => 'Назначение',
                'property' => SM_NAZNACHENIE,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_NAZNACHENIE, $IBLOCK_ID, $SECTION_CODE),
            ),
            SM_VIDMASLA      => array(
                'title'    => 'Вид масла',
                'property' => SM_VIDMASLA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_VIDMASLA, $IBLOCK_ID, $SECTION_CODE),
            ),
            OZH_TIP          => array(
                'title'    => 'Тип охдаждающей жидкости',
                'property' => OZH_TIP,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(OZH_TIP, $IBLOCK_ID, $SECTION_CODE),
            ),
            OZH_TSVET        => array(
                'title'    => 'Цвет антифриза',
                'property' => OZH_TSVET,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(OZH_TSVET, $IBLOCK_ID, $SECTION_CODE),
            ),
            SM_NAZNACHENIEDV => array(
                'title'    => 'Тип двигателя',
                'property' => SM_NAZNACHENIEDV,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(SM_NAZNACHENIEDV, $IBLOCK_ID, $SECTION_CODE),
            ),
            SALE             => array(
                'title'    => '',
                'property' => SALE,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Распродажа')),
            ),
            SALE_DAY         => array(
                'title'    => '',
                'property' => SALE_DAY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Цена дня')),
            ),
            HIT              => array(
                'title'    => '',
                'property' => HIT,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Хит продаж')),
            ),
            BONUS            => array(
                'title'    => '',
                'property' => BONUS,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Бонус')),
            ),
            AKTSIYA          => array(
                'title'    => 'Акция',
                'property' => AKTSIYA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => $arActions,
            ),
            'QUANTITY'       => array(
                'title'    => '',
                'property' => 'QUANTITY',
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'В наличии от 4 шт.')),
            ),
        );

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arSmartFilter as &$arBlock)
        {
            $filterProp = $arBlock['property'];

            if (!empty($arSectionFilter[$filterProp]))
            {
                $arBlock['selected'] = $arSectionFilter[$filterProp];
            }
        }

        return $arSmartFilter;
    }

    public static function getOilsHeadFilters($SECTION_CODE, $arSmartFilter)
    {
        $arHeaderFilters = array();

        if ($SECTION_CODE == MASLA)
        {
            $arHeaderFilters = array(
                $arSmartFilter[SM_NAZNACHENIEDV],
                $arSmartFilter[SM_TIP],
                $arSmartFilter[SM_VYAZKOST],
                $arSmartFilter[SM_PROIZVODITEL],
            );
        }
        elseif ($SECTION_CODE == TRANSM)
        {
            $arHeaderFilters = array(
                $arSmartFilter[SM_NAZNACHENIE],
                $arSmartFilter[SM_TIP],
                $arSmartFilter[SM_VYAZKOST],
                $arSmartFilter[SM_PROIZVODITEL],
                    //$arSmartFilter[SM_NAZNACHENIEDV],
            );
        }
        elseif ($SECTION_CODE == FLUIDS)
        {
            $arHeaderFilters = array(
                $arSmartFilter[SM_VIDMASLA],
                $arSmartFilter[OZH_TIP],
                $arSmartFilter[OZH_TSVET],
                $arSmartFilter[SM_PROIZVODITEL],
            );
        }

        return $arHeaderFilters;
    }

    public static function getOilsBottomFilters($SECTION_CODE, $arSmartFilter)
    {
        $arBottomFilters = array(
            $arSmartFilter[SALE],
        );

        return $arBottomFilters;
    }

    public static function getAkbSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arSmartFilter = array(
            AKB_PROIZVODITEL => array(
                'title'    => 'Бренд товара',
                'property' => AKB_PROIZVODITEL,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(AKB_PROIZVODITEL, $IBLOCK_ID, $SECTION_CODE),
            ),
            AKB_POLYARNOST   => array(
                'title'    => 'Полярность',
                'property' => AKB_POLYARNOST,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(AKB_POLYARNOST, $IBLOCK_ID, $SECTION_CODE),
            ),
            AKB_EMKOST       => array(
                'title'    => 'Емкость, А·ч',
                'property' => AKB_EMKOST,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(AKB_EMKOST, $IBLOCK_ID, $SECTION_CODE, 1, true),
            ),
            AKB_DLINA        => array(
                'title'    => 'Длина, мм',
                'property' => AKB_DLINA,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(AKB_DLINA, $IBLOCK_ID, $SECTION_CODE, 5, true),
            ),
            AKB_SHIRINA      => array(
                'title'    => 'Ширина, мм',
                'property' => AKB_SHIRINA,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(AKB_SHIRINA, $IBLOCK_ID, $SECTION_CODE, 5, true),
            ),
            AKB_VYSOTA       => array(
                'title'    => 'Высота, мм',
                'property' => AKB_VYSOTA,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(AKB_VYSOTA, $IBLOCK_ID, $SECTION_CODE, 5, true),
            ),
            SALE             => array(
                'title'    => '',
                'property' => SALE,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Распродажа')),
            ),
        );

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arSmartFilter as &$arBlock)
        {
            $filterProp = $arBlock['property'];

            if (!empty($arSectionFilter[$filterProp]))
            {
                $arBlock['selected'] = $arSectionFilter[$filterProp];
            }
        }

        return $arSmartFilter;
    }

    public static function getAkbHeadFilters($SECTION_CODE, $arSmartFilter)
    {
        $arHeaderFilters = array();

        if ($SECTION_CODE == AKB_AVTO)
        {
            $arHeaderFilters = array(
                $arSmartFilter[AKB_POLYARNOST],
                $arSmartFilter[AKB_PROIZVODITEL],
            );
        }
        elseif ($SECTION_CODE == AKB_MOTO)
        {
            $arHeaderFilters = array(
                $arSmartFilter[AKB_POLYARNOST],
                $arSmartFilter[AKB_PROIZVODITEL],
            );
        }

        return $arHeaderFilters;
    }

    public static function getAkbBottomFilters($SECTION_CODE, $arSmartFilter)
    {
        $arBottomFilters = array(
            $arSmartFilter[SALE],
        );

        return $arBottomFilters;
    }

    public static function getAkbRangeFilters($SECTION_CODE, $arSmartFilter)
    {
        $arRangeFilters = array(
            $arSmartFilter[AKB_EMKOST],
            $arSmartFilter[AKB_DLINA],
            $arSmartFilter[AKB_SHIRINA],
            $arSmartFilter[AKB_VYSOTA],
        );

        return $arRangeFilters;
    }

    public static function getDiscsSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arSmartFilter = array(
            DIAMETRDISKA   => array(
                'title'    => 'Диаметр',
                'property' => DIAMETRDISKA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-block-brands-335',
                'selected' => '',
                'items'    => self::getListVariants(DIAMETRDISKA, $IBLOCK_ID, $SECTION_CODE),
            ),
            KREPLENIEDISKA => array(
                'title'    => 'Крепление',
                'property' => KREPLENIEDISKA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-block-brands-210',
                'selected' => '',
                'items'    => self::getListVariants(KREPLENIEDISKA, $IBLOCK_ID, $SECTION_CODE),
            ),
            VYLET          => array(
                'title'    => 'Вылет',
                'property' => VYLET,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(VYLET, $IBLOCK_ID, $SECTION_CODE, 1, true),
            ),
            DIA            => array(
                'title'    => 'Центральное отверстие',
                'property' => DIA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-block-brands-210',
                'selected' => '',
                'items'    => self::getListVariants(DIA, $IBLOCK_ID, $SECTION_CODE),
            ),
            SHIRINADISKA   => array(
                'title'    => 'Ширина',
                'property' => SHIRINADISKA,
                'type'     => 'range',
                'class'    => '',
                'selected' => '',
                'range'    => self::getRange(SHIRINADISKA, $IBLOCK_ID, $SECTION_CODE, 0.25, true),
            ),
            DISKI_MARKA    => array(
                'title'    => 'Марка',
                'property' => DISKI_MARKA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-block-brands-210',
                'selected' => '',
                'items'    => self::getListVariants(DISKI_MARKA, $IBLOCK_ID, $SECTION_CODE),
            ),
            SALE           => array(
                'title'    => '',
                'property' => SALE,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-margin',
                'selected' => '',
                'items'    => array(array('value' => 'Да', 'title' => 'Распродажа')),
            ),
        );

        $showPresets = false;
        if (!empty($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]))
        {
            if ($_REQUEST["FILTER"]["CLEAR_PROPERTY"] == "1" && $_REQUEST["FILTER_PROPERTY"] == "TUNING")
            {
                $showPresets = true;
            }
            else
            {
                if ($_REQUEST["FILTER"]["TUNING"] != "Y")
                {
                    $showPresets = true;
                }
            }
        }

        if ($showPresets)
        {
            $PRESETS = $_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE];

            $arPresets = array();
            foreach ($PRESETS as $key => $arPreset)
            {
                $arPresetParts = explode("|", $arPreset["value"]);
                $TYPE          = $arPresetParts[0]; //2 - front; 4 - back

                if (!if_array_key_exists($key, $arPresets[$TYPE]))
                {
                    $arPresets[$TYPE][$key] = $arPreset;
                }
            }

            //if (empty($_REQUEST["TUNING"]))
            //{
            if (!empty($arPresets[4]))
            {
                $arSmartFilter['PRESET_FRONT'] = array(
                    'title'    => 'Передняя / любая ось',
                    'property' => 'PRESET',
                    'type'     => 'checkbox',
                    'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-nowrap smartfilter-block-brands-1000',
                    'selected' => '',
                    'items'    => $arPresets[2],
                );

                $arSmartFilter['PRESET_BACK'] = array(
                    'title'    => 'Задняя ось',
                    'property' => 'PRESET',
                    'type'     => 'checkbox',
                    'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-nowrap smartfilter-block-brands-1000',
                    'selected' => '',
                    'items'    => $arPresets[4],
                );
            }
            else
            {
                $arSmartFilter['PRESET_FRONT'] = array(
                    'title'    => 'Передняя / любая ось',
                    'property' => 'PRESET',
                    'type'     => 'checkbox',
                    'class'    => 'smartfilter-block-brands js-block-scrollbox smartfilter-nowrap smartfilter-block-brands-1000',
                    'selected' => '',
                    'items'    => $arPresets[2],
                );
            }
            //}
        }

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arSmartFilter as &$arBlock)
        {
            $filterProp = $arBlock['property'];

            if (!empty($arSectionFilter[$filterProp]))
            {
                $arBlock['selected'] = $arSectionFilter[$filterProp];
            }
        }

        return $arSmartFilter;
    }

    public static function getDiscsHeadFilters($SECTION_CODE, $arSmartFilter)
    {
        $arHeaderFilters = array();

        if (!empty($arSmartFilter['PRESET_FRONT']))
        {
            $arHeaderFilters[] = $arSmartFilter['PRESET_FRONT'];
        }

        if (!empty($arSmartFilter['PRESET_BACK']))
        {
            $arHeaderFilters[] = $arSmartFilter['PRESET_BACK'];
        }

        $arHeaderFilters[] = $arSmartFilter[DISKI_MARKA];
        $arHeaderFilters[] = $arSmartFilter[DIAMETRDISKA];
        $arHeaderFilters[] = $arSmartFilter[KREPLENIEDISKA];
        $arHeaderFilters[] = $arSmartFilter[DIA];

        return $arHeaderFilters;
    }

    public static function getDiscsBottomFilters($SECTION_CODE, $arSmartFilter)
    {
        $arBottomFilters = array(
            $arSmartFilter[SALE],
        );

        return $arBottomFilters;
    }

    public static function getDiscsRangeFilters($SECTION_CODE, $arSmartFilter)
    {
        $arRangeFilters = array(
            $arSmartFilter[VYLET],
            $arSmartFilter[SHIRINADISKA],
        );

        return $arRangeFilters;
    }

    public static function getMiscSmartFilter($IBLOCK_ID, $SECTION_CODE)
    {
        $arSmartFilter = array(
            BREND_SOPUTSTVUYUSHCHIETOVARY  => array(
                'title'    => 'Сопутствующие товары',
                'property' => BREND_SOPUTSTVUYUSHCHIETOVARY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_SOPUTSTVUYUSHCHIETOVARY, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_AVTOLAMPY  => array(
                'title'    => 'Автолампы',
                'property' => BREND_AVTOLAMPY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_AVTOLAMPY, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_FILTRY  => array(
                'title'    => 'Фильтры',
                'property' => BREND_FILTRY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_FILTRY, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_SHCHETKISTEKLOOCHISTITELEY  => array(
                'title'    => 'Щетки стеклоочистителей',
                'property' => BREND_SHCHETKISTEKLOOCHISTITELEY,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_SHCHETKISTEKLOOCHISTITELEY, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_TORMOZNYEKOLODKI  => array(
                'title'    => 'Тормозные колодки',
                'property' => BREND_TORMOZNYEKOLODKI,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_TORMOZNYEKOLODKI, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_SVECHI  => array(
                'title'    => 'Свечи',
                'property' => BREND_SVECHI,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_SVECHI, $IBLOCK_ID, $SECTION_CODE),
            ),
            BREND_AVTOKOSMETIKA  => array(
                'title'    => 'Автокосметика',
                'property' => BREND_AVTOKOSMETIKA,
                'type'     => 'checkbox',
                'class'    => 'smartfilter-block-brands js-block-scrollbox',
                'selected' => '',
                'items'    => self::getListVariants(BREND_AVTOKOSMETIKA, $IBLOCK_ID, $SECTION_CODE),
            ),
        );

        $arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);

        foreach ($arSmartFilter as &$arBlock)
        {
            $filterProp = $arBlock['property'];

            if (!empty($arSectionFilter[$filterProp]))
            {
                $arBlock['selected'] = $arSectionFilter[$filterProp];
            }
        }

        return $arSmartFilter;
    }

    public static function getMiscHeadFilters($SECTION_CODE, $arSmartFilter)
    {
        $arHeaderFilters = array();

        if ($SECTION_CODE == MISC_SVECHI)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_SVECHI],
            );
        }
        elseif ($SECTION_CODE == MISC_KOLODKI)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_TORMOZNYEKOLODKI],
            );
        }
        elseif ($SECTION_CODE == MISC_MISCGOODS)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_SOPUTSTVUYUSHCHIETOVARY],
            );
        }
        elseif ($SECTION_CODE == MISC_KOSMETIKA)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_AVTOKOSMETIKA],
            );
        }
        elseif ($SECTION_CODE == MISC_LAMPY)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_AVTOLAMPY],
            );
        }
        elseif ($SECTION_CODE == MISC_FILTRY)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_FILTRY],
            );
        }
        elseif ($SECTION_CODE == MISC_SHETKI)
        {
            $arHeaderFilters = array(
                $arSmartFilter[BREND_SHCHETKISTEKLOOCHISTITELEY],
            );
        }

        return $arHeaderFilters;
    }

    /**
     * Определяет какие доступны варианты фильтра с учетом других фильтров
     * @param array $arFilter
     * @param int $IBLOCK_ID
     * @param array|string $property
     * @param string $type
     * @return array
     */
    public static function getSmartFilterDataAvailable($arFilter = array(), $IBLOCK_ID = TIRES_IB, $property = null, $type = null)
    {
        if (empty($arFilter))
        {
            $arFilter = array();
        }

        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getSmartFilterDataAvailable/";
        $cacheID   = "getSmartFilterDataAvailable" . serialize($arFilter) . $IBLOCK_ID . $property . $type;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arAvailableValues"]))
            {
                $arAvailableValues = $vars["arAvailableValues"];
                $lifeTime          = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $key = "PROPERTY_" . $property . "_VALUE";

            $availableAll      = false; //флаг указывает, что доступны все значения свойства
            $arAvailableValues = null; //массив доступных значений свойства

            if ($type != 'checkbox')
            {
                foreach ($arFilter as $arFilterNode)
                {
                    if (array_key_exists($key, $arFilterNode))
                    {
                        $availableAll = true;
                        break;
                    }
                }
            }

            if ($property == 'PRESET' || $property == 'QUANTITY' || $property == AKTSIYA || $property == SM_NAZNACHENIEDV)
            {
                $availableAll = true;
            }

            if (!$availableAll)
            {
                foreach ($arFilter as &$arFilterNode)
                {
                    if (isset($arFilterNode[$key]))
                    {
                        unset($arFilterNode[$key]);
                    }
                }

                $arFilterL = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y") + $arFilter;
                $arGroupBy = array("PROPERTY_" . $property);

                $arData = array();

                $obList = \CIBlockElement::GetList(array(), $arFilterL, $arGroupBy);
                while ($arItem = $obList->Fetch())
                {
                    $arData[] = $arItem;
                }

                foreach ($arData as $arDataVal)
                {
                    $arAvailableValues[] = $arDataVal[$key];
                }
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arAvailableValues" => $arAvailableValues,
            ));
        }

        return $arAvailableValues;
    }

    /**
     * Определяет какие доступны варианты фильтра без учет других фильтров, но с учетом доступности товара
     */
    public static function getSmartFilterDataExists($IBLOCK_ID, $property)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("15min", 0);
        $cachePath = "/ccache_filter/getSmartFilterDataExists/";
        $cacheID   = "getSmartFilterDataExists" . $IBLOCK_ID . $property;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arAvailableValues"]))
            {
                $arAvailableValues = $vars["arAvailableValues"];
                $lifeTime          = 0;
            }
        }

        if ($lifeTime > 0)
        {
            $key = "PROPERTY_" . $property . "_VALUE";

            $arAvailableValues = null; //массив доступных значений свойства

            $arFilterL = array(
                "IBLOCK_ID"         => $IBLOCK_ID,
                "ACTIVE"            => "Y",
                ">CATALOG_QUANTITY" => "0",
            );
            $arGroupBy = array("PROPERTY_" . $property);

            $arData = array();
            $obList = \CIBlockElement::GetList(array(), $arFilterL, $arGroupBy);
            while ($arItem = $obList->Fetch())
            {
                $arData[] = $arItem;
            }

            foreach ($arData as $arDataVal)
            {
                $arAvailableValues[] = $arDataVal[$key];
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arAvailableValues" => $arAvailableValues,
            ));
        }

        return $arAvailableValues;
    }

    public static function sortSmartFilterBlock($arBlock, $arAvailableValues, $minCount = 0)
    {
        $items    = $arBlock["items"];
        $property = $arBlock["property"];

        $props = array(
            AKB_PROIZVODITEL,
            "MARKA",
            SM_PROIZVODITEL,
            SM_VYAZKOST,
            DISKI_MARKA,
            DIA,
        );

        if (count($items) > $minCount && in_array($property, $props))
        {
            $items_selected  = array();
            $items_available = array();
            $items_others    = array();

            foreach ($items as $arItem)
            {
                $value = $arItem["value"];

                $available = empty($value) || !is_array($arAvailableValues) || in_array($value, $arAvailableValues);
                $selected  = $value == $arBlock["selected"] || if_in_array($value, $arBlock["selected"]);

                if ($selected)
                {
                    $items_selected[] = $arItem;
                }
                elseif ($available)
                {
                    $items_available[] = $arItem;
                }
                else
                {
                    $items_others[] = $arItem;
                }
            }

            $items = array_merge($items_selected, $items_available, $items_others);
        }

        return $items;
    }

    public static function parseDiskPreset($preset, $mode = false)
    {
        $matches = array();

        $pattern = '/'
                . '(?P<type>[0-9]+)'
                . '\|(?P<width>[0-9\.\,]+)'
                . 'Jx(?P<diameter>[0-9]+)'
                . ' (?P<holes>[0-9]+)'
                . 'x(?P<pcd>[0-9]+)'
                . ' ET(?P<etfrom>[0-9]+)(\.\.(?P<etto>[0-9]+))?'
                . ' d(?P<dia>[0-9\.]+)'
                . '/';

        preg_match_all($pattern, $preset, $matches);

        if (empty($matches["etto"][0]))
        {
            $matches["etto"][0] = $matches["etfrom"][0];
        }

        //свойства инфоблока каталога товаров (disks)
        $prop_w = "PROPERTY_" . SHIRINADISKA . "_VALUE";
        $prop_d = "=PROPERTY_" . DIAMETRDISKA . "_VALUE";
        $prop_v = "PROPERTY_" . VYLET . "_VALUE";
        $prop_k = "PROPERTY_" . KREPLENIEDISKA . "_VALUE";
        $prop_o = "PROPERTY_" . DIA . "_VALUE";

        $TYPE     = ceil($matches["type"][0]);
        $WIDTH    = /* ceil */($matches["width"][0]);
        $DIAMETER = ceil($matches["diameter"][0]);
        $ET_FROM  = ceil($matches["etfrom"][0]);
        $ET_TO    = ceil($matches["etto"][0]);

        if ($WIDTH == ceil($WIDTH))
        {
            $WIDTH = ceil($WIDTH);
        }

        $COUNTHOLE = ceil($matches["holes"][0]);
        $PCD       = floor($matches["pcd"][0]);
        $DIA       = round($matches["dia"][0], 1);
        $DIAMAX    = round($matches["dia"][0], 1);

        if (empty($WIDTH) || empty($DIAMETER) || (empty($ET_FROM) && empty($ET_TO)) || empty($COUNTHOLE) || empty($PCD) || empty($DIA))
        {
            return false;
        }

        $arDIA = \CFilterExt::getDiscksDia($DIA, $DIAMAX, 1);
        $arPCD = \CFilterExt::getDiscksPcd($COUNTHOLE, $PCD);

        if (!$mode)
        {
            $result = array(
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
        }
        elseif ($mode == "analogs_array")
        {
            $arDIA = \CFilterExt::getDiscksDia($DIA, $DIAMAX, 3000, true);

            $result = array(
                SHIRINADISKA   => $WIDTH,
                DIAMETRDISKA   => "R" . $DIAMETER,
                VYLET          => array(
                    "FROM" => $ET_FROM - 5,
                    "TO"   => $ET_TO + 5
                ),
                KREPLENIEDISKA => $arPCD,
                DIA            => $arDIA,
            );
        }
        elseif ($mode == "analogs")
        {
            $arDIA = \CFilterExt::getDiscksDia($DIA, $DIAMAX, 3000, true);

            $result = array(
                $prop_w => array(
                    $WIDTH,
                    number_format($WIDTH, 1, '.', ' '),
                    $WIDTH . ".0",
                    str_replace(".", ",", $WIDTH),
                ),
                $prop_d => "R" . $DIAMETER,
                array(
                    "LOGIC"        => "AND",
                    ">=" . $prop_v => $ET_FROM - 5,
                    "<=" . $prop_v => $ET_TO + 5
                ),
                $prop_k => $arPCD,
                $prop_o => $arDIA,
            );
        }
        else
        {
            $result = array(
                SHIRINADISKA   => $WIDTH,
                DIAMETRDISKA   => "R" . $DIAMETER,
                VYLET          => array($ET_FROM, $ET_TO),
                KREPLENIEDISKA => array($COUNTHOLE, $PCD),
                DIA            => $arDIA,
            );
        }

        return $result;
    }

}
