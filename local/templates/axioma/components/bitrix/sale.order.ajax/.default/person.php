<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="order-block order-person-type">
    <div class="order-block-title"><? \Axi::GT("kabinet/order-person-type-title") ?></div>
    <div class="order-person-type-variants">
        <? foreach ($arPersonTypes as $arPersonType): ?>
            <button
                class="order-radio inline <?= $arPersonType['CHECKED'] == "Y" ? "selected" : "" ?>"
                data-person-type="<?= $arPersonType['ID'] ?>"
                onclick="Order.changePersonType(event, this)"
                >
                <i></i><span><?= $arPersonType['NAME'] ?></span>
            </button>

            <? if ($arPersonType['CHECKED'] == "Y"): ?>
                <input type="hidden" id="PERSON_TYPE_OLD" value="<?= $arPersonType['ID'] ?>" />
                <input type="hidden" id="PERSON_TYPE" value="<?= $arPersonType['ID'] ?>" />
            <? endif; ?>
        <? endforeach; ?>
    </div>
</div>