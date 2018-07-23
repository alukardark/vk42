<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// подключаем файл локализации
include(GetLangFileName(dirname(__FILE__) . '/', '/egopay.php'));

CModule::IncludeModule('sale');
$statusesObj = CSaleStatus::GetList(array(), array('LID' => 'ru'), false, false, array('ID', 'NAME', 'LID'));
$statuses = array();
$statusToRemove = array('F', 'N');
while ($s = $statusesObj->Fetch()) {
    if (!in_array($s['ID'], $statusToRemove)) {
        $statuses[$s['ID']] = $s;
    }
}

$itemTypes = array(
    '' => array('NAME' => GetMessage('Not set')),
    'contract' => array('NAME' => GetMessage('Contract')),
    'service' => array('NAME' => GetMessage('Service')),
);

$psTitle = GetMessage('EGOPAY_TITLE');
$psDescription = GetMessage('EGOPAY_DESCR');

$arPSCorrespondence = array(
        'SHOP_ID' => array(
                'NAME' => GetMessage('SHOP_ID'),
                'DESCR' => GetMessage('SHOP_ID_DESCR'),
                'VALUE' => '',
                'TYPE' => ''
            ),
        'SHOP_LOGIN' => array(
                'NAME' => GetMessage('SHOP_LOGIN'),
                'DESCR' => GetMessage('SHOP_LOGIN_DESCR'),
                'VALUE' => '',
                'TYPE' => ''
            ),
        'SHOP_PASSWORD' => array(
                'NAME' => GetMessage('SHOP_PASSWORD'),
                'DESCR' => GetMessage('SHOP_PASSWORD_DESCR'),
                'VALUE' => '',
                'TYPE' => ''
            ),
        'SHOP_URL' => array(
                'NAME' => GetMessage('SHOP_URL'),
                'DESCR' => GetMessage('SHOP_URL_DESCR'),
                'VALUE' => '',
                'TYPE' => ''
            ),
        'ORDER_ID' => array(
                'NAME' => GetMessage('ORDER_ID'),
                'DESCR' => GetMessage('ORDER_ID_DESCR'),
                'VALUE' => 'ID',
                'TYPE' => 'ORDER'
            ),
        'SHOULD_PAY' => array(
                'NAME' => GetMessage('SHOULD_PAY'),
                'DESCR' => GetMessage('SHOULD_PAY_DESCR'),
                'VALUE' => 'SHOULD_PAY',
                'TYPE' => 'ORDER'
            ),
        'USER_ID' => array(
                'NAME' => GetMessage('USER_ID'),
                'DESCR' => GetMessage('USER_ID_DESCR'),
                'VALUE' => 'USER_ID',
                'TYPE' => 'ORDER'
            ),
        'USER_NAME' => array(
                'NAME' => GetMessage('USER_FNAME'),
                'DESCR' => GetMessage('USER_FNAME_DESCR'),
                'VALUE' => 'FIRST_NAME',
                'TYPE' => 'PROPERTY'
            ),
        'USER_EMAIL' => array(
                'NAME' => GetMessage('USER_EMAIL'),
                'DESCR' => GetMessage('USER_EMAIL_DESCR'),
                'VALUE' => 'EMAIL',
                'TYPE' => 'PROPERTY'
            ),
        'USER_PHONE' => array(
                'NAME' => GetMessage('USER_PHONE'),
                'DESCR' => GetMessage('USER_PHONE_DESCR'),
                'VALUE' => 'PHONE',
                'TYPE' => 'PROPERTY'
            ),
        'PAY_STATUS' => array(
                'NAME' => GetMessage('PAY_STATUS'),
                'DESCR' => GetMessage('PAY_STATUS_DESCR'),
                'VALUE' => $statuses,
                'TYPE' => 'SELECT'
            ),
        'COMMENT' => array(
                'NAME' => GetMessage('COMMENT'),
                'DESCR' => GetMessage('COMMENT_DESCR'),
                'VALUE' => 'COMMENT',
                'TYPE' => 'PROPERTY'
            ),

        'ITEM_TYPE' => array(
                'NAME' => GetMessage('ITEM_TYPE'),
                'DESCR' => GetMessage('ITEM_DESC_DESCR'),
                'VALUE' => $itemTypes,
                'TYPE' => 'SELECT'
            ),
        'ITEM_DESC' => array(
                'NAME' => GetMessage('ITEM_DESC'),
                'DESCR' => GetMessage('ITEM_DESC_DESCR'),
                'VALUE' => '',
                'TYPE' => ''
            ),
    );