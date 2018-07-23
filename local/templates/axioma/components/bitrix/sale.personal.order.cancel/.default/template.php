<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$pageTitle = "Отмена заказа";
if (SHOW_ORDER_NUMBER) $pageTitle = " №" . $arResult["ACCOUNT_NUMBER"];

$APPLICATION->SetTitle($pageTitle);
$APPLICATION->AddChainItem($pageTitle);
?>

<div class="row auth reg order-cancel" style="background-color: #fff;">
    <div class="row auth-inner reg-inner">
        <div class="personal-title">
            Отмена <a href="<?= $arResult["URL_TO_DETAIL"] ?>">заказа</a>
        </div>

        <? if (strlen($arResult["ERROR_MESSAGE"]) <= 0): ?>
            <form method="post" action="<?= POST_FORM_ACTION_URI ?>">
                <? //= bitrix_sessid_post() ?>
                <input type="hidden" name="CANCEL" value="Y" />
                <input type="hidden" name="ID" value="<?= $_REQUEST["ID"] ?>" />

                <div class="col-24">
                    <div class="auth-form reg-form">
                        <div class="form-question form-question-textarea">
                            <span
                                class="form-question-placeholder active"
                                ><?= GetMessage("SALE_CANCEL_ORDER4") ?></span>

                            <textarea
                                name="REASON_CANCELED"
                                data-required="Y"
                                required="required"
                                maxlength="1000"
                                title="<?= GetMessage("SALE_CANCEL_ORDER4") ?>"
                                ></textarea>

                            <span class="form-question-description"><?= GetMessage("SALE_CANCEL_ORDER3") ?></span>
                        </div>

                        <div class="form-submit">

                            <input
                                type="submit"
                                class="form-submit-button"
                                name="action"
                                title="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>"
                                value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>"
                                />

                            <a
                                class="form-submit-button form-submit-button-cancel"
                                href="<?= $arResult["URL_TO_DETAIL"] ?>"
                                title="Отмена">Отмена
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        <? else: ?>
            <?= ShowError($arResult["ERROR_MESSAGE"]); ?>
        <? endif; ?>

    </div>
</div>