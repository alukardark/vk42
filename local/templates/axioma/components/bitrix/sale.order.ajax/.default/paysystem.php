<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$STORES_KREDIT  = \WS_PSettings::getFieldValue("STORES_KREDIT", false);
$arStoresKredit = explode(" ", $STORES_KREDIT);
?>
<div class="order-block order-paysystem">
    <div class="order-block-title">Способ оплаты</div>
    <div class="order-paysystem-variants row">
        <?
        foreach ($arPaySystems as $arPaySystem):
            if (!empty($arStoresKredit) && $arPaySystem['ID'] == KPAYMENT_ID && !in_array($iBuyerStore, $arStoresKredit))
            {
                continue;
            }
            ?>
            <button
                class="order-checkbox <?= $arPaySystem['CHECKED'] == 'Y' ? "selected" : "" ?>"
                data-paysystem-id="<?= $arPaySystem['ID'] ?>"
                onclick="Order.changePaySystem(event, this)"
                >
                <i></i><span>
                    <?= $arPaySystem['NAME'] ?>
                </span>
            </button>

            <? if ($arPaySystem['CHECKED'] == "Y"): ?>
                <input type="hidden" id="PAY_SYSTEM_ID_OLD" value="<?= $arPaySystem['ID'] ?>" />
                <input type="hidden" id="PAY_SYSTEM_ID" value="<?= $arPaySystem['ID'] ?>" />
            <? endif; ?>
        <? endforeach; ?>
    </div>
</div>