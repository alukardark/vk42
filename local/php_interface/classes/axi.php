<?php

class Axi
{

    private static $sIncludePath = "include/";
    private static $sTextsPath   = "_text/";
    private static $sSvgPath     = "_svg/";

    const DEFAULT_CITY = 'Kemerovo';

    private static $arAllowedCities = array(
        'Kemerovo'    => "Кемерово",
        'Novokuzneck' => "Новокузнецк",
    );
    private static $arSmallCities   = array(
        'Leninsk-Kuzneckij' => "Ленинск-Кузнецкий",
        'Belovo'            => "Белово",
        'Kiselevsk'         => "Киселевск",
        'Polysaevo'         => "Полысаево",
        'Prokopevsk'        => "Прокопьевск",
    );
    private static $arAnotherCities = array(
        ANOTHER_CITY_CODE => "Другой город",
    );

    public static function getDefaultCityKey()
    {
        return self::DEFAULT_CITY;
    }

    public static function getCities()
    {
        return self::$arAllowedCities;
    }

    public static function getSmallCities()
    {
        return self::$arSmallCities;
    }

    public static function getMainCitiesString()
    {
        $arCities = self::$arAllowedCities + self::$arSmallCities;
        return implode(', ', $arCities);
    }

    public static function getAllCities()
    {
        return self::$arAllowedCities + self::$arSmallCities + self::$arAnotherCities;
    }

    public static function getLocations($REGION_NAME_ORIG = "Kemerovskaya oblast")
    {
        $arLocations = array();
        $curCityKey  = \Axi::getCityKey();
        $allCities   = \Axi::getAllCities();

        $obList  = \CSaleLocation::GetList(
                        array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"), //array arSort
                        array("REGION_NAME_ORIG" => $REGION_NAME_ORIG, "!CITY_ID" => false), //array arFilter =
                        false, //array arGroupBy =
                        false, // array arNavStartParams = 
                        array()//array arSelectFields =
        );
        while ($arFetch = $obList->Fetch())
        {
            if (!array_key_exists($arFetch["CITY_NAME_ORIG"], $allCities)) continue;

            $arFetch_ZIP = \CSaleLocation::GetLocationZIP($arFetch["ID"])->Fetch();

            $arFetch["SELECTED"] = $arFetch["CITY_NAME_ORIG"] == $curCityKey;
            $arFetch["CODE"]     = \CSaleLocation::getLocationCODEbyID($arFetch["ID"]);
            $arFetch["ZIP"]      = $arFetch_ZIP["ZIP"];

            $arLocations[$arFetch["ID"]] = $arFetch;
        }

        return $arLocations;
    }

    public static function getLocationById($ID)
    {
        $obList  = \CSaleLocation::GetList(
                        array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"), //array arSort
                        array("ID" => $ID), //array arFilter =
                        false, //array arGroupBy =
                        false, // array arNavStartParams = 
                        array()//array arSelectFields =
        );
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch;
        }

