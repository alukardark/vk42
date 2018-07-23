<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;

$arResult = $arParams['arResult'];
$arParams = $arParams['arParams'];

$IBLOCK_ID    = $arParams["IBLOCK_ID"];
$SECTION_CODE = $arResult['VARIABLES']['SECTION_CODE'];

$arPriceRange = \CFilterExt::getPriceRange($IBLOCK_ID, $SECTION_CODE);
list($arHeaderFilters, $arBottomFilters, $arRangeFilters) = \CFilterExt::getSmartFilter($IBLOCK_ID, $SECTION_CODE);

//$cssNotAvailableClass = "not-available";
$cssNotAvailableClass = "hidden";

global ${$arParams["FILTER_NAME"]};
$arGlobalFilter = ${$arParams["FILTER_NAME"]};
?>
<div id="smartfilter" class="smartfilter clearfix">

    <div class="smartfilter-title">
        <span>Поиск по параметрам</span>
    </div>

    <div class="smartfilter-separator"></div>

    <?
    /**
     * Выводим верхние фильтры $arHeaderFilters (сезон, шипы, бренд..)
     */
    foreach ($arHeaderFilters as $arBlock):
        $property = $arBlock["property"];
        $type     = $arBlock["type"];
        $key      = "PROPERTY_" . $property . "_VALUE";

        $arAvailableValues = \CFilterExt::getSmartFilterDataAvailable($arGlobalFilter, $IBLOCK_ID, $property, $type);
        $items             = \CFilterExt::sortSmartFilterBlock($arBlock, $arAvailableValues);
        //if ($property == DIA)printrau($items);
        ?>
        <?
        //удаляем недоступные элементы из массива
        foreach ($items as $key => $arItem)
        {
            $value     = $arItem["value"];
            $available = empty($value) || !is_array($arAvailableValues) || in_array($value, $arAvailableValues);
            $selected  = $value == $arBlock["selected"] || if_in_array($value, $arBlock["selected"]);

            if (!($available || $selected))
            {
                unset($items[$key]);
            }
        }

        if (count($items)):
            ?>
            <div class="smartfilter-block">
                <? if (!empty($arBlock["title"])): ?>
                    <div class="smartfilter-block-title"><?= $arBlock["title"] ?></div>
                <? else: ?>
                    <div class="smartfilter-block-title-empty" style="height: 30px;"></div>
                <? endif; ?>

                <div
                    class="smartfilter-<?= $type ?> smartfilter-block-content <?= $arBlock["class"] ?>"
                    data-property="<?= $property ?>"
                    data-filter-type="<?= $type ?>"
                    >
                        <?
                        foreach ($items as $arItem):
                            $value = $arItem["value"];

                            $available = empty($value) || !is_array($arAvailableValues) || in_array($value, $arAvailableValues);
                            $selected  = $value == $arBlock["selected"] || if_in_array($value, $arBlock["selected"]);
                            ?>
                        <button
                            class="<?= $selected ? "selected" : "" ?> <?= $available || $selected ? "" : $cssNotAvailableClass ?>"
                            data-value="<?= $value ?>"
                            onclick="Filter.smart(this)"
                            >
                            <i></i><span><?= $arItem['title'] ?></span>
                        </button>
                    <? endforeach; ?>

                </div>
            </div>

            <div class="smartfilter-separator"></div>
        <? endif; ?>


        <?
        unset($arBlock);
        unset($arItem);
    endforeach;
    ?>

    <? if (!empty($arRangeFilters)): ?>
        <div class="smartfilter-block">
            <?
            foreach ($arRangeFilters as $arBlock):
                $arRange = $arBlock["range"];
                if ($arRange['MIN'] >= $arRange['MAX']) continue;

                $property = $arBlock["property"];
                $type     = $arBlock["type"];
                ?>

                <div class="smartfilter-block-title"><?= $arBlock["title"] ?></div>

                <div class="smartfilter-slider">
                    <div class="smartfilter-slider-inputs">
                        <input
                            class="js-range-slider-input input-min"
                            type="number"
                            autocomplete="off"
                            value="<?= $arRange['FROM'] ?>"
                            />
                        <input type="password" autocomplete="off" class="hidden" name="<?= md5(rand(0, time())) ?>">
                        <span>—</span>
                        <input
                            class="js-range-slider-input input-max"
                            type="number"
                            autocomplete="off"
                            value="<?= $arRange['TO'] ?>"
                            />
                        <input type="password" autocomplete="off" class="hidden" name="<?= md5(rand(0, time())) ?>">
                    </div>

                    <input name="<?= $property ?>_range" value="" type="text" class="js-range-slider"
                           data-from="<?= $arRange['FROM'] ?>" data-to="<?= $arRange['TO'] ?>"
                           data-min="<?= $arRange['MIN'] ?>" data-max="<?= $arRange['MAX'] ?>"
                           data-step="<?= $arRange['STEP'] ?>" data-property="<?= $property ?>"
                           data-postfix="" />
                </div>
            <? endforeach; ?>
        </div>

        <div class="smartfilter-separator"></div>
    <? endif; ?>
    <?
    /**
     * выводим слайдер цены
     */
    if (!empty($arPriceRange) && $arPriceRange['MIN'] != $arPriceRange['MAX']):
        ?>
        <div class="smartfilter-block">
            <div class="smartfilter-block-title">Цена, руб.</div>

            <div class="smartfilter-slider">
                <div class="smartfilter-slider-inputs">
                    <input
                        class="js-range-slider-input input-min"
                        type="number"
                        autocomplete="off"
                        value="<?= $arPriceRange['FROM'] ?>"
                        />
                    <input type="password" autocomplete="off" class="hidden" name="<?= md5(rand(0, time())) ?>">
                    <span>—</span>
                    <input
                        class="js-range-slider-input input-max"
                        type="number"
                        autocomplete="off"
                        value="<?= $arPriceRange['TO'] ?>"
                        />
                    <input type="password" autocomplete="off" class="hidden" name="<?= md5(rand(0, time())) ?>">
                </div>
                <input name="PRICE_range" value="" type="text" class="js-range-slider"
                       data-from="<?= $arPriceRange['FROM'] ?>" data-to="<?= $arPriceRange['TO'] ?>"
                       data-min="<?= $arPriceRange['MIN'] ?>" data-max="<?= $arPriceRange['MAX'] ?>"
                       data-step="1000" data-property="PRICE"
                       data-postfix="" />
            </div>
        </div>

        <div class="smartfilter-separator"></div>
    <? endif; ?>


    <? if (!empty($arBottomFilters)): ?>

        <div class="smartfilter-block smartfilter-block-notitle">
            <?
            /**
             * выводим чекбоксы нижних фильтров $arBottomFilters (различные акции)
             */
            foreach ($arBottomFilters as $arBlock):
                $property = $arBlock["property"];
                $type     = $arBlock["type"];
                $key      = "PROPERTY_" . $property . "_VALUE";

                $arAvailableValues = \CFilterExt::getSmartFilterDataAvailable($arGlobalFilter, $IBLOCK_ID, $property, $type);
                ?>
                <div
                    class="smartfilter-<?= $type ?> smartfilter-block-content <?= $arBlock['class'] ?>"
                    data-property="<?= $arBlock['property'] ?>"
                    data-filter-type="<?= $type ?>"
                    >

                    <?
                    foreach ($arBlock['items'] as $arItem):
                        $value = $arItem["value"];

                        $available = empty($value) || !is_array($arAvailableValues) || in_array($value, $arAvailableValues);
                        $selected  = $value == $arBlock["selected"] || in_array($value, $arBlock["selected"]);
                        ?>
                        <button
                            class="<?= $selected ? "selected" : "" ?> <?= $available || $selected ? "" : $cssNotAvailableClass ?>"
                            data-value="<?= $value ?>"
                            onclick="Filter.smart(this)"
                            >
                            <i></i><span><?= $arItem['title'] ?></span>
                        </button>
                    <? endforeach; ?>
                </div>

                <?
                unset($arBlock);
                unset($arItem);
            endforeach;
            ?>
        </div>

        <div class="smartfilter-separator"></div>
    <? endif; ?>

    <div class="smartfilter-block smartfilter-block-notitle">
        <div class="smartfilter-reset smartfilter-block-content">
            <button onclick="Filter.clear(this)">
                <i class="ion-android-close"></i><span>Очистить все</span>
            </button>
        </div>
    </div>
</div>
