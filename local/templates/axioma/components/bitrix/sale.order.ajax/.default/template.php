<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $USER;

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

$signer             = new \Bitrix\Main\Security\Sign\Signer;
$signedParams       = $signer->sign(base64_encode(serialize($arParams)), 'sale.order.ajax');
$signedParamsString = \CUtil::JSEscape($signedParams);

$SITE_ID = \CUtil::JSEscape($component->getSiteId());

$obOrderExt = \COrderExt::get($arResult);

$iQuantity = $obOrderExt->getQuantity();

$iPriceTotal = $iPriceFull  = $obOrderExt->getPrice('total', false);
$sPriceTotal = $sPriceFull  = $obOrderExt->getPrice('total');

$iPrepayKoeff   = $obOrderExt->getPrepayKoeff();
$iPricePrepay   = $obOrderExt->getPrice('prepay', false);
$sPricePrepay   = $obOrderExt->getPrice('prepay');
$arActionsGoals = $obOrderExt->getActionGoals();
?>

<? if (0 && empty($arResult['JS_DATA']['TOTAL'])): ?>
    <div id="order" class="order dddump">
        <div id="order-content" class="order-content">
            <div id="order-content" class="order-content">
                <div class="order-content-outer paddings">
                    <div class="order-content-wrap">
                        <span class="bold red">Заказ не найден</span>
                        <br/>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? return; ?>
<? endif; ?>
<?
/**
 * Массив $arResult модифицируется в Axi\Handlers\Sale
 */
$arErrors          = $arResult['ERROR'];
$GRID_ROWS         = $arResult['JS_DATA']["GRID"]["ROWS"];
$ORDER_DESCRIPTION = $arResult['JS_DATA']['ORDER_DESCRIPTION'];
$arPersonTypes     = $arResult['JS_DATA']['PERSON_TYPE'];
$arUserProfiles    = $arResult['JS_DATA']['USER_PROFILES'];
$arTotal           = $arResult['JS_DATA']['TOTAL'];
$arDeliveries      = $arResult['JS_DATA']['DELIVERY'];
$arPaySystems      = $arResult['JS_DATA']['PAY_SYSTEM'];
$arStores          = $arResult['JS_DATA']['STORE_LIST'];
$iBuyerStore       = $arResult['JS_DATA']['BUYER_STORE']; //точка выдачи товара. Может быть модифицирована в событии OnSaleComponentOrderOneStepDelivery
$arOrderProps      = $arResult['JS_DATA']['ORDER_PROP']; //array(groups => ..., properties => ... )
//printrau($arDeliveries);
/**
 * код склада ВК => код точки MoneyCare
 */
$arMoneyCareCodes  = array();
if (!empty($iBuyerStore))
{
    //соответствие точки выдачи и кода moneycare
    $arMoneyCareCodes = \COrderExt::getMoneyCarePoints();

    foreach ($arMoneyCareCodes as $storeID => $moneyCareCode)
    {
        if ($storeID == $iBuyerStore && empty($moneyCareCode))
        {
            foreach ($arPaySystems as $key => $arPaySystem)
            {
                if ($arPaySystem["PAY_SYSTEM_ID"] == KPAYMENT_ID)
                {
                    unset($arPaySystems[$key]);
                }
            }
        }
    }
}

$PROFILE_ID = null;
if (!empty($arUserProfiles))
{
    foreach ($arUserProfiles as $arUserProfile)
    {
        if ($arUserProfile['CHECKED'] == "Y")
        {
            $PROFILE_ID = $arUserProfile['ID'];
            break;
        }
    }
}

$btnTitle           = "Оформить заказ";
$PAY_SYSTEM_CHECKED = false;
if (!empty($arPaySystems))
{
    foreach ($arPaySystems as $arPaySystem)
    {
        if ($arPaySystem['CHECKED'] == "Y")
        {
            $PAY_SYSTEM_CHECKED = $arPaySystem;
            if ($arPaySystem["ID"] == EPAYMENT_ID)
            {
                $btnTitle = "Перейти к оплате";
            }
            elseif ($arPaySystem["ID"] == KPAYMENT_ID)
            {
                $btnTitle = "Оформить кредит";
            }
            else
            {
                $btnTitle = "Оформить заказ";
            }
        }
    }
}

