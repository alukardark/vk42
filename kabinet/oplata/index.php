<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
$APPLICATION->SetTitle("Оплата заказа");

//fucking fix
//если ORDER_ID не начинается с префикса VK, то добавим его
if (isset($_REQUEST["ORDER_ID"]))
{
    $ORDER_ID = intval($_REQUEST["ORDER_ID"]);

    if (is_numeric($ORDER_ID) && $ORDER_ID > 0)
    {
        $_REQUEST["ORDER_ID"] = VK_PREFIX . $ORDER_ID;
    }
}

?>
<?

$APPLICATION->IncludeComponent(
        "bitrix:sale.order.payment", "", Array(
        )
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php"); ?>