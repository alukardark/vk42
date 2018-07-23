<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
//$orderId     = $arResult['ID'];
//$orderNumber = $arResult['ACCOUNT_NUMBER'];
////printrak($arResult);
////$order   = \Bitrix\Sale\Order::load($orderNumber);
//$order       = \Bitrix\Sale\Order::loadByAccountNumber($orderNumber);
//$discount    = $order->getDiscount();
//\Bitrix\Sale\DiscountCouponsManager::clearApply(true);
//\Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
//$discount->setOrderRefresh(true);
//$discount->setApplyResult(array());
//
///** @var \Bitrix\Sale\Basket $basket */
//$basket = $order->getBasket();
//
//$basket->refreshData(array('PRICE', 'COUPONS'));
//$discount->calculate();
//$order->save();


$pageTitle = "Заказ";
if (SHOW_ORDER_NUMBER) $pageTitle = " №" . $arResult["ACCOUNT_NUMBER"];

$APPLICATION->SetTitle($pageTitle);
$APPLICATION->AddChainItem($pageTitle);

$STATUS_S        = null;
$RESERVE_UNTIL   = null;
$MANAGER_COMMENT = null;
$DELIVERY_COST   = null;
$DELIVERY_DATE   = null;

foreach ($arResult["ORDER_PROPS"] as $prop)
{
    if ($prop["CODE"] === "STATUS_S")
    {
        $STATUS_S = $prop["VALUE"];
    }
    if ($prop["CODE"] === "RESERVE_UNTIL")
    {
        $RESERVE_UNTIL = $prop["VALUE"];
    }
    if ($prop["CODE"] === "MANAGER_COMMENT")
    {
        $MANAGER_COMMENT = $prop["VALUE"];
    }
    if ($prop["CODE"] === "DELIVERY_DATE")
    {
        $DELIVERY_DATE = $prop["VALUE"];
    }
    if ($prop["CODE"] === "DELIVERY_COST")
    {
        $DELIVERY_COST = (int) $prop["VALUE"];
    }
}

if (!empty($STATUS_S)) $arResult["STATUS"]["NAME"] = $STATUS_S;
?>
<? if (!empty($arResult['ERRORS']['FATAL'])): ?>
    <? foreach ($arResult['ERRORS']['FATAL'] as $error): ?>
        <?= ShowError($error) ?>
    <? endforeach; ?>
    <? return; ?>
<? endif; ?>

<? if (!empty($arResult['ERRORS']['NONFATAL'])): ?>
    <? foreach ($arResult['ERRORS']['NONFATAL'] as $error): ?>
        <?= ShowError($error) ?>
    <? endforeach; ?>
<? endif; ?>

