<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$IBLOCK_ID    = OILS_IB;
$SECTION_CODE = $arParams['SECTION_CODE'];
$sFilterTitle = "Масла";
$sFilterType  = "oil";

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_filter/oils.arBlocks/";
$cacheID   = "arBlocks" . $IBLOCK_ID . $SECTION_CODE;

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
    if ($SECTION_CODE == MASLA)
    {
        $arBlocks = array(
            array(
                "property" => SM_VYAZKOST,
                "title"    => "Вязкость",
            ),
            array(
                "property" => SM_TIP,
                "title"    => "Тип масла",
            ),
        );
    }
    elseif ($SECTION_CODE == TRANSM)
    {
        $arBlocks = array(
            array(
                "property" => SM_VYAZKOST,
                "title"    => "Вязкость",
            ),
            array(
                "property" => SM_TIP,
                "title"    => "Тип масла",
            ),
            array(
                "property" => SM_NAZNACHENIE,
                "title"    => "Назначение",
            ),
        );
    }
    elseif ($SECTION_CODE == FLUIDS)
    {
        $arBlocks = array(
            array(
                "property" => SM_VIDMASLA,
                "title"    => "Вид жидкости",
            ),
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
        $arGroup = array();

        $obList = \CIBlockElement::GetList(Array(), $arFilter, array("PROPERTY_" . $arBlock['property']), false, false);
        while ($arItem = $obList->Fetch())
        {
            $sValue = $arItem["PROPERTY_" . $arBlock['property'] . "_VALUE"];

            if (empty($sValue)) continue;

            if (!in_array($sValue, $arGroup)) $arGroup[$sValue] = $sValue;
            else continue;
        }

        $arBlock['items'] = array('' => $arBlock['title']) + $arGroup;

        unset($arGroup);
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

global ${$arParams["FILTER_NAME"]};
$arGlobalFilter = ${$arParams["FILTER_NAME"]};
?>
<div data-filter-type="<?= $sFilterType ?>" class="filter-wrap active">
    <div class="filter-title"><?= $sFilterTitle ?></div>
    <div class="filter">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-size">
            <?
            foreach ($arBlocks as $arBlock):
                $blockValue = null; //активное значение
                if (!empty($arSectionFilter[$arBlock["property"]]))
                {
                    $blockValue = $arSectionFilter[$arBlock["property"]];
                }
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
                                <?= $blockValue ?>
                            <? else: ?>
                                <?= $arBlock["title"] ?>
                            <? endif; ?>
                        </span>
                        <i class="ion-android-arrow-dropdown"></i>
                    </div>
                    <ul class="noliststyle">
                        <?
                        foreach ($arBlock['items'] as $sKey => $sValue):
                            ?>
                            <li
                                data-value="<?= $sKey ?>"
                                class="
                                <?= !empty($blockValue) && $blockValue == $sKey ? "selected" : "" ?>
                                "
                                ><?= $sValue ?></li>
                            <? endforeach; ?>
                    </ul>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>