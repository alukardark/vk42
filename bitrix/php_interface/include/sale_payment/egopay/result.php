<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

set_time_limit(0);

// файл локализации
include(GetLangFileName(dirname(__FILE__) . '/', '/egopay.php'));
// модуль egopay
CModule::IncludeModule('egopay');

// массив с данными по магазину и заказу
$settings = array(
    'shop_id'    => CSalePaySystemAction::GetParamValue('SHOP_ID'),
    'shop_login' => CSalePaySystemAction::GetParamValue('SHOP_LOGIN'),
    'shop_pass'  => CSalePaySystemAction::GetParamValue('SHOP_PASSWORD'),
    'shop_url'   => CSalePaySystemAction::GetParamValue('SHOP_URL'),
    'order_id'   => IntVal($GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID']),
);

$iOrderId = $settings['order_id'];

$soapClient = CEgoPay::newSoap($settings);
$objStatus  = CEgoPay::getOrderStatus($settings['shop_id'], $iOrderId);

// получаем данные по заказу

$arOrder     = \CSaleOrder::GetByID($iOrderId);
$arProps     = \CSaleExt::getOrderPoperties($iOrderId);
$prePayKoeff = \CSaleExt::getOrderPrepayKoef($iOrderId);

try
{
    $info = $soapClient->get_status($objStatus);

    switch ($info->status)
    {
        //case 'acknowledged':
        //case 'not_acknowledged':
        case 'authorized':
            // заказ успешно оплачен
            CEgoPay::updatePaymentStatus($order, 'Y', $info->status, $info->status);

            $newStatus = $prePayKoeff > 0 && $prePayKoeff < 100 ? STATUS_PREPAYED : STATUS_PAYED;
            CSaleOrder::StatusOrder($iOrderId, $newStatus);
            break;

        default:
            CEgoPay::updatePaymentStatus($order, false, $info->status, $info->status);

            $newStatus = $prePayKoeff > 0 && $prePayKoeff < 100 ? STATUS_WAIT_PREPAY : STATUS_WAIT_PAY;
            CSaleOrder::StatusOrder($settings['order_id'], $newStatus);
            break;
    }
}
catch (SoapFault $fault)
{
    if ($fault->faultstring === 'INVALID_ORDER')
    {
        // заказ с таким номером не зарегистрирован
        CEgoPay::updatePaymentStatus($order, 'N', GetMessage('EGOPAY_ORDER_INVALID'), $fault->faultstring);

        //$newStatus = $prePayKoeff > 0 && $prePayKoeff < 100 ? STATUS_WAIT_PREPAY : STATUS_WAIT_PAY;
        //CSaleOrder::StatusOrder($settings['order_id'], $newStatus);
    }
}

return true;
