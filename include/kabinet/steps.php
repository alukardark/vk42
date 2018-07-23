<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$step = $arParams['step'];
?>
<div class="cart-steps hidden-sm-down">
    <div class="cart-steps-content section">
        <div class="cart-steps-item <?= $step == 1 ? "active" : "" ?>">
            <i>1</i>
            <span>Мой заказ</span>
        </div>
        <div class="cart-steps-item cart-steps-item--arrow"></div>
        <div class="cart-steps-item <?= $step == 2 ? "active" : "" ?>">
            <i>2</i>
            <span>Оформление <br class="hidden-md-up" />заказа</span>
        </div>
        <div class="cart-steps-item cart-steps-item--arrow"></div>
        <div class="cart-steps-item <?= $step == 3 ? "active" : "" ?>">
            <i>3</i>
            <span>Оплата</span>
        </div>
    </div>
</div>