$PERSON_TYPE_ID = FIZ_LICO;
foreach ($arPersonTypes as $arPersonType)
{
    if ($arPersonType["CHECKED"] == "Y")
    {
        $PERSON_TYPE_ID = $arPersonType["ID"];
    }
}

//ID свойства "Местоположение" для текущего типа плательщика
$LOCATION_PROP_ID    = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "LOCATION");
$CITY_PROP_ID        = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "CITY");
$ZIP_PROP_ID         = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "ZIP");
$ADDRESS_PROP_ID     = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "ADDRESS");
$BONUS_COUNT_PROP_ID = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "BONUS_COUNT");

//свойство "стоимость доставки"
$arDeliveryCostProp = \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_COST");
$arDeliveryDateProp = \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_DATE");
$arBonusCountProp   = \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "BONUS_COUNT");

//массив доступных местоположений
$arLocations = \Axi::getLocations();

//printrau($arOrderProps["properties"]);
//определим выбранное местоположение
$LOCATION_SELECTED_CODE = false;
$LOCATION_SELECTED_ID   = false;
$BONUS_COUNT_PROP_VALUE = false;

foreach ($arOrderProps["properties"] as $arOrderPropsProperties)
{
    if ($arOrderPropsProperties["ID"] == $LOCATION_PROP_ID)
    {
        $LOCATION_SELECTED_CODE = $arOrderPropsProperties["VALUE"][0];
        $LOCATION_SELECTED_ID   = \Axi::getLocationIdByCode($LOCATION_SELECTED_CODE);
    }

    if ($arOrderPropsProperties["ID"] == $BONUS_COUNT_PROP_ID)
    {
        $BONUS_COUNT_PROP_VALUE = $arOrderPropsProperties["VALUE"][0];

        $iPriceTotal  -= $BONUS_COUNT_PROP_VALUE;
        $iPricePrepay = $iPriceTotal * $iPrepayKoeff / 100;

        $sPriceTotal  = printPrice($iPriceTotal);
        $sPricePrepay = printPrice($iPricePrepay);
    }
}

if (empty($LOCATION_SELECTED_CODE))
{
    foreach ($arLocations as $arLocation)
    {
        if ($arLocation["SELECTED"])
        {
            $LOCATION_SELECTED_CODE = $arLocation["CODE"];
            $LOCATION_SELECTED_ID   = $arLocation["ID"];
            break;
        }
    }
}

// для пункта "Другой город"
if ($LOCATION_SELECTED_CODE == mb_strtolower(ANOTHER_CITY_CODE)) {
    $arDeliveryTKOptions = [
        'DELIVERY_TK' => \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_TK"),
        'DELIVERY_TK_NEED_DELIVERY' => \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_TK_NEED_DELIVERY"),
        'DELIVERY_TK_LATHING' => \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_TK_LATHING"),
        'DELIVERY_TK_INSURING' => \CSaleExt::getPropertyArrayByCode($PERSON_TYPE_ID, "DELIVERY_TK_INSURING"),
    ];
}

//удалим из служб доставки склады самовывоза, находящиеся в другом городе
$storesSorted   = \CCatalogExt::getStores(null, null, "Y", $arDelivery['STORE']);
$storeSortedIds = array();
foreach ($storesSorted as $storesSortedOne)
{
    $storeSortedIds[] = $storesSortedOne["ID"];
}

foreach ($arDeliveries as $k => $arDelivery)
{
    usort($arDeliveries[$k]['STORE'], function($a, $b) use($storeSortedIds) {
        $flipped = array_flip($storeSortedIds);

        $leftPos  = $flipped[$a];
        $rightPos = $flipped[$b];
        return $leftPos >= $rightPos;
    });
}



foreach ($arDeliveries as $k => $arDelivery)
{
    if (empty($arDelivery['STORE'])) continue;

    //if ($arDelivery["CHECKED"] != "Y") unset($arDeliveries[$k]);

    foreach ($arDelivery['STORE'] as $key => $iDeliveryStoreId)
    {
        if (empty($arStores[$iDeliveryStoreId])) unset($arDeliveries[$k]['STORE'][$key]);
    }
}

