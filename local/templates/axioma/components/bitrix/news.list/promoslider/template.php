<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div class="promo-slider-list">
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $sImageSrc    = $arItem["DETAIL_RESIZED"];
            $sTitle       = $arItem['NAME'];
            $sButtonLink  = $arItem['PROPERTIES']['BUTTON_URL']['VALUE'];
            $sButtonTitle = $arItem['PROPERTIES']['BUTTON_TEXT']['VALUE'];
            $sButtonType  = $arItem['PROPERTIES']['BUTTON_TYPE']['VALUE_XML_ID'];
            ?>
            <a
            <?= !empty($sButtonLink) ? 'href="' . $sButtonLink . '"' : "" ?>
                title="<?= $sTitle ?>"
                class="promo-slider-item"
                id="<?= $this->GetEditAreaId($arItem['ID']); ?>"
                <?= $sButtonType == "EXTERNAL" ? ' rel="nofollow" target="_blank" ' : '' ?>
                >
                <figure style="background-image: url(<?= $sImageSrc ?>)" title="<?= $sTitle ?>"></figure>

                <? if (!empty($sButtonLink)): ?>
                    <? if ($sButtonType == "POPUP"): ?>
                        <span role="button" class="promo-slider-item-link" onclick="App.popup('<?= $sButtonLink ?>', 'popup')">
                            <?= $sButtonTitle ?>
                        </span>
                    <? else: ?>
                        <span role="button" class="promo-slider-item-link">
                            <?= $sButtonTitle ?>
                        </span>
                    <? endif; ?>
                <? endif; ?>
            </a>
        <? endforeach; ?>
    </div>
<? endif; ?>