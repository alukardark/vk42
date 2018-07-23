<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
$tags_url = urlencode(json_encode(array("TAGS" => $arParams['TAGS_SELECTED'])));
?>



<? if (!empty($arResult['ITEMS'])): ?>
    <div class="actions-list row">

        <div class="news-list-title">
            <? if (!empty($arResult['TAGS'])): ?>
                <? foreach ($arResult['TAGS'] as $arTag): ?>
                    <span><?= $arTag["NAME"] ?></span>
                <? endforeach; ?>
            <? else: ?>
                <span>Новости</span>
            <? endif; ?>

            <? if ($arParams["FROM_ACTIONS"] == "Y"): ?>
                <a class="news-list-title-link" href="/novosti/" title="Читать больше новостей">Читать больше новостей</a>
            <? endif; ?>
        </div>

        <?
        $i = -1;
        foreach ($arResult["ITEMS"] as $arItem):
            $i++;
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $sName    = $arItem['NAME'];
            $sText    = $arItem["PREVIEW_TEXT"];
            $sUrl     = NormalizeLink($arItem['DETAIL_PAGE_URL'] . "/");
            $sPicture = $arItem['PREVIEW_RESIZED'];
            $arTags   = $arItem["TAGS"];

            $sDate = $arItem["DISPLAY_ACTIVE_FROM"];
            ?>
            <div
                class="actions-list-item col-12 col-md-24 <?= $arParams["FROM_ACTIONS"] == "Y" && $i % 2 ? "right" : "" ?>"
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
                        <button class="actions-list-item-preview-button">
                            <span>Подробнее</span><i class="ion-ios-arrow-forward"></i>
                        </button>
                    </div>

                    <div class="actions-list-item-anons">
                        <span class="actions-list-item-anons-title"><?= $sName ?></span>
                        <div class="actions-list-item-anons-text"><?= $sText ?></div>

                        <? if (!empty($arTags)): ?>
                            <div class="news-list-item-tags">
                                <? foreach ($arTags as $arTag): ?>
                                    <span>#<?= $arTag["NAME"] ?></span>
                                <? endforeach; ?>
                            </div>
                        <? endif; ?>
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

    <div class="hidden" id="tags-url" data-tags-url="<?= $tags_url ?>"></div>

    <? if (!empty($arResult['NAV_STRING'])): ?>
        <div class="actions-list-pagination">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <? endif; ?>
<? else: ?>
    <div class="actions-list-empty">
        Ничего не найдено
    </div>
<? endif; ?>