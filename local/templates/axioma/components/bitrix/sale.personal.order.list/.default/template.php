<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$pageTitle = "История заказов";
$APPLICATION->SetTitle($pageTitle);

$STATUSES    = $arResult["INFO"]["STATUS"];
$PAY_SYSTEMS = $arResult["INFO"]["PAY_SYSTEM"];
$DELIVERIES  = $arResult["INFO"]["DELIVERY"];
$ORDERS      = $arResult["ORDERS"];
?>

<? if (!empty($arParams["NAV_TEMPLATE"])): ?>
    <div class="personal-title">История заказов</div>
<? else: ?>
    <div class="personal-title">Последние заказы</div>
<? endif; ?>

<? if (!empty($ORDERS)): ?>


    <div class="orders-list" id="orders-list">
        <table class=""> 
            <thead class="hidden-md-down">
                <tr class="">
                    <th class="">Дата</th>
                    <? if (SHOW_ORDER_NUMBER): ?>
                        <th class="">Номер</th>
                    <? endif; ?>
                    <th class="">Сумма</th>
                    <th class="">Оплачено</th>
                    <th class="">Статус</th>
                </tr>
            </thead>

            <tbody class="">
                <?
                foreach ($ORDERS as $arOrderItem):

                    $ORDER        = $arOrderItem["ORDER"];
                    $BASKET_ITEMS = $arOrderItem["BASKET_ITEMS"];
                    $SHIPMENT     = $arOrderItem["SHIPMENT"];
                    $PAYMENT      = $arOrderItem["PAYMENT"];

                    $order          = \Bitrix\Sale\Order::load($ORDER["ID"]);
                    $PERSON_TYPE_ID = $order->getPersonTypeId();
                    $PROPERTY_ID    = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "STATUS_S");

                    $propertyCollection = $order->getPropertyCollection();
                    $property           = $propertyCollection->getItemByOrderPropertyId($PROPERTY_ID);                    
                    $STATUS_S           = method_exists($property, 'getValue') ? $property->getValue("STATUS_S") : null;

                    if (!empty($STATUS_S)) $status = $STATUS_S;
                    else $status = $STATUSES[$ORDER["STATUS_ID"]]["NAME"];
                    ?>
                    <tr
                        title="Подробнее о заказе"
                        onclick="document.location = '<?= $ORDER["URL_TO_DETAIL"] ?>';"
                        class="modern-table"
                        >
                        <td class="nobr">
                            <span class="hidden-md-up">Дата: </span>
                            <?= $ORDER["DATE_INSERT_FORMATED"] ?>
                        </td>

                        <? if (SHOW_ORDER_NUMBER): ?>
                            <td class="nobr">
                                <span class="hidden-md-up">Номер: </span>
                                <?= $ORDER["ACCOUNT_NUMBER"] ?>
                            </td>
                        <? endif; ?>

                        <td class="bold nobr">
                            <span class="hidden-md-up">Сумма: </span>
                            <?= printPrice($ORDER["PRICE"]) ?>
                        </td>

                        <td class="nobr">
                            <span class="hidden-md-up">Оплачено: </span>
                            <?= printPrice($ORDER["SUM_PAID"]) ?>
                        </td>

                        <td class="">
                            <span class="hidden-md-up">Статус: </span>
                            <?= $ORDER['CANCELED'] != 'Y' ? $status : '<span class="red">Отменен</span>'; ?>

                            <div class="hidden-md-up">
                                <br/>
                                <a href="<?= $ORDER["URL_TO_DETAIL"] ?>" title="Подробнее">Детальная информция</a>
                            </div>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </div>

    <? if (empty($arParams["NAV_TEMPLATE"])): ?>
        <div>
            <a href="<?= PATH_PERSONAL_ORDERS ?>" title="История заказов">Полная история заказов</a>
        </div>
    <? endif; ?>
<? endif; ?>

<? if (empty($ORDERS)): ?>
    <div>
        Не найдено ни одного заказа
    </div>

    <? if (!empty($arParams["NAV_TEMPLATE"])): ?>
        <br/>
    <? endif; ?>
<? endif; ?>

<? if (!empty($arParams["NAV_TEMPLATE"]) && !empty($arResult['NAV_STRING'])): ?>
    <div class="orders-list-pagination">
        <?= $arResult['NAV_STRING'] ?>
    </div>
<? endif; ?>