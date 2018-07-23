<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$context   = \Bitrix\Main\Application::getInstance()->getContext();
$request   = $context->getRequest();
$IBLOCK_ID = OILS_IB;

if (isPost("get_filter_oils_params"))
{
    $SECTION_CODE = $request->getPost("SECTION_CODE");
    $APPLICATION->RestartBuffer();
}

if (!in_array($SECTION_CODE, array(MASLA, TRANSM, FLUIDS)))
{
    $SECTION_CODE = MASLA;
}

$arBlocks        = \CCatalogExt::getOilFilterParams($IBLOCK_ID, $SECTION_CODE);
$arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);
?>
<div data-filter-type="oils" data-filter-code="<?= $SECTION_CODE ?>" class="filter-wrap active">
    <div class="filter-title">Поиск по параметрам</div>
    <div class="filter">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-oils">
            <?
            foreach ($arBlocks as $arBlock):
                $blockValue = null; //активное значение
                if (!empty($arSectionFilter[$arBlock["property"]]))
                {
                    $blockValue = $arSectionFilter[$arBlock["property"]];
                }

                if (is_array($blockValue)) $blockValue      = $blockValue[0];
                if ($blockValue == "universe") $blockValueTitle = "Универсальные";
                else $blockValueTitle = $blockValue;
                ?>
                <div
                    class="filter-block clearfix active"
                    data-property="<?= $arBlock["property"] ?>"
                    data-title="<?= $arBlock["title"] ?>"
                    >
                    <div class="filter-block-htitle"><?= $arBlock["title"] ?></div>
                    <div
                        class="filter-block-title"
                        onclick="Filter.toggleDropdown(this)"
                        title="<?= $arBlock["title"] ?>"
                        >
                        <span>
                            <? if (!empty($blockValue)) : ?>
                                <?= $blockValueTitle ?>
                            <? else: ?>
                                <?= $arBlock["title"] ?>
                            <? endif; ?>
                        </span>
                        <i class="ion-ios-arrow-down"></i>
                    </div>
                    <ul class="noliststyle">
                        <?
                        foreach ($arBlock['items'] as $sKey => $sValue):
                            $onclick = "";
                            if ($arBlock["property"] == "SECTION_CODE")
                            {
                                $onclick    = ' onclick="Filter.getOilFilterParams(this, \'' . $sKey . '\')" ';
                                $blockValue = $SECTION_CODE;
                            }
                            ?>
                            <li
                            <?= $onclick ?>
                                data-value="<?= $sKey ?>"
                                class="
                                <?= !empty($blockValue) && $blockValue == $sKey ? "selected" : "" ?>
                                <?= !in_array($arAvailableValues, $sValue) ? "inactive" : "" ?>
                                "
                                ><?= $sValue ?></li>
                            <? endforeach; ?>
                    </ul>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <button
        title="Подобрать масла и смазочные материалы по параметрам"
        class="filter-wrap-button"
        data-button-filter="oils"
        onclick="Filter.redirectToFilterOils(this)"
        >
        <span>Подобрать</span>
        <i class="ion-chevron-right"></i>
    </button>
</div>

<?
if (isPost("get_filter_oils_params"))
{
    die();
}
?>