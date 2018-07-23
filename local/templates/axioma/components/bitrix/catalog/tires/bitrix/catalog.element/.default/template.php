<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
global $USER;
?>
<? if (!empty($arResult['ID'])): ?>
    <?
    $component->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], \CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));

    global $USER;

    $IBLOCK_ID = $arResult['IBLOCK_ID'];

    $arPrices = $arResult['PRICES'];
    $arProps  = $arResult['PROPERTIES'];

    $arDisplayProps = $arResult['DISPLAY_PROPERTIES'];

    $sName       = empty($arResult['GENERATED_NAME']) ? $arResult['NAME'] : $arResult['GENERATED_NAME'];
    $sDetailText = $arResult['DETAIL_TEXT'];
    $sUrl        = $arResult['DETAIL_PAGE_URL'];

    $sDetailPicture = $arResult['DETAIL_BIG'];
    $sPicture       = $arResult['DETAIL_RESIZED'];
    $sPictureAlt    = $arResult['DETAIL_PICTURE']['ALT'];
    $sPictureTitle  = $arResult['DETAIL_PICTURE']['TITLE'];

    $iCatalogQuantity = $arResult['STORES_AMOUNT']; //общее доступное кол-во на всех складах
    $iRecordQuantity  = (int) $arResult['BASKET_RECORD_QUANTITY']; //колисетсво товара, которое уже добавлено в корзину
    $iMaxQuantity     = $iCatalogQuantity - $iRecordQuantity; //количество которое еще можно добавить в корзину
    if ($iMaxQuantity > MAX_QUANTITY) $iMaxQuantity     = MAX_QUANTITY;
    ?>
    <div class="catalog-detail-picture col-12 col-md-24">
        <figure class="zoom" data-src="<?= $sDetailPicture ?>">
            <? $APPLICATION->IncludeFile("/include/catalog/actions_shields.php", array("arProps" => $arProps), array("SHOW_BORDER" => false)); ?>
            <img src="<?= $sPicture ?>" alt="<?= $sPictureAlt ?>" title="<?= $sPictureTitle ?>" />
        </figure>
    </div>

    <div class="catalog-detail-info col-12 col-md-24" id="<?= $component->GetEditAreaId($arResult['ID']) ?>">
        <span
            class="catalog-detail-title hidden"
            style="display: block;margin: 0 0 1em;"
            ><?= $sName ?></span>

        <? if (!empty($arDisplayProps)): ?>
            <dl class="catalog-detail-props row" style="margin-top: 0">
                <?
                foreach ($arDisplayProps as $arProperty) :
                    if (empty($arProperty['DISPLAY_VALUE']) && empty($arProperty['VALUE']))
                            $arProperty['VALUE'] = "&nbsp;";
                    ?>
                    <dt><span><?= $arProperty['NAME'] ?>:</span></dt>
                    <dd><?= !empty($arProperty['DISPLAY_VALUE']) ? $arProperty['DISPLAY_VALUE'] : $arProperty['VALUE'] ?></dd>
                <? endforeach; ?>
            </dl>
        <? endif; ?>

        <? if ($USER->IsAdmin() && $_REQUEST['allprops'] != 'allprops'): ?>
            <a href="<?= $arResult['DETAIL_PAGE_URL'] ?>?allprops=allprops">Все свойства</a>
        <? endif; ?>

        <? if ($USER->IsAdmin() && $_REQUEST['allprops'] == 'allprops'): ?>
            <a href="<?= $arResult['DETAIL_PAGE_URL'] ?>">Основные свойства</a>
        <? endif; ?>

        <? if ($IBLOCK_ID == AKB_IB && $arProps[SALE]['VALUE'] == "Да"): ?>
            <div class="catalog-detail-akb-disclaimer ve">
                <?= \Axi::GT("catalog/catalog-detail-akb-disclaimer"); ?>
            </div>
        <? endif; ?>

        <div class="catalog-detail-prices row">

            <?
            $HIDDEN_DISCOUNTS_IBLOCK_ID = $arParams['HIDDEN_DISCOUNTS_IBLOCK_ID'];
            //$bDiscount                  = $arPrices['VALUE'] != $arPrices['DISCOUNT_VALUE'];
            $bDiscount                  = true;

            $showOldPrice = true;
            if (in_array($IBLOCK_ID, $HIDDEN_DISCOUNTS_IBLOCK_ID))
            {
                $showOldPrice = false;
            }
            ?>
            <? if (USE_DISCOUNT): ?>
                <div class="catalog-detail-prices-row row <?= $showOldPrice ?: "hidden" ?>">
                    <span class="catalog-detail-prices-description float-left">Стоимость в розничном магазине</span>
                    <span class="catalog-detail-prices-discount float-right"><?= printPrice($arPrices[RETAIL_PRICE_NAME]['VALUE']) ?></span>
                </div>

                <div class="catalog-detail-prices-row row">
                    <span class="catalog-detail-prices-description float-left">Стоимость на сайте</span>
                    <span class="catalog-detail-prices-priceinline float-right"><?= printPrice($arPrices[CATALOG_PRICE_NAME]['VALUE']) ?></span>
                </div>
            <? else: ?>
                <span class="catalog-detail-prices-price"><?= printPrice($arPrices[CATALOG_PRICE_NAME]['VALUE']) ?></span>
            <? endif; ?>
        </div>

        <div class="catalog-detail-available">
            <span class="catalog-detail-available__quantity">
                В наличии: <b>
                    <? if ($iCatalogQuantity > 4): ?>
                        от 4 штук
                    <? elseif ($iCatalogQuantity > 0): ?>
                        <?= $iCatalogQuantity ?> шт.
                    <? else: ?>
                        под заказ
                    <? endif; ?></b>
            </span>
            <a href="#" class="btn-clarify"><i class="ion-ios-information"></i> <span>Уточнить наличие</span></a>
        </div>

        <div id="detail-buy" class="catalog-detail-buy <?= $iMaxQuantity > 0 ? "" : "disabled" ?>">
            <div class="catalog-detail-buy-quantity row noselect">
                <button
                    class="noselect"
                    title="Уменьшить количество"
                    onclick="Basket.setInput(this, 'minus');"
                    ><i class="ion-android-remove"></i></button>
                <input
                    id="quantity-input"
                    data-quantity="Y"
                    data-max-value="<?= $iMaxQuantity ?>"
                    data-product-id="<?= $arResult['ID'] ?>"
                    value="1"
                    type="text"
                    title="Добавить количество в корзину"
                    onchange="Basket.onInputChange(this)"
                    onkeyup="Basket.onInputKeyUp(event, this)"
                    />
                <button
                    class="noselect"
                    title="Увеличить количество"
                    onclick="Basket.setInput(this, 'plus');"
                    ><i class="ion-android-add"></i></button>

                <a
                    href="<?= PATH_BASKET ?>"
                    title="В корзину"
                    id="quantity-inbasket"
                    class="catalog-detail-buy-quantity-inbasket <?= $iRecordQuantity > 0 ? "" : "hidden" ?>"
                    >В корзине <b><?= $iRecordQuantity ?></b> шт.
                </a>
            </div>

            <div class="catalog-detail-buy-buttons noselect">
                <button
                    class="catalog-detail-buy-regular noselect"
                    data-product-id="<?= $arResult['ID'] ?>"
                    data-basket-action="plus_quantity"
                    title="В корзину"
                    onclick="Basket.doAction(this);"
                    >
                    <mark><i></i><i></i><i></i></mark>В корзину
                </button>

                <button
                    id="buy-oneclick"
                    class="catalog-detail-buy-oneclick noselect"
                    data-product-name="<?= $arResult['NAME'] ?>"
                    data-product-xml-id="<?= $arResult['EXTERNAL_ID'] ?>"
                    data-quantity="1"
                    data-user-id="<?= $USER->GetID() ?>"
                    data-user-city="<?= $arResult['USER_CITY'] ?>"
                    data-user-name="<?= $USER->GetFullName() ?>"
                    data-user-phone="<?= \CUserExt::getPhone() ?>"
                    title="Купить в 1 клик"
                    onclick="Form.toggleBuyOneClickForm(this);"
                    >Купить в 1 клик</button>
            </div>
        </div>

        <?
        if (isPost("get_detail_notes"))
        {
            $APPLICATION->RestartBuffer();
        }
        ?>
        <div class="catalog-detail-notes clearfix" id="detail-notes">
            <mark class='catalog-detail-notes-spinner'><i></i><i></i><i></i></mark>

            <?
            if ($arResult['DELIVERY_DATE_PRINT'] !== null):
                ?>
                <div class="catalog-detail-notes-ship">
                    <span>
                        <?= htmlspecialcharsBack($arResult['DELIVERY_DATE_PRINT']) ?>
                    </span>
                </div>
            <? endif; ?>

            <div class="catalog-detail-notes-credit">
                <span><?= \Axi::GT("catalog/catalog-detail-notes-credit"); ?></span>
            </div>

            <? if ((isAdmin() || SHOW_HELP_AKB) && \CSite::InDir('/akkumulyatory/')): ?>
                <div class="catalog-detail-notes-helpbutton catalog-helpbutton">
                    <div
                        class="noselect"
                        title="Помощь в подборе аккумулятора"
                        onclick="Form.toggleForm(this);"
                        data-form="#help_akb"
                        >
                        <mark><i></i></mark><span>Помощь в подборе аккумулятора</span>
                    </div>
                </div>
            <? endif; ?>

            <div
                class="catalog-detail-notes-bonus <?= WS_PSettings::getFieldValue("SHOW_BONUSES_INFO", false) ? "" : "hidden" ?>"
                data-target="#bonus-full"
                onmouseenter="App.showNoteTip(this)"
                onmouseleave="App.hideNoteTip(this)"
                >
                <span
                    class="catalog-detail-notes-bonus-short noselect"
                    data-target="#bonus-full"
                    onclick="App.showNoteTip(this)"
                    >
                    <span class="red bold"><?= \Axi::GT("catalog/catalog-detail-notes-bonus-short", "bonus-short"); ?></span>
                    <i class="ion-ios-help-outline"></i>
                </span>
                <span
                    id="bonus-full"
                    class="catalog-detail-notes-bonus-full">
                        <?= \Axi::GT("catalog/catalog-detail-notes-bonus-full"); ?>
                </span>
            </div>
        </div>
        <?
        if (isPost("get_detail_notes"))
        {
            die;
        }
        ?>
    </div>
<? else: ?>
    <div class="catalog-detail-title">Элемент не найден</div>
<? endif; ?>