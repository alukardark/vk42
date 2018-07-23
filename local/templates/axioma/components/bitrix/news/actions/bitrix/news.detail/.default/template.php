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

        $refAction    = $arResult['PROPERTIES']['REF_ACTION']['VALUE']; /* PROPERTY_VALUE_ID */
        $sIconPicture = $arResult['ICON_RESIZED'];
        ?>
        <div class="actions-detail-header">
            <figure class="actions-detail-header-picture">
                <img src="<?= $sPicture ?>" alt="<?= $sName ?>" />
            </figure>
            <span class="actions-detail-header-date"><?= $sDate ?></span>
        </div>
        <h2 class="actions-detail-title"><?= $sName ?></h2>

        <div class="actions-detail-text section ve"><?= $sText ?></div>

        <? if (!empty($refAction)): ?>
            <?
            global $arActionTiresFilter;
            $arActionTiresFilter = array("PROPERTY_" . AKTSIYA => $refAction);

            $arCatalogParams                        = \CCatalogExt::getParams(TIRES_IB);
            $arCatalogParams["SHOW_ALL_WO_SECTION"] = "Y";
            $arCatalogParams["PAGE_ELEMENT_COUNT"]  = 24;
            $arCatalogParams["ELEMENT_SORT_FIELD"]  = 'SHOWS';
            $arCatalogParams["ELEMENT_SORT_ORDER"]  = 'desc';
            $arCatalogParams["FILTER_NAME"]         = 'arActionTiresFilter';
            ?>

            <?
            $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section", "actions", $arCatalogParams, false, array("HIDE_ICONS" => "Y")
            );
            ?>

            <div class="actions-detail-footer">
                <?
                $link                                   = PATH_CATALOG . LEGKOVYE . "/?FILTER[CLEAR_BEFORE]=1&FILTER[" . AKTSIYA . "]=" . $refAction;
                ?>
                <a
                    onclick="yaCounter12153865.reachGoal('akcii_katalog');"
                    href="<?= $link ?>"
                    title="Все товары акции <?= $sName ?>"
                    class="actions-detail-footer-link"
                    >
                    <i><? \Axi::GSVG('wheel') ?></i>
                    <span>Смотреть все товары акции</span>
                </a>
            </div>
        <? endif; ?>
    <? else: ?>
        <div class="actions-detail-title">Элемент не найден</div>
    <? endif; ?>
</div>