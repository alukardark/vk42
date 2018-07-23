<?php

//set_time_limit(5);
ini_set('max_execution_time', 5000);

class CXmlExt
{

    private static $_instance;
    private static $countries;

    public static function getCategories()
    {
        $categories = array(
            array(
                'id'        => 1000,
                'parent_id' => false,
                'name'      => 'Авто',
            ),
            array(
                'id'        => 2000,
                'parent_id' => 1000,
                'name'      => 'Шины и диски',
            ),
            array(
                'id'           => 3000,
                'parent_id'    => 2000,
                'name'         => 'Шины',
                'IBLOCK_ID'    => TIRES_IB,
                'SECTION_CODE' => array(LEGKOVYE),
            ),
            array(
                'id'           => 4000,
                'parent_id'    => 2000,
                'name'         => 'Грузовые шины',
                'IBLOCK_ID'    => TIRES_IB,
                'SECTION_CODE' => array(GRUZOVYE),
            ),
            array(
                'id'           => 5000,
                'parent_id'    => 2000,
                'name'         => 'Мотошины',
                'IBLOCK_ID'    => TIRES_IB,
                'SECTION_CODE' => array(MOTO),
            ),
//            array(
//                'id'           => 6000,
//                'parent_id'    => 2000,
//                'name'         => 'Колесные диски',
//                'IBLOCK_ID'    => DISCS_IB,
//                'SECTION_CODE' => array(DISCS_LIGHT, DISCS_STEEL),
//            ),
        );

        return $categories;
    }

    public static function getParams()
    {
        $params = array(
            'available'   => true,
            'picture'     => true,
            'delivery'    => true,
            'pickup'      => true,
            'store'       => false,
            'sales_notes' => 'Доставка осуществляется при 100% предоплате.',
        );

        return $params;
    }

    public static function generateSitemap()
    {
        $arIblockIds = array(TIRES_IB, DISCS_IB, OILS_IB, AKB_IB);


        $arFilter = Array(
            "IBLOCK_ID" => $arIblockIds,
        );

        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');

        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        $xml->startElement('url');
        $xml->writeElement('loc', "https://cs42.ru" . PATH_CATALOG);
        $xml->writeElement('lastmod', date("c", time()));
        $xml->writeElement('changefreq', 'weekly');
        $xml->writeElement('priority', '0.5');
        $xml->endElement(); // end a url element

        $obList  = \CIBlockElement::GetList(Array(), $arFilter);
        while ($arFetch = $obList->Fetch())
        {
            $IBLOCK_SECTION_ID = $arFetch["IBLOCK_SECTION_ID"];

            if (empty($IBLOCK_SECTION_ID))
            {
                continue;
            }

            $xml->startElement('url');
            $xml->writeElement('loc', "https://cs42.ru" . PATH_CATALOG . \CCatalogExt::getProductUrl($arFetch));
            $xml->writeElement('lastmod', date("c", time(strtotime($arFetch["TIMESTAMP_X"]))));
            $xml->writeElement('changefreq', 'weekly');
            $xml->writeElement('priority', '0.5');
            $xml->endElement(); // end a url element
        }

        $xml->endElement(); // end a urlset element
        $xml->endDocument();

        $content = $xml->outputMemory();

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/sitemap_files.xml", $content);
    }