<div class="order-detail">
    <div class="personal-title">Общая информация</div>

    <table class="order-detail-table modern-table">
        <tbody>
            <? if (SHOW_ORDER_NUMBER): ?>
                <tr>
                    <th>Номер заказа</th>
                    <td><?= $arResult["ACCOUNT_NUMBER"] ?></td>
                </tr>
            <? endif; ?>

            <tr>
                <th>Дата заказа</th>
                <td><?= $arResult["DATE_INSERT_FORMATED"] ?></td>
            </tr>

            <? if (0 && isAdmin()): ?>
                <tr>
                    <th>Срок резерва</th>
                    <td><?= $RESERVE_UNTIL ?></td>
                </tr>
            <? endif; ?>

            <tr>
                <th>Текущий статус</th>
                <td>
                    <?= $arResult['CANCELED'] !== 'Y' ? $arResult["STATUS"]["NAME"] : '<span class="red">Отменен</span>'; ?>
                </td>
            </tr>

            <? if (!empty($MANAGER_COMMENT)): ?>
                <tr>
                    <th>Комментарий менеджера</th>
                    <td>
                        <?= $MANAGER_COMMENT; ?>
                    </td>
                </tr>
            <? endif; ?>

            <? if ($arResult["CANCELED"] == "Y" || $arResult["CAN_CANCEL"] == "Y"): ?>
                <tr>
                    <th>Заказ отменен</th>
                    <td>
                        <? if ($arResult["CANCELED"] == "Y"): ?>
                            <?= GetMessage('SPOD_YES') ?>
                            <? if (strlen($arResult["DATE_CANCELED_FORMATED"])): ?>
                                (<?= GetMessage('SPOD_FROM') ?> <?= $arResult["DATE_CANCELED_FORMATED"] ?>)
                            <? endif; ?>
                        <? elseif ($arResult["CAN_CANCEL"] == "Y"): ?>
                            <?= GetMessage('SPOD_NO') ?>&nbsp;&nbsp;&nbsp;
                            <? if (1 || SITE_TEST || $USER->IsAdmin()): ?>
                                [<a href="<?= $arResult["URL_TO_CANCEL"] ?>&ID=<?= $arResult["ID"] ?>"><?= GetMessage("SPOD_ORDER_CANCEL") ?></a>]
                            <? endif; ?>
                            <!--[Для отмены заказа свяжитесь с менеджером интернет-магазина по телефону: 8 (903) 942 90 90]-->
                        <? endif; ?>
                    </td>
                </tr>

                <? if (0 && $arResult["CANCELED"] == "Y"): ?>
                    <tr>
                        <th>Причина отмены заказа</th>
                        <td>
                            <? if (!empty($arResult["REASON_CANCELED"])): ?>
                                <?= $arResult["REASON_CANCELED"] ?>
                            <? endif ?>
                        </td>
                    </tr>
                <? endif ?>
            <? endif ?>

            <tr>
                <th>Сумма заказа</th>
                <td><?= printPrice($arResult["PRICE"]) ?></td>
            </tr>

            <tr>
                <th>Оплачено</th>
                <td><?= printPrice($arResult["SUM_PAID"]) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="personal-title">Информация о покупателе</div>

    <table class="order-detail-table modern-table">
        <tbody>
            <tr class="hidden">
                <th>Тип плательщика</th>
                <td><?= $arResult["PERSON_TYPE"]["NAME"] ?></td>
            </tr>

            <? if (isset($arResult["ORDER_PROPS"])): ?>
                <?
                foreach ($arResult["ORDER_PROPS"] as $prop):
                    if (in_array($prop["CODE"], array(
                                "NOTIFICATION",
                                "PREPAY",
                                "SUBSCRIBE",
                                "DELIVERY_COST",
                                "BONUS_COUNT",
                                "KREDIT_ID",
                                "STATUS_S",
                                "RESERVE_UNTIL",
                                "MANAGER_COMMENT",
                            ))) continue;
                    ?>

                    <? if (0 & $prop["SHOW_GROUP_NAME"] == "Y"): ?>
                        <tr>
                            <td colspan="2"><?= $prop["GROUP_NAME"] ?></td>
                        </tr>
                    <? endif ?>

                    <tr>
                        <th><?= $prop['NAME'] ?></th>
                        <td>

                            <? if ($prop["TYPE"] == "CHECKBOX"): ?>
                                <?= GetMessage('SPOD_' . ($prop["VALUE"] == "Y" ? 'YES' : 'NO')) ?>
                            <? else: ?>
                                <?= $prop["VALUE"] ?>
                            <? endif ?>
                        </td>
                    </tr>
                <? endforeach ?>
            <? endif; ?>

            <? if (!empty($arResult["USER_DESCRIPTION"])): ?>
                <tr>
                    <th><?= GetMessage('SPOD_ORDER_USER_COMMENT') ?></th>
                    <td><?= $arResult["USER_DESCRIPTION"] ?></td>
                </tr>
            <? endif ?>

        </tbody>
    </table>

    <div class="personal-title">Информация об оплатах</div>

    <table class="order-detail-table modern-table">
        <tbody>
            <?
            foreach ($arResult['PAYMENT'] as $payment):
                printrak($payment);
                ?>
                <tr>
                    <th><?= GetMessage('SPOD_PAY_SYSTEM') ?></th>
                    <td>
                        <? if (intval($payment["PAY_SYSTEM_ID"])): ?>
                            <? if ($payment['PAY_SYSTEM']): ?>
                                <?= $payment["PAY_SYSTEM"]["NAME"] . ' (' . printPrice($payment['SUM']) . ')' ?>
                            <? else: ?>
                                <?= $payment["PAY_SYSTEM_NAME"] . ' (' . printPrice($payment['SUM']) . ')'; ?>
                            <? endif; ?>
                        <? else: ?>
                            <?= GetMessage("SPOD_NONE") ?>
                        <? endif ?>
                    </td>
                </tr>

                <tr>
                    <th><?= GetMessage('SPOD_ORDER_PAYED') ?></th>
                    <td>
                        <? if ($payment["PAID"] == "Y"): ?>
                            <?= GetMessage('SPOD_YES') ?>
                            <? if (strlen($payment["DATE_PAID_FORMATED"])): ?>
                                (<?= GetMessage('SPOD_FROM') ?> <?= $payment["DATE_PAID_FORMATED"] ?>)
                            <? endif; ?>
                        <? else: ?>
                            <?= GetMessage('SPOD_NO') ?>
                            <? if ($payment["CAN_REPAY"] == "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] == "Y" && !strstr("bill", $payment["PAY_SYSTEM"]["ACTION_FILE"])): ?>
                                &nbsp;&nbsp;&nbsp;[<a href="<?= $payment["PAY_SYSTEM"]["PSA_ACTION_FILE"] ?>" target="_blank">Оплатить</a>]
                            <? endif; ?>

                            <? if ($payment["PAY_SYSTEM"]["ID"] == BPAYMENT_ID && ($payment["CAN_REPAY"] == "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] != "Y") || strstr("bill", $payment["PAY_SYSTEM"]["ACTION_FILE"])): ?>
                                &nbsp;&nbsp;&nbsp;[<a href="<?= $payment["PAY_SYSTEM"]["PSA_ACTION_FILE"] ?>&pdf=1&DOWNLOAD=Y" target="_blank">Скачать счет</a>]
                            <? endif; ?>
                        <? endif; ?>
                    </td>
                </tr>

                <? if ($payment["PAY_SYSTEM"]["ID"] == BPAYMENT_ID && ($payment["CAN_REPAY"] == "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] != "Y") || strstr("bill", $payment["PAY_SYSTEM"]["ACTION_FILE"])): ?>
                    <tr class="hidden">
                        <td colspan="2">
                            <? if (array_key_exists('ERROR', $payment) && strlen($payment['ERROR']) > 0): ?>
                                <? ShowError($payment['ERROR']); ?>
                            <? elseif (array_key_exists('BUFFERED_OUTPUT', $payment)): ?>
                                <a
                                    href="<?= $payment["PAY_SYSTEM"]["PSA_ACTION_FILE"] ?>&pdf=1&DOWNLOAD=Y"
                                    target="_blank">Скачать счет
                                </a>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endif ?>
            <? endforeach; ?>
        </tbody>
    </table>

    <div class="personal-title">Информация о доставке</div>

    <table class="order-detail-table modern-table">
        <tbody>
            <? if (0 && !empty($DELIVERY_DATE)): ?>
                <tr>
                    <th>Дата доставки</th>
                    <td>
                        <?= $DELIVERY_DATE; ?>
                    </td>
                </tr>
            <? endif; ?>

            <? if (!empty($DELIVERY_COST)): ?>
                <tr>
                    <th>Стоимость доставки</th>
                    <td>
                        <?= printPrice($DELIVERY_COST); ?>
                    </td>
                </tr>
            <? endif; ?>

            <? foreach ($arResult['SHIPMENT'] as $shipment): ?>
                <tr>
                    <td colspan="2">
                        <? if (intval($shipment["DELIVERY_ID"])): ?>
                            <?= $shipment["DELIVERY"]["NAME"] ?>

                            <? if (intval($shipment['STORE_ID']) && !empty($arResult["DELIVERY"]["STORE_LIST"][$shipment['STORE_ID']])): ?>

                                <? $store = $arResult["DELIVERY"]["STORE_LIST"][$shipment['STORE_ID']]; ?>
                                <div class="bx_ol_store">
                                    <div class="bx_old_s_row_title">
                                        <?= GetMessage('SPOD_TAKE_FROM_STORE') ?>: <b><?= $store['TITLE'] ?></b>

                                        <? if (!empty($store['DESCRIPTION'])): ?>
                                            <div class="bx_ild_s_desc">
                                                <?= $store['DESCRIPTION'] ?>
                                            </div>
                                        <? endif ?>

                                    </div>

                                    <? if (!empty($store['ADDRESS'])): ?>
                                        <div class="bx_old_s_row">
                                            <b><?= GetMessage('SPOD_STORE_ADDRESS') ?></b>: <?= $store['ADDRESS'] ?>
                                        </div>
                                    <? endif ?>

                                    <? if (!empty($store['SCHEDULE'])): ?>
                                        <div class="bx_old_s_row">
                                            <b><?= GetMessage('SPOD_STORE_WORKTIME') ?></b>: <?= $store['SCHEDULE'] ?>
                                        </div>
                                    <? endif ?>

                                    <? if (!empty($store['PHONE'])): ?>
                                        <div class="bx_old_s_row">
                                            <b><?= GetMessage('SPOD_STORE_PHONE') ?></b>: <?= $store['PHONE'] ?>
                                        </div>
                                    <? endif ?>

                                    <? if (!empty($store['EMAIL'])): ?>
                                        <div class="bx_old_s_row">
                                            <b><?= GetMessage('SPOD_STORE_EMAIL') ?></b>: <a href="mailto:<?= $store['EMAIL'] ?>"><?= $store['EMAIL'] ?></a>
                                        </div>
                                    <? endif ?>

                                    <? if (($store['GPS_N'] = floatval($store['GPS_N'])) && ($store['GPS_S'] = floatval($store['GPS_S']))): ?>

                                        <div id="bx_old_s_map">

                                            <div class="bx_map_buttons">
                                                <a href="javascript:void(0)" class="bx_big bx_bt_button_type_2 bx_cart" id="map-show">
                                                    <?= GetMessage('SPOD_SHOW_MAP') ?>
                                                </a>

                                                <a href="javascript:void(0)" class="bx_big bx_bt_button_type_2 bx_cart" id="map-hide">
                                                    <?= GetMessage('SPOD_HIDE_MAP') ?>
                                                </a>
                                            </div>

                                            <? ob_start(); ?>
                                            <div><? $mg = $arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE']; ?>
                                                <? if (!empty($mg['SRC'])): ?><img src="<?= $mg['SRC'] ?>" width="<?= $mg['WIDTH'] ?>" height="<?= $mg['HEIGHT'] ?>"><br /><br /><? endif ?>
                                                <?= $store['TITLE'] ?></div>
                                            <? $ballon = ob_get_contents(); ?>
                                            <? ob_end_clean(); ?>

                                            <?
                                            $mapId  = '__store_map';

                                            $mapParams = array(
                                                'yandex_lat'   => $store['GPS_N'],
                                                'yandex_lon'   => $store['GPS_S'],
                                                'yandex_scale' => 16,
                                                'PLACEMARKS'   => array(
                                                    array(
                                                        'LON'  => $store['GPS_S'],
                                                        'LAT'  => $store['GPS_N'],
                                                        'TEXT' => $ballon
                                                    )
                                            ));
                                            ?>

                                            <div id="map-container">
                                                <?
                                                $APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
                                                    "INIT_MAP_TYPE" => "MAP",
                                                    "MAP_DATA"      => serialize($mapParams),
                                                    "MAP_WIDTH"     => "100%",
                                                    "MAP_HEIGHT"    => "200",
                                                    "CONTROLS"      => array(
                                                        0 => "SMALLZOOM",
                                                    ),
                                                    "OPTIONS"       => array(
                                                        0 => "ENABLE_SCROLL_ZOOM",
                                                        1 => "ENABLE_DBLCLICK_ZOOM",
                                                        2 => "ENABLE_DRAGGING",
                                                    ),
                                                    "MAP_ID"        => $mapId
                                                        ), false
                                                );
                                                ?>

                                            </div>

                                            <? CJSCore::Init(); ?>
                                            <script>
                                                new CStoreMap({mapId: "<?= $mapId ?>", area: '.bx_old_s_map'});
                                            </script>
                                        </div>
                                    <? endif ?>
                                </div>
                            <? endif ?>

                        <? else: ?>
                            <?= GetMessage("SPOD_NONE") ?>
                        <? endif ?>
                    </td>
                </tr>

                <? if ($shipment["TRACKING_NUMBER"]): ?>
                    <tr>
                        <th><?= GetMessage('SPOD_ORDER_TRACKING_NUMBER') ?></th>
                        <td><?= $shipment["TRACKING_NUMBER"] ?></td>
                    </tr>

                    <? if (isset($shipment["TRACKING_STATUS"])): ?>
                        <tr>
                            <th><?= GetMessage('SPOD_ORDER_TRACKING_STATUS') ?></th>
                            <td><?= $shipment["TRACKING_STATUS"] ?></td>
                        </tr>
                    <? endif ?>

                    <? if (!empty($shipment["TRACKING_DESCRIPTION"])): ?>
                        <tr>
                            <th><?= GetMessage('SPOD_ORDER_TRACKING_DESCRIPTION') ?></th>
                            <td><?= $shipment["TRACKING_DESCRIPTION"] ?></td>
                        </tr>
                    <? endif ?>
                <? endif ?>

                <tr class="hidden">
                    <th><?= GetMessage('SPOD_ORDER_SHIPMENT_BASKET') ?></th>
                    <td>
                        <? foreach ($shipment['ITEMS'] as $item): ?>
                            <?= $item['NAME'] . " (" . $item['QUANTITY'] . ' ' . $item['MEASURE_NAME'] . ") " ?><br>
                        <? endforeach; ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
    </table>

    <div class="personal-title">Содержимое заказа</div>

    <table class="order-detail-table-order modern-table">
        <thead>
            <tr>
                <td colspan="2" class="hidden-md-down"><?= GetMessage('SPOD_NAME') ?></td>
                <td class="hidden-md-down"><?= GetMessage('SPOD_PRICE') ?></td>

                <? if ($arResult['HAS_PROPS']): ?>
                    <td class="hidden-md-down"><?= GetMessage('SPOD_PROPS') ?></td>
                <? endif ?>

                <? if ($arResult['HAS_DISCOUNT']): ?>
                    <td class="hidden-md-down"><?= GetMessage('SPOD_DISCOUNT') ?></td>
                <? endif ?>

                <td class="hidden-md-down"><?= GetMessage('SPOD_QUANTITY') ?></td>
            </tr>
        </thead>

        <tbody>
            <? if (isset($arResult["BASKET"])): ?>
                <? foreach ($arResult["BASKET"] as $prod): ?>
                    <tr>
                        <? $hasLink = !empty($prod["DETAIL_PAGE_URL"]); ?>

                        <td class="order-detail-table-order-image">
                            <? if ($hasLink): ?>
                                <a href="<?= $prod["DETAIL_PAGE_URL"] ?>" target="_blank">
                                <? endif; ?>

                                <img
                                    src="<?= $prod['PICTURE_RESIZED'] ?>"
                                    width=""
                                    height="100"
                                    alt="<?= $prod['NAME'] ?>" />

                                <? if ($hasLink): ?>
                                </a>
                            <? endif; ?>
                        </td>

                        <td class="">
                            <? if ($hasLink): ?>
                                <a href="<?= $prod["DETAIL_PAGE_URL"] ?>" target="_blank">
                                <? endif; ?>
                                <?= htmlspecialcharsEx($prod["NAME"]) ?>
                                <? if ($hasLink): ?>
                                </a>
                            <? endif; ?>
                        </td>

                        <td class="">
                            <span class="hidden-md-up"><?= GetMessage('SPOD_PRICE') ?>:</span>
                            <?= printPrice($prod["PRICE"]) ?>
                        </td>

                        <? if ($arResult['HAS_PROPS']): ?>
                            <?
                            $actuallyHasProps = is_array($prod["PROPS"]) && !empty($prod["PROPS"]);
                            ?>
                            <td class="custom">
                                <? if ($actuallyHasProps): ?>
                                    <span class="fm"><?= GetMessage('SPOD_PROPS') ?>:</span>
                                <? endif ?>

                                <table cellspacing="0" class="bx_ol_sku_prop">
                                    <? if ($actuallyHasProps): ?>
                                        <? foreach ($prod["PROPS"] as $prop): ?>
                                            <? if (!empty($prop['SKU_VALUE']) && $prop['SKU_TYPE'] == 'image'): ?>
                                                <tr>
                                                    <td colspan="2">
                                                        <?= $prop["NAME"] ?>
                                                        <img src="<?= $prop['SKU_VALUE']['PICT']['SRC'] ?>" width="<?= $prop['SKU_VALUE']['PICT']['WIDTH'] ?>" height="<?= $prop['SKU_VALUE']['PICT']['HEIGHT'] ?>" title="<?= $prop['SKU_VALUE']['NAME'] ?>" alt="<?= $prop['SKU_VALUE']['NAME'] ?>" />
                                                    </td>
                                                </tr>
                                            <? else: ?>
                                                <tr>
                                                    <td><?= $prop["NAME"] ?></td>
                                                    <td><?= $prop["VALUE"] ?></td>
                                                </tr>
                                            <? endif; ?>
                                        <? endforeach; ?>
                                    <? endif; ?>
                                </table>
                            </td>
                        <? endif; ?>

                        <? if ($arResult['HAS_DISCOUNT']): ?>
                            <td class="">
                                <span class="hidden-md-up"><?= GetMessage('SPOD_DISCOUNT') ?>:</span>
                                <?= $prod["DISCOUNT_PRICE_PERCENT_FORMATED"] ?>
                            </td>
                        <? endif; ?>

                        <td class="">
                            <span class="hidden-md-up"><?= GetMessage('SPOD_QUANTITY') ?>:</span>
                            <?= $prod["QUANTITY"] ?>

                            <? if (strlen($prod['MEASURE_TEXT'])): ?>
                                <?= $prod['MEASURE_TEXT'] ?>
                            <? else: ?>
                                <?= GetMessage('SPOD_DEFAULT_MEASURE') ?>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            <? endif; ?>
        </tbody>
    </table>


    <table class="bx_ordercart_order_sum modern-table hidden">
        <tbody>
            <? ///// WEIGHT     ?>
            <? if (floatval($arResult["ORDER_WEIGHT"])): ?>
                <tr>
                    <td class="custom_t1"><?= GetMessage('SPOD_TOTAL_WEIGHT') ?>:</td>
                    <td class="custom_t2"><?= $arResult['ORDER_WEIGHT_FORMATED'] ?></td>
                </tr>
            <? endif; ?>

            <? ///// PRICE SUM         ?>
            <!--            <tr>
                            <td class="custom_t1"><?= GetMessage('SPOD_PRODUCT_SUM') ?>:</td>
                            <td class="custom_t2"><?= $arResult['PRODUCT_SUM_FORMATTED'] ?></td>
                        </tr>-->

            <? ///// DELIVERY PRICE: print even equals 2 zero     ?>
            <? if ($arResult["PRICE_DELIVERY_FORMATED"] > 0): ?>
                <tr>
                    <td class="custom_t1"><?= GetMessage('SPOD_DELIVERY') ?>:</td>
                    <td class="custom_t2"><?= printPrice($arResult["PRICE_DELIVERY"]) ?></td>
                </tr>
            <? endif; ?>

            <? ///// TAXES DETAIL     ?>
            <? foreach ($arResult["TAX_LIST"] as $tax): ?>
                <tr>
                    <td class="custom_t1"><?= $tax["TAX_NAME"] ?>:</td>
                    <td class="custom_t2"><?= printPrice($tax["VALUE_MONEY"]) ?></td>
                </tr>
            <? endforeach; ?>

            <? ///// DISCOUNT     ?>
            <? if (floatval($arResult["DISCOUNT_VALUE"])): ?>
                <tr>
                    <td class="custom_t1"><?= GetMessage('SPOD_DISCOUNT') ?>:</td>
                    <td class="custom_t2"><?= printPrice($arResult["DISCOUNT_VALUE"]) ?></td>
                </tr>
            <? endif; ?>

            <tr>
                <td class="custom_t1 fwb">Итого</td>
                <td class="custom_t2 fwb"><?= printPrice($arResult["PRICE"]) ?></td>
            </tr>
        </tbody>
    </table>
</div>