<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

if (empty($_REQUEST['ready_to_pay']) && empty($_REQUEST['status']))
{
    return;
}

// файл локализации
include(GetLangFileName(dirname(__FILE__) . '/', '/egopay.php'));
// модуль egopay
CModule::IncludeModule('egopay');
// путь к шаблонам
$templateDir = dirname(__FILE__) . '/templates/' . LANGUAGE_ID . '/';

// имя сайта для использования в ссылках
$SERVER_NAME_tmp = '';
if (defined('SITE_SERVER_NAME'))
{
    $SERVER_NAME_tmp = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . SITE_SERVER_NAME;
}
if (strlen($SERVER_NAME_tmp) <= 0)
{
    $SERVER_NAME_tmp = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . COption::GetOptionString('main', 'server_name', '');
}

try
{
    $iOrderId    = \CSalePaySystemAction::GetParamValue('ORDER_ID');
    $arProps     = \CSaleExt::getOrderPoperties($iOrderId);
    $prePayKoeff = \CSaleExt::getOrderPrepayKoef($iOrderId);

    $orderSum = CSalePaySystemAction::GetParamValue('SHOULD_PAY');
    if (false !== $i        = strrpos($orderSum, ','))
    {
        $orderSum[$i] = '.';
    }
    $orderSum = preg_replace('/[^0-9.]/', '', $orderSum);


    if ($prePayKoeff != 100)
    {
        //$orderSum = ceil($orderSum * $prePayKoeff / 100);
    }

    // массив с данными по магазину и заказу
    $settings = array(
        'shop_id'    => CSalePaySystemAction::GetParamValue('SHOP_ID'),
        'shop_login' => CSalePaySystemAction::GetParamValue('SHOP_LOGIN'),
        'shop_pass'  => CSalePaySystemAction::GetParamValue('SHOP_PASSWORD'),
        'shop_url'   => CSalePaySystemAction::GetParamValue('SHOP_URL'),
        'order_id'   => $iOrderId,
        'order_sum'  => $orderSum,
        'user_id'    => CSalePaySystemAction::GetParamValue('USER_ID'),
        'user_name'  => $arProps['FIO']['VALUE'],
        'user_email' => $arProps['EMAIL']['VALUE'],
        'user_phone' => $arProps['PHONE']['VALUE'],
        'return_url' => $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $iOrderId,
        'comment'    => CSalePaySystemAction::GetParamValue('COMMENT'),
        'item_type'  => CSalePaySystemAction::GetParamValue('ITEM_TYPE'),
        'item_desc'  => CSalePaySystemAction::GetParamValue('ITEM_DESC'),
    );
}
catch (Exception $e)
{
    CEgoPay::fetch($templateDir . 'error.tpl', array('e' => $e->__toString()));
}


