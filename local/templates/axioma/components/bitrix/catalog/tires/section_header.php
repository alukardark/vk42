<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/filter.js");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$isPost  = $request->isPost();

$IBLOCK_ID    = $arParams["IBLOCK_ID"];
$SECTION_CODE = $arResult['VARIABLES']['SECTION_CODE'];

$arTree       = \CCatalogExt::getTree($IBLOCK_ID);
$arIBlockInfo = getIBlockInfo($IBLOCK_ID);

if (empty($arTree))
{
    die("error load tree");
}

//редирект в первый подраздел, если мы в корневом разделе
if (!isPost() && $SECTION_CODE == $arTree[0]['CODE'] && empty($_REQUEST['q']))
{
    foreach ($arTree as $arTreeNode)
    {
        if ($arTreeNode['DEPTH_LEVEL'] == 1) continue;

        $arTemplateVars = array(
            '#SITE_DIR#'     => SITE_DIR,
            '#SECTION_CODE#' => $arTreeNode['CODE'],
            '#IBLOCK_CODE#'  => $arTreeNode['IBLOCK_CODE'],
        );

        $sLink = NormalizeLink(strtr($arTreeNode['SECTION_PAGE_URL'], $arTemplateVars));
        LocalRedirect($sLink, false, '301 Moved permanently');
        exit;
    }
}

$arParentSection = getParentSection($SECTION_CODE, 2);

if ($SECTION_CODE != $arParentSection["CODE"] && !empty($arParentSection))
{
    $arTemplateVars = array(
        '#SITE_DIR#'     => SITE_DIR,
        '#IBLOCK_CODE#'  => $arParentSection['IBLOCK_CODE'],
        '#SECTION_CODE#' => $arParentSection['CODE'],
    );

    $sLink = NormalizeLink(strtr($arParentSection['SECTION_PAGE_URL'], $arTemplateVars));
    LocalRedirect($sLink, false, '301 Moved permanently');
    exit;
}

global ${$arParams["FILTER_NAME"]};

$filterArray   = \CCatalogExt::setFilters($IBLOCK_ID, $SECTION_CODE);
if (HIDE_NULL) $filterArray[] = array(">CATALOG_QUANTITY" => "0");
$filterUrl     = \CCatalogExt::getFilterUrl($IBLOCK_ID, $SECTION_CODE);

if (!$USER->IsAdmin())
{
    //это тестовый товар. если юзер не админ - скроем его
    //$filterArray[] = array("!CODE" => array("bp_energrease_l_21_m_smazka_0_4kg", "elektrolit_elton_1_l_1_29"));
}

//printrau($filterArray);
${$arParams["FILTER_NAME"]} = $filterArray;

$active = \CCatalogExt::getActiveFilterTab($IBLOCK_ID, $SECTION_CODE);
?>
<?
/**
 * Этот кусок будет выведен выше по коду в хедере примерно
 */
?>

<? $this->SetViewTarget("catalog_sections_header"); ?>
<?
$APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list", "", array(
    "SECTION"            => $SECTION_CODE,
    "IBLOCK_TYPE"        => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID"          => $IBLOCK_ID,
    "SECTION_ID"         => $arTree[0]['ID'],
    "SECTION_CODE"       => $arTree[0]['CODE'],
    "CACHE_TYPE"         => "A",
    "CACHE_TIME"         => $arParams["CACHE_TIME"],
    "CACHE_GROUPS"       => $arParams["CACHE_GROUPS"],
    "COUNT_ELEMENTS"     => "N",
    "TOP_DEPTH"          => 1,
    "SECTION_URL"        => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
    "VIEW_MODE"          => $arParams["SECTIONS_VIEW_MODE"],
    "SHOW_PARENT_NAME"   => "Y",
    "HIDE_SECTION_NAME"  => "Y",
    "ADD_SECTIONS_CHAIN" => "Y",
        ), $component, array("HIDE_ICONS" => "Y")
);
?>
<? $this->EndViewTarget(); ?>

