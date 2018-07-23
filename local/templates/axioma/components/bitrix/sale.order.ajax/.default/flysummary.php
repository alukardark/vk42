<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div id="order-flysummary" class="order-flysummary">
    <div class="order-flysummary-title">Ваш заказ:</div>

    <div class="order-flysummary-block row">
        <div class="order-flysummary-block-descr">
            <strong id="summary-quantity"><?= $iQuantity ?></strong> <?= wordPlural($iQuantity, array("товар", "товара", "товаров")) ?> общей стоимостью:
        </div>
        <div class="order-flysummary-block-value" id="summary-price">
            <?= $sPriceFull ?>
        </div>
    </div>

    <?
    if (!empty($iBuyerStore)):
        $deliveryDate = $obOrderExt->getDeliveryDate($iBuyerStore);
        ?>
        <div class="order-flysummary-block row">
            <div class="order-flysummary-block-descr">
                Ориентировочная дата доставки до точки выдачи:
            </div>
            <div class="order-flysummary-block-value order-flysummary-block-value-smaller">
                <?= $deliveryDate ?>
            </div>

            <input
                type="hidden"
                data-property-code="<?= $arDeliveryDateProp["CODE"] ?>"
                name="ORDER_PROP_<?= $arDeliveryDateProp['ID'] ?>"
                id="<?= $arDeliveryDateProp["CODE"] ?>_PROP"
                value="<?= $deliveryDate ?>"
                />
        </div>
    <? endif; ?>

    <?
    //printrau($iDeliveryCost);
    if (empty($iBuyerStore)):
        $showDeliveryCost = $iDeliveryCost ? $iDeliveryCost . '<span class="rouble"></span>' : 'Бесплатно';
        $showDeliveryCostDesc = 'Стоимость доставки:';

        if ($DELIVERY_CHECKED == KDELIVERY_ID)
        {
            $notefile = "basket/basket-delivery-info-kemerovo";
        }
        elseif ($DELIVERY_CHECKED == TKDELIVERY_ID)
        {
            $notefile = "basket/basket-delivery-info-tk";
            $showDeliveryCost = $iDeliveryCost ?: 'Не рассчитана';
            $showDeliveryCostDesc = 'Ориентировочная стоимость доставки:';
        }
        else
        {
            $notefile = $iDeliveryCost ? "basket/basket-delivery-info" : "basket/basket-delivery-info__free";
        }

        ?>
        <div class="order-flysummary-block row">
            <div class="order-flysummary-block-descr">
                <?= $showDeliveryCostDesc ?>
            </div>
            <div class="order-flysummary-block-value order-flysummary-block-value-smaller">
                <div 
                    class="tooltip-block tooltip-block--inline" 
                    data-target="#delivery-info"
                    onmouseenter="App.showNoteTip(this)" 
                    onmouseleave="App.hideNoteTip(this)"
                    >
                    <div id="order-flysummary-delivery-cost"
                        class="tooltip-block__title" 
                        data-target="#delivery-info"
                        onclick="App.showNoteTip(this)"
                        >
                            <?= $showDeliveryCost ?>
                        &nbsp;<i class="ion-ios-help-outline"></i>
                    </div>
                    <div class="tooltip-block__text tooltip-block__text--right tooltip-block__text--width" id="delivery-info">
                        <? \Axi::GT($notefile); ?>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>

    <div class="order-flysummary-block row">
        <div class="phone-note order-phone-note"><? \Axi::GT("phone"); ?></div>
    </div>
</div>