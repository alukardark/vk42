<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Автосервис-центр \"Континент шин\"");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Сеть сервис-центров «Континент шин» предлагает купить шины, масла и аккумуляторы, а также пройти обслуживание своего автомобиля. Полный перечень услуг, гарантия качества.");
$APPLICATION->SetPageProperty("title", "Автоcервис-центр «Континент шин» в Кемерово и Новокузнецке. Шины, диски, масла для автомобиля");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/index.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/filter.js");
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/index.css");

\Axi::GF("index/promo", "промоблок");
\Axi::GF("index/services", "услуги");
\Axi::GF("index/contacts", "контакты");

global $USER;

if (/* $USER->IsAdmin() && */ $_REQUEST["get"] == "test")
{
//    $arData = array(
//        "Date" => $_REQUEST["Date"]
//    );
//
//    $tt = \CURL::getReplayTest("GetCatalog", $arData, true, false, true, false, "Service");
//    
//    printra($tt);
//    $arCategories = array();
//    $arServices = array();
//
//    $arFilter = array('IBLOCK_ID' => 23, 'DEPTH_LEVEL' => 1, 'ACTIVE' => 'Y');
//    $arSelect = array('ID', 'NAME', 'CODE', 'EXTERNAL_ID');
//    $obList   = \CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
//    while ($arFetch  = $obList->Fetch())
//    {
//        $arCategories[] = $arFetch;
//    }
//    
//    $arFilter = array('IBLOCK_ID' => 23, 'ACTIVE' => 'Y');
//    $arSelect = array('ID', 'NAME', 'CODE', 'EXTERNAL_ID', 'IBLOCK_SECTION_ID', 'PROPERTY_PRICE', 'PROPERTY_IS_PRICE_EXACT', 'PROPERTY_STORES');
//    $obList   = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
//    while ($arFetch  = $obList->Fetch())
//    {
//        $arServices[] = $arFetch;
//    }
    //список ТСЦ выбранного города
    //$arTSC = \CServicesExt::getOperations('Кемерово');
    //printra($arTSC);
    //XML_ID этих ТСЦ
    ///$atTSCXMLIDs = array();
    //foreach ($arTSC as $TSC)
    {
        //$atTSCXMLIDs[] = $TSC["XML_ID"];
    }

    //ID категорий и услуг этих ТСЦ
    //$arServicesItems = \CServicesExt::getItemsTree($atTSCXMLIDs);
    //$arCategories    = $arServicesItems["SECTIONS"];
    //$arServices      = $arServicesItems["ELEMENTS"];

    $arOperations = \CServicesExt::getOperations('Новокузнецк');
    printra($arOperations);
}

if (/* $USER->IsAdmin() && */ $_REQUEST["get"] == "feed")
{
    $APPLICATION->RestartBuffer();
    \CXmlExt::start();
    die;
//
//    $products = \Bitrix\Catalog\ProductTable::getList(array(
//                //'select' => array('*'),
//                'filter' => array('ID' => [654123, 653215])
//            ))->fetchAll();
//
//
//    printra($products);
}

//if ($_REQUEST['dd'] == 'dd')
//{
//    $arUserFields = array(
//        "XML_ID" => "sdfgasdgasdfgfdg",
//    );
//
//    $arResult = $USER->Update(78334, $arUserFields);
//
//    printra($USER->LAST_ERROR);
//}
if ($_REQUEST['get'] == 'sm')
{
    $APPLICATION->RestartBuffer();
    \CXmlExt::generateSitemap();
    die;
}

if ($USER->IsAdmin() && $_REQUEST['get'] == 'doubles')
{
    $arUsersByEmail    = array();
    $arUsersByPhone    = array();
    $arUsersEmptyPhone = array();

    $obList = \CUser::GetList(
                    ($by     = "id"), ($order  = "desc"), array(), array()
    );

    while ($arFetch = $obList->Fetch())
    {
        //printra($arFetch);
        $ID    = $arFetch["ID"];
        $LOGIN = $arFetch["LOGIN"];
        $NAME  = $arFetch["NAME"] . " " . $arFetch["LAST_NAME"];
        $EMAIL = $arFetch["EMAIL"];
        $PHONE = fixPhoneNumber($arFetch["PERSONAL_PHONE"]);

        $arData = array($ID, $LOGIN, $NAME, $EMAIL, $arFetch["PERSONAL_PHONE"]);

        $arUsersByEmail[$EMAIL][] = $arData;
        $arUsersByPhone[$PHONE][] = $arData;

        if (empty($arFetch["PERSONAL_PHONE"])) $arUsersEmptyPhone[] = $arData;
    }

    foreach ($arUsersByEmail as $key => $arUsers)
    {
        if (count($arUsers) == 1) unset($arUsersByEmail[$key]);
    }

    foreach ($arUsersByPhone as $key => $arUsers)
    {
        if (count($arUsers) == 1) unset($arUsersByPhone[$key]);
    }

    $APPLICATION->RestartBuffer();

    echo '<pre>';

    echo "<hr/>С одинаковым email: " . count($arUsersByEmail) . '<hr/>';
    var_dump($arUsersByEmail);

    echo "<hr/>С одинаковым phone: " . count($arUsersByPhone) . '<hr/>';
    var_dump($arUsersByPhone);

    echo "<hr/>С пустым phone: " . count($arUsersEmptyPhone) . '<hr/>';
    var_dump($arUsersByPhone);

    die;
}


//if ($USER->IsAdmin() && $_REQUEST['get'] == 'doubles')
//{
//    $obList  = \CUser::GetList(
//                    ($by      = "id"), ($order   = "desc"), array(), array()
//    );
//    while ($arFetch = $obList->Fetch())
//    {
//        $ID = $arFetch["ID"];
//        if (empty($arFetch["PERSONAL_PHONE"])) continue;
//
//        $PHONE = fixPhoneNumber($arFetch["PERSONAL_PHONE"]);
//
//        $arUserFields = array(
//            "PERSONAL_MOBILE" => $PHONE,
//        );
//
//        $USER->Update($ID, $arUserFields);
//    }
//
//    die;
//}
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>