<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $axi;
$curAlias = $axi->getAlias();

$arParams = $arParams['arParams'];

$arBlocks = array(
    array(
        "property" => SHIRINA,
        "title"    => "Ширина",
    ),
    array(
        "property" => VYSOTA,
        "title"    => "Высота",
    ),
    array(
        "property" => DIAMETR,
        "title"    => "Диаметр",
    ),
);

if ($curAlias == "index-page")
{
    $SECTION_CODE = LEGKOVYE;

    $arBlocks[] = array(
        "property" => SEZON,
        "title"    => "Сезон",
    );
}

$arFilter = Array(
    "IBLOCK_ID"           => TIRES_IB,
    "ACTIVE"              => "Y",
    "SECTION_CODE"        => $SECTION_CODE,
    'INCLUDE_SUBSECTIONS' => 'Y',
);


foreach ($arBlocks as $key => &$arBlock)
{
    // в некоторых случаев будет разбивать значения на два массива, каждый из который будет сортироваться по разному,
    //  а затем объединяться с другим
    $arGroup1 = array();
    $arGroup2 = array();

    $obList = CIBlockElement::GetList(Array(), $arFilter, array("PROPERTY_" . $arBlock['property']), false, false);
    while ($arItem = $obList->Fetch())
    {
        $sValue = $arItem["PROPERTY_" . $arBlock['property'] . "_VALUE"];

        if (empty($sValue)) continue;

        if ($arBlock['property'] == SHIRINA)
        {
            if ($sValue < 100 && !in_array($sValue, $arGroup1)) $arGroup1[$sValue] = $sValue;
            elseif (!in_array($sValue, $arGroup2)) $arGroup2[$sValue] = $sValue;
            else continue;
        }
        elseif ($arBlock['property'] == VYSOTA)
        {
            if ($sValue < 20 && !in_array($sValue, $arGroup1)) $arGroup1[$sValue] = $sValue;
            elseif (!in_array($sValue, $arGroup2)) $arGroup2[$sValue] = $sValue;
            else continue;
        }
        else
        {
            if (!in_array($sValue, $arGroup1)) $arGroup1[$sValue] = $sValue;
            else continue;
        }
    }


    if ($arBlock['property'] == SHIRINA)
    {
        asort($arGroup1);
        asort($arGroup2);
        $arBlock['items'] = array('' => $arBlock['title']) + $arGroup2 + $arGroup1;
    }
    elseif ($arBlock['property'] == VYSOTA)
    {
        asort($arGroup1);
        asort($arGroup2);
        $arBlock['items'] = array('' => $arBlock['title']) + $arGroup2 + $arGroup1;
    }
    else
    {
        $arBlock['items'] = array('' => $arBlock['title']) + $arGroup1;
    }

    unset($arGroup1);
    unset($arGroup2);
    unset($obList);
    unset($arItem);
    unset($arBlock);
}

$arSectionFilter     = $_SESSION['FILTER'][$SECTION_CODE];
$activeFilter = \CCatalogExt::getActivefilter($arSectionFilter);

$active = "";
if ($curAlias != "index-page")
{
    $active = $SECTION_CODE != LEGKOVYE || $activeFilter == "size" ? "active" : "";
}
else
{
    $active = $activeFilter == "size" ? "active" : "";
}

global ${$arParams["FILTER_NAME"]};
$arGlobalFilter = ${$arParams["FILTER_NAME"]};
?>
<div data-filter-type="size" class="filter-wrap <?= $active ?>">
    <div class="filter-title">Поиск по размеру</div>
    <div class="filter">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-size">
            <?
            foreach ($arBlocks as $arBlock):
                $blockValue = null; //активное значение
                if (!empty($_SESSION['FILTER'][$SECTION_CODE][$arBlock["property"]]))
                {
                    $blockValue = $_SESSION['FILTER'][$SECTION_CODE][$arBlock["property"]];
                }

                $arAvailableValues = array();
                /* $obExCatalog = CCatalogExt::get();
                  $arData      = $obExCatalog->getSmartFilterDataAvailable($arGlobalFilter, $arBlock["property"]);
                  foreach ($arData as $arDataVal)
                  {
                  $arAvailableValues[] = $arDataVal[$key];
                  } */
                //printra($arAvailableValues);
                ?>
                <div
                    class="filter-block clearfix active"
                    data-property="<?= $arBlock["property"] ?>"
                    data-title="<?= $arBlock["title"] ?>"
                    >
                    <div class="filter-block-htitle"><?= $arBlock["title"] ?></div>
                    <div
                        class="filter-block-title"
                        onclick="Filter.dropdown_toggle(this)"
                        title="<?= $arBlock["title"] ?>"
                        >
                        <span>
                            <? if (!empty($blockValue)) : ?>
                                <?= $blockValue ?>
                            <? else: ?>
                                <?= $arBlock["title"] ?>
                            <? endif; ?>
                        </span>
                        <i class="<?= $curAlias == "index-page" ? "ion-ios-arrow-down" : "ion-android-arrow-dropdown" ?>  "></i>
                    </div>
                    <ul class="noliststyle">
                        <?
                        foreach ($arBlock['items'] as $sKey => $sValue):
                            if ($sValue == 'Всесезонная') continue;
                            ?>
                            <li
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

    <? if ($curAlias == "index-page"): ?>
        <button
            title="Подобрать шины по размеру"
            class="filter-wrap-button"
            data-button-filter="size"
            onclick="Filter.redirectToFilter(this)"
            >
            <span>Подобрать шины</span>
            <i class="ion-chevron-right"></i>
        </button>
    <? endif; ?>
</div>