        return false;
    }

    public static function getLocationIdByCode($CODE)
    {
        $obList  = \CSaleLocation::GetList(
                        array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"), //array arSort
                        array("CODE" => $CODE), //array arFilter =
                        false, //array arGroupBy =
                        false, // array arNavStartParams = 
                        array()//array arSelectFields =
        );
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch["ID"];
        }

        return false;
    }

    public static function getLocationIdByName($NAME)
    {
        $obList  = \CSaleLocation::GetList(
                        array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"), //array arSort
                        array("CITY_NAME" => $NAME), //array arFilter =
                        false, //array arGroupBy =
                        false, // array arNavStartParams = 
                        array()//array arSelectFields =
        );
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch["ID"];
        }

        return false;
    }

    /**
     * возвращает 1, если юзер сам выбрал город
     * @global type $APPLICATION
     * @return type
     */
    public static function isUserSetCityKey()
    {
        global $APPLICATION;
        return $APPLICATION->get_cookie("USER_CITY_SET") /* || !empty($arUser['PERSONAL_CITY']) */;
    }

    public static function getCityKey()
    {
        global $APPLICATION;

        $allCities   = \Axi::getAllCities();
        $userCityKey = $APPLICATION->get_cookie("USER_CITY");

        if (empty($userCityKey) || !array_key_exists($userCityKey, $allCities))
        {
            $userCityKey = self::setCityKey();
        }

        return $userCityKey;
    }

    public static function getCityNameByKey($key)
    {
        $allCities = \Axi::getAllCities();

        if (array_key_exists($key, $allCities))
        {
            return $allCities[$key];
        }

        return false;
    }

    public static function getCityKeyByName($cityName)
    {
        $allCities = \Axi::getAllCities();

        foreach ($allCities as $key => $value)
        {
            if ($value == $cityName)
            {
                return $key;
            }
        }

        return false;
    }

    public static function updateCityKey($sCityKey = null)
    {
        global $APPLICATION;
        $APPLICATION->set_cookie("USER_CITY_SET", 1); //флаг указывает, что юзер установил сам свой город (выбрал из списка или нажал "да")
        return self::setCityKey($sCityKey);
    }

    /**
     * Возвращает название текущего города
     * @return type
     */
    public static function getCityName($onlyAllowedCities = false)
    {
        $currCityKey = self::getCityKey();

        if ($onlyAllowedCities)
        {
            $allowedCities = \Axi::getCities();
            if (!in_array($currCityKey, $allowedCities))
            {
                $currCityKey = self::DEFAULT_CITY;
            }
        }
        return self::getCityNameByKey($currCityKey);
    }

    public static function setCityKey($sCityKey = null)
    {
        global $APPLICATION, $USER;

        $allCities = \Axi::getAllCities();

        if ($sCityKey === null)
        {
            $arConvertNames = array(
                "Novokuznetsk"       => "Novokuzneck",
                "Leninsk-kuznetsk"   => "Leninsk-Kuzneckij",
                "Leninsk-kuznetskiy" => "Leninsk-Kuzneckij",
                "Prokopyevsk"        => "Prokopevsk",
                "Prokop'yevsk"       => "Prokopevsk",
            );

            \Bitrix\Main\Loader::includeModule('statistic');
            $cityObj    = new \CCity();
            $arThisCity = $cityObj->GetFullInfo();
            $sCityKey   = strtr($arThisCity['CITY_NAME']['VALUE'], $arConvertNames);
        }

        if (!empty($sCityKey) && array_key_exists($sCityKey, $allCities))
        {
            $sCurrentCityKey = $sCityKey;
        }
        else
        {
            $sCurrentCityKey = self::DEFAULT_CITY;
        }

        $APPLICATION->set_cookie("USER_CITY", $sCurrentCityKey);

        if ($USER->IsAuthorized())
        {
            $obUser = new \CUser;
            $obUser->Update($USER->GetID(), array('PERSONAL_CITY' => $allCities[$sCurrentCityKey]));
        }

        return $sCurrentCityKey;
    }

    public static function fileExist($filename, $type = false, $sExtension = ".php")
    {
        if ($type == "text") $filename = self::$sTextsPath . $filename;

        $file = $_SERVER["DOCUMENT_ROOT"] . SITE_DIR . self::$sIncludePath . $filename . $sExtension;
        return file_exists($file);
    }

    public static function fileEmpty($filename, $type = false, $sExtension = ".php")
    {
        if ($type == "text") $filename = self::$sTextsPath . $filename;

        $file = $_SERVER["DOCUMENT_ROOT"] . SITE_DIR . self::$sIncludePath . $filename . $sExtension;
        return !(file_exists($file) && filesize($file));
    }

    /**
     * Выводит $filename из папки $sIncludePath
     * @global type $APPLICATION
     * @param string $filename Имя файла без расширения (возможно с указанием папки)
     * @param bool $bHideIcons запретить/разрешить редактирование в режиме правки
     * @param string $sEditMode html|text|php
     * @param type $sExtension Расширение файла
     */
    public static function GF($filename, $sTitle = "Редактирование включаемой области раздела", $bHideIcons = true, $sEditMode = "text", $sExtension = ".php", $bCache = false)
    {
        global $APPLICATION;

        if ($bCache)
        {
            $APPLICATION->IncludeFile(SITE_DIR . self::$sIncludePath . $filename . $sExtension, Array(), Array(
                "MODE"        => $sEditMode,
                "NAME"        => $sTitle,
                "SHOW_BORDER" => !$bHideIcons
            ));
        }
        else
        {
            $APPLICATION->IncludeComponent("bitrix:main.include", "", array(
                "AREA_FILE_SHOW" => "file",
                "EDIT_MODE"      => $sEditMode,
                "NAME"           => "tzt",
                "PATH"           => SITE_DIR . self::$sIncludePath . $filename . $sExtension,
                    ), false, array("HIDE_ICONS" => $bHideIcons ? "Y" : "N"));
        }
    }

    /**
     * @see Axi::GF()
     */
    public static function GT($filename, $sTitle = "Текстовая область", $bHideIcons = false, $sEditMode = "text")
    {
        self::GF(self::$sTextsPath . $filename, $sTitle, $bHideIcons, $sEditMode, ".php");
    }

    /**
     * @see Axi::GF()
     */
    public static function GSVG($filename, $sTitle = "SVG-файл")
    {
        self::GF(self::$sSvgPath . $filename, $sTitle, true, "text", ".svg");
    }

    /**
     * Get contents from the file.
     * @param string $filename
     */
    public static function GC($filename, $pathMode = 'include', $sExtension = ".php")
    {
        switch ($pathMode)
        {
            case 'include':
                $path = self::$sIncludePath;
                break;

            case 'text':
                $path = self::$sIncludePath . self::$sTextsPath;
                break;

            case 'svg':
                $path = self::$sIncludePath . self::$sSvgPath;
                break;

            default:
                $path = '';
                break;
        }
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $path . $filename . $sExtension);
    }

