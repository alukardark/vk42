<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="order-summary col-8 col-xl-10 col-lg-24">
    <? if ($PAY_SYSTEM_CHECKED["ID"] != KPAYMENT_ID): ?>
        <div class="order-summary-price row">
            <span class="order-summary-price-title">Общая сумма заказа:</span>
            <span class="order-summary-price-value" id="order-price-full" data-value="<?= $iPriceFull ?>">
                <?= $sPriceFull ?>
            </span>
        </div>

        <? if ($iPriceFull != $iPriceTotal): ?>
            <div class="order-summary-price row">
                <span class="order-summary-price-title">С учетом бонусов:</span>
                <span class="order-summary-price-value" id="order-price-total" data-value="<?= $iPriceTotal ?>">
                    <?= $sPriceTotal ?>
                </span>
            </div>
        <? endif; ?>

        <? if ($iPricePrepay != $iPriceTotal): ?>
            <div class="order-summary-price row">
                <span class="order-summary-price-title">Итого к предоплате:</span>
                <span class="order-summary-price-value" id="order-price-prepay" data-value="<?= $iPricePrepay ?>">
                    <?= $sPricePrepay ?>
                </span>
            </div>
        <? endif; ?>
    <? else: ?>
        <div class="order-summary-price row">
            <span class="order-summary-price-title">Стоимость товаров:</span>
            <span class="order-summary-price-value" id="order-price-full" data-value="<?= $iPriceFull ?>">
                <?= $sPriceFull ?>
            </span>
        </div>
    <? endif; ?>

    <div class="order-summary-button">
        <button onclick="Order.send(event, this)" title="<?= $btnTitle ?>">
            <?= $btnTitle ?><i class="ion-ios-arrow-right"></i>
        </button>
    </div>

    <div class="order-fakebox selected" onclick="Form.toggleFakeCheckbox(this)">
        <i></i><span><a href="/info/personal-information/" target="_blank" title="Подробнее">Согласие</a> на обработку персональных данных</span>
        <input type="hidden" name="CONSENT" id="CONSENT" value="1" />
    </div>
</div>