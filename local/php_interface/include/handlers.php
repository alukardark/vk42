<?php

/**
 * Добавляем классы в автозагрузку
 */
\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\Axi\Handlers\Main'    => '/local/php_interface/classes/handlers/main.php',
    '\Axi\Handlers\Search'  => '/local/php_interface/classes/handlers/search.php',
    '\Axi\Handlers\Catalog' => '/local/php_interface/classes/handlers/catalog.php',
    '\Axi\Handlers\Sale'    => '/local/php_interface/classes/handlers/sale.php',
    '\Axi\Handlers\Form'    => '/local/php_interface/classes/handlers/form.php',
    '\Axi\Handlers\IBlock'  => '/local/php_interface/classes/handlers/iblock.php',
));

/**
 * Регистрируем обработчики событий
 */
$em = \Bitrix\Main\EventManager::getInstance();

//$em->addEventHandler('main', 'OnEndBufferContent', array('\Axi\Handlers\Main', 'OnEndBufferContent'));
$em->addEventHandler('main', 'OnBuildGlobalMenu', array('\Axi\Handlers\Main', 'OnBuildGlobalMenu'));
$em->addEventHandler('main', 'OnBeforeUserUpdate', array('\Axi\Handlers\Main', 'OnBeforeUserUpdate'));
$em->addEventHandler('main', 'OnBeforeUserAdd', array('\Axi\Handlers\Main', 'OnBeforeUserAdd'));
//$em->addEventHandler('main', 'OnBeforeUserRegister', array('\Axi\Handlers\Main', 'OnBeforeUserRegister'));
//$em->addEventHandler('main', 'OnBeforeUserSimpleRegister', array('\Axi\Handlers\Main', 'OnBeforeUserSimpleRegister'));
$em->addEventHandler('main', 'OnAfterUserAdd', array('\Axi\Handlers\Main', 'OnAfterUserAdd'));
//$em->addEventHandler('main', 'OnAdminTabControlBegin', array('\Axi\Handlers\Main', 'OnAdminTabControlBegin'));
//$em->addEventHandler('main', 'OnBeforeProlog', array('\Axi\Handlers\Main', 'OnBeforeProlog'));
$em->addEventHandler('iblock', 'OnBeforeIBlockUpdate', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockUpdate'));
$em->addEventHandler('iblock', 'OnBeforeIBlockElementAdd', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockElementAdd'));
$em->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', array('\Axi\Handlers\IBlock', 'OnBeforeIBlockElementUpdate'));
$em->addEventHandler('iblock', 'OnAfterIBlockElementAdd', array('\Axi\Handlers\IBlock', 'OnAfterIBlockElementAdd'));
$em->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', array('\Axi\Handlers\IBlock', 'OnAfterIBlockElementUpdate'));
//$em->addEventHandler('catalog', 'OnBeforeCatalogImport1C', array('\Axi\Handlers\Catalog', 'OnBeforeCatalogImport1C'));
$em->addEventHandler('catalog', 'OnSuccessCatalogImport1C', array('\Axi\Handlers\Catalog', 'OnSuccessCatalogImport1C'));
//$em->addEventHandler('search', 'BeforeIndex', array('\Axi\Handlers\Search', 'beforeIndex'));
//$em->addEventHandler('search', 'OnBeforeIndexUpdate', array('\Axi\Handlers\Search', 'beforeIndexUpdate'));
//$em->addEventHandler('search', 'OnAfterIndexAdd', array('\Axi\Handlers\Search', 'beforeIndexUpdate'));

$em->addEventHandler('sale', 'OnSaleComponentOrderProperties', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderProperties'));
$em->addEventHandler('sale', 'OnSaleComponentOrderCreated', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderCreated'));
$em->addEventHandler('sale', 'OnSaleComponentOrderOneStepDelivery', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderOneStepDelivery'));
//$em->addEventHandler('sale', 'OnBeforeOrderUpdate', array('\Axi\Handlers\Sale', 'OnBeforeOrderUpdate'));
//$em->addEventHandler('sale', 'OnSaleOrderBeforeSaved', array('\Axi\Handlers\Sale', 'OnSaleOrderBeforeSaved'));
//$em->addEventHandler('sale', 'OnSaleComponentOrderOneStepOrderProps', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderOneStepOrderProps'));
//$em->addEventHandler('sale', 'OnSaleComponentOrderOneStepProcess', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderOneStepProcess'));
$em->addEventHandler('sale', 'OnSaleComponentOrderOneStepFinal', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderOneStepFinal'));
//$em->addEventHandler('sale', 'OnBeforeSaleOrderSetField', array('\Axi\Handlers\Sale', 'OnBeforeSaleOrderSetField'));
//$em->addEventHandler('sale', 'OnSaleOrderSetField', array('\Axi\Handlers\Sale', 'OnSaleOrderSetField'));
//$em->addEventHandler('sale', 'OnSaleBeforeCancelOrder', array('\Axi\Handlers\Sale', 'OnSaleBeforeCancelOrder'));
//$em->addEventHandler('sale', 'onSaleDeliveryRestrictionsClassNamesBuildList', array('\Axi\Handlers\Sale', 'onSaleDeliveryRestrictionsClassNamesBuildList'));
$em->addEventHandler('sale', 'onSalePaySystemRestrictionsClassNamesBuildList', array('\Axi\Handlers\Sale', 'onSalePaySystemRestrictionsClassNamesBuildList'));
//$em->addEventHandler('sale', 'OnSaleComponentOrderShowAjaxAnswer', array('\Axi\Handlers\Sale', 'OnSaleComponentOrderShowAjaxAnswer'));

$em->addEventHandler('form', 'onAfterResultAdd', array('\Axi\Handlers\Form', 'onAfterResultAdd'));
$em->addEventHandler('form', 'onBeforeResultDelete', array('\Axi\Handlers\Form', 'onBeforeResultDelete'));
