<?

define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/logans/" . date("Y_m_d") . ".log");

//define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . date("Y_m_d") . ".log");

$arSite = getSiteInfo();
define("SITE_NAME", $arSite['SITE_NAME']);
define("SITE_URL", $arSite['SERVER_NAME']);
define("SITE_TEST", SITE_URL == "vk.axiomatest.ru");
define("SITE_PROTOCOL", SITE_TEST ? "http://" : "https://");

define("BONUSES_ENABLE", true);
define("SHOW_ORDER_NUMBER", true);
define("CHECK_UNIQUE_ENABLE_ON_ORDER", 0); //при оформлении заказа будет првоеряться, что тел и почта не принадлжат разным юзерам
define("CHECK_UNIQUE_ENABLE_ON_REG", 1); //при оформлении заказа будет првоеряться, что тел и почта не принадлжат разным юзерам
define("PERSONAL_ENABLE", true);
define("FILTER_BY_AUTO_ENABLE", true);
//define("HIDE_NULL", !SITE_TEST); //скрывать недоступные товары
define("HIDE_NULL", true); //скрывать недоступные товары

define("VK_SESSID", md5(date("d..m..", time()) . 'sessid')); //идентификатор сессии (имя переменной)
define("VK_PREFIX", "CS");
define("ENCRYPTION_KEY", "!@VK#$%^&42*");

define("CATALOG_PRICE_ID", basePrice());
define("CATALOG_PRICE", "CATALOG_PRICE_" . CATALOG_PRICE_ID);
define("CATALOG_PRICE_NAME", basePrice("NAME"));

define("RETAIL_PRICE_NAME", "Розничный");
define("RETAIL_PRICE_ID", getPrice(array("NAME" => RETAIL_PRICE_NAME), "ID"));
define("RETAIL_PRICE", "CATALOG_PRICE_" . RETAIL_PRICE_ID);


define("USE_DISCOUNT", true);
define("SORTPROP", "INDEKS_SORTIROVKI_DLYA_SAYTA");

define("BASE_CURRENCY", 'RUB');
define("PUBL_CURRENCY", 'RUT'); //используется только для вывода цены со знаком рубля ₽
define("MAX_QUANTITY", 1000); //макс. кол-во товара в корзине
define("MAX_DISCOUNT", 0); //коэффициент, равный максимальной скидке на сайте

//инфоблоки
define("PROMOSLIDER_IB", 4);
define("BANNERS_IB", 5);
define("SOCNETS_IB", 6);

//Инфоблоки каталоги (id)
define("TIRES_IB", 11);
define("OILS_IB", SITE_TEST ? 21 : 21);
define("AKB_IB", SITE_TEST ? 26 : 26);
define("DISCS_IB", 27);
define("MISC_IB", 28);

// Инфоблоки каталоги (code)
// @todo дописать коды остальных каталожных инфоблоков
define("TIRES_IB_CODE", 'katalog');

//инфоблоки базы автомобилей и шин
define("TX_CARS_IB", 7);
define("TX_TYRES_IB", 13);
define("TX_DISKS_IB", 9);

//другие инфоблоки
define("ARTICLES_IB", 1);
define("SETTINGS_FORMS", 15);
define("ACTIONS_IB", 18);
define("USLUGI_IB", SITE_TEST ? 23 : 23);
define("NEWS_TAGS_IB", SITE_TEST ? 19 : 19);
define("NEWS_IB", SITE_TEST ? 20 : 20);

//highloadblock
define("ACTIONS_HB", 2);

//пути каталоги
define("PATH_CATALOG", '/katalog/');
define("PATH_OILS", '/masla/');
define("PATH_AKB", '/akkumulyatory/');
define("PATH_DISCS", '/diski/');
define("PATH_MISC", '/prochie_tovary/');

//пути kabinet
define("PATH_BASKET", '/kabinet/korzina/');
define("PATH_ORDER", '/kabinet/zakaz/');
define("PATH_ORDER_DETAIL", '/kabinet/zakaz/detail/');
define("PATH_PAYMENT", "/kabinet/oplata/");
define("PATH_AUTH", "/kabinet/auth/");
define("PATH_REGISTER", "/kabinet/auth/register.php");
define("PATH_RECOVERY", "/kabinet/auth/recovery.php");
define("PATH_CHANGE", "/kabinet/auth/change.php");
define("PATH_LOGOUT", "/kabinet/auth/exit.php");

