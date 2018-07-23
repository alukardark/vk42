<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
if (isAdmin() || SHOW_DELIVERY_CHECKBOX)
{

    if (!empty($arDeliveries) && count($arDeliveries) > 1):
        ?>
        <div class="order-block">
            <div class="order-block-title">Способ доставки</div>

            <?
            foreach ($arDeliveries as $arDelivery):
                $deliveryName = $arDelivery['NAME'] == "Точки выдачи товара" ? "Самовывоз" : $arDelivery['NAME'];
                ?>
                <button
                    class="order-checkbox order-checkbox-line <?= $arDelivery["CHECKED"] == "Y" ? "selected" : "" ?>"
                    data-delivery-id="<?= $arDelivery["ID"] ?>"
                    onclick="Order.changeDeliveyMethod(event, this)"
                    >
                    <i></i><span style="vertical-align: top !important;">
                        <strong  class="order-pickup-variants-title"><?= $deliveryName ?></strong>
                        <? if (!empty($arDelivery['DESCRIPTION'])): ?>
                            <br/><?= $arDelivery['DESCRIPTION'] ?>
                        <? endif; ?>
                    </span>
                </button>

                <? if ($arDelivery["CHECKED"] == "Y"): ?>
                    <input type="hidden" name="DELIVERY_ID" id="DELIVERY_ID" value="<?= $arDelivery['ID'] ?>" />
                <? endif; ?>

            <? endforeach; ?>
        </div>
        <?
    endif;

    unset($arDelivery);
}


foreach ($arDeliveries as $arDelivery):

    if (count($arDeliveries) > 1 && $arDelivery["CHECKED"] != "Y") continue;
    // Определяем точную стоимость доставки
    // @todo! Переделать. Потому что мне кажется, что сейчас 
    // как-то криао это опредедяется.
    // По-хорошему, это нужно пережелать функцию getDeliveryCost() в классе COrderExt
    $_iDeliveryCost = $iDeliveryCost;
    ?>
    <?
    if (!empty($arDelivery['STORE'])):

        $_iDeliveryCost = 0;
        ?>
        <div class="order-block order-pickup order-pickup-stores">
            <div class="order-block-title"><?= $arDelivery['NAME'] ?></div>
            <div class="order-pickup-variants row">
                <?
                foreach ($arDelivery['STORE'] as $iDeliveryStoreId):
                    $deliveryStoreDate = \COrderExt::getDeliveryDate($iDeliveryStoreId);
                    ?>
                    <button
                        class="order-checkbox order-checkbox-line <?= $iDeliveryStoreId == $iBuyerStore ? "selected" : "" ?>"
                        data-store-id="<?= $iDeliveryStoreId ?>"
                        onclick="Order.changeStore(event, this)"
                        >
                        <i></i><span class="order-pickup-variants-store">
                            <strong  class="order-pickup-variants-title"><?= $arStores[$iDeliveryStoreId]['TITLE'] ?></strong>
                            <? if (!empty($arStores[$iDeliveryStoreId]['ADDRESS'])): ?>
                                <br/><?= $arStores[$iDeliveryStoreId]['ADDRESS'] ?>
                            <? endif; ?>
                        </span>

                        <div class="order-pickup-variants-info">
                            <span class="order-pickup-variants-info-title">Забрать товар:</span>
                            <? if ($deliveryStoreDate == "Готов к выдаче"): ?>
                                <span class="order-pickup-variants-info-date green">сегодня</span>
                            <? else : ?>
                                <span class="order-pickup-variants-info-date"><?= $deliveryStoreDate ?></span>
                            <? endif; ?>
                        </div>
                    </button>
                <? endforeach; ?>
            </div>

            <input type="hidden" name="BUYER_STORE_OLD" id="BUYER_STORE_OLD" value="<?= $iBuyerStore ?>" />
            <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $iBuyerStore ?>" />
        </div>
    <? else: ?>
        <?
        //Адрес доставки
        if ($DELIVERY_CHECKED == KDELIVERY_ID)
        {
            $notefile = "basket/basket-address-delivery-info-kemerovo";
        }
        elseif ($DELIVERY_CHECKED == TKDELIVERY_ID)
        {
            $notefile = "basket/basket-address-delivery-info-tk";
        }
        else
        {
            $notefile = $iDeliveryCost ? "basket/basket-address-delivery-info" : "basket/basket-address-delivery-info__free";
        }
        $addressDeliverDesc = \Axi::GC($notefile, 'text');
        $obOrderExt->showProps(array(), $arPropsDelivery, $addressDeliverDesc);
        ?>

        <? if ($DELIVERY_CHECKED == TKDELIVERY_ID): ?>
        <div id="order-dc" class="order-block order-props-block order-dc">
            <figure class="form-spinner"><i></i><i></i></figure>

            <div class="order-block-title">Параметры доставки</div>

            <? foreach ($arDeliveryTKOptions as $dOption): ?>
            <? if ($dOption['CODE'] == 'DELIVERY_TK') continue; ?>
                <button class="order-checkbox" id="<?= $dOption['CODE'] ?>" data-hinput="<?= $dOption['CODE'] ?>_PROP"
                        onclick="Order.setDeliveryCalcOption(event, this);"
                >
                    <i></i>
                    <span>
                        <?= $dOption['NAME'] ?>
                        <span class="form-question-fakebox-descr" style="color: rgba(0,0,0,0.66) !important;">
                            <?= $dOption['DESCRIPTION'] ?>
                        </span>
                    </span>
                </button>
            <? endforeach; ?>

            <input class="form-submit-button disabled" title="Рассчитать" value="Рассчитать"
                   type="submit" onclick="Order.deliveryCalc(event, this);" id="ORDER_DC_SUBMIT"
            >

            <div id="order-dc-results" class="order-dc__results"></div>

            <? foreach ($arDeliveryTKOptions as $dOption): ?>
                <input type="hidden"
                       data-property-code="<?= $dOption['CODE'] ?>"
                       name="ORDER_PROP_<?= $dOption['ID'] ?>"
                       id="<?= $dOption['CODE'] ?>_PROP"
                       value="<?= $dOption['DEFAULT_VALUE'] ?>" />
            <? endforeach; ?>
        </div>
        <? endif; ?>

        <input type="hidden" name="BUYER_STORE_OLD" id="BUYER_STORE_OLD" value="" />
        <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="" />
    <? endif; ?>

    <? if (count($arDeliveries) === 1): ?>
        <input type="hidden" name="DELIVERY_ID" id="DELIVERY_ID" value="<?= $arDelivery['ID'] ?>" />
    <? endif; ?>

    <input
        type="hidden"
        data-property-code="<?= $arDeliveryCostProp["CODE"] ?>"
        name="ORDER_PROP_<?= $arDeliveryCostProp['ID'] ?>"
        id="<?= $arDeliveryCostProp["CODE"] ?>_PROP"
        value="<?= $_iDeliveryCost ?>"
        />
<? endforeach; ?>