$DELIVERY_CHECKED = null;
foreach ($arDeliveries as $arDelivery)
{
    if ($arDelivery["CHECKED"] == "Y")
    {
        $DELIVERY_CHECKED = $arDelivery["ID"];
        break;
    }
}


if ($DELIVERY_CHECKED == KDELIVERY_ID)
{
    $iBuyerStore = null;
}

//определим срок доставки
//Id ГРУПП свйоств
$arPropsDelivery  = array(2, 11); //Данные для доставки
$arPropsPrepay    = array(7, 8); //Выберите сумму предоплаты
$arPropsSubscribe = array(9, 10); //Хотите получать новости и акции от вк сервис?
$arPropsService   = array(12, 13); //служебные свойства

$iDeliveryCost   = $obOrderExt->getDeliveryCost(false, $arDeliveries);
$arExcludedProps = array_merge($arPropsPrepay, $arPropsSubscribe, $arPropsDelivery, $arPropsService);
?>

<div id="order" class="order dddump">
    <?
    if (strlen($request->get('ORDER_ID')) > 0):
        $step = $PAY_SYSTEM_CHECKED["ID"] != KPAYMENT_ID ? 3 : 2;
        $APPLICATION->IncludeFile("/include/kabinet/steps.php", array("step" => $step));
        include($server->getDocumentRoot() . $templateFolder . "/confirm.php");
    else:
        $APPLICATION->IncludeFile("/include/kabinet/steps.php", array("step" => '2'));
        ?>
        <div id="order-content" class="order-content">
            <form id="order-form" method="POST" onsubmit="Order.submit(event, this)">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="signedParamsString" id="signedParamsString" value="<?= $signedParamsString ?>" />
                <input type="hidden" name="SITE_ID" id="SITE_ID" value="<?= $SITE_ID ?>" />
                <input type="hidden" name="PROFILE_ID" id="PROFILE_ID" value="<?= $PROFILE_ID ?>" />
                <input type="hidden" name="ACTIONS_GOALS" id="ACTIONS_GOALS" value='<?= json_encode($arActionsGoals) ?>' />

                <div class="order-content-outer paddings">
                    <div class="order-content-wrap">

                        <? include($server->getDocumentRoot() . $templateFolder . "/flysummary.php"); ?>

                        <div class="order-content-inner">
                            <? include($server->getDocumentRoot() . $templateFolder . "/errors.php"); ?>

                            <? if (!$USER->IsAuthorized() || isAdmin()): ?>
                                <? include($server->getDocumentRoot() . $templateFolder . "/person.php"); ?>
                            <? else: ?>
                                <? foreach ($arPersonTypes as $arPersonType): ?>
                                    <? if ($arPersonType['CHECKED'] == "Y"): ?>
                                        <input type="hidden" id="PERSON_TYPE_OLD" value="<?= $arPersonType['ID'] ?>" />
                                        <input type="hidden" id="PERSON_TYPE" value="<?= $arPersonType['ID'] ?>" />
                                    <? endif; ?>
                                <? endforeach; ?>
                            <? endif; ?>

                            <? $obOrderExt->showProps($arExcludedProps, array()); ?>

                            <? if (!empty($arLocations)): ?>
                                <? include($server->getDocumentRoot() . $templateFolder . "/pickup.php"); ?>
                            <? endif; ?>

                            <? if (!empty($arDeliveries)): ?>
                                <? include($server->getDocumentRoot() . $templateFolder . "/stores.php"); ?>
                            <? endif; ?>

                            <? include($server->getDocumentRoot() . $templateFolder . "/paysystem.php"); ?>
                            <? include($server->getDocumentRoot() . $templateFolder . "/description.php"); ?>

                            <div id="prepay-block">
                                <?
                                //Выберите сумму предоплаты
                                $obOrderExt->showProps(array(), $arPropsPrepay);
                                ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="order-footer">
                    <div class="order-footer-content paddings">
                        <div class="row">
                            <? include($server->getDocumentRoot() . $templateFolder . "/gift.php"); ?>
                            <? include($server->getDocumentRoot() . $templateFolder . "/bonus.php"); ?>
                            <? include($server->getDocumentRoot() . $templateFolder . "/summary.php"); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <? endif; ?>
</div>