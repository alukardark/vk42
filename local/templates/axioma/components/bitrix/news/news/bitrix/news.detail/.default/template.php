<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<div class="actions-detail">
    <?
    if (!empty($arResult['ID'])):
        $sName          = $arResult['NAME'];
        $sText          = $arResult["DETAIL_TEXT"];
        $sPicture       = $arResult['DETAIL_RESIZED'];
        $sDetailPicture = $arResult['DETAIL_PICTURE']["SRC"];
        $sDate          = FormatDateFromDB($arResult["ACTIVE_FROM"], "DD MMMM YYYY");
        $arTags         = $arResult["TAGS"];
        ?>
        <div class="actions-detail-header">
            <figure class="actions-detail-header-picture zoom" data-src="<?= $sDetailPicture ?>">
                <img src="<?= $sPicture ?>" alt="<?= $sName ?>" />
            </figure>
            <span class="actions-detail-header-date"><?= $sDate ?></span>
        </div>
        <h3 class="actions-detail-title"><?= $sName ?></h3>
        <div class="actions-detail-text section ve"><?= $sText ?></div>

        <? if (!empty($arTags)): ?>
            <div class="news-detail-tags">
                <? foreach ($arTags as $arTag): ?>
                    <a 
                        href="<?= $arResult["LIST_PAGE_URL"] ?>?TAGS[]=<?= $arTag["CODE"] ?>"
                        title="<?= $arTag["NAME"] ?>"
                        >#<?= $arTag["NAME"] ?></a>
                    &nbsp;
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <? if (!empty($arResult['NEXT_NEWS'])): ?>
            <div class="news-detail-nextlink">
                <a 
                    href="/novosti/<?= $arResult['NEXT_NEWS']["CODE"] ?>/"
                    title="<?= $arResult['NEXT_NEWS']["NAME"] ?>"
                    >Следующая новость<i class="ion-ios-arrow-right"></i></a>
            </div>
        <? endif; ?>

    <? else: ?>
        <div class="actions-detail-title">Элемент не найден</div>
    <? endif; ?>
</div>