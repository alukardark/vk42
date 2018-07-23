<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arServices = \CServicesExt::getServicesSections();

$iServicesCount  = count($arServices);
$iServicesOnPage = ceil($iServicesCount / 2);

$src      = $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/images/bg/index_services_original_cropped60.jpg";
$iFileId  = \CPic::makeFile($src);
$sFileSrc = \CPic::getResized($iFileId, 950, 730, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
?>
<section id="index-services" class="index-services">
    <div class="index-services-bg" style="background-image: url(<?= $sFileSrc ?>);"></div>

    <div class="container-fluid">
        <div class="row">
            <div class="offset-4 offset-xxl-2 offset-xl-0 col-16 col-xxl-20 col-xl-24">
                <div class="index-services-content">
                    <div class="services-list row">
                        <div class="services-list-title col-8 col-xl-24">
                            <h2 class="float-xl-left">Наши услуги</h2>
                            <!--<button class="services-list-button float-xl-right hidden-md-down" title="Записаться">Записаться</button>-->
                        </div>
                        <div id="services-slider" class="services-list-content row col-16 col-xl-24">
                            <ul class="noliststyle services-list-items col-12">
                                <?
                                for ($i = 0; $i < $iServicesOnPage; $i++):
                                    $sServiceTitle = $arServices[$i]['NAME'];
                                    $sServiceUrl   = $arServices[$i]['SECTION_PAGE_URL'];
                                    $bNewTab       = $arServices[$i]['NEW_TAB'];
                                    ?>
                                    <li class="services-list-item">
                                        <a
                                            href="<?= $sServiceUrl ?>"
                                            title="<?= $sServiceTitle ?>"
                                            <?= $bNewTab ? 'target="_blank"' : '' ?>
                                            >
                                                <?= $sServiceTitle ?>
                                            <i class="ion-ios-arrow-right"></i></a>
                                    </li>
                                <? endfor; ?>
                            </ul>
                            <ul class="noliststyle services-list-items col-12">
                                <?
                                for ($i = $iServicesOnPage; $i < $iServicesCount; $i++):
                                    $sServiceTitle = $arServices[$i]['NAME'];
                                    $sServiceUrl   = $arServices[$i]['SECTION_PAGE_URL'];
                                    ?>
                                    <li class="services-list-item">
                                        <a
                                            href="<?= $sServiceUrl ?>"
                                            title="<?= $sServiceTitle ?>"
                                            >
                                                <?= $sServiceTitle ?>
                                            <i class="ion-ios-arrow-right"></i>
                                        </a>
                                    </li>
                                <? endfor; ?>
                            </ul>
                        </div>

                        <!--<button class="services-list-button hidden-md-up" title="Записаться">Записаться</button>-->

                    </div>

                    <div class="services-list-banners row">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:news.list", "banners", Array(
                            "IBLOCK_TYPE"               => "content",
                            "IBLOCK_ID"                 => BANNERS_IB,
                            "NEWS_COUNT"                => "2",
                            "FIELD_CODE"                => array("*"),
                            "PROPERTY_CODE"             => array("*"),
                            "SORT_BY1"                  => "SORT",
                            "SORT_BY2"                  => "ID",
                            "SORT_ORDER1"               => "ASC",
                            "SORT_ORDER2"               => "DESC",
                            "AJAX_MODE"                 => "N",
                            "CHECK_DATES"               => "N",
                            "DETAIL_URL"                => "",
                            "FILTER_NAME"               => "",
                            "PARENT_SECTION"            => "",
                            "PARENT_SECTION_CODE"       => "",
                            "PREVIEW_TRUNCATE_LEN"      => "",
                            "ADD_SECTIONS_CHAIN"        => "N",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "INCLUDE_SUBSECTIONS"       => "N",
                            "SET_TITLE"                 => "N",
                            "SET_BROWSER_TITLE"         => "N",
                            "SET_LAST_MODIFIED"         => "N",
                            "SET_META_DESCRIPTION"      => "N",
                            "SET_META_KEYWORDS"         => "N",
                            "SET_STATUS_404"            => "N",
                            "SHOW_404"                  => "N",
                            "CACHE_FILTER"              => "Y",
                            "CACHE_GROUPS"              => "Y",
                            "CACHE_TIME"                => "36000000",
                            "CACHE_TYPE"                => "A",
                            )
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>