    public static function start()
    {
        $categories = self::getCategories();

        self::$countries = file('xml.countries.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $arOffers = array();

        foreach ($categories as $category)
        {
            self::parseCategory($arOffers, $category);
        }

        //printra($arOffers);
        $fname = self::makeYML($arOffers);

        download($fname, "test.yml");
    }

    public static function parseCategory(&$arOffers, $category)
    {
        $params = self::getParams();
        self::getOffers($arOffers, $category, $params);
    }

    public static function getOffers(&$arOffers, $category, $params)
    {
        $SECTION_CODE = $category['SECTION_CODE'];
        $IBLOCK_ID    = $category['IBLOCK_ID'];
        $PRICE_ID     = basePrice();

        $arFilter = Array(
            "IBLOCK_ID"           => $IBLOCK_ID,
            "SECTION_CODE"        => $SECTION_CODE,
            "INCLUDE_SUBSECTIONS" => "Y",
        );

        if ($params['available'])
        {
            $arFilter["ACTIVE"]            = "Y";
            $arFilter["CATALOG_AVAILABLE"] = "Y";
            $arFilter[">CATALOG_QUANTITY"] = "0";
        }

        if ($params['picture'])
        {
            $arFilter["!DETAIL_PICTURE"] = false;
        }

        if ($IBLOCK_ID == TIRES_IB)
        {
            $arFilter["!PROPERTY_MARKA_VALUE"]           = false;
            $arFilter["!PROPERTY_MODEL_VALUE"]           = false;
            $arFilter["!PROPERTY_" . SHIRINA . "_VALUE"] = false;
            $arFilter["!PROPERTY_" . VYSOTA . "_VALUE"]  = false;
            $arFilter["!PROPERTY_" . DIAMETR . "_VALUE"] = false;
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $arFilter["!PROPERTY_" . DISKI_MARKA . "_VALUE"]    = false;
            $arFilter["!PROPERTY_" . DISKI_MODEL . "_VALUE"]    = false;
            $arFilter["!PROPERTY_" . KREPLENIEDISKA . "_VALUE"] = false;
            $arFilter["!PROPERTY_" . SHIRINADISKA . "_VALUE"]   = false;
            $arFilter["!PROPERTY_" . DIAMETRDISKA . "_VALUE"]   = false;
            $arFilter["!PROPERTY_" . DIA . "_VALUE"]            = false;
            $arFilter["!PROPERTY_" . VYLET . "_VALUE"]          = false;
        }

        $attributes = array(
            'type'      => 'vendor.model',
            'available' => $params['available'] ? "true" : "false", //Статус товара: true — «готов к отправке»; false — «на заказ»
        );

        $offer = array(
            'currencyId'            => 'RUB',
            'categoryId'            => $category['id'],
            'delivery'              => $params['delivery'], //Возможность курьерской доставки соответствующего товара.
            'pickup'                => $params['pickup'], //Возможность самовывоза из пунктов выдачи.
            'store'                 => $params['store'], //Возможность купить товар без предварительного заказа..
            'delivery-options'      => array(
                'option' => array(
                    'cost'         => '500',
                    'days'         => '7-9',
                    'order-before' => '18',
                ),
            ),
            'sales_notes'           => $params['sales_notes'],
            'manufacturer_warranty' => "true",
        );

        //$words     = file(dirname(__FILE__) . '/xml.words.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);




        $i         = 0;
        $obList    = \CIBlockElement::GetList(Array(), $arFilter);
        while ($obElement = $obList->GetNextElement(false, false))
        {
            //if (++$i > 500) break;
            $arFields = $obElement->GetFields();
            $arProps  = $obElement->GetProperties();

            $PRODUCT_ID       = $arFields['ID'];
            $PRODUCT_NAME     = $arFields['NAME'];
            $STRANA           = $arProps['STRANA']['VALUE'];
            $CATALOG_QUANTITY = (int) $arFields['CATALOG_QUANTITY'];
            $ZAKAZ_QUANTITY   = 0; //кол-во на складе под заказ


            $obListStore  = \CCatalogStore::GetList(
                            array(), array('ID' => '221', 'PRODUCT_ID' => array($PRODUCT_ID)), false, false, array("PRODUCT_AMOUNT")
            );
            if ($arFetchStore = $obListStore->Fetch())
            {
                $ZAKAZ_QUANTITY = $arFetchStore["PRODUCT_AMOUNT"];
            }

            $QUANTITY = $CATALOG_QUANTITY - $ZAKAZ_QUANTITY;
            if ($QUANTITY < 1) continue;

            $PRODUCT_SECTION  = getSection($arFields['IBLOCK_ID'], $arFields['IBLOCK_SECTION_ID']);
            $attributes['id'] = $PRODUCT_ID;
            $offer['url']     = SITE_PROTOCOL . SITE_URL . \CCatalogExt::getProductUrl($arFields);
            $offer['price']   = \CBasketExt::getProductPrice($PRODUCT_ID, $PRICE_ID);
            //$offer['barcode'] = $arProps['CML2_ARTICLE']['VALUE'];

            $offer['outlets'] = array(
                'outlet' => array(
                    'id'      => 1,
                    'instock' => $QUANTITY,
                ),
            );

            if (in_array($STRANA, self::$countries)) $offer['country_of_origin'] = $STRANA;

            $offer['param'] = array();

            if ($params['picture'])
            {
                $obFile = \CFile::GetByID($arFields["DETAIL_PICTURE"]);
                if ($arFile = $obFile->Fetch())
                {
                    $offer['picture'] = SITE_PROTOCOL . SITE_URL . '/upload/' . $arFile['SUBDIR'] . '/' . $arFile['FILE_NAME'];
                }
            }

            if ($IBLOCK_ID == TIRES_IB)
            {
                //continue;
                $MARKA = htmlspecialcharsbx(trim($arProps['MARKA']['VALUE']));
                $MODEL = htmlspecialcharsbx(trim($arProps['MODEL']['VALUE']));

                if (empty($MARKA) || empty($MODEL)) continue;

                $SHIRINA  = $arProps[SHIRINA]['VALUE'];
                $VYSOTA   = $arProps[VYSOTA]['VALUE'];
                $DIAMETR  = $arProps[DIAMETR]['VALUE'];
                $RADIUS   = str_replace("R", "", $DIAMETR);
                $SEZON    = $arProps[SEZON]['VALUE'];
                $SHIPY    = $arProps['SHIPY']['VALUE'];
                $KAMERA   = $arProps['KAMERA']['VALUE'];
                $RUN_FLAT = $arProps[RUN_FLAT]['VALUE'] == "Да" ? "true" : "false";
                $SKOROST  = (int) $arProps['MAKSIMALNAYA_SKOROST']['VALUE'];
                $NAGRUZKA = str_replace(" ", "", $arProps['NAGRUZKA']['VALUE']);

                $offer['param'][] = array('name' => 'Диаметр', 'unit' => 'Дюйм', 'value' => $RADIUS);
                $offer['param'][] = array('name' => 'Ширина профиля', 'value' => $SHIRINA);
                $offer['param'][] = array('name' => 'Высота профиля', 'value' => $VYSOTA);
                $offer['param'][] = array('name' => 'Технология RunFlat', 'value' => "$RUN_FLAT");

                if ($SEZON == SUMMER) $offer['param'][] = array('name' => 'Сезонность', 'value' => "летние");
                elseif ($SEZON == WINTER) $offer['param'][] = array('name' => 'Сезонность', 'value' => "зимние");

                if ($PRODUCT_SECTION["CODE"] == LEGKOVYE)
                        $offer['param'][] = array('name' => 'Назначение', 'value' => "для легкового автомобиля");
                elseif ($PRODUCT_SECTION["CODE"] == GRUZOVYE)
                        $offer['param'][] = array('name' => 'Назначение', 'value' => "для грузового автомобиля");
                elseif ($PRODUCT_SECTION["CODE"] == MOTO)
                        $offer['param'][] = array('name' => 'Назначение', 'value' => "для мотоциклов");

                if ($SHIPY == 'Шипы') $offer['param'][] = array('name' => 'Шипы', 'value' => "есть");
                elseif ($SHIPY == 'Нешип') $offer['param'][] = array('name' => 'Шипы', 'value' => "нет");

                if ($KAMERA == 'Камерная') $offer['param'][] = array('name' => 'Камерные', 'value' => "да");
                elseif ($KAMERA == 'Бескамерная') $offer['param'][] = array('name' => 'Камерные', 'value' => "нет");

                if (!empty($NAGRUZKA) && !strstr($NAGRUZKA, "/"))
                {
                    $offer['param'][] = array('name' => 'Максимальная нагрузка (на одну шину)', 'unit' => "кг", 'value' => $NAGRUZKA);
                }

                if (!empty($SKOROST))
                {
                    $offer['param'][] = array('name' => 'Индекс максимальной скорости', 'unit' => "км/ч", 'value' => $SKOROST);
                }
                else
                {
                    $SKOROST = "";
                }

                $ALT_NAME = $MODEL;

                $offer['typePrefix'] = "Автошина";
                $offer['vendor']     = $MARKA;
                // $offer['model']      = trim($ALT_NAME);

                $descr = array();

                if ($SEZON == SUMMER) $descr[] = "летние";
                elseif ($SEZON == WINTER) $descr[] = "зимние";

                if ($PRODUCT_SECTION["CODE"] == LEGKOVYE) $descr[] = "легковые";
                elseif ($PRODUCT_SECTION["CODE"] == GRUZOVYE) $descr[] = "грузовые";
                elseif ($PRODUCT_SECTION["CODE"] == MOTO) $descr[] = "мото";

                $descr[] = "шины";
                $descr[] = $SHIRINA . "/" . $VYSOTA;
                $descr[] = $DIAMETR;
                $descr[] = $ALT_NAME;
                $descr[] = "фирмы " . $MARKA;

                /**
                 * Чтобы система смогла соотнести ваше предложение с карточкой модели на Яндекс.Маркете, укажите в наименовании предложения (в том же порядке):
                  Название модели. //Excellence [пробел]
                  Ширину профиля шины. //275 [слеш]
                  Серию шины (высоту профиля). //30 [пробел]
                  Диаметр шины. //R18 [пробел]
                  Индекс нагрузки. //102 [без пробела]
                  Максимальную скорость (индекс). //Y [пробел]
                  Наличие технологии RunFlat (если предусмотрено). //RunFlat [пробел]
                  Наличие шипов (если предусмотрено). //шип

                  Внимание.
                  Необходимо всегда указывать наличие технологии RunFlat, даже если аналогичного типоразмера без технологии RunFlat не существует.
                  Необходимо всегда указывать наличие шипов, даже если модели с аналогичным типоразмером без шипов не существует.
                 */
                //рассчитываем индекс нагрузки. Возможно несколько значений, разделенных слешем
                $indexNagruzki = "";

                if (!empty($NAGRUZKA))
                {
                    $arNagruzka = explode("/", $NAGRUZKA);

                    foreach ($arNagruzka as $i => $value)
                    {
                        $indexNagruzki .= self::getNagruzkaIndex($value);
                        if (count($arNagruzka) > 1 && $i < count($arNagruzka) && !empty($indexNagruzki))
                        {
                            $indexNagruzki .= "/";
                        }
                    }
                }

                //рассчитываем индекс скорости
                $indexSpeed = self::getSpeedIndex($SKOROST);

                $indexes = "";

                if (!empty($indexNagruzki) && !empty($indexSpeed))
                {
                    $indexes = " " . $indexNagruzki . $indexSpeed;
                }

                $RUN_FLAT = $RUN_FLAT == "true" ? " RunFlat" : "";
                $SHIPY    = $SHIPY == 'Шипы' ? " шип" : "";

                $offer['model'] = trim($ALT_NAME)
                        . " " . $SHIRINA . "/" . $VYSOTA
                        . " " . $DIAMETR
                        . $indexes
                        . $RUN_FLAT
                        . $SHIPY;

                $offer['description'] = htmlspecialcharsbx(mb_ucfirst(trim(implode(" ", $descr))));
            }
            elseif ($IBLOCK_ID == DISCS_IB)
            {
                $MARKA = htmlspecialcharsbx(trim($arProps[DISKI_MARKA]['VALUE']));
                $MODEL = htmlspecialcharsbx(trim($arProps[DISKI_MODEL]['VALUE']));

                if (empty($MARKA) || empty($MODEL)) continue;

                $DIAMETR   = $arProps[DIAMETR]['VALUE'];
                $SHIRINA   = $arProps[SHIRINA_DISKA]['VALUE'];
                $VYLET     = $arProps[VYLET]['VALUE'];
                $KREPLENIE = $arProps[KREPLENIEDISKA]['VALUE'];
                $DIA       = $arProps[DIA]['VALUE'];
                $COLOR     = $arProps['TSVET']['VALUE'];

                $RADIUS = str_replace("R", "", $DIAMETR);

                $matches = array();
                preg_match_all('/(?P<hc>[0-9]{1,2})\*(?P<hd>[0-9]{1,3})(\/(?P<hd2>[0-9]+))?/', $KREPLENIE, $matches);

                $HOLES_DIAMETR = $matches["hd"][0];
                $HOLES_COUNT   = $matches["hc"][0];

                $DISK_NAME = \CCatalogExt::getDiskName($SHIRINA, $RADIUS, $HOLES_COUNT, $HOLES_DIAMETR, $DIA, $VYLET);

                $offer['param'][] = array('name' => 'Диаметр обода (D)', 'unit' => "дюйм", 'value' => $RADIUS);
                $offer['param'][] = array('name' => 'Диаметр расположения отверстий', 'value' => $HOLES_DIAMETR);
                $offer['param'][] = array('name' => 'Количество крепежных отверстий', 'value' => $HOLES_COUNT);
                $offer['param'][] = array('name' => 'Диаметр центрального отверстия (DIA)', 'unit' => "мм", 'value' => $DIA);
                $offer['param'][] = array('name' => 'Ширина обода (J)', 'value' => $SHIRINA);
                $offer['param'][] = array('name' => 'Вылет (ET)', 'unit' => "мм", 'value' => $VYLET);

                if ($PRODUCT_SECTION["CODE"] == DISCS_LIGHT)
                        $offer['param'][] = array('name' => 'Материал', 'value' => "легкий сплав");
                elseif ($PRODUCT_SECTION["CODE"] == DISCS_STEEL)
                        $offer['param'][] = array('name' => 'Материал', 'value' => "сталь");

                $ALT_NAME = /* trim($MARKA) . " " . */ trim($MODEL) . " "
                        . $DISK_NAME . " "
                        . trim($COLOR);

                //$offer['name']   = $PRODUCT_NAME;
                $offer['vendor'] = $MARKA;
                $offer['model']  = trim($ALT_NAME);

                $descr = array();

                if ($PRODUCT_SECTION["CODE"] == DISCS_LIGHT) $descr[] = "легкосплавные";
                elseif ($PRODUCT_SECTION["CODE"] == DISCS_STEEL) $descr[] = "стальные";

                $descr[] = "диски";
                $descr[] = $DISK_NAME;
                if (!empty($COLOR)) $descr[] = "цвета " . $COLOR;
                $descr[] = "фирмы " . $MARKA;

                $offer['description'] = htmlspecialcharsbx(trim(implode(" ", $descr)));
            }
            else
            {
                continue;
            }

            $arOffers[] = array(
                'attributes' => $attributes,
                'offer'      => $offer,
            );
        }
    }

    public static function getSpeedIndex($value)
    {
        if (!is_numeric($value)) return null;

        $speed = intval($value);

        if (empty($speed)) return null;
        elseif ($speed <= 80) return "F";
        elseif ($speed > 80 && $speed <= 90) return "F";
        elseif ($speed > 90 && $speed <= 100) return "G";
        elseif ($speed > 100 && $speed <= 110) return "K";
        elseif ($speed > 110 && $speed <= 120) return "L";
        elseif ($speed > 120 && $speed <= 130) return "M";
        elseif ($speed > 130 && $speed <= 140) return "N";
        elseif ($speed > 140 && $speed <= 150) return "P";
        elseif ($speed > 150 && $speed <= 160) return "Q";
        elseif ($speed > 160 && $speed <= 170) return "R";
        elseif ($speed > 170 && $speed <= 180) return "S";
        elseif ($speed > 180 && $speed <= 190) return "T";
        elseif ($speed > 190 && $speed <= 210) return "H";
        elseif ($speed > 210 && $speed <= 240) return "V";
        elseif ($speed > 240 && $speed <= 270) return "W";
        elseif ($speed > 270) return "Y";
    }

    public static function getNagruzkaIndex($value)
    {
        if (!is_numeric($value)) return null;

        $nagruzka = intval($value);

        if (empty($nagruzka)) return null;
        elseif ($nagruzka <= 190) return 50;
        elseif ($nagruzka > 190 && $nagruzka <= 195) return 51;
        elseif ($nagruzka > 195 && $nagruzka <= 200) return 52;
        elseif ($nagruzka > 200 && $nagruzka <= 206) return 53;
        elseif ($nagruzka > 206 && $nagruzka <= 212) return 54;
        elseif ($nagruzka > 212 && $nagruzka <= 218) return 55;
        elseif ($nagruzka > 218 && $nagruzka <= 224) return 56;
        elseif ($nagruzka > 224 && $nagruzka <= 230) return 57;
        elseif ($nagruzka > 230 && $nagruzka <= 236) return 58;
        elseif ($nagruzka > 236 && $nagruzka <= 243) return 59;

        elseif ($nagruzka > 243 && $nagruzka <= 250) return 60;
        elseif ($nagruzka > 250 && $nagruzka <= 257) return 61;
        elseif ($nagruzka > 257 && $nagruzka <= 265) return 62;
        elseif ($nagruzka > 265 && $nagruzka <= 272) return 63;
        elseif ($nagruzka > 272 && $nagruzka <= 280) return 64;
        elseif ($nagruzka > 280 && $nagruzka <= 290) return 65;
        elseif ($nagruzka > 290 && $nagruzka <= 300) return 66;
        elseif ($nagruzka > 300 && $nagruzka <= 307) return 67;
        elseif ($nagruzka > 307 && $nagruzka <= 315) return 68;
        elseif ($nagruzka > 315 && $nagruzka <= 325) return 69;

        elseif ($nagruzka > 325 && $nagruzka <= 335) return 70;
        elseif ($nagruzka > 335 && $nagruzka <= 345) return 71;
        elseif ($nagruzka > 345 && $nagruzka <= 355) return 72;
        elseif ($nagruzka > 355 && $nagruzka <= 365) return 73;
        elseif ($nagruzka > 365 && $nagruzka <= 375) return 74;
        elseif ($nagruzka > 375 && $nagruzka <= 387) return 75;
        elseif ($nagruzka > 387 && $nagruzka <= 400) return 76;
        elseif ($nagruzka > 400 && $nagruzka <= 412) return 77;
        elseif ($nagruzka > 412 && $nagruzka <= 425) return 78;
        elseif ($nagruzka > 425 && $nagruzka <= 437) return 79;

        elseif ($nagruzka > 437 && $nagruzka <= 450) return 80;
        elseif ($nagruzka > 450 && $nagruzka <= 462) return 81;
        elseif ($nagruzka > 462 && $nagruzka <= 475) return 82;
        elseif ($nagruzka > 475 && $nagruzka <= 487) return 83;
        elseif ($nagruzka > 487 && $nagruzka <= 500) return 84;
        elseif ($nagruzka > 500 && $nagruzka <= 515) return 85;
        elseif ($nagruzka > 515 && $nagruzka <= 530) return 86;
        elseif ($nagruzka > 530 && $nagruzka <= 545) return 87;
        elseif ($nagruzka > 545 && $nagruzka <= 560) return 88;
        elseif ($nagruzka > 560 && $nagruzka <= 580) return 89;

        elseif ($nagruzka > 580 && $nagruzka <= 600) return 90;
        elseif ($nagruzka > 600 && $nagruzka <= 615) return 91;
        elseif ($nagruzka > 615 && $nagruzka <= 630) return 92;
        elseif ($nagruzka > 630 && $nagruzka <= 650) return 93;
        elseif ($nagruzka > 650 && $nagruzka <= 670) return 94;
        elseif ($nagruzka > 670 && $nagruzka <= 690) return 95;
        elseif ($nagruzka > 690 && $nagruzka <= 710) return 96;
        elseif ($nagruzka > 710 && $nagruzka <= 730) return 97;
        elseif ($nagruzka > 730 && $nagruzka <= 750) return 98;
        elseif ($nagruzka > 750 && $nagruzka <= 775) return 99;

        elseif ($nagruzka > 775 && $nagruzka <= 800) return 100;
        elseif ($nagruzka > 800 && $nagruzka <= 825) return 101;
        elseif ($nagruzka > 825 && $nagruzka <= 850) return 102;
        elseif ($nagruzka > 850 && $nagruzka <= 875) return 103;
        elseif ($nagruzka > 875 && $nagruzka <= 900) return 104;
        elseif ($nagruzka > 900 && $nagruzka <= 925) return 105;
        elseif ($nagruzka > 925 && $nagruzka <= 950) return 106;
        elseif ($nagruzka > 950 && $nagruzka <= 975) return 107;
        elseif ($nagruzka > 975 && $nagruzka <= 1000) return 108;
        elseif ($nagruzka > 1000 && $nagruzka <= 1030) return 109;

        elseif ($nagruzka > 1030 && $nagruzka <= 1060) return 110;
        elseif ($nagruzka > 1060 && $nagruzka <= 1090) return 111;
        elseif ($nagruzka > 1090 && $nagruzka <= 1120) return 112;
        elseif ($nagruzka > 1120 && $nagruzka <= 1150) return 113;
        elseif ($nagruzka > 1150 && $nagruzka <= 1180) return 114;
        elseif ($nagruzka > 1180 && $nagruzka <= 1215) return 115;
        elseif ($nagruzka > 1215 && $nagruzka <= 1250) return 116;
        elseif ($nagruzka > 1250 && $nagruzka <= 1285) return 117;
        elseif ($nagruzka > 1285 && $nagruzka <= 1320) return 118;
        elseif ($nagruzka > 1320 && $nagruzka <= 1360) return 119;
        elseif ($nagruzka > 1360 && $nagruzka <= 1400) return 120;

        elseif ($nagruzka > 1400 && $nagruzka <= 1450) return 121;
        elseif ($nagruzka > 1450 && $nagruzka <= 1500) return 122;
        elseif ($nagruzka > 1500 && $nagruzka <= 1550) return 123;
        elseif ($nagruzka > 1550 && $nagruzka <= 1600) return 124;
        elseif ($nagruzka > 1600 && $nagruzka <= 1650) return 125;
        elseif ($nagruzka > 1650 && $nagruzka <= 1700) return 126;
        elseif ($nagruzka > 1700 && $nagruzka <= 1750) return 127;
        elseif ($nagruzka > 1750 && $nagruzka <= 1800) return 128;
        elseif ($nagruzka > 1800 && $nagruzka <= 1850) return 129;
        elseif ($nagruzka > 1850) return 130;
    }

    public static function printCategories(&$yml, $categories)
    {
        foreach ($categories as $category)
        {
            if (isset($category['items']) && is_array($category['items']))
            {
                self::printCategories($yml, $category['items']);
            }
            else
            {
                $parent_id     = $category['parent_id'];
                $category_id   = $category['id'];
                $category_name = htmlspecialcharsbx($category['name']);

                $yml .= '<category id="' . $category_id . '"';

                if (!empty($parent_id)) $yml .= ' parentId="' . $parent_id . '"';

                $yml .= '>' . $category_name . '</category>' . "\n";
            }
        }
    }

    public static function printOffers(&$yml, $arOffers)
    {

        foreach ($arOffers as $offer)
        {

            $attr  = $offer['attributes'];
            $offer = $offer['offer'];

            $yml .= '<offer'
                    . ' id="' . $attr['id'] . '"'
                    . ' available="' . $attr['available'] . '"'
                    . ' type="' . $attr['type'] . '"'
                    . '>' . "\n";

            foreach ($offer as $key => $data)
            {
                if ($key == 'param')
                {
                    foreach ($data as $param)
                    {
                        $yml .= '<param name="' . $param['name'] . '"';

                        if (!empty($param['unit'])) $yml .= ' unit="' . htmlspecialcharsbx($param['unit']) . '"';

                        $yml .= '>' . htmlspecialcharsbx($param['value']) . '</param>' . "\n";
                    }
                }
                elseif (!is_array($data))
                {
                    if ($data === true) $data = 'true';
                    elseif ($data === false) $data = 'false';

                    $yml .= '<' . $key . '>' . $data . '</' . $key . '>' . "\n";
                }
                else
                {
                    $yml .= '<' . $key . '>' . "\n";

                    foreach ($data as $data_key => $data_values)
                    {
                        $yml .= '<' . $data_key;

                        foreach ($data_values as $data_value_key => $data_value_value)
                        {
                            $yml .= ' ' . $data_value_key . '="' . htmlspecialcharsbx($data_value_value) . '"';
                        }

                        $yml .= '/>' . "\n";
                    }

                    $yml .= '</' . $key . '>' . "\n";
                }
            }

            $yml .= '</offer>' . "\n";
        }
    }

    public static function makeYML($arOffers)
    {
        $yml = "";

        $yml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
        $yml .= '<yml_catalog date="' . date("Y-m-d H:i") . '">' . "\n";
        $yml .= '<shop>' . "\n";

        $yml .= '<name>' . htmlspecialcharsbx(\COption::GetOptionString('main', 'site_name', '')) . "</name>" . "\n";
        $yml .= '<company>' . htmlspecialcharsbx(\COption::GetOptionString('main', 'site_name', '')) . "</company>" . "\n";
        $yml .= '<url>' . SITE_PROTOCOL . SITE_URL . "</url>" . "\n";
        $yml .= '<platform>1C-Bitrix</platform>' . "\n";

        $yml .= '<currencies>' . "\n";
        $yml .= '<currency id="RUB" rate="1"/>' . "\n";
        $yml .= '</currencies>' . "\n";

        $yml .= '<categories>' . "\n";
        self::printCategories($yml, self::getCategories());
        $yml .= '</categories>' . "\n";

        $yml .= '<delivery-options>' . "\n";
        $yml .= '<option cost="500" days="7-9" order-before="18"/>' . "\n";
        $yml .= '</delivery-options>' . "\n";

        $yml .= '<offers>' . "\n";
        self::printOffers($yml, $arOffers);
        $yml .= '</offers>' . "\n";

        $yml .= '</shop>' . "\n";
        $yml .= '</yml_catalog>' . "\n";

        $fname = $_SERVER['DOCUMENT_ROOT'] . "/logans/feed/" . md5(time()) . ".yml";

        file_put_contents($fname, $yml);

        return $fname;
    }

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
