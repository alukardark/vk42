<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<div class="uslugi-cards">
    <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

        //printra($arResult["ITEMS"]);
        $sName        = $arItem["NAME"];
        $sDescription = $arItem["DETAIL_TEXT"];
        $sPriceExact  = $arItem["PROPERTIES"]["IS_PRICE_EXACT"]["VALUE_XML_ID"] != "Y" ? 'от&nbsp;' : "";
        $sPrice       = printPrice($arItem["PROPERTIES"]["PRICE"]["VALUE"]);
        $arStores     = $arItem["PROPERTIES"]["STORES"]["VALUE"];
        ?>
        <div
            class="uslugi-card <?= !$arItem["ACTIVE"] ? "inactive" : "" ?>"
            id="<?= $this->GetEditAreaId($arItem['ID']); ?>"
            data-element-id="<?= $arItem['ID'] ?>"
            >
            <div class="uslugi-card-content row">
                <div
                    class="uslugi-card-arrow"
                    onclick="Services.toggleDescription(this);"
                    title="Подробнее об услуге"
                    >
                    <i class="ion-chevron-down ion-thin"></i>
                </div>

                <div
                    class="uslugi-card-title"
                    onclick="Services.toggleDescription(this);"
                    title="Подробнее об услуге"
                    >
                    <span><?= $sName ?></span>
                </div>

                <div class="uslugi-card-priceblock row">
                    <!--<div class="uslugi-card-priceblock-price col-10">-->
                    <div class="uslugi-card-priceblock-price col-24">
                        <span class="uslugi-card-priceblock-price-exact"><?= $sPriceExact ?></span>
                        <span class="uslugi-card-priceblock-price-value"><?= $sPrice ?></span>
                    </div>
                    <!--                    <div class="uslugi-card-priceblock-button col-14">
                                            <button title="Записаться на <?= htmlspecialcharsbx($sName) ?>">Записаться</button>
                                        </div>-->
                </div>
            </div>

            <div class="uslugi-card-description">
                <div class="uslugi-card-description-content ve">
                    <?= $sDescription ?>
                </div>
            </div>
        </div>
    <? endforeach; ?>
</div>