<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="order-block order-pickup order-pickup-cities">
    <div class="order-block-title">Город получения заказа</div>

    <div class="order-pickup-variants row">
        <?
        foreach ($arLocations as $arLocation):
            if ($arLocation["CODE"] == $LOCATION_SELECTED_CODE)
            {
                $LOCATION_SELECTED_ID   = $arLocation["ID"];
                $LOCATION_SELECTED_CITY = $arLocation["CITY_NAME"];
                $LOCATION_SELECTED_ZIP  = $arLocation["ZIP"];
            }
            ?>
            <button
                class="order-checkbox <?= $arLocation["CODE"] == $LOCATION_SELECTED_CODE ? "selected" : "" ?>"
                data-location-code="<?= $arLocation["CODE"] ?>"
                data-location-id="<?= $arLocation["ID"] ?>"
                data-location-name="<?= $arLocation["CITY_NAME"] ?>"
                data-location-key="<?= $arLocation["CITY_NAME_ORIG"] ?>"
                data-location-zip="<?= $arLocation["ZIP"] ?>"
                onclick="Order.changeLocation(event, this)"
                >
                <i></i><span><?= $arLocation['CITY_NAME'] ?></span>
            </button>
        <? endforeach; ?>
    </div>
    <div class="notice-warning" style="padding-top: 20px;"><? \Axi::GT("order/pickup") ?></div>


    <input type="hidden" name="ORDER_PROP_<?= $LOCATION_PROP_ID ?>" id="LOCATION_PROP" value="<?= $LOCATION_SELECTED_ID ?>" />
    <input type="hidden" data-property-code="ORDER_PROP_<?= $CITY_PROP_ID ?>" name="ORDER_PROP_<?= $CITY_PROP_ID ?>" id="CITY_PROP" value="<?= $LOCATION_SELECTED_CITY ?>" />
    <input type="hidden" data-property-code="ORDER_PROP_<?= $ZIP_PROP_ID ?>" name="ORDER_PROP_<?= $ZIP_PROP_ID ?>" id="ZIP_PROP" value="<?= $LOCATION_SELECTED_ZIP ?>" />
    <input type="hidden" data-property-code="ORDER_PROP_<?= $ADDRESS_PROP_ID ?>" name="ORDER_PROP_<?= $ADDRESS_PROP_ID ?>" id="ADDRESS_PROP" value="" />

    <input type="hidden" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?= $LOCATION_PROP_ID ?>]" id="LOCATION_ALT_PROP_DISPLAY_MANUAL" value="0" />
    <input type="hidden" name="RECENT_DELIVERY_VALUE" id="RECENT_DELIVERY_VALUE" value="" />
</div>