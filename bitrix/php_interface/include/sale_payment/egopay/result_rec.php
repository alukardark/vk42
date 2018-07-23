<?
$path = dirname(__FILE__);

if (($pos = strrpos($path, 'bitrix')) !== false) {
    $path = substr($path, 0, $pos - 1);
    $_SERVER['DOCUMENT_ROOT'] = $path;
    $DOCUMENT_ROOT = $path;
} else {
    die('Не могу найти установленный Bitrix');
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$APPLICATION->RestartBuffer();
CModule::IncludeModule('egopay');
CEgoPay::checkOrders();
