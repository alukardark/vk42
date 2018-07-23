<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="catalog-actions-shields">
    <? if ($arProps[SALE]['VALUE'] == "Да"): ?>
        <div class="catalog-actions-shields-item sale">
            <div>
                <i></i>Распродажа!
            </div>
        </div>
    <? endif; ?>

    <? if ($arProps[SALE_DAY]['VALUE'] == "Да"): ?>
        <div class="catalog-actions-shields-item saleday">
            <div>
                <i></i>Цена дня!
            </div>
        </div>
    <? endif; ?>

    <? if (!empty($arProps[BONUS]['VALUE'])): ?>
        <div class="catalog-actions-shields-item bonus">
            <div>
                <i></i><?= $arProps[BONUS]['VALUE'] ?>
            </div>
        </div>
    <? endif; ?>

    <? if ($arProps[HIT]['VALUE'] == "Да"): ?>
        <div class="atalog-actions-shields-item hit">
            <div>
                <i></i>Хит продаж!
            </div>
        </div>
    <? endif; ?>
</div>