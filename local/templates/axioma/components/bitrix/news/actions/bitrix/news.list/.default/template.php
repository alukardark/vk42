<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div class="actions-list row">
        <?
        $i = -1;
        foreach ($arResult["ITEMS"] as $arItem):
            ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], \CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $sName        = $arItem['NAME'];
            $sText        = $arItem["PREVIEW_TEXT"];
            $sUrl         = NormalizeLink($arItem['DETAIL_PAGE_URL'] . "/");
            $sPicture     = $arItem['PREVIEW_RESIZED'];
            $isMainAction = $arItem['PROPERTIES']['PROMO']['VALUE_XML_ID'] == "Y";

            if (!$isMainAction) $i++;
            $sDate = $isMainAction ? FormatDateFromDB($arItem["DATE_ACTIVE_FROM"], "DD MMMM YYYY") : $arItem["DISPLAY_ACTIVE_FROM"];
            ?>
            <div
                class="actions-list-item col-12 col-md-24 <?= $isMainAction ? "main" : "" ?> <?= !$isMainAction && $i % 2 ? "right" : "" ?>"
                id="<?= $this->GetEditAreaId($arItem['ID']) ?>"
                >
                <a
                    href="<?= $sUrl ?>"
                    title="<?= $sName ?>"
                    class="actions-list-item-content"
                    >
                    <div class="actions-list-item-preview">
                        <figure class="actions-list-item-preview-picture">
                            <img src="<?= $sPicture ?>" alt="<?= $sName ?>" />
                        </figure>

                        <span class="actions-list-item-preview-date"><?= $sDate ?></span>
                        <div class="actions-list-item-preview-button">
                            <span>Подробнее</span><i class="ion-ios-arrow-forward"></i>
                        </div>
                    </div>

                    <div class="actions-list-item-anons">
                        <span class="actions-list-item-anons-title"><?= $sName ?></span>
                        <div class="actions-list-item-anons-text"><?= $sText ?></div>
                    </div>
                </a>
            </div>
        <? endforeach; ?>
    </div>

    <? if ($arResult['MORE_COUNT'] > 0): ?>
        <div class="actions-list-more">
            <button
                data-navnum="<?= $arResult["NavNum"] ?>"
                data-pagenomer="<?= $arResult["NavPageNomer"] ?>"
                title="Показать еще"
                onclick="Actions.showMore(this);"
                >
                <figure class='actions-list-more-spinner'><i></i><i></i><i></i></figure>Показать еще
            </button>
        </div>
    <? endif; ?>

    <? if (!empty($arResult['NAV_STRING'])): ?>
        <div class="actions-list-pagination">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <? endif; ?>
<? else: ?>
    <div class="actions-list-empty">Ничего не найдено</div>
<? endif; ?>