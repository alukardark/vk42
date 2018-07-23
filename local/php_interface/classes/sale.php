<?php

class CSaleExt
{

    private static $request;
    private static $server;
    private static $isPost;

    /**
     * удаляет все оплаты для заказа
     */
    public static function clearOrderPayments($iOrderId)
    {
        $order             = \Bitrix\Sale\Order::load($iOrderId);
        $paymentCollection = $order->getPaymentCollection();

        foreach ($paymentCollection as $payment)
        {
            $payment->delete();
        }

        $order->save();
    }

    /**
     * Разделяет оплату заказа на части
     * 
     * @see /bitrix/modules/sale/admin/order_payment_edit.php:83
     * @see /bitrix/modules/sale/lib/helpers/admin/blocks/orderpayment.php:900
     * @see https://mrcappuccino.ru/blog/post/work-with-order-bitrix-d7
     * 
     */
    public static function splitOrderPayments($iOrderId)
    {
        global $USER;
        $USER_ID = $USER->GetId();

        $order = \Bitrix\Sale\Order::load($iOrderId);

        $prePayKoeff  = self::getOrderPrepayKoef($iOrderId); //процент предоплаты
        $bonusesCount = self::getOrderBonusesCount($iOrderId); //кол-во бонусов

        $priceTotal   = $order->getPrice(); // Сумма заказа
        $sumPaid      = $order->getSumPaid(); // Оплаченная сумма
        $priceRemains = $priceTotal - $sumPaid; // осталось оплатить

        $isPaid     = $order->isPaid(); // true, если оплачен
        $isCanceled = $order->isCanceled();

        $status = $order->getField('STATUS_ID');

        if ($isPaid || $isCanceled || $status != STATUS_NEW || $sumPaid > 0)
        {
            return false;
        }

        $result          = new \Bitrix\Sale\Result();
        $data['PAYMENT'] = array();

        $payment['PAID']            = 'N';
        $payment['IS_RETURN']       = 'N';
        $payment['ORDER_STATUS_ID'] = 'N';

        $CARD       = null;
        $arPayments = array();
        $psServiceI = false;
        $psServiceE = false;

        if (!empty($bonusesCount))
        {
            //получаем карту юзера и актульный бонусный баланс
            \CUserExt::updateUserFrom1C();
            $CARD = \COrderExt::getCard();

            $CARD_TYPE    = $CARD["TYPE"];
            $CARD_NUMBER  = $CARD["NUMBER"];
            $CARD_BALANCE = $CARD["BALANCE"];

            $BONUS_MAX_PERCENT = \COrderExt::getCardMaxPercent($CARD_TYPE);
            $PAY_MAX_BY_BONUS  = floor($priceRemains * $BONUS_MAX_PERCENT / 100);

            if ($CARD_BALANCE < $bonusesCount || $PAY_MAX_BY_BONUS < $bonusesCount)
            {
                //не хватает бонусов
                return false;
            }

            //необходимо удалить все текущие оплаты для заказа
            if (empty($arPayments))
            {
                self::clearOrderPayments($iOrderId);
            }

            $psServiceI   = \Bitrix\Sale\PaySystem\Manager::getObjectById(IPAYMENT_ID); //оплата (внутр. счет)
            $priceRemains -= $bonusesCount; //осталось оплатить

            $arPayments[] = array(
                //предоплата
                'PAY_SYSTEM_ID'   => IPAYMENT_ID,
                'COMMENTS'        => 'bonuses',
                'PAY_SYSTEM_NAME' => $psServiceI->getField('NAME'),
                'SUM'             => $bonusesCount,
                'DATE_BILL'       => new \Bitrix\Main\Type\DateTime(),
            );
        }

        if (!empty($prePayKoeff) && $prePayKoeff > 0 && $prePayKoeff < 100)
        {
            $pricePrePay = ceil($priceRemains * $prePayKoeff / 100); //сумма предоплаты
            //необходимо удалить все текущие оплаты для заказа
            if (empty($arPayments))
            {
                self::clearOrderPayments($iOrderId);
            }

            $psServiceE   = \Bitrix\Sale\PaySystem\Manager::getObjectById(EPAYMENT_ID);
            $priceRemains -= $pricePrePay;

            $arPayments[] = array(
                //предоплата
                'PAY_SYSTEM_ID'   => EPAYMENT_ID,
                'COMMENTS'        => 'prepay',
                'PAY_SYSTEM_NAME' => $psServiceE->getField('NAME'),
                'SUM'             => $pricePrePay,
                'DATE_BILL'       => new \Bitrix\Main\Type\DateTime(),
            );
        }

        if (empty($arPayments))
        {
            return false;
        }

        $psServiceP = \Bitrix\Sale\PaySystem\Manager::getObjectById(CPAYMENT_ID); //оплата остатка (банк)

        $arPayments[] = array(
            'PAY_SYSTEM_ID'   => CPAYMENT_ID,
            'COMMENTS'        => 'mainpay',
            'PAY_SYSTEM_NAME' => $psServiceP->getField('NAME'),
            'SUM'             => $priceRemains,
            'DATE_BILL'       => new \Bitrix\Main\Type\DateTime(),
        );

        $paymentCollection = $order->getPaymentCollection();

        foreach ($arPayments as $paymentFields)
        {
            $paymentItem = $paymentCollection->createItem();

            if ($result->isSuccess())
            {
                $paymentItem->setFields($paymentFields);

                $data['PAYMENT'][] = $paymentItem;
                $result->setData($data);
            }

            if ($result->isSuccess())
            {
                //сохраням
                $order->save();
            }
        }

        if ($result->isSuccess())
        {
            //ставим статус "Принят, ожидается предооплата" (M)
            $order->setField('STATUS_ID', STATUS_WAIT_PREPAY);

            //сохраням
            $order->save();

            //отправим в 1с инфу о резервировании бонусов
            if ($bonusesCount > 0 && !empty($CARD))
            {
                $CARD_TYPE   = $CARD["TYPE"];
                $CARD_NUMBER = $CARD["NUMBER"];

                //$CARD_CODE              = $CARD_TYPE == "REGULAR" ? '"BKBONUS"_' : '"VIPBONUS"_';
                $CARD_CODE = $CARD_TYPE == "REGULAR" ? 'CS.' : 'VIP.';
                //$CARD_NUMBER_LENGTH     = $CARD_TYPE == "REGULAR" ? 7 : 5;
                //$CARD_NUMBER_WITH_NULLS = str_pad($CARD_NUMBER, $CARD_NUMBER_LENGTH, "0", STR_PAD_LEFT);

                global $USER;
                $USER_ID  = $USER->GetId();
                $USER_NFO = \CUserExt::getById($USER_ID);

                $arData           = array(
                    "ID"      => $iOrderId,
                    "XML_ID"  => $USER_NFO["XML_ID"],
                    "BONUSES" => $bonusesCount,
                    //"CARD"    => $CARD_CODE . $CARD_NUMBER_WITH_NULLS,
                    "CARD"    => $CARD_CODE . $CARD_NUMBER,
                );
                $ar1CBonusPayment = \CURL::getReplay("BonusPayment", $arData, true, false, true, false);

                if ($ar1CBonusPayment['RESULT'] == true)
                {
                    setUF("USER", $USER_ID, array("UF_CARD_BALANCE" => $ar1CBonusPayment["BONUSES"]));
                }
            }
        }
    }

