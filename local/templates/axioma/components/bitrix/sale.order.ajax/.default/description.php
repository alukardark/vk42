<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="order-block order-description">
    <div class="order-block-title">Комментарии к заказу</div>

    <div class="order-props-block-textarea">
        <span
            class="<?= !empty($ORDER_DESCRIPTION) ? "active" : "" ?>"
            onclick="Order.onClickPlaceholder(this)"
            >Комментарии к заказу</span>
        <textarea
            name="ORDER_DESCRIPTION"
            id="ORDER_DESCRIPTION"
            title="Комментарии к заказу"
            onfocus="Order.onInputFocus(this)"
            onblur="Order.onInputBlur(this)"
            ><?= $ORDER_DESCRIPTION ?></textarea>
    </div>
</div>