<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetTitle("Оплата");

global $USER;
$USER_ID = $USER->GetId();

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

$PAY_SYSTEM = $arResult['PAY_SYSTEM'];
$ORDER      = $arResult['ORDER'];

$ORDER_ID = $ORDER['ID'];

$obOrder      = \Bitrix\Sale\Order::load($ORDER_ID);
$prePayKoeff  = \CSaleExt::getOrderPrepayKoef($ORDER_ID); //процент предоплаты
$bonusesCount = \CSaleExt::getOrderBonusesCount($ORDER_ID); //кол-во бонусов

$ORDER_PRICE        = $obOrder->getPrice(); // Сумма заказа
$ORDER_SUM_PAID     = $obOrder->getSumPaid(); // Оплаченная сумма
$ORDER_PRICE_REMAIN = $ORDER_PRICE - $ORDER_SUM_PAID; // осталось оплатить
$ORDER_IS_PAID      = $obOrder->isPaid();
$ORDER_IS_CANCELED  = $obOrder->isCanceled();

$ORDER_DATE            = $obOrder->getField('DATE_INSERT')->format("d.m.Y H:i:s");
$ORDER_USER_ID         = $obOrder->getField('USER_ID');
$ORDER_STATUS_ID       = $obOrder->getField('STATUS_ID');
$ORDER_ACCOUNT_NUMBER  = $obOrder->getField("ACCOUNT_NUMBER");
$ORDER_PAY_SYSTEM_ID   = $obOrder->getField("PAY_SYSTEM_ID"); //выбранный юзером способ оплаты
$ORDER_DELIVERY_ID     = $obOrder->getField("DELIVERY_ID"); //выбранный юзером способ доставки
$ORDER_REASON_CANCELED = $obOrder->getField("REASON_CANCELED");

$ORDER_PAYMENTS       = $obOrder->getPaymentCollection();
$ORDER_PAYMENT_IDS    = $obOrder->getPaymentSystemId(); //ID оплат
$ORDER_PERSON_TYPE_ID = $obOrder->getPersonTypeId();

//id заявки на кредит
$ORDER_PROPERTY_COLLECTION = $obOrder->getPropertyCollection();
$KREDIT_ID_PROP_ID         = \CSaleExt::getPropertyIdByCode($ORDER_PERSON_TYPE_ID, "KREDIT_ID");
$KREDIT_ID_PROP_OBJECT     = $ORDER_PROPERTY_COLLECTION->getItemByOrderPropertyId($KREDIT_ID_PROP_ID);
$KREDIT_ID_PROP_VALUE      = method_exists($KREDIT_ID_PROP_OBJECT, 'getValue') ? $KREDIT_ID_PROP_OBJECT->getValue() : null;

$PROCCES_TO_PAYMENT = false; //показать ли кнопку "оплатить/скачать счет"

if (empty($ORDER) || empty($obOrder) || empty($ORDER_ID) || empty($ORDER_PRICE) || empty($ORDER_PAYMENT_IDS) /* || $USER_ID != $ORDER_USER_ID */)
{
    $CONFIRM_CODE = "error";
}
elseif ($ORDER_IS_PAID)
{
    $CONFIRM_CODE = "payed";
}
elseif ($ORDER_STATUS_ID == STATUS_PREPAYED)
{
    $CONFIRM_CODE = "prepayed";
}
elseif ($ORDER_STATUS_ID == STATUS_KREDIT_NEW)
{
    $CONFIRM_CODE = "kredit-new";
}
elseif ($ORDER_STATUS_ID == STATUS_KREDIT_ACCEPT)
{
    $CONFIRM_CODE = "kredit-accept";

//    //детали принятой заявки
//    $KREDIT_ID_PROP_VALUE = 185391;
//    $sUrl   = "https://test.moneycare.su/rest/broker/orders/$KREDIT_ID_PROP_VALUE/details";
//    $arAuth = array(
//        'LOGIN'    => 'vk42_3',
//        'PASSWORD' => 'vk42_3',
//    );
//
//    $arData = array(
//        "id" => $KREDIT_ID_PROP_VALUE,
//    );
//
//    $result = \CURL::sendMoneyCare($sUrl, $arData, $arAuth, true, true);
//
//    printra($result);
}
elseif ($ORDER_STATUS_ID == STATUS_KREDIT_DECLINE)
{
    $CONFIRM_CODE = "kredit-decline";
}
elseif ($ORDER_IS_CANCELED)
{
    $CONFIRM_CODE = "canceled";
}
else
{
    if (in_array(EPAYMENT_ID, $ORDER_PAYMENT_IDS))
    {
        $CONFIRM_CODE = "payment";
    }
    else
    {
        $CONFIRM_CODE = "created";
    }

    if (in_array(EPAYMENT_ID, $ORDER_PAYMENT_IDS) || in_array(BPAYMENT_ID, $ORDER_PAYMENT_IDS))
    {
        $PROCCES_TO_PAYMENT = true;
    }
}



