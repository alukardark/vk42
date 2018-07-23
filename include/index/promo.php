<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$src      = $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/images/bg/promo.jpg";
$iFileId  = \CPic::makeFile($src);
$sFileSrc = \CPic::getResized($iFileId, 1900, 800, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
?>
<section id="promo" class="promo" style="background-image: url(<?= $sFileSrc ?>);">
    <div class="container-fluid">
        <div class="row">
            <div class="promo-left col-4 col-xxl-5 col-xl-6 col-lg-7 col-md-24">
                <div class="promo-about row">
                    <a class="promo-about-logo" href="/" title="<?= SITE_NAME ?>">
                        <figure>
                            <img src="/images/logo_ct_main.png" alt="<?= SITE_NAME ?>" title="<?= SITE_NAME ?>" />
                        </figure>
                    </a>
                    <div class="promo-about-content">
                        <div class="promo-about-title"><? \Axi::GT("index/promo-about-title", "промо: заголовок"); ?></div>
                        <div class="promo-about-descr"><? \Axi::GT("index/promo-about-descr", "промо: описание"); ?></div>
                    </div>
                    <!--                    <button class="promo-about-button" title="Записаться на сервис">
                    <? \Axi::GSVG('key') ?>
                                            <span>Записаться на сервис</span>
                                        </button>-->
                </div>
                <div id="promo-slider" class="promo-slider hidden-md-down">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:news.list", "promoslider", Array(
                        "IBLOCK_TYPE"               => "content",
                        "IBLOCK_ID"                 => PROMOSLIDER_IB,
                        "NEWS_COUNT"                => "5",
                        "FIELD_CODE"                => array("*"),
                        "PROPERTY_CODE"             => array("*"),
                        "SORT_BY1"                  => "SORT",
                        "SORT_BY2"                  => "ID",
                        "SORT_ORDER1"               => "ASC",
                        "SORT_ORDER2"               => "DESC",
                        "AJAX_MODE"                 => "N",
                        "CHECK_DATES"               => "Y",
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
            <div class="promo-right col-20 col-xxl-19 col-xl-18 col-lg-17 col-md-24">
                <div class="promo-content container-fluid">

                    <div class="row active" data-filter-container-code="tires">
                        <div class="promo-controls col-12 col-xxl-14 col-xl-16 col-lg-24">
                            <h2 class="promo-controls-title">Подбор автошин</h2>
                            <div class="promo-controls-buttons row">
                                <?
                                $active   = \CCatalogExt::getActiveFilterTab(TIRES_IB, LEGKOVYE);
                                ?>
                                <button
                                    title="Подбор автошин по автомобилю"
                                    data-form-id="car"
                                    onclick="Index.setFilterForm(this)"
                                    class="promo-controls-buttons-item <?= $active == "car" ? "selected" : "" ?>"
                                    >
                                    <i class="car"><? \Axi::GSVG('car') ?></i>
                                    <span>По автомобилю</span>
                                </button>
                                <button
                                    title="Подбор автошин по размеру"
                                    data-form-id="size"
                                    onclick="Index.setFilterForm(this)"
                                    class="promo-controls-buttons-item <?= $active == "size" ? "selected" : "" ?>"
                                    >
                                    <i class="wheel"><? \Axi::GSVG('wheel') ?></i>
                                    <span>По размеру</span>
                                </button>
                            </div>
                        </div>

                        <div class="promo-filter col-12 col-xxl-10 col-xl-8 col-lg-24">
                            <?= \Axi::GF("filter/tires_car", "фильтр по автомобилю") ?>
                            <?= \Axi::GF("filter/tires_size", "фильтр по размеру") ?>
                        </div>

                        <div class="promo-arrow"></div>
                    </div>

                    <div class="row" data-filter-container-code="oils">
                        <div class="promo-controls col-12 col-xxl-14 col-xl-16 col-lg-24">
                            <h2 class="promo-controls-title promo-controls-title-small">Подбор смазочных материалов</h2>
                        </div>

                        <div class="promo-filter col-12 col-xxl-10 col-xl-8 col-lg-24">
                            <?= \Axi::GF("filter/oils_params", "фильтр по параметрам СМ") ?>
                        </div>
                    </div>

                    <div class="row" data-filter-container-code="akb">
                        <div class="promo-controls col-12 col-xxl-14 col-xl-16 col-lg-24">
                            <h2 class="promo-controls-title">Подбор аккумуляторов</h2>
                            <div class="promo-controls-buttons row">
                                <a 
                                    class="promo-controls-buttons-item"
                                    href="<?= PATH_AKB ?><?= AKB_AVTO ?>/"
                                    title="Аккумуляторы для автомобилей"
                                    >
                                    <i class="auto"><? \Axi::GSVG('auto') ?></i>
                                    <span>Для автомобилей</span>
                                </a>
                                <a
                                    class="promo-controls-buttons-item"
                                    href="<?= PATH_AKB ?><?= AKB_MOTO ?>/"
                                    title="Аккумуляторы для мототехники"
                                    >
                                    <i class="moto"><? \Axi::GSVG('moto') ?></i>
                                    <span>Для мототехники</span>
                                </a>
                            </div>
                        </div>

                        <div class="promo-filter col-12 col-xxl-10 col-xl-8 col-lg-24">
                        </div>
                    </div>

                    <div class="row" data-filter-container-code="discs">
                        <div class="promo-controls col-12 col-xxl-14 col-xl-16 col-lg-24">
                            <h2 class="promo-controls-title">Подбор дисков</h2>
                            <div class="promo-controls-buttons row">
                                <?
                                $active   = \CCatalogExt::getActiveFilterTab(DISCS_IB, DISCS_LIGHT);
                                $active = "discs_car";
                                ?>
                                <button
                                    title="Подбор дисков по автомобилю"
                                    data-form-id="discs_car"
                                    onclick="Index.setFilterForm(this)"
                                    class="promo-controls-buttons-item <?= $active == "discs_car" ? "selected" : "" ?>"
                                    >
                                    <i class="car"><? \Axi::GSVG('car') ?></i>
                                    <span>По автомобилю</span>
                                </button>
                                <button
                                    title="Подбор дисков по параметрам"
                                    data-form-id="discs_size"
                                    onclick="Index.setFilterForm(this)"
                                    class="promo-controls-buttons-item <?= $active == "discs_size" ? "selected" : "" ?>"
                                    >
                                    <i class="wheel"><? \Axi::GSVG('disk') ?></i>
                                    <span>По параметрам</span>
                                </button>
                            </div>
                        </div>

                        <div class="promo-filter col-12 col-xxl-10 col-xl-8 col-lg-24">
                            <?= \Axi::GF("filter/discs_car", "фильтр по автомобилю") ?>
                            <?= \Axi::GF("filter/discs_size", "фильтр по параметрам") ?>
                        </div>

                        <div class="promo-arrow"></div>
                    </div>
                </div>
            </div>

            <div id="promo-slider-m" class="promo-slider-m col-24 hidden-md-up"></div>
        </div>
    </div>

    <div class="promo-toservices">
        <div class="promo-toservices-arrow" onclick="Index.scrollToServices()">
            <i class="ion-ios-arrow-thin-down"></i>
            <span>Наши услуги</span>
        </div>
    </div>
</section>