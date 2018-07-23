<?

/**
 * этот файл будет вызываться в /local/templates/axioma/components/bitrix/sale.order.ajax/.default/kredit.php
 */
use \Bitrix\Main\Application;

global $USER, $APPLICATION;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$isPost  = $request->isPost();
$sAction = $request->getPost("ACTION");

if (isPost("KREDIT_REQUEST") || isPost("KREDIT_RESULT"))
{
    $APPLICATION->RestartBuffer();

    parse_str($request->getPost("VALUES"), $VALUES);

    if (!vk_check_bitrix_sessid($VALUES))
    {
        json_result(false, array('message' => '<p>Session error</p>', 'errcode' => 'session'));
    }

    //$USER_ID  = $USER->GetId();
    $ORDER_ID = $VALUES['ORDER_ID'];

    if (/* empty($USER_ID) || */ empty($ORDER_ID))
    {
        json_result(false, array('message' => '<p>Ошибка безопасности</p>', 'errcode' => 'safety'));
    }

    $obOrder = \Bitrix\Sale\Order::load($ORDER_ID);

    $ORDER_PRICE        = $obOrder->getPrice(); // Сумма заказа
    $ORDER_SUM_PAID     = $obOrder->getSumPaid(); // Оплаченная сумма
    $ORDER_PRICE_REMAIN = $ORDER_PRICE - $ORDER_SUM_PAID; // осталось оплатить
    $ORDER_IS_PAID      = $obOrder->isPaid();
    $ORDER_IS_CANCELED  = $obOrder->isCanceled();

    $ORDER_DATE           = $obOrder->getField('DATE_INSERT')->format("d.m.Y H:i:s");
    $ORDER_USER_ID        = $obOrder->getField('USER_ID');
    $ORDER_STATUS_ID      = $obOrder->getField('STATUS_ID');
    $ORDER_ACCOUNT_NUMBER = $obOrder->getField("ACCOUNT_NUMBER");
    $ORDER_PAY_SYSTEM_ID  = $obOrder->getField("PAY_SYSTEM_ID"); //выбранный юзером способ оплаты
    $ORDER_DELIVERY_ID    = $obOrder->getField("DELIVERY_ID"); //выбранный юзером способ доставки

    $ORDER_PAYMENTS       = $obOrder->getPaymentCollection();
    $ORDER_PAYMENT_IDS    = $obOrder->getPaymentSystemId(); //ID оплат
    $ORDER_PERSON_TYPE_ID = $obOrder->getPersonTypeId();

    //id заявки на кредит
    $ORDER_PROPERTY_COLLECTION = $obOrder->getPropertyCollection();
    $KREDIT_ID_PROP_ID         = \CSaleExt::getPropertyIdByCode($ORDER_PERSON_TYPE_ID, "KREDIT_ID");
    $KREDIT_ID_PROP_OBJECT     = $ORDER_PROPERTY_COLLECTION->getItemByOrderPropertyId($KREDIT_ID_PROP_ID);
    $KREDIT_ID_PROP_VALUE      = $KREDIT_ID_PROP_OBJECT->getValue();

    $ORDER_SHIPMENT_COLLECTION = $obOrder->getShipmentCollection();
    $ORDER_STORE_ID            = null;
    foreach ($ORDER_SHIPMENT_COLLECTION as $shipment)
    {
        $ORDER_STORE_ID = $shipment->getStoreId();

        //вообще, теоретически для каждой отгрузки может быть выбран свой склад. Но не в нашем случае. Поэтому break
        break;
    }

    $PS_SETTINGS_FOR_STORE = \COrderExt::getMoneyCareSettingsByStoreId($ORDER_STORE_ID);

    /* if ($USER_ID != $ORDER_USER_ID)
      {
      json_result(false, array('message' => '<p>Ошибка пользователя</p>', 'errcode' => 'user'));
      } */

    if ($ORDER_IS_PAID)
    {
        json_result(false, array('message' => '<p>Заказ уже оплачен</p>', 'errcode' => 'payed'));
    }

    if ($ORDER_IS_CANCELED)
    {
        json_result(false, array('message' => '<p>Заказ отменен</p>', 'errcode' => 'canceled'));
    }

    if (empty($ORDER_PRICE))
    {
        json_result(false, array('message' => '<p>Неверная стоимость заказа</p>', 'errcode' => 'price'));
    }

    if ($ORDER_PAY_SYSTEM_ID != KPAYMENT_ID)
    {
        json_result(false, array('message' => '<p>Неверная платежная система</p>', 'errcode' => 'paysystem'));
    }

    $PS_SETTINGS = array(
        'URL_STAND'      => \CSalePaySystemAction::GetParamValue('URL_STAND'),
        //'POINT_ID'       => \CSalePaySystemAction::GetParamValue('POINT_ID'),
        'POINT_ID'       => $PS_SETTINGS_FOR_STORE["POINT_ID"],
        'API_CLIENT_ID'  => $PS_SETTINGS_FOR_STORE["API_CLIENT_ID"],
        'API_CLIENT_PWD' => $PS_SETTINGS_FOR_STORE["API_CLIENT_PWD"],
        //'POINT_PWD'      => \CSalePaySystemAction::GetParamValue('POINT_PWD'),
        //'API_CLIENT_ID'  => \CSalePaySystemAction::GetParamValue('API_CLIENT_ID'),
        //'API_CLIENT_PWD' => \CSalePaySystemAction::GetParamValue('API_CLIENT_PWD'),
        'OPERATOR_ID'    => \CSalePaySystemAction::GetParamValue('OPERATOR_ID'),
            //'OPERATOR_PWD'   => \CSalePaySystemAction::GetParamValue('OPERATOR_PWD'),
            //'MANAGER_ID'     => \CSalePaySystemAction::GetParamValue('MANAGER_ID'),
            //'MANAGER_PWD'    => \CSalePaySystemAction::GetParamValue('MANAGER_PWD'),
    );

    /**
     * KREDIT_REQUEST
     * отправка запроса на создание заявки на кредит
     */
    if (isPost("KREDIT_REQUEST") && empty($KREDIT_ID_PROP_VALUE))
    {
        //список корзины
        $arGoods = array();
        foreach ($obOrder->getBasket() as $basketItem)
        {
            $arGoods[] = array(
                'type'  => 'product',
                'title' => $basketItem->getField('NAME'),
                'price' => $basketItem->getField('PRICE'),
                'count' => $basketItem->getQuantity(),
            );
        }

        //проверка паспортных данных
        $passport_matches = array();

        $passport_pattern = '/'
                . '(?P<series>[0-9][0-9] [0-9][0-9])'
                . ' (?P<number>[0-9]{6})'
                . '/';

        preg_match_all($passport_pattern, $VALUES["PASSPORT"], $passport_matches);

        $passport_series = trim(str_replace(" ", "", $passport_matches["series"][0]));
        $passport_number = trim($passport_matches["number"][0]);

        if (strlen($passport_series) != 4 || strlen($passport_number) != 6)
        {
            json_result(false, array('message' => '<p>Неверные паспортные данные</p>', 'errcode' => 'passport'));
        }

        $arPassport = array(
            'series'    => $passport_series,
            'number'    => $passport_number,
            'issueDate' => date("Y-m-d", strtotime($VALUES["PASSPORT_DATE"])),
        );


        $sUrl   = $PS_SETTINGS["URL_STAND"] . "/rest/broker/orders/create";
        $arAuth = array(
            'LOGIN'    => $PS_SETTINGS["API_CLIENT_ID"],
            'PASSWORD' => $PS_SETTINGS["API_CLIENT_PWD"],
        );

        $arData = array(
            "forceScore" => true,
            "orderId"    => $ORDER_ACCOUNT_NUMBER,
            "pointId"    => $PS_SETTINGS["POINT_ID"],
            "operatorId" => $PS_SETTINGS["OPERATOR_ID"],
            "firstName"  => $VALUES["FIRSTNAME"],
            "lastName"   => $VALUES["SURNAME"],
            "secondName" => $VALUES["PATRONYMIC"],
            "birthDate"  => date("Y-m-d", strtotime($VALUES["BIRTHDAY"])),
            "passport"   => $arPassport,
            "phone"      => getPhoneFromString($VALUES["PHONE"], true, true),
            "email"      => $VALUES["EMAIL"],
            "goods"      => $arGoods,
        );

        $result = \CURL::sendMoneyCare($sUrl, $arData, $arAuth);

        if ($result["accepted"] == true)
        {
            $KREDIT_ID = (int) $result["id"];

            if (!empty($KREDIT_ID))
            {
                //древняя магия
                $ORDER_PROPERTY_COLLECTION = $obOrder->getPropertyCollection();
                $KREDIT_ID_PROP_ID         = \CSaleExt::getPropertyIdByCode($ORDER_PERSON_TYPE_ID, "KREDIT_ID");
                $KREDIT_ID_PROP_OBJECT     = $ORDER_PROPERTY_COLLECTION->getItemByOrderPropertyId($KREDIT_ID_PROP_ID);
                $KREDIT_ID_PROP_OBJECT->setValue($KREDIT_ID);
                $obOrder->save();

                json_result(true, $result);
            }
            else
            {
                json_result(false, $result);
            }
        }
        else
        {
            json_result(false, array('message' => '<p>' . $result["reason"] . '</p>', 'code' => 'moneycare_fail'));
        }

        json_result(false, array('message' => '<p>Неизвестная ошибка</p>', 'errcode' => 'unknown1'));
    }



    /**
     * KREDIT_RESULT
     * отправка запроса на получение результата заявки на кредит (одобрено/ отказано)
     */
    elseif (isPost("KREDIT_RESULT") && !empty($KREDIT_ID_PROP_VALUE))
    {
        $sUrl   = $PS_SETTINGS["URL_STAND"] . "/rest/broker/orders/score/status";
        $arAuth = array(
            'LOGIN'    => $PS_SETTINGS["API_CLIENT_ID"],
            'PASSWORD' => $PS_SETTINGS["API_CLIENT_PWD"],
        );

        $arData = array(
            "id" => $KREDIT_ID_PROP_VALUE,
        );

        $result = \CURL::sendMoneyCare($sUrl, $arData, $arAuth, true);

        if (empty($result) || !is_array($result) || $result["status"] == "error")
        {
            json_result(false, array('message' => '<p>' . $result["reason"] . '</p>', 'code' => 'moneycare_fail2'));
        }
        else
        {
            $UF_EMAIL = getUF("CAT_STORE", $ORDER_STORE_ID, "UF_EMAIL");

            if ($result["status"] == "accepted")
            {
                $USER_NFO     = \CUserExt::getById($ORDER_USER_ID);
                $arSendFields = array(
                    "EMAIL"           => $USER_NFO["EMAIL"],
                    "UF_EMAIL"        => $UF_EMAIL,
                    "ORDER_USER"      => $USER_NFO["NAME"] . " " . $USER_NFO["LAST_NAME"],
                    "ORDER_ID"        => $ORDER_ID,
                    "ORDER_DATE"      => $ORDER_DATE,
                    "ORDER_KREDIT_ID" => $KREDIT_ID_PROP_VALUE,
                    "SALE_EMAIL"      => \COption::GetOptionString("sale", "order_email"),
                );
                \CEvent::SendImmediate("KREDIT_ACCEPTED", SITE_ID, $arSendFields);

                \CSaleOrder::StatusOrder($ORDER_ID, STATUS_KREDIT_ACCEPT);
                json_result(true, array($result));
            }
            elseif ($result["status"] == "cancel")
            {
                $USER_NFO     = \CUserExt::getById($ORDER_USER_ID);
                $arSendFields = array(
                    "EMAIL"           => $USER_NFO["EMAIL"],
                    "UF_EMAIL"        => $UF_EMAIL,
                    "ORDER_USER"      => $USER_NFO["NAME"] . " " . $USER_NFO["LAST_NAME"],
                    "ORDER_ID"        => $ORDER_ID,
                    "ORDER_DATE"      => $ORDER_DATE,
                    "ORDER_KREDIT_ID" => $KREDIT_ID_PROP_VALUE,
                    "SALE_EMAIL"      => \COption::GetOptionString("sale", "order_email"),
                );
                \CEvent::SendImmediate("KREDIT_CANCEL", SITE_ID, $arSendFields);

                \CSaleOrder::StatusOrder($ORDER_ID, STATUS_KREDIT_DECLINE);
                json_result(true, array($result));
            }
            else
            {
                json_result(true, array($result));
            }
        }

        json_result(false, array('message' => '<p>Неизвестная ошибка</p>', 'errcode' => 'unknown2'));
    }
    else
    {
        json_result(false, array('message' => '<p>Неизвестная ошибка</p>', 'errcode' => 'unknown3' . $KREDIT_ID_PROP_VALUE));
    }
}