$arConfirmVars = array(
    '#ORDER_ACCOUNT_NUMBER#'  => $ORDER_ACCOUNT_NUMBER,
    '#ORDER_DATE#'            => $ORDER_DATE,
    '#ORDER_REASON_CANCELED#' => $ORDER_REASON_CANCELED,
);

$CONFIRM_FILE  = $server->getDocumentRoot() . "/include/_text/order/confirm/$CONFIRM_CODE.php";
$CONFIRM_TITLE = strtr(file_get_contents($CONFIRM_FILE), $arConfirmVars);
?>

<div id="order-content" class="order-content order-content--confirm">
    <div class="order-content-outer paddings">
        <?= $CONFIRM_TITLE ?>

        <? if (empty($ORDER) || empty($ORDER_ID) || empty($ORDER_PRICE)): ?>

        <? elseif ($ORDER_STATUS_ID == STATUS_KREDIT_NEW): ?>
            <? include($server->getDocumentRoot() . $templateFolder . "/kredit.php"); ?>
        <? else: ?>

            <?
            //if ($PROCCES_TO_PAYMENT)
            {
                foreach ($ORDER_PAYMENTS as $payment)
                {
                    $sum        = $payment->getSum(); // сумма к оплате
                    $isPaid     = $payment->isPaid(); // true, если оплачена
                    $isReturned = $payment->isReturn(); // true, если возвращена

                    $ps        = $payment->getPaySystem(); // платежная система (объект Sale\PaySystem\Service)
                    $psID      = $payment->getPaymentSystemId(); // ID платежной системы
                    $psName    = $payment->getPaymentSystemName(); // название платежной системы
                    $isInnerPs = $payment->isInner(); // true, если это оплата с внутреннего счета

                    $psActionFile  = $ps->getField('ACTION_FILE');
                    $psNewWindow   = $ps->getField('NEW_WINDOW');
                    $psIsCash      = $ps->getField('IS_CASH');
                    $psDescroption = $ps->getField('DESCRIPTION');
                    $psComments    = $payment->getField('COMMENTS');
                    //if ($psID == BPAYMENT_ID) printrau($ps);

                    if ($isInnerPs || $isPaid || $psComments == "bonuses" || $psComments == "prepay")
                    {
                        continue;
                    }

                    $iPrice       = $iPricePrePay = $ORDER_PRICE_REMAIN;

                    if ($prePayKoeff > 0 && $prePayKoeff < 100)
                    {
                        $iPricePrePay = $iPrice * $prePayKoeff / 100;
                    }
                    ?>
                    <div class="order-confirm-row">
                        Способ оплаты: <strong><?= $psName ?></strong>
                    </div>

                    <div class="order-confirm-row">
                        Сумма заказа: <strong><?= printPrice($iPrice) ?></strong>
                    </div>

                    <? if ($iPrice != $iPricePrePay): ?>
                        <div class="order-confirm-row">
                            Сумма предоплаты: <strong><?= printPrice($iPricePrePay) ?></strong>
                        </div>
                    <? endif; ?>

                    <?
                    if ($PROCCES_TO_PAYMENT)
                    {
                        $orderAccountNumber   = urlencode(urlencode($ORDER_ACCOUNT_NUMBER));
                        $paymentAccountNumber = str_replace(VK_PREFIX, "", $payment->getField('ACCOUNT_NUMBER'));

                        $payLink = $arParams["PATH_TO_PAYMENT"]
                                . "?ORDER_ID=" . $orderAccountNumber
                                . "&PAYMENT_ID=" . $paymentAccountNumber
                                . "&ready_to_pay=1";

                        $btnTitle = $iPrice != $iPricePrePay ? "Внести предоплату" : "Оплатить заказ";

                        if (/* $psActionFile == "bill" && */ stristr($psActionFile, "bill") && $psNewWindow == "Y" && \CSalePdf::isPdfAvailable())
                        {
                            $payLink  = $arParams["PATH_TO_PAYMENT"]
                                    . "?ORDER_ID=" . $orderAccountNumber
                                    . "&pdf=1&DOWNLOAD=Y";
                            $btnTitle = "Скачать счет";
                        }
                        ?>

                        <a
                            class="order-confirm-button"
                            href="<?= $payLink ?>"
                            target="_blank"
                            title="<?= $btnTitle ?>"
                            ><?= $btnTitle ?><i class="ion-chevron-right"></i>
                        </a>
                        <?
                    }
                    ?>

                    <? if (!empty($psDescroption)): ?>
                        <div class=""><?= $psDescroption ?></div>
                    <? endif; ?>
                    <?
                }
            }
            ?>

            <div class="order-confirm-row order-confirm-link">
                Вы можете следить за выполнением своего заказа в <a title="Личный кабинет" target="_blank" href="<?= PATH_PERSONAL ?>">личном кабинете</a>.
            </div>
        <? endif ?>
    </div>
</div>