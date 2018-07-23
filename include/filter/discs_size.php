<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$context  = \Bitrix\Main\Application::getInstance()->getContext();
$request  = $context->getRequest();
$curAlias = \Axi::getAlias();

$IBLOCK_ID    = DISCS_IB;
$SECTION_CODE = $arParams['SECTION_CODE'];
$arParams     = $arParams['arParams'];

if (isPost("get_filter_discs_params"))
{
    $SECTION_CODE = $request->getPost("SECTION_CODE");
    $APPLICATION->RestartBuffer();

    $active = "active";
}
else
{
    //$active = $activeFilter == "discs_size" || isPost("get_filter_discs_params") ? "active" : "";
    $active = "";
}

if (!in_array($SECTION_CODE, array(DISCS_STEEL, DISCS_LIGHT)))
{
    $SECTION_CODE = DISCS_LIGHT;
}

$arBlocks        = \CCatalogExt::getDiscsFilterParams($IBLOCK_ID, $SECTION_CODE);
$arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);
$activeFilter    = \CCatalogExt::getActiveFilterTab($IBLOCK_ID, $SECTION_CODE);



global ${$arParams["FILTER_NAME"]};
$arGlobalFilter = ${$arParams["FILTER_NAME"]};
?>
<div data-filter-type="discs_size" class="filter-wrap <?= $active ?>">
    <div class="filter-title">Поиск по параметрам</div>
    <div class="filter">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div
            class="filter-container js-filter-container-size js-filter-container-discssize"
            data-tuning="<?= $arSectionFilter["TUNING"] == "Y" ? "Y" : "" ?>"
            >
                <?
                foreach ($arBlocks as $arBlock):
                    if ($arBlock["property"] == "SECTION_CODE" && $curAlias != "index-page") continue;

                    $property = $arBlock["property"];
                    $type     = $arBlock["type"];
                    $title    = $arBlock["title"];

                    $blockValue = null; //активное значение
                    if (!empty($arSectionFilter[$property]))
                    {
                        $blockValue = $arSectionFilter[$property];
                    }

                    $arAvailableValues = array();
                    ?>
                <div
                    class="filter-block clearfix active"
                    data-property="<?= $property ?>"
                    data-value="<?= $arBlock["value"] ?>"
                    data-title="<?= $title ?>"
                    >
                    <div class="filter-block-htitle"><?= $title ?></div>
                    <div
                        class="filter-block-title"
                        onclick="Filter.toggleDropdown(this)"
                        title="<?= $title ?>"
                        >
                        <span>
                            <? if (!empty($blockValue)) : ?>
                                <?=
                                count($blockValue) > 1 ?
                                        "Несколько" :
                                        (is_array($blockValue) ? $blockValue[0] : $blockValue)
                                ?>
                            <? else: ?>
                                <?= $title ?>
                            <? endif; ?>
                        </span>
                        <i class="<?= $curAlias == "index-page" ? "ion-ios-arrow-down" : "ion-android-arrow-dropdown" ?>"></i>
                    </div>
                    <ul class="noliststyle">
                        <?
                        $items             = $arBlock['items'];
                        $arAvailableValues = \CFilterExt::getSmartFilterDataExists($IBLOCK_ID, $property);

                        foreach ($items as $sKey => $sValue):
                            if ($property != "SECTION_CODE" && !in_array($sValue, $arAvailableValues) && $sValue != $arBlock["title"])
                                    continue;
                            $onclick = "";
                            if ($property == "SECTION_CODE")
                            {
                                //printrau($items);
                                $onclick    = ' onclick="Filter.getDiscsFilterParams(this, \'' . $sKey . '\')" ';
                                $blockValue = $SECTION_CODE;
                            }

                            $selected = !empty($blockValue) &&
                                    (
                                    (is_array($blockValue) && in_array($sKey, $blockValue)) ||
                                    (!is_array($blockValue) && $blockValue == $sKey)
                                    );

                            $inactive = !in_array($arAvailableValues, $sValue);
                            ?>
                            <li
                            <?= $onclick ?>
                                data-value="<?= $sKey ?>"
                                class="
                                <?= $selected ? "selected" : "" ?>
                                <?= $inactive ? "inactive" : "" ?>
                                "
                                ><?= $sValue ?></li>
                            <? endforeach; ?>
                    </ul>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <? if ($curAlias == "index-page"): ?>
        <button
            title="Подобрать диски по параметрам"
            class="filter-wrap-button"
            data-button-filter="discs_size"
            onclick="Filter.redirectToFilter(this)"
            >
            <span>Подобрать диски</span>
            <i class="ion-chevron-right"></i>
        </button>
    <? endif; ?>
</div>

<?
if (isPost("get_filter_discs_params"))
{
    die();
}
?>