define("PATH_PERSONAL", "/kabinet/personal/");
define("PATH_PERSONAL_ACCOUNT", "/kabinet/personal/account/");
define("PATH_PERSONAL_PASSWORD", "/kabinet/personal/password/");
define("PATH_PERSONAL_SUBSCRIBES", "/kabinet/personal/subscribes/");
define("PATH_PERSONAL_ADDCARD", "/kabinet/personal/addcard/");
define("PATH_PERSONAL_ORDERS", "/kabinet/personal/orders/");

//id типов плательщиков
define("FIZ_LICO", 1);
define("UR_LICO", 2);

//ID платежных систем
define("EPAYMENT_ID", 10); //online
define("BPAYMENT_ID", 8); //bank
define("CPAYMENT_ID", 1); //cash
define("IPAYMENT_ID", SITE_TEST ? 11 : 11); //inner
define("KPAYMENT_ID", SITE_TEST ? 12 : 12); //kredit

//ID служб доставки
define("CDELIVERY_ID", 2); //courier
define("PDELIVERY_ID", 3); //pickup
define("KDELIVERY_ID", 5); //kemerovo courier
define("TKDELIVERY_ID", 6); //another city delivery

define("ANOTHER_CITY_CODE", "anotherCity");
define("RESTRICTED_IBLOCKS_FOR_ANOTER_CITY", (array) \WS_PSettings::getFieldValue("RESTRICTED_IBLOCKS_FOR_ANOTER_CITY", false));

//ID свойств заказа "предоплата" для физ и юр. лиц
define("PREPAY_PROP_ID_FIZ_LICO", 22);
define("PREPAY_PROP_ID_UR_LICO", 23);

//статусы заказа
define("STATUS_NEW", 'N'); //Принят, ожидается оплата
define("STATUS_PAYING", 'E'); //Оплачивается
define("STATUS_WAIT_PREPAY", 'M'); //Принят, ожидается предооплата
define("STATUS_PREPAYED", 'H'); //Частично оплачен, формируется к отправке
define("STATUS_PAYED", 'P'); //Оплачен, формируется к отправке
define("STATUS_ACCEPTED", 'C'); //Принят, формируется к отправке
define("STATUS_FIN", 'F'); //Выполнен
define("STATUS_KREDIT_NEW", 'K'); //кредит - новый заказ (ожидание решения)
define("STATUS_KREDIT_ACCEPT", 'A'); //кредит - добро
define("STATUS_KREDIT_DECLINE", 'D'); //кредит - отказ
//
//коды разделов инфоблока шин
define("LEGKOVYE", 'legkovye');
define("GRUZOVYE", 'gruzovye');
define("MOTO", 'moto');
//
//коды разделов инфоблока СМ
define("MASLA", 'motornye_masla');
define("TRANSM", 'transmissionnye_masla');
define("FLUIDS", 'tekhnicheskie_zhidkosti');

//коды свойств шины
define("RUN_FLAT", 'RUN_FLAT');
//коды свойств для фильтра по размеру
define("SHIRINA", 'SHIRINA_PROFILYA');
define("VYSOTA", 'VYSOTA_PROFILYA');
define("DIAMETR", 'DIAMETR');
define("SEZON", 'SEZON');
//коды свойств инфоблока шин для иконок снежинки и солнышка
define("SUMMER", 'Лето');
define("WINTER", 'Зима');

//коды свойства инфоблока шин для шильдиков акций
define("SALE", 'RASPRODAZHA');
define("SALE_DAY", 'TSENA_DNYA_SAYT');
define("BONUS", 'BONUS_SAYT');
define("HIT", 'KHIT_PRODAZH_SAYT');
define("AKTSIYA", 'AKTSII_NA_SAYTE');

