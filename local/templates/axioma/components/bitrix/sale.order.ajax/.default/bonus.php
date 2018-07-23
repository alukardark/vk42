<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;
$USER_ID = (int) $USER->GetId();

if (!BONUSES_ENABLE)
{
    return;
}

if (empty($USER_ID) || $PERSON_TYPE_ID != FIZ_LICO)
{
    return;
}

if (!isPost())
{
    \CUserExt::updateUserFrom1C();
}

$CARD = \COrderExt::getCard();

if ($USER_ID === 1)
{
//    $CARD = array(
//        "TYPE"    => "PREMIUM",
//        "NUMBER"  => "666666",
//        "BALANCE" => "666666",
//    );
}

if (empty($CARD))
{
    return;
}

$CARD_TYPE    = $CARD["TYPE"];
$CARD_NUMBER  = $CARD["NUMBER"];
$CARD_BALANCE = $CARD["BALANCE"];

$BONUS_NOTE         = "bonus/bonus-note-" . strtolower($CARD_TYPE);
$BONUS_SAVE_HELP    = "bonus/bonus-save-" . strtolower($CARD_TYPE);
$BONUS_SAVE_HELP2   = "bonus/bonus-save2-" . strtolower($CARD_TYPE);
$CARD_TYPE_NAME     = \COrderExt::getCardTypeName($CARD_TYPE);
$BONUS_MAX_PERCENT  = \COrderExt::getCardMaxPercent($CARD_TYPE);
$BONUS_SAVE_PERCENT = \COrderExt::getCardSavePercent($CARD_TYPE);

$PAY_MAX_BY_BONUS = floor($iPriceFull * $BONUS_MAX_PERCENT / 100);
if ($PAY_MAX_BY_BONUS > $CARD_BALANCE) $PAY_MAX_BY_BONUS = $CARD_BALANCE;

$CARD_BALANCE_NUMBER = number_format($CARD_BALANCE, 0, 0, " ");
$CARD_BALANCE_TEXT   = wordPlural($CARD_BALANCE, array("бонус", "бонуса", "бонусов"));

//$SAVED_BONUSES = ceil($iPriceFull * $BONUS_SAVE_PERCENT / 100);
$SAVED_BONUSES        = \COrderExt::calculateOrderSaveBonuses($CARD_TYPE, $GRID_ROWS);
$SAVED_BONUSES_NUMBER = number_format($SAVED_BONUSES, 0, 0, " ");
$SAVED_BONUSES_TEXT   = wordPlural($SAVED_BONUSES, array("бонус", "бонуса", "бонусов"));
?>
<div class="order-bonus col-16 col-xl-14 col-lg-24">
    <div class="row">
        <div class="col-16 col-xl-20 col-lg-24">
            <div class="order-bonus-wrap">
                <div class="order-bonus-header order-bonus-header-<?= strtolower($CARD_TYPE) ?>">
                    <div class="order-bonus-header-title">
                        Вы владелец карты «<?= $CARD_TYPE_NAME ?>»
                    </div>

                    <div class="order-bonus-header-info">
                        На вашей карте: <span class="order-bonus-header-info-balans"><?= $CARD_BALANCE_NUMBER ?></span> <?= $CARD_BALANCE_TEXT ?>
                    </div>

                    <div class="order-bonus-header-note"><?= \Axi::GT($BONUS_NOTE) ?></div>
                </div>

                <div class="order-bonus-content">
                    <div class="order-bonus-variants">
                        <button
                            id="bonus1button"
                            class="order-radio <?= empty($BONUS_COUNT_PROP_VALUE) ? "selected" : "" ?>"
                            data-bonus-type="1"
                            data-input-target="<?= $arBonusCountProp["CODE"] ?>_PROP"
                            onclick="Order.changeBonusType(event, this);"
                            >
                            <i></i>
                            <span>Копить бонусы</span>

                            <? if (!\Axi::fileEmpty($BONUS_SAVE_HELP2, 'text')): ?>
                                <div
                                    class="order-bonus-save order-bonus-help"
                                    data-target="#bonus-save"
                                    onmouseenter="App.showNoteTip(this)"
                                    onmouseleave="App.hideNoteTip(this)"
                                    >
                                    <div
                                        class="order-bonus-help-short noselect"
                                        data-target="#bonus-save"
                                        onclick="App.showNoteTip(this)"
                                        >
                                        <mark class="ion-ios-help-outline"></mark>
                                    </div>
                                    <div id="bonus-save" class="order-bonus-help-full"><?= \Axi::GT($BONUS_SAVE_HELP2) ?></div>
                                </div>
                            <? endif; ?>

                        </button>

                        <div class="order-bonus-accured">
                            <b><?= $SAVED_BONUSES_NUMBER ?> <?= $SAVED_BONUSES_TEXT ?></b> будет начислено на бонусную карту после оплаты данного заказа
                        </div>

                        <button
                            id="bonus2button"
                            class="order-radio <?= empty($BONUS_COUNT_PROP_VALUE) ? "" : "selected" ?> <?= empty($PAY_MAX_BY_BONUS) ? "disabled" : "" ?>"
                            data-bonus-type="2"
                            data-input-target="<?= $arBonusCountProp["CODE"] ?>_PROP"
                            onclick="Order.changeBonusType(event, this)"
                            >
                            <i></i>
                            <span>Списать бонусы</span>
                        </button>

                        <div
                            class="order-bonus-pay <?= empty($PAY_MAX_BY_BONUS) ? "disabled" : "" ?>"
                            onclick="Order.changeBonusType(event, document.getElementById('bonus2button'))"
                            >
                            <input
                                id="BONUS_COUNT"
                                type="number"
                                autocomplete="off"
                                title="Количество списываемых бонусов"
                                pattern="[0-9]*"
                                step="100"
                                min="1"
                                max="<?= $PAY_MAX_BY_BONUS ?>"
                                value="<?= empty($BONUS_COUNT_PROP_VALUE) || $BONUS_COUNT_PROP_VALUE > $PAY_MAX_BY_BONUS ? $PAY_MAX_BY_BONUS : $BONUS_COUNT_PROP_VALUE ?>"
                                data-input-target="<?= $arBonusCountProp["CODE"] ?>_PROP"
                                onclick2="App.selectAll(event, this);"
                                onchange="Order.onBonusChange(event, this)"
                                onkeyup="Order.onBonusKeyUp(event, this)"
                                onmouseup2="Order.onBonusKeyUp(event, this)"
                                />
                        </div>
                    </div>
                </div>

                <div
                    class="order-bonus-help"
                    data-target="#bonus-help"
                    onmouseenter="App.showNoteTip(this)"
                    onmouseleave="App.hideNoteTip(this)"
                    >
                    <div
                        class="order-bonus-help-short noselect"
                        data-target="#bonus-help"
                        onclick="App.showNoteTip(this)"
                        >
                        <mark class="ion-ios-help-outline <?= strtolower($CARD_TYPE) ?>"></mark>
                    </div>
                    <div id="bonus-help" class="order-bonus-help-full"><?= \Axi::GT($BONUS_SAVE_HELP) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<input
    type="hidden"
    data-property-code="<?= $arBonusCountProp["CODE"] ?>"
    name="ORDER_PROP_<?= $arBonusCountProp['ID'] ?>"
    id="<?= $arBonusCountProp["CODE"] ?>_PROP"
    value="<?= !empty($BONUS_COUNT_PROP_VALUE) && $BONUS_COUNT_PROP_VALUE <= $PAY_MAX_BY_BONUS ? $BONUS_COUNT_PROP_VALUE : "" ?>"
    />