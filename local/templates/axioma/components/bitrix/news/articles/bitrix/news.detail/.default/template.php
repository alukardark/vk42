<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
//printra($arResult);
?>
<div class="actions-detail">
    <?
    if (!empty($arResult['ID'])):
        $sName    = $arResult['NAME'];
        $sText    = $arResult["DETAIL_TEXT"];
        $sPicture = $arResult['DETAIL_RESIZED'];
        $sDate    = FormatDateFromDB($arResult["ACTIVE_FROM"], "DD MMMM YYYY");
        ?>
        <div class="actions-detail-header">
            <figure class="actions-detail-header-picture">
                <img src="<?= $sPicture ?>" alt="<?= $sName ?>" />
            </figure>
            <span class="actions-detail-header-date"><?= $sDate ?></span>
        </div>
        <h3 class="actions-detail-title"><?= $sName ?></h3>
        <div class="actions-detail-text section ve"><?= $sText ?></div>
    <? else: ?>
        <div class="actions-detail-title">Элемент не найден</div>
    <? endif; ?>
</div>