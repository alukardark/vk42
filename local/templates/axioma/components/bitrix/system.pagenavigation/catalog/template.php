<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

if ($arResult["NavRecordCount"] == 0 || $arResult["NavPageCount"] < 2) return;

if (isPost()) $arResult["NavQueryString"] = "";

$strNavQueryString     = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");

$arResult["sUrlPath"] = $APPLICATION->GetCurDir();

$nn       = "PAGEN_" . $arResult["NavNum"];
$iPage    = $arResult["NavPageNomer"];
$sUrl     = $arResult["sUrlPath"] . "?" . $strNavQueryString . $nn . "=";
$sUrlFull = $arResult["sUrlPath"] . $strNavQueryStringFull;
?>
<ul class="row noliststyle">
    <? if ($iPage > 1): ?>
        <? if ($arResult["bSavePage"]): ?>
            <li class="prev"><a href="<?= $sUrl ?><?= ($iPage - 1) ?>" title="Предыдущая страница" data-rel="innerlink"><?= GetMessage("prev") ?></a></li>
            <li class=""><a href="<?= $sUrl ?>1" title="Первая страница" data-rel="innerlink">1</a></li>
        <? else: ?>
            <? if ($iPage > 2): ?>
                <li class="prev"><a href="<?= $sUrl ?><?= ($iPage - 1) ?>" title="Предыдущая страница" data-rel="innerlink"><?= GetMessage("prev") ?></a></li>
            <? else: ?>
                <li class="prev"><a href="<?= $sUrlFull ?>" title="Предыдущая страница" data-rel="innerlink"><?= GetMessage("prev") ?></a></li>
            <? endif ?>
            <li class=""><a href="<?= $sUrlFull ?>" title="Первая страница" data-rel="innerlink">1</a></li>
        <? endif ?>
    <? else: ?>
        <li class="prev"><?= GetMessage("prev") ?></li>
        <li class="active"><span>1</span></li>
    <? endif ?>

    <? if ($arResult["nStartPage"] > 1): ?>
        <li class="dots"><span>...</span></li>
    <? endif ?>

    <?
    $arResult["nStartPage"] ++;
    while ($arResult["nStartPage"] <= $arResult["nEndPage"] - 1):
        ?>
        <? if ($arResult["nStartPage"] == $iPage): ?>
            <li class="active"><span><?= $arResult["nStartPage"] ?></span></li>
        <? else: ?>
            <li class=""><a href="<?= $sUrl ?><?= $arResult["nStartPage"] ?>" title="Страница <?= $arResult["nStartPage"] ?>" data-rel="innerlink"><?= $arResult["nStartPage"] ?></a></li>
        <? endif ?>
        <? $arResult["nStartPage"] ++ ?>
    <? endwhile ?>

    <? if ($arResult["NavPageCount"] - $arResult["nStartPage"] > 0): ?>
        <li class="dots"><span>...</span></li>
    <? endif ?>

    <? if ($iPage < $arResult["NavPageCount"]): ?>
        <? if ($arResult["NavPageCount"] > 1): ?>
            <li class=""><a href="<?= $sUrl ?><?= $arResult["NavPageCount"] ?>" title="Страница <?= $arResult["NavPageCount"] ?>" data-rel="innerlink"><?= $arResult["NavPageCount"] ?></a></li>
        <? endif ?>
        <li class="next"><a href="<?= $sUrl ?><?= ($iPage + 1) ?>" title="Следующая страница" data-rel="innerlink"><?= GetMessage("next") ?></a></li>
    <? else: ?>
        <? if ($arResult["NavPageCount"] > 1): ?>
            <li class="active"><span><?= $arResult["NavPageCount"] ?></span></li>
        <? endif ?>
        <li class="next"><span><?= GetMessage("next") ?></span></li>
            <? endif; ?>
</ul>