    public static function getOrderPoperties($iOrderId)
    {
        $arProps  = array();
        $db_props = \CSaleOrderPropsValue::GetOrderProps($iOrderId);
        while ($arFetch  = $db_props->Fetch())
        {
            $arProps[$arFetch['CODE']] = $arFetch;
        }

        return $arProps;
    }

    public static function getOrderPrepayKoef($iOrderId)
    {
        $prePay      = false;
        $prePayKoeff = 100;

        $arProps = self::getOrderPoperties($iOrderId);

        if (!empty($arProps['PREPAY']['VALUE']))
        {
            $arPrepayValue = @unserialize($arProps['PREPAY']['VALUE']);

            if (!empty($arPrepayValue) && is_array($arPrepayValue))
            {
                $prePay = $arPrepayValue[0];
            }
        }

        if (!empty($prePay))
        {
            $prePayKoeff = (int) str_replace("PREPAY", "", $prePay);
            if (empty($prePayKoeff)) $prePayKoeff = 100;
        }

        return $prePayKoeff;
    }

    /**
     * количечство бонусов, которые юзер хочет списать
     */
    public static function getOrderBonusesCount($iOrderId)
    {
        $arProps = self::getOrderPoperties($iOrderId);
        return (int) $arProps['BONUS_COUNT']['VALUE'];
    }

    public static function getPropertyArrayByCode($PERSON_TYPE_ID, $CODE)
    {
        $obList = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"), array(
                    "=PERSON_TYPE_ID" => $PERSON_TYPE_ID,
                    "=CODE"           => $CODE,
                    "ACTIVE"          => "Y",
                        ), false, false, array()
        );

        if ($arFetch = $obList->Fetch())
        {
            return $arFetch;
        }

        return false;
    }

    public static function getPropertyIdByCode($PERSON_TYPE_ID, $CODE)
    {
        $obList = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"), array(
                    "=PERSON_TYPE_ID" => $PERSON_TYPE_ID,
                    "=CODE"           => $CODE,
                    "ACTIVE"          => "Y",
                        ), false, false, array()
        );

        if ($arFetch = $obList->Fetch())
        {
            return $arFetch["ID"];
        }

        return false;
    }

    public static function setDefaultOrderLocation(&$arUserResult)
    {
        global $USER;

        $USER_ID        = $USER->GetId();
        $ORDER_PROP     = $arUserResult["ORDER_PROP"];
        $PERSON_TYPE_ID = $arUserResult["PERSON_TYPE_ID"];

        $LOCATION_PROP_ID = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "LOCATION");
        $ZIP_PROP_ID      = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "ZIP");
        $CITY_PROP_ID     = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "CITY");

        //printra($arUserResult["ORDER_PROP"][$LOCATION_PROP_ID]);
        //if (empty($arUserResult["ORDER_PROP"][$LOCATION_PROP_ID]))
        {
            //не установлен город. Выбираем город по умолчанию
            $cityKey  = \Axi::getCityKey();
            $cityName = \Axi::getCityNameByKey($cityKey);

            $locationId   = \Axi::getLocationIdByName($cityName);
            $locationCode = \CSaleLocation::getLocationCODEbyID($locationId);
            $arFetch_ZIP  = \CSaleLocation::GetLocationZIP($locationId)->Fetch();

            $arUserResult["ORDER_PROP"][$LOCATION_PROP_ID] = $locationCode;
            $arUserResult["ORDER_PROP"][$CITY_PROP_ID]     = $cityName;
            $arUserResult["ORDER_PROP"][$ZIP_PROP_ID]      = $arFetch_ZIP["ZIP"];

            $arUserResult["DELIVERY_LOCATION_BCODE"] = $locationCode;
            $arUserResult["DELIVERY_LOCATION_ZIP"]   = $arFetch_ZIP["ZIP"];
            $arUserResult["DELIVERY_LOCATION"]       = $locationId;
        }
    }

    private static $_instance;

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
        $context       = \Bitrix\Main\Application::getInstance()->getContext();
        self::$request = $context->getRequest();
        self::$server  = $context->getServer();
        self::$isPost  = self::$request->isPost();
    }

}
