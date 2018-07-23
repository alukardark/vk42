<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$curAlias = \Axi::getAlias();

$arParams  = $arParams['arParams'];
$IBLOCK_ID = TIRES_IB;

//пытаемся получить данные из кеша
$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_filter/tires.arBlocks/";
$cacheID   = "arBlocks" . $curAlias . $IBLOCK_ID;

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arBlocks"]))
    {
        $arBlocks = $vars["arBlocks"];
        $lifeTime = 0;
    }
}

if ($lifeTime > 0)
{
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
        "IBLOCK_ID"           => $IBLOCK_ID,
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

        $obList = \CIBlockElement::GetList(Array(), $arFilter, array("PROPERTY_" . $arBlock['property']), false, false);
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

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arBlocks" => $arBlocks,
    ));
}

$arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);
$activeFilter    = \CCatalogExt::getActiveFilterTab($IBLOCK_ID, $SECTION_CODE);

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
    <div class="filter filter-with-reset">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-size">
            <?
            $btnDisabled    = true;

            foreach ($arBlocks as $arBlock):
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
                        foreach ($arBlock['items'] as $sKey => $sValue):
                            if ($sValue == 'Всесезонная') continue;

                            $selected = !empty($blockValue) &&
                                    (
                                    (is_array($blockValue) && in_array($sKey, $blockValue)) ||
                                    (!is_array($blockValue) && $blockValue == $sKey)
                                    );

                            if ($selected) $btnDisabled = false;

                            $inactive = !in_array($arAvailableValues, $sValue);
                            ?>
                            <li
                                data-value="<?= $sKey ?>"
                                class="
                                <?= $selected ? "selected" : "" ?>
                                <?= $inactive ? "inactive" : "" ?>
                                "
                                ><?= $sValue ?></li>
                            <? endforeach; ?>
                    </ul>

                    <? if ($property == DIAMETR && !$btnDisabled && $curAlias != "index-page"): ?>
                        <button
                            title="Сбросить фильтр по размеру"
                            class="filter-reset-button filter-reset-button-size2"
                            onclick="Filter.clearSize(this)"
                            >
                            <i class="ion-close-round"></i>
                            <span>сбросить</span>
                        </button>
                    <? endif; ?>
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