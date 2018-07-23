<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//$this->createFrame()->begin('');

$arBasket = $arResult["BASKET"];
?>
<? if (!empty($arBasket)): ?>
    <nav id="basket-line" class="nav-cart">
        <a href="<?= PATH_BASKET ?>" class="nav-cart-link" title="Корзина">
            <div class="nav-cart-link-summ hidden-xl-down js-line-price"><?= $arBasket['PRICES']['PRINT']['DISCOUNT_PRICE'] ?></div>
            <div class="nav-cart-link-quantity js-line-quantity"><?= $arBasket['QUANTITY'] ?></div>
        </a>
        <div class="nav-cart-notice js-cart-notice">
            <span>Товар добавлен</span>
        </div>
    </nav>
<? endif; ?>