if (isset($_REQUEST['status']))
{
    // заказ отработан

    if ($_REQUEST['status'] == 'ok')
    {
        // заказ проведён успешно, проверяем статус

        $soapClient = CEgoPay::newSoap($settings);
        $objStatus  = CEgoPay::getOrderStatus($settings['shop_id'], $settings['order_id']);

        // получаем данные по заказу
        $order   = Bitrix\Sale\Order::load($settings['order_id']);
        $arOrder = $order->getFieldValues();

        try
        {
            // получаем статуса заказа от платёжной системы
            $info = $soapClient->get_status($objStatus);

            switch ($info->status)
            {
                case 'acknowledged':
                case 'not_acknowledged':
                case 'authorized':
                    CEgoPay::updatePaymentStatus($order, 'Y', $info->status, $info->status);
                    CSaleOrder::StatusOrder($settings['order_id'], CSalePaySystemAction::GetParamValue('PAY_STATUS'));

                    // переменные для вывода в шаблоне
                    $templateOrderStatus = GetMessage('EGOPAY_ORDER_SUCCESS');
                    $templateRedirectUrl = $SERVER_NAME_tmp . PATH_ORDER_DETAIL . $settings['order_id'] . '/';
                    break;

                case 'canceled':
                case 'not_authorized':
                    // оплата заказа не удалась
                    CEgoPay::updatePaymentStatus($order, 'N', $info->status, $info->status);
                    CSaleOrder::StatusOrder($settings['order_id'], 'N');

                    $templateOrderStatus = GetMessage('EGOPAY_ORDER_FAIL');
                    $templateRedirectUrl = $SERVER_NAME_tmp . PATH_ORDER_DETAIL . $settings['order_id'] . '/';
                    break;

                case 'registered':
                    $templateOrderStatus = GetMessage('EGOPAY_ORDER_REGISTERED');
                    $templateRedirectUrl = $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $settings['order_id'] . '&status=ok';
                    break;

                case 'failed':
                case 'in_progress':
                    $templateOrderStatus = GetMessage('EGOPAY_ORDER_IN_PROGRESS');
                    $templateRedirectUrl = $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $settings['order_id'] . '&status=ok';
                    break;

                default:
                    $templateOrderStatus = GetMessage('EGOPAY_ORDER_PROCESSING');
                    $templateRedirectUrl = $SERVER_NAME_tmp . PATH_ORDER_DETAIL . $settings['order_id'] . '/';

                    break;
            }
        }
        catch (SoapFault $fault)
        {
            if ($fault->faultstring === 'INVALID_ORDER')
            {
                // заказ с таким номером не зарегистрирован
                CEgoPay::updatePaymentStatus($order, 'N', GetMessage('EGOPAY_ORDER_INVALID'), $fault->faultstring);
                CSaleOrder::StatusOrder($settings['order_id'], 'N');

                $templateOrderStatus = GetMessage('EGOPAY_ORDER_FAIL');
                $templateRedirectUrl = $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $settings['order_id'];
            }
        }
        catch (Exception $e)
        {
            CEgoPay::fetch($templateDir . 'pay_fault.tpl', array('form_url' => $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $settings['order_id'], 'e' => $e->__toString()));
        }

        // выводим шаблон с информацией о статусе заказа
        CEgoPay::fetch($templateDir . 'order_status.tpl', array(
            'order_status' => $templateOrderStatus,
            'redirect_url' => $templateRedirectUrl,
            'detail_url'   => $SERVER_NAME_tmp . PATH_ORDER_DETAIL . intval($_REQUEST['ORDER_ID']) . '/'));
    }

    if ($_REQUEST['status'] == 'fault')
    {
        // ошибка при проведении платежа
        CEgoPay::fetch($templateDir . 'pay_cancel.tpl', array('form_url' => $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . intval($_REQUEST['ORDER_ID']), 'e' => ''));
    }
}
else
{
    $arOrder = CSaleOrder::GetByID($settings['order_id']);
    if (($arOrder['CANCELED'] == 'N') && ($arOrder['PS_STATUS'] != 'Y'))
    {
        if (!empty($_REQUEST['ready_to_pay']))
        {
            $soapClient = CEgoPay::newSoap($settings);
            $request    = CEgoPay::prepareOrder($settings);

            try
            {
                CSaleOrder::StatusOrder($settings['order_id'], 'E');
                CSaleOrder::Update($settings['order_id'], array('PS_STATUS' => ''));

                $info = $soapClient->register_online($request);
                //всё ок, зарегистрировали, перенаправляем на платёжную систему
                header("Location: " . $info->redirect_url . "?session=" . $info->session);
                die;

                /* CEgoPay::fetch($templateDir . 'register_ok.tpl', array(
                  'redirect_url' => $info->redirect_url,
                  'session'      => $info->session)); */
            }
            catch (SoapFault $fault)
            {
                // ошибка, предлагаем попробовать снова
                CEgoPay::fetch($templateDir . 'register_fault.tpl', array(
                    'error_code'   => $fault->faultcode,
                    'error_string' => $fault->faultstring,
                    'form_url'     => $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . intval($_REQUEST['ORDER_ID'])));
            }
            catch (Exception $e)
            {
                CEgoPay::fetch($templateDir . 'pay_fault.tpl', array('form_url' => $SERVER_NAME_tmp . PATH_PAYMENT . '?ORDER_ID=' . $settings['order_id'], 'e' => $e->__toString()));
            }
        }
        /* else
          {
          //echo $settings['return_url'];
          // выводим форму оплаты
          CEgoPay::fetch($templateDir . 'prepare.tpl', array(
          'sum'    => $settings['order_sum'],
          'action' => $settings['return_url']
          ), false);
          } */
    }
}
