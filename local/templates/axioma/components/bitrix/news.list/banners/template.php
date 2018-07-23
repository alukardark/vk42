<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$iCounter = 0;
?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div class="banners-list row">
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $iCounter++;
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $sImageSrc    = $arItem["DETAIL_RESIZED"];
            $sTitle       = $arItem['NAME'];
            $sDescription = $arItem['DETAIL_TEXT'];
            $sButtonLink  = $arItem['PROPERTIES']['BUTTON_URL']['VALUE'];
            $sButtonTitle = $arItem['PROPERTIES']['BUTTON_TEXT']['VALUE'];
            $sButtonType  = $arItem['PROPERTIES']['BUTTON_TYPE']['VALUE_XML_ID'];
            ?>
            <a
            <?= !empty($sButtonLink) ? 'href="' . $sButtonLink . '"' : "" ?>
                title="<?= $sTitle ?>"
                class="banners-item"
                id="<?= $this->GetEditAreaId($arItem['ID']); ?>"
                <?= $sButtonType == "EXTERNAL" ? ' rel="nofollow" target="_blank" ' : '' ?>
                >
                <figure style="background-image: url(<?= $sImageSrc ?>)" title="<?= $sTitle ?>"></figure>

                <div class="banners-item-title"><?= $sTitle ?></div>
                <div class="banners-item-description"><?= $sDescription ?></div>

                <? if (!empty($sButtonLink)): ?>
                    <? if ($sButtonType == "POPUP"): ?>
                        <span role="button" class="banners-item-link" onclick="App.popup('<?= $sButtonLink ?>', 'popup')">
                            <?= $sButtonTitle ?><i class="ion-ios-arrow-right"></i>
                        </span>
                    <? else: ?>
                        <span role="button" class="banners-item-link">
                            <?= $sButtonTitle ?><i class="ion-ios-arrow-right"></i>
                        </span>
                    <? endif; ?>
                <? endif; ?>
            </a>
        <? endforeach; ?>
    </div>
<? endif; ?>