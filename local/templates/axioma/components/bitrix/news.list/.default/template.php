<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div class="news-list">
        <? if (0 && $arParams["DISPLAY_TOP_PAGER"]): ?>
            <?= $arResult["NAV_STRING"] ?>
        <? endif; ?>

        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $sImageSrc  = $arItem["PREVIEW_RESIZED"];
            $sImageSrc  = $arItem["DETAIL_RESIZED"];
            $sTitle     = $arItem['NAME'];
            $sAnons     = $arItem["PREVIEW_TEXT"];
            $sDetail    = $arItem["DETAIL_TEXT"];
            $sDetailUrl = $arItem["DETAIL_PAGE_URL"];
            $sDate      = $arItem["DISPLAY_ACTIVE_FROM"];
            ?>
            <p class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">

            </p>
        <? endforeach; ?>

        <? if (0 && $arParams["DISPLAY_BOTTOM_PAGER"]): ?>
            <?= $arResult["NAV_STRING"] ?>
        <? endif; ?>
    </div>
<? endif; ?>