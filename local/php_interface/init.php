<?php

use Bitrix\Main\Loader;

if ($_REQUEST['kk'] == 'kk' || $_REQUEST['kk'] == 'kkk')
{
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', 1);
}


date_default_timezone_set("Asia/Novokuznetsk");

Loader::includeModule("main");
Loader::includeModule("iblock");
Loader::includeModule("form");
Loader::includeModule("catalog");
Loader::includeModule("sale");
Loader::includeModule("highloadblock");
Loader::includeModule("ws.projectsettings");


require_once "include/functions.php";

require_once "globals.php";


require_once "include/handlers.php";
require_once "include/validators.php";
require_once "include/ie.php";
//require_once "include/youtube.php";

require_once "classes/axi.php";
require_once "classes/pic.php";
//require_once "classes/db.php";
require_once "classes/user.php";
require_once "classes/catalog.php";
require_once "classes/filter.php";
require_once "classes/basket.php";
require_once "classes/order.php";
require_once "classes/sale.php";
require_once "classes/services.php";
require_once "classes/form.php";
require_once "classes/curl.php";
require_once "classes/oauth.php";
require_once "classes/xml.php";

require_once "include/agents.php";


//require_once "include/custom_mail.php";
//require_once "include/custom_sms.php";