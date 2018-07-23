<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Корзина | Сервис-центры «ВК» — шины, масла, технические жидкости, обслуживание автомобиля");

use \Bitrix\Sale;

$arBasket  = array();
$arRecords = array();

if (!isBot())
{
    $arCoupons   = \CBasketExt::getCoupons();
    $BASKET_DATA = \CBasketExt::getBasketNew();

    $arRecords        = $BASKET_DATA["RECORDS"];
    $arBasketPrices   = $BASKET_DATA["PRICES"];
    $arApplyedCoupons = $BASKET_DATA["COUPON_LIST"];
}

$APPLICATION->SetTitle("Корзина");
?>

<div id="cart" class="cart row <?= empty($arRecords) ? "hidden" : "" ?>">
    <? $APPLICATION->IncludeFile("/include/kabinet/steps.php", array("step" => '1')); ?>

    <div class="cart-table">

        <table> 
            <thead class="hidden-lg-down">
                <tr class="cart-row">
                    <th class="cart-row__col-title">Наименование</th>
                    <th class="cart-row__col-code">Код товара</th>
                    <th class="cart-row__col-price">Цена</th>
                    <th class="cart-row__col-inbasket">Количество</th>
                    <th class="cart-row__col-sum">Стоимость</th>
                    <th class="cart-row__col-remove"></th>
                </tr>
            </thead>
            <tbody>
                <?
                foreach ($arRecords as $arRecord):

                    $PRODUCT_ID = $arRecord['PRODUCT_ID'];

                    $PRODUCT_NAME = $arRecord['NAME'];
                    $QUANTITY     = $arRecord['QUANTITY'];

                    $arPrices = $arRecord['PRICES'];

                    $arProduct = \CBasketExt::getProductInfo($PRODUCT_ID);

                    $arFields = $arProduct['FIELDS'];
                    $arProps  = $arProduct['PROPS'];

                    $sDetailSrc  = \CPic::getDetailSrc($arFields, 115, 115);
                    $sProductUrl = \CCatalogExt::getProductUrl($arFields);

                    $sArticle = $arProps['CML2_ARTICLE']['VALUE'];
                    $code     = $sArticle ?: 'Не указан';
                    ?>
                    <tr class="cart-row" data-product-id="<?= $PRODUCT_ID ?>">
                        <td class="cart-row__col-title">
                            <div class="cart-row__col-title__container">
                                <a href="<?= $sProductUrl ?>" target="_blank" title="<?= $PRODUCT_NAME ?>">
                                    <figure style="background-image: url(<?= $sDetailSrc ?>)"></figure>
                                    <span><?= $PRODUCT_NAME ?></span>
                                </a>
                                <div class="cart-row__col-title__container__code hidden-lg-up"><?= $code ?></div>
                            </div>
                        </td>

                        <td class="cart-row__col-code">
                            <?= $code ?>
                        </td>

                        <td class="cart-row__col-price">
                            <? if ($arPrices["DISCOUNT"]): ?>
                                <div class="oldprice" title="<?= $arPrices["BASE_PRICE"] ?>"><?= $arPrices["PRINT"]["BASE_PRICE"] ?></div>
                                <div class="item-price"><?= $arPrices["PRINT"]["DISCOUNT_PRICE"] ?></div>
                            <? else: ?>
                                <div class="item-price"><?= $arPrices["PRINT"]["BASE_PRICE"] ?></div>
                            <? endif; ?>
                        </td>

                        <td class="cart-row__col-inbasket">
                            <div class="inbasket">
                                <div class="inbasket__form" data-product-id="<?= $PRODUCT_ID ?>">
                                    <button
                                        class="inbasket__form__btn inbasket__form__btn--down"
                                        title="Уменьшить количество в корзине"
                                        data-product-id="<?= $PRODUCT_ID ?>"
                                        data-basket-action="minus"
                                        onclick="Basket.doAction(this)"
                                        ><i class="ion-android-remove"></i></button>
                                    <input
                                        type="text"
                                        title="Количество в корзине"
                                        value="<?= $QUANTITY ?>"
                                        class="inbasket__form__count"
                                        data-product-id="<?= $PRODUCT_ID ?>"
                                        data-basket-action="set"
                                        onchange="Basket.doAction(this)"
                                        />
                                    <button
                                        class="inbasket__form__btn inbasket__form__btn--up"
                                        title="Увеличить количество в корзине"
                                        data-product-id="<?= $PRODUCT_ID ?>"
                                        data-basket-action="plus"
                                        onclick="Basket.doAction(this)"
                                        ><i class="ion-android-add"></i></button>
                                </div>
                            </div>
                        </td>

                        <td class="cart-row__col-sum">
                            <? if ($arPrices["TOTAL_DISCOUNT"] > 0): ?>
                                <div
                                    data-total-base-price-product-id="<?= $PRODUCT_ID ?>"
                                    class="total-price oldprice <?= ($arPrices["TOTAL_BASE_PRICE"] > 100000) ? 'total-price-small' : '' ?>"
                                    title="<?= $arPrices["TOTAL_BASE_PRICE"] ?>"
                                    ><?= $arPrices["PRINT"]["TOTAL_BASE_PRICE"] ?>
                                </div>

                                <div
                                    data-total-discount-price-product-id="<?= $PRODUCT_ID ?>"
                                    class="total-price <?= ($arPrices["TOTAL_DISCOUNT_PRICE"] > 100000) ? 'total-price-small' : '' ?>"
                                    ><?= $arPrices["PRINT"]["TOTAL_DISCOUNT_PRICE"] ?>
                                </div>
                            <? else: ?>
                                <div
                                    data-total-base-price-product-id="<?= $PRODUCT_ID ?>"
                                    class="total-price <?= ($arPrices["TOTAL_BASE_PRICE"] > 100000) ? 'total-price-small' : '' ?>"
                                    ><?= $arPrices["PRINT"]["TOTAL_BASE_PRICE"] ?>
                                </div>
                            <? endif; ?>


                            <figure class="total-price-spinner"><i><i></i><i></i></i></figure>
                        </td>

                        <td class="cart-row__col-remove">
                            <button
                                title="Удалить из корзины"
                                data-product-id="<?= $PRODUCT_ID ?>"
                                data-basket-action="remove"
                                onclick="Basket.doAction(this)"
                                >
                                <i class="ion-android-close"></i>
                            </button>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>

        <div class="cart-ctrl row">
            <div class="col-16 col-md-24">
                <?
                foreach ($arCoupons as $arCoupon):
                    $COUPON               = $arCoupon["COUPON"];
                    $COUPON_TYPE          = $arCoupon["TYPE"]; //1 - на одну позицию заказа; 4 - многоразовый, 
                    $DISCOUNT_NAME        = $arCoupon["DATA"]["DISCOUNT_NAME"];
                    $DISCOUNT_DESCRIPTION = $arCoupon["DESCRIPTION"];

                    //check if coupon is apply
                    $selected = false;
                    foreach ($arApplyedCoupons as $arApplyedCoupon)
                    {
                        $JS_STATUS = $arApplyedCoupon["JS_STATUS"];

                        if (in_array($JS_STATUS, array("BAD", "ENTERED"))) continue;

                        if ($arApplyedCoupon["COUPON"] == $COUPON && in_array($JS_STATUS, array("APPLYED")))
                        {
                            $selected = true;
                            break;
                        }
                    }
                    ?>
                    <div class="cart-coupon-wrap">
                        <div
                            class="cart-coupon <?= $selected ? "selected" : "" ?>"
                            data-coupon="<?= $COUPON ?>"
                            onclick="Basket.applyCoupon(this)"
                            >
                            <i class="cart-coupon-box"></i>
                            <span class="cart-coupon-name"><?= $DISCOUNT_NAME ?></span>
                        </div>

                        <? if (!empty($DISCOUNT_DESCRIPTION)): ?>
                            <i
                                data-target="#<?= md5($COUPON) ?>"
                                onclick="App.showNoteTip(this)"
                                class="cart-coupon-help ion-ios-help-outline"
                                ></i>
                            <div
                                id="<?= md5($COUPON) ?>"
                                class="cart-coupon-description">
                                    <?= $DISCOUNT_DESCRIPTION ?>
                            </div>
                        <? endif; ?>
                    </div>
                <? endforeach; ?>
            </div>

            <div class="col-16 col-md-24 float-left">
                <div class="phone-note"><? \Axi::GT("phone"); ?></div>
                <div class="notice-warning" style="padding-top: 20px;"><? \Axi::GT("basket/another-city-info"); ?></div>
            </div>
            <div class="col-8 col-md-24 float-right">
                <button class="btn-cart-clear float-right float-md-none" onclick="Basket.clear(this)">
                    <span>Очистить корзину</span><i class="ion-android-close"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="cart-footer">
        <div class="cart-footer__content section row">
            <div class="col-24 row">
                <div class="cart-recom col-11 col-md-24 <?= \WS_PSettings::getFieldValue("SHOW_BONUSES_INFO", false) ? "" : "hidden" ?>">
                    <!--<div class="cart-recom-title">С этим товаром заказывают:</div>-->
                    <!--<div class="cart-recom-block"></div>-->
                    <!--<div class="cart-recom-block"></div>-->
                    <? \Axi::GT("catalog/catalog-detail-notes-bonus-full"); ?>
                    <div class="hidden-md-up">
                        <br/><br/>
                    </div>
                </div>

                <div class="cart-summary col-13 col-md-24 float-right">

                    <? if ($arBasketPrices["DISCOUNT"] > 0): ?>
                        <div class="cart-summary-price cart-summary-price-old row">
                            <span
                                class="cart-summary-price-title cart-summary-price-title-small float-left"
                                >Стоимость без скидки:
                            </span>
                            <span
                                class="cart-summary-price-value cart-summary-price-value-small float-right through lighter <?= $arBasketPrices["BASE_PRICE"] > 100000 ? "smaller" : "" ?>"
                                id="cart-summary-price-base"
                                ><?= $arBasketPrices["PRINT"]["BASE_PRICE"] ?>
                            </span>
                        </div>

                        <div class="cart-summary-price row">
                            <span
                                class="cart-summary-price-title cart-summary-price-title-small float-left"
                                >Итого к оплате:
                            </span>
                            <span
                                class="cart-summary-price-value float-right <?= $arBasketPrices["DISCOUNT_PRICE"] > 100000 ? "smaller" : "" ?>"
                                id="cart-summary-price-discount"
                                ><?= $arBasketPrices["PRINT"]["DISCOUNT_PRICE"] ?>
                            </span>
                        </div>
                    <? else: ?>
                        <div class="cart-summary-price">
                            <span class="cart-summary-price-title cart-summary-price-title-small float-left">Итого к оплате:</span>
                            <span
                                class="cart-summary-price-value float-right"
                                id="cart-summary-price-base"
                                ><?= $arBasketPrices["PRINT"]["BASE_PRICE"] ?>
                            </span>
                        </div>
                    <? endif; ?>

                    <div class="cart-summary-button">
                        <a href="<?= PATH_ORDER ?>" title="Оформить заказ" onclick="yaCounter12153865.reachGoal('perehod_oform');">
                            Оформить заказ<i class="ion-ios-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cart-empty" class="cart-empty <?= !empty($arRecords) ? "hidden" : "" ?>">
    <div class="cart-empty-content">
        <figure class="cart-empty-content-figure"></figure>
        <span class="cart-empty-content-note">Ваша корзина пуста</span>
        <a class="cart-empty-content-button" href="<?= PATH_CATALOG ?>" title="Перейти к каталогу">
            <span>Перейти к каталогу</span><i class="ion-ios-arrow-forward"></i>
        </a>
    </div>
</div>

<div class="backlink <?= empty($arRecords) ? "hidden" : "" ?>">
    <a href="<?= PATH_CATALOG ?>" title="Продолжить покупки">
        <i class="ion-ios-arrow-back"></i><span>Продолжить покупки</span>
    </a>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>