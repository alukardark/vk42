<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$ID = (int) $_REQUEST['ID'];

if (strlen($ID) > 0 && $_REQUEST["CANCEL"] == "Y" && $_SERVER["REQUEST_METHOD"] == "POST" && strlen($_REQUEST["action"]) > 0)
{
    global $APPLICATION, $USER;

    $arParams["PATH_TO_LIST"] = Trim($arParams["PATH_TO_LIST"]);
    if (strlen($arParams["PATH_TO_LIST"]) <= 0)
            $arParams["PATH_TO_LIST"] = htmlspecialcharsbx($APPLICATION->GetCurPage());

    $arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
    if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
            $arParams["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage() . "?" . "ID=#ID#");

    if ($arParams["SET_TITLE"] == 'Y') $APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("SPOC_TITLE")));

    $bUseAccountNumber = (COption::GetOptionString("sale", "account_number_template", "") !== "") ? true : false;

    $errors = array();


    $arOrder = false;

    $order             = \Bitrix\Sale\Order::load($ID);
    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment)
    {
        $sum        = $payment->getSum(); // сумма к оплате
        $isPaid     = $payment->isPaid(); // true, если оплачена
        $isReturned = $payment->isReturn(); // true, если возвращена

        $ps        = $payment->getPaySystem(); // платежная система (объект Sale\PaySystem\Service)
        $psID      = $payment->getPaymentSystemId(); // ID платежной системы
        $psName    = $payment->getPaymentSystemName(); // название платежной системы
        $isInnerPs = $payment->isInner(); // true, если это оплата с внутреннего счета

        if ($isPaid)
        {
            $payment->setPaid("N"); // отмена оплаты
            $payment->setReturn("Y");
            $order->save();
        }
    }

    $dbOrder = CSaleOrder::GetList(
                    array("ID" => "DESC"), array(
                "ID"      => $ID,
                "USER_ID" => IntVal($USER->GetID())
                    ), false, false, array("ID")
    );

    if ($arOrder = $dbOrder->Fetch())
    {
        CSaleOrder::CancelOrder($arOrder["ID"], "Y", $_REQUEST["REASON_CANCELED"]);

        if ($ex = $APPLICATION->GetException())
        {
            printra($ex);

            if ($ex['id'] == 'CANCEL_ERROR')
            {
                
            }
            $errors[] = $ex->GetString();
        }
        else
        {
            LocalRedirect($arParams["PATH_TO_LIST"]);
        }
    }
}

if (!empty($errors) && is_array($errors))
{
    foreach ($errors as $errorMessage)
    {
        $arResult["ERROR_MESSAGE"] .= $errorMessage . ".";
    }
}