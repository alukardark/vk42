<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$isPost  = $request->isPost();
$bResult = false;

$ACTION     = $request->getPost("action");
$QUANTITY   = intval($request->getPost("quantity"));
$PRODUCT_ID = intval($request->getPost("product_id"));

if (!$isPost)
{
    json_result(false, array('alert' => "Ошибочный запрос"));
}

if (empty($ACTION))
{
    json_result(false, array('alert' => "Неверное действие"));
}

if (isBot())
{
    json_result(false, array('alert' => "Мы определили, что вы бот. Если это не так - свяжитесь, пожалуйста, с администрацией"));
}

//очистка корзины
if ($ACTION == 'clear')
{
    \CBasketExt::deleteAllRecords();
    json_result(true, null);
}

if (empty($PRODUCT_ID))
{
    json_result(false, array('alert' => "Не найден товар"));
}

if (empty($QUANTITY))
{
    json_result(false, array('alert' => "Неверное количество"));
}

$arProductInfo = \CBasketExt::getProductInfo($PRODUCT_ID);

if (!$arProductInfo)
{
    json_result(false, array('alert' => "Не найден товар"));
}

$BASKET_DATA = \CBasketExt::getBasketNew();
$arRecord    = $BASKET_DATA["RECORDS"][$PRODUCT_ID];

//$arRecord    = \CBasketExt::getRecord($PRODUCT_ID);
//количество этого товара, которое уже есть в корзине
$iRecordQuantity = empty($arRecord) ? 0 : $arRecord['QUANTITY'];

//всего всех товаров в корзине
$iAllRecordsQuantity = $BASKET_DATA['QUANTITY'];

//доступное количество товара на всех складах либо MAX_QUANTITY
\CCatalogExt::updateProductStoreAmount(false, $PRODUCT_ID);
$iMaxQuantity = \CCatalogExt::getProductAmountInStores($PRODUCT_ID);
if ($iMaxQuantity > MAX_QUANTITY)
{
    $iMaxQuantity = MAX_QUANTITY;
}

//сколько товара должно стать в корзине после выполнения действия
$iNewRecordQuantity = 0;

if ($ACTION == 'minus')
{
    $iNewRecordQuantity = $iRecordQuantity - $QUANTITY;
}
elseif ($ACTION == 'plus')
{
    $iNewRecordQuantity = $iRecordQuantity + $QUANTITY;
}
elseif ($ACTION == 'set')
{
    $iNewRecordQuantity = $QUANTITY;
}

//сколько будет всех всех товаров в корзине после выполнения
$iNewAllRecordsQuantity = $iAllRecordsQuantity - $iRecordQuantity + $iNewRecordQuantity;

//удалить товар из корзины, если новое кол-во 0 или меньше
if ($iNewRecordQuantity <= 0)
{
    $ACTION = 'remove';
}

//проверка на лимит
if ($iNewRecordQuantity > $iMaxQuantity)
{
    $iNewRecordQuantity = $iMaxQuantity;
}

//кол-во товара не изменилось
if ($iNewAllRecordsQuantity > MAX_QUANTITY)
{
    json_result(false, array('alert' => "Нельзя добавить больше " . MAX_QUANTITY . " товаров в корзину"));
}

//кол-во товара не изменилось
if ($iNewRecordQuantity == $iRecordQuantity)
{
    json_result(false, array('alert' => "Нельзя добавить больше этого товара"));
}


$goal = null;

if ($ACTION == "remove")
{
    $bResult = \CBasketExt::deleteRecord($PRODUCT_ID);
}
else
{
    if ($iRecordQuantity == 0)
    {
        $bResAdd = \CBasketExt::addRecord($PRODUCT_ID);

        if (!$bResAdd)
        {
            json_result(false, array('alert' => "Не удалось добавить товар в корзину"));
        }

        $IBLOCK_ID = getIBlockByElement($PRODUCT_ID);

        if ($IBLOCK_ID == TIRES_IB)
        {
            $goal = "add_to_cart_shiny";
        }
        elseif ($IBLOCK_ID == AKB_IB)
        {
            $goal = "add_to_cart_akb";
        }
        elseif ($IBLOCK_ID == DISCS_IB)
        {
            $goal = "add_to_cart_disks";
        }
        elseif ($IBLOCK_ID == OILS_IB)
        {
            $goal = "add_to_cart_oil";
        }
        elseif ($IBLOCK_ID == MISC_IB)
        {
            $goal = "add_to_cart_misc";
        }

        global $APPLICATION;
        $goalcookie = $APPLICATION->get_cookie($goal);

        if ($goalcookie == "Y")
        {
            $goal = null;
        }
        else
        {
            $APPLICATION->set_cookie($goal, "Y", time() + 3600);
        }
    }

    $bResult = \CBasketExt::setRecordQuantity($PRODUCT_ID, $iNewRecordQuantity);
}


/**
 * OUTPUT
 */
if ($bResult)
{
    //$arRecord = \CBasketExt::getRecord($PRODUCT_ID);
    //$arCoupons = \Bitrix\Sale\DiscountCouponsManager::get();

    $BASKET_DATA = \CBasketExt::getBasketNew();
    $arRecord    = $BASKET_DATA["RECORDS"][$PRODUCT_ID];

    $arResult = array(
        //'action' => $ACTION,
        //'basket' => \CBasketExt::getBasketNew(),
        'basket' => $BASKET_DATA,
        'record' => empty($arRecord) ? $PRODUCT_ID : $arRecord,
        'goal'   => $goal
    );

    json_result(true, $arResult);
}


json_result(false, array('alert' => "unknown error"));