//    public function getPhones($id = null)
//    {
//        if (!is_null($id) && array_key_exists($id, self::$arPhones)) return self::$arPhones[$id];
//        return self::$arPhones;
//    }
//    public function getAlias()
//    {
//        return self::$sAlias;
//    }

    public static function getAlias($asArray = false)
    {
        global $APPLICATION;
        $sCurDir = $APPLICATION->GetCurDir();
        $arAlias = array();

        if ($sCurDir == "/")
        {
            $arAlias[] = "index-page";
        }
        else
        {
            $arAlias[] = "inner-page";

            if (\CSite::InDir(PATH_CATALOG)) $arAlias[] = "catalog-page";
            elseif (\CSite::InDir(PATH_OILS)) $arAlias[] = "catalog-page";
            elseif (\CSite::InDir(PATH_AKB)) $arAlias[] = "catalog-page";
            elseif (\CSite::InDir(PATH_DISCS)) $arAlias[] = "catalog-page";
            elseif (\CSite::InDir(PATH_MISC)) $arAlias[] = "catalog-page";
            elseif (\CSite::InDir('/akcii/')) $arAlias[] = "actions-page";
            elseif (\CSite::InDir('/kabinet/')) $arAlias[] = "kabinet-page";
            elseif (\CSite::InDir('/servis/')) $arAlias[] = "services-page";
            elseif (\CSite::InDir('/uslugi/')) $arAlias[] = "uslugi-page";
            elseif (\CSite::InDir('/novosti/')) $arAlias[] = "news-page";
            elseif (\CSite::InDir('/faq/')) $arAlias[] = "faq-page";

            if (\CSite::InDir(PATH_BASKET)) $arAlias[] = "basket-page";
            elseif (\CSite::InDir(PATH_ORDER)) $arAlias[] = "order-page";
            elseif (\CSite::InDir(PATH_AUTH)) $arAlias[] = "auth-page";
            elseif (\CSite::InDir(PATH_PERSONAL)) $arAlias[] = "personal-page";
        }

        return $asArray ? $arAlias : implode(' ', $arAlias);
    }

//    private static function setPhones()
//    {
//        global $APPLICATION;
//        $arPhones  = array();
//        $arOrder   = Array("PROPERTY_MAIN_PHONE" => "ASC", "SORT" => "ASC", "ID" => "ASC");
//        $arSelect  = Array("ID", "NAME", "IBLOCK_ID", 'CODE', 'ACTIVE', 'PROPERTY_*');
//        $arFilter  = Array("IBLOCK_ID" => PHONES_IB, "ACTIVE" => "Y");
//        $obList    = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
//        while ($obElement = $obList->GetNextElement())
//        {
//            $arFields = $obElement->GetFields();
//            $arProps  = $obElement->GetProperties();
//
//            //формируем список телефонов
//            $arPhones[] = $APPLICATION->IncludeComponent(
//                    "axioma:phone_subst", ".default", Array(
//                "CACHE_DAYS"         => "7",
//                "COOKIE_NAME"        => "",
//                "ENABLE_PHONE_SUBST" => $arProps["enable_phone_subst"]['VALUE_XML_ID'],
//                "IBLOCK_ID"          => PHONES_IB,
//                "PHONE_ID"           => $arFields["ID"],
//                "REPLACE_CACHE"      => "Y",
//                "SUBJECT"            => "",
//                "USE_CACHE"          => "Y",
//                "OUTPUT_MODE"        => "RETURN"
//                    ), false, array("HIDE_ICONS" => "Y")
//            );
//        }
//
//        self::$arPhones = $arPhones;
//    }

    public static function getSocNets($id = null)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("30day", 0);
        $cachePath = "/ccache_common/getSocNets/";
        $cacheID   = "getSocNets" . SOCNETS_IB;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arSocNets"]))
            {
                $arSocNets = $vars["arSocNets"];
            }
        }

        if ($lifeTime > 0)
        {
            $arSocNets = array();

            $arOrder   = Array("SORT" => "ASC", "ID" => "ASC");
            $arSelect  = Array("ID", "NAME", "IBLOCK_ID", 'CODE', 'ACTIVE', 'PROPERTY_LINK');
            $arFilter  = Array("IBLOCK_ID" => SOCNETS_IB, "ACTIVE" => "Y");
            $obList    = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
            while ($obElement = $obList->GetNextElement())
            {
                $arFields = $obElement->GetFields();
                $arProps  = $obElement->GetProperties();

                $arSocNets[$arFields['CODE']] = array(
                    'NAME' => $arFields['NAME'],
                    'CODE' => $arFields['CODE'],
                    'LINK' => $arProps['LINK']['VALUE'],
                );
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arSocNets" => $arSocNets,
            ));
        }

        if (!is_null($id) && array_key_exists($id, $arSocNets)) return $arSocNets[$id];
        return $arSocNets;
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
        //self::setPhones();
        //self::setAlias();
    }

}