<div id="catalog" class="catalog-wrap">
    <?
    if (isPost("get_section"))
    {
        $APPLICATION->RestartBuffer();
    }
    ?>
    <div class="container-fluid catalog-list-wrap0000">
        <div class="row catalog-list-wrap000">
            <? if (empty($request['q'])): ?>

                <? if ($IBLOCK_ID == TIRES_IB): ?>
                    <div class="catalog-filtertop row" id="catalog-filtertop">
                        <div class="catalog-filtertop-controls col-6 col-md-24">
                            <div class="catalog-filtertop-controls-buttons">
                                <? if ($SECTION_CODE == LEGKOVYE): ?>
                                    <button
                                        class="<?= $active == "car" ? "active" : "" ?>"
                                        data-button-type="car"
                                        title="По автомобилю"
                                        onclick="Filter.choose(this)"
                                        ><? \Axi::GSVG('car-filtertop') ?>
                                        По автомобилю<i class="ion-ios-arrow-forward"></i>
                                    </button>
                                <? endif; ?>

                                <button
                                    class="<?= $active == "size" || $SECTION_CODE != LEGKOVYE ? "active" : "" ?>"
                                    title="По размеру"
                                    data-button-type="size"
                                    onclick="<?= $SECTION_CODE == LEGKOVYE ? "Filter.choose(this)" : "" ?>"
                                    ><? \Axi::GSVG('wheel-filtertop') ?>
                                    По размеру<i class="ion-ios-arrow-forward"></i>
                                </button>
                            </div>
                        </div>

                        <div class="catalog-filtertop-content col-18 col-md-24">
                            <? if ($SECTION_CODE == LEGKOVYE): ?>
                                <? $APPLICATION->IncludeFile("/include/filter/tires_car.php"); ?>
                            <? endif; ?>
                            <?
                            $APPLICATION->IncludeFile("/include/filter/tires_size.php", array(
                                "arParams"     => $arParams,
                                "SECTION_CODE" => $SECTION_CODE,
                            ));
                            ?>
                        </div>
                    </div>
                <? endif; ?>

                <? if ($IBLOCK_ID == DISCS_IB): ?>
                    <div class="catalog-filtertop row" id="catalog-filtertop">
                        <div class="catalog-filtertop-controls col-6 col-md-24">
                            <div class="catalog-filtertop-controls-buttons">
                                <button
                                    class="active <?= $active == "discs_car" ? "active" : "" ?>"
                                    data-button-type="discs_car"
                                    title="По автомобилю"
                                    onclick="Filter.choose(this)"
                                    ><? \Axi::GSVG('car-filtertop') ?>
                                    По автомобилю<i class="ion-ios-arrow-forward"></i>
                                </button>

                                <button
                                    class="hidden <?= $active == "discs_size" ? "active" : "" ?>"
                                    title="По параметрам"
                                    data-button-type="discs_size"
                                    onclick="Filter.choose(this)"
                                    ><? \Axi::GSVG('wheel-filtertop') ?>
                                    По параметрам<i class="ion-ios-arrow-forward"></i>
                                </button>
                            </div>
                        </div>

                        <div class="catalog-filtertop-content col-18 col-md-24">
                            <?
                            $APPLICATION->IncludeFile("/include/filter/discs_car.php", array(
                                "arParams"     => $arParams,
                                "SECTION_CODE" => $SECTION_CODE,
                            ));
                            ?>
                            <?
                            /* $APPLICATION->IncludeFile("/include/filter/discs_size.php", array(
                              "arParams"     => $arParams,
                              "SECTION_CODE" => $SECTION_CODE,
                              )); */
                            ?>
                        </div>
                    </div>
                <? endif; ?>
            <? endif; ?>

            <div class="catalog-section" data-filter-url="<?= $filterUrl ?>">
                <?
                $APPLICATION->IncludeFile("/include/catalog/settings.php", array(
                    "arParams" => $arParams)
                );
                ?>

                <div class="row catalog-list-wrap00">
                    <div class="col-6 col-xxl-8 col-xl-9">
                        <div class="catalog-filterleft">
                            <? if (empty($request['q'])): ?>
                                <?
                                $APPLICATION->IncludeFile("/include/catalog/smartfilter.php", array(
                                    "arParams" => $arParams,
                                    "arResult" => $arResult,
                                    "arData"   => $arData)
                                );
                                ?>
                            <? else: ?>
                                <? $APPLICATION->IncludeFile("/include/catalog/simple_search.php"); ?>
                            <? endif; ?>
                        </div>
                    </div>

                    <div class="col-18 col-xxl-16 col-xl-15 col-lg-24 catalog-list-wrap0">
                        <div class="catalog-list-wrap row" id="catalog-section-list">
                            <? if (!empty($arIBlockInfo["DESCRIPTION"])): ?>
                                <div class="catalog-list-description"><?= $arIBlockInfo["DESCRIPTION"] ?></div>
                            <? endif; ?>

                            <? if (in_array($IBLOCK_ID, RESTRICTED_IBLOCKS_FOR_ANOTER_CITY)):?>
                                <div class="notice-warning" style="padding-top: 20px;"><? \Axi::GT("catalog/another-city-info"); ?></div>
                            <? endif; ?>

                            <? if (0 && (isAdmin() || SHOW_HELP_AKB) && \CSite::InDir('/akkumulyatory/')): ?>
                                <div class="catalog-helpbutton">
                                    <div
                                        class="noselect"
                                        title="Помощь в подборе аккумулятора"
                                        onclick="Form.toggleForm(this);"
                                        data-form="#help_akb"
                                        >
                                        <i></i><span>Помощь в подборе аккумулятора</span>
                                    </div>
                                </div>
                            <? endif; ?>

                            <?
                            if (isPost("get_section_list"))
                            {
                                $APPLICATION->RestartBuffer();
                            }
                            ?>