//коды разделов инфоблока АКБ
define("AKB_AVTO", 'avtomobilnye_akkumulyatory');
define("AKB_MOTO", 'moto_akkumulyatory');

//коды разделов инфоблока ДИСКИ
define("DISCS_LIGHT", 'legkosplavnye_diski');
define("DISCS_STEEL", 'stalnye_diski');

//коды свойств дисков
define("DISKI_MARKA", 'DISKI_MARKA');
define("DISKI_MODEL", 'DISKI_MODEL');
define("KREPLENIEDISKA", 'KREPLENIEDISKA');
define("SHIRINADISKA", 'SHIRINA_DISKA');
define("DIAMETRDISKA", 'DISKI_DIAMETR');
define("DIA", 'DIA');
define("VYLET", 'VYLET');

//коды разделов инфоблока ПРОЧИЕ ТОВАРЫ
define("MISC_SVECHI", 'svechi_zazhiganiya');
define("MISC_KOLODKI", 'tormoznye_kolodki');
define("MISC_MISCGOODS", 'soputstvuyushchie_tovary');
define("MISC_KOSMETIKA", 'avtokosmetika');
define("MISC_LAMPY", 'avtolampy');
define("MISC_FILTRY", 'filtry');
define("MISC_SHETKI", 'shchetki_stekloochistiteley');

//коды свойств ПРОЧИЕ ТОВАРЫ
define("BREND_SOPUTSTVUYUSHCHIETOVARY", 'BREND_SOPUTSTVUYUSHCHIETOVARY');
define("BREND_AVTOLAMPY", 'BREND_AVTOLAMPY');
define("BREND_FILTRY", 'BREND_FILTRY');
define("BREND_SHCHETKISTEKLOOCHISTITELEY", 'BREND_SHCHETKISTEKLOOCHISTITELEY');
define("BREND_TORMOZNYEKOLODKI", 'BREND_TORMOZNYEKOLODKI');
define("BREND_SVECHI", 'BREND_SVECHI');
define("BREND_AVTOKOSMETIKA", 'BREND_AVTOKOSMETIKA');

//коды свойств СМ
define("SM_VYAZKOST", 'SM_VYAZKOST');
define("SM_PROIZVODITEL", 'SM_PROIZVODITEL');
define("SM_MARKA", 'SM_MARKA');
define("SM_TIP", 'SM_TIP');
define("SM_NAZNACHENIEDV", 'SM_NAZNACHENIEDV');
define("SM_NAZNACHENIE", 'SM_NAZNACHENIE');
define("SM_VIDMASLA", 'SM_VIDMASLA');
define("OZH_TIP", 'OZH_TIP');
define("OZH_TSVET", 'OZH_TSVET');
define("SM_API", 'SM_API');
define("SM_ACEA", 'SM_ACEA');
define("OBYEM", 'OBYEM');

//коды АКБ
define("AKB_EMKOST", 'AKB_EMKOST');
define("AKB_POLYARNOST", 'AKB_POLYARNOST');
define("AKB_PROIZVODITEL", 'AKB_PROIZVODITEL');
define("AKB_MODEL", 'AKB_MODEL');
define("AKB_DLINA", 'AKB_DLINA');
define("AKB_SHIRINA", 'AKB_SHIRINA');
define("AKB_VYSOTA", 'AKB_VYSOTA');

//id блога и группы блога для комментариев товаров
define("BLOG_ID", 1);
define("BLOG_GROUP_ID", 1);
define("BLOG_DEFAULT_AUTHOR_ID", 1);

//web-forms
define("FORM_BUY_ONE_CLICK", 2);
define("FORM_SUPPORT", 4);
define("FORM_HELP_AKB", 5);
define("FORM_DELIVERY_CALC", 6);
define("FORM_SERVICE_ENTRY", 7);

define("SHOW_HELP_AKB", 1);
define("SHOW_DELIVERY_CHECKBOX", 1);
define("SHOW_DELIVERY_CALC", 1);
define("SHOW_SERVICE_ENTRY", 0);

define("FORM_FAQ", 3); //id web-формы FAQ
define("FORM_FAQ_STATUS_PUBLIC", 4); //статус опубликованного результата веб-формы
