<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Личный кабинет | Сервис-центры «Континент шин» — шины, диски, масла, технические жидкости, обслуживание автомобиля");

$_REQUEST["show_all"] = "Y";


global $USER;
$USER_ID = $USER->GetID();

if (empty($USER_ID))
{
    LocalRedirect(PATH_AUTH);
    die;
}

\CUserExt::updateUserFrom1C();
$CARD = \COrderExt::getCard();

$fullName = $USER->GetFullName();

$arNames = explode(" ", $fullName);

$firstName = $USER->GetFirstName();
$lastName  = $USER->GetLastName();
$login     = $USER->GetLogin();
$email     = $USER->GetEmail();
$phone     = \CUserExt::getPhone();


$CARD_TYPE    = $CARD["TYPE"];
$CARD_NUMBER  = $CARD["NUMBER"];
$CARD_BALANCE = $CARD["BALANCE"];

$CARD_BALANCE_NUMBER = number_format($CARD_BALANCE, 0, 0, " ");
$CARD_BALANCE_TEXT   = wordPlural($CARD_BALANCE, array("бонус", "бонуса", "бонусов"));

$CARD_TYPE_TITLE = \WS_PSettings::getFieldValue("BONUS_CARD_NAME_{$CARD_TYPE}", $CARD_TYPE);


$USER_PROFILE = \CUserExt::getProfile();

$subscribes     = explode(";", $USER_PROFILE["PROPS_VALUE"]["SUBSCRIBE"]["VALUE"]);
$subscribeSms   = in_array("SMS", $subscribes);
$subscribeEmail = in_array("EMAIL", $subscribes);

$PERSON_TYPE_ID = $USER_PROFILE["PERSON_TYPE_ID"];

$sBonusesRulesUrl = \WS_PSettings::getFieldValue("PATH_BONUSES_RULES_{$CARD_TYPE}", false);

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/personal.js");

$APPLICATION->SetTitle("Личный кабинет");
?>

<div class="row">
    <div class="offset-4 col-16 offset-xxl-3 col-xxl-18 offset-xl-2 col-xl-20 offset-lg-1 col-lg-22">
        <div class="personal-wrap">

            <div class="row">
                <div class="col-24">
                    <div class="personal-greetings">
                        <div class="personal-greetings-hello"><? \Axi::GT("kabinet/personal-greetings-hello", "приветствие"); ?>, <?= count($arNames) > 2 ? $arNames[1] . " " . $arNames[2] : $arNames[0] ?>!</div>
                        <div class="personal-greetings-welcome"><? \Axi::GT("kabinet/personal-greetings-welcome", "приветствие"); ?></div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-24">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:sale.personal.order", "", Array(
                        "STATUS_COLOR_N"                => "green",
                        "STATUS_COLOR_P"                => "yellow",
                        "STATUS_COLOR_F"                => "gray",
                        "STATUS_COLOR_PSEUDO_CANCELLED" => "red",
                        "SEF_MODE"                      => "Y",
                        "ORDERS_PER_PAGE"               => 5,
                        "PATH_TO_PAYMENT"               => PATH_PAYMENT,
                        "PATH_TO_BASKET"                => PATH_BASKET,
                        "SET_TITLE"                     => "N",
                        "SAVE_IN_SESSION"               => "N",
                        "NAV_TEMPLATE"                  => false,
                        "ACTIVE_DATE_FORMAT"            => "d.m.Y",
                        "PROP_1"                        => Array(),
                        "PROP_2"                        => Array(),
                        "CACHE_TYPE"                    => "A",
                        "CACHE_TIME"                    => "3600",
                        "CACHE_GROUPS"                  => "Y",
                        "CUSTOM_SELECT_PROPS"           => "",
                        "HISTORIC_STATUSES"             => "Я",
                        "SEF_FOLDER"                    => PATH_PERSONAL_ORDERS,
                        "SEF_URL_TEMPLATES"             => Array(
                            "list"   => "list/",
                            "detail" => "detail/#ID#/",
                            "cancel" => "cancel/#ID#/"
                        ),
                        "VARIABLE_ALIASES"              => Array(
                            "list"   => Array(),
                            "detail" => Array(
                                "ID" => "ID"
                            ),
                            "cancel" => Array(
                                "ID" => "ID"
                            ),
                        )
                            )
                    );
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-24">
                    <div class="personal-block-wrap">
                        <div class="personal-title">Личные данные</div>

                        <?
                        if ($PERSON_TYPE_ID == UR_LICO):
                            $PROPS_VALUE = $USER_PROFILE["PROPS_VALUE"];
                            ?>

                            <div class="personal-block personal-urlico row">
                                <div class="personal-block-icon personal-urlico-icon"><i></i></div>
                                <div class="personal-block-data personal-urlico-data">

                                    <div>
                                        <div class="bold uppercase">Юридическое наименование:</div>
                                        <div><?= htmlspecialcharsbx($PROPS_VALUE["COMPANY"]["VALUE"]) ?></div>
                                        <br/>
                                    </div>

                                    <div>
                                        <div class="bold uppercase">Юридический адрес:</div>
                                        <div><?= htmlspecialcharsbx($PROPS_VALUE["COMPANY_ADR"]["VALUE"]) ?></div>
                                        <br/>
                                    </div>

                                    <div>
                                        <span class="bold uppercase">ИНН:</span>
                                        <span><?= htmlspecialcharsbx($PROPS_VALUE["INN"]["VALUE"]) ?></span>
                                    </div>

                                    <div>
                                        <span class="bold uppercase">КПП:</span>
                                        <span><?= htmlspecialcharsbx($PROPS_VALUE["KPP"]["VALUE"]) ?></span>
                                    </div>

                                </div>
                            </div>
                        <? endif; ?>

                        <div class="personal-block personal-userinfo row">
                            <div class="personal-block-icon personal-userinfo-icon"><i></i></div>
                            <div class="personal-block-data personal-userinfo-data">

                                <? if ($PERSON_TYPE_ID == UR_LICO): ?>
                                    <div class="bold uppercase">Контактное лицо</div>
                                <? endif; ?>

                                <? if (!empty($firstName) || !empty($lastName)): ?>
                                    <? if (!empty($firstName)): ?>
                                        <div><?= $firstName ?></div>
                                    <? endif; ?>

                                    <? if (!empty($lastName)): ?>
                                        <div><?= $lastName ?></div>
                                    <? endif; ?>

                                    <div style="height: 40px;"></div>
                                <? endif; ?>

                                <div class="bold"><?= $email ?></div>
                                <div class="bold"><?= $phone ?></div>
                            </div>
                        </div>

                        <div class="personal-block personal-subscribes row">
                            <div class="personal-block-icon personal-subscribes-icon"><i></i></div>
                            <div class="personal-block-data personal-subscribes-data">

                                <div class="bold uppercase">Рассылки</div>

                                <? if (!empty($subscribeSms) || !empty($subscribeEmail)): ?>
                                    <? if (!empty($subscribeEmail)): ?>
                                        <div>E-mail рассылка</div>
                                    <? endif; ?>

                                    <? if (!empty($subscribeSms)): ?>
                                        <div>SMS рассылка</div>
                                    <? endif; ?>
                                <? else: ?>
                                    <div>Нет подписок</div>
                                <? endif; ?>
                            </div>
                        </div>

                        <div class="personal-block personal-links">
                            <div class="personal-block-data personal-links-data">
                                <a class="bold" href="<?= PATH_PERSONAL_ACCOUNT ?>" title="Изменить данные ">Изменить данные </a>
                            </div>
                            <div class="personal-block-data personal-links-data">
                                <a class="bold" href="<?= PATH_LOGOUT ?>" title="Выход">Выход</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?
                if (BONUSES_ENABLE && !empty($CARD_NUMBER) && $PERSON_TYPE_ID == FIZ_LICO):
                    $CARD_CODE = $CARD_TYPE == "REGULAR" ? 'CS.' : 'VIP.';
                    ?>
                    <div class="col-12 col-md-24">
                        <div class="personal-block-wrap">
                            <div class="personal-title">Бонусная карта</div>
                            <div class="personal-block personal-card personal-block-right row">
                                <div class="personal-block-data personal-card-header">
                                    <div>
                                        <div class="personal-card-bonuses-info">На вашей карте:</div>
                                        <div class="personal-card-bonuses-count"><?= $CARD_BALANCE_NUMBER ?></div>
                                        <div class="personal-card-bonuses-text"><?= $CARD_BALANCE_TEXT ?></div>
                                        <div class="personal-card-number">
                                            <span class="black">Тип карты: </span>
                                            <span class="white"><?= $CARD_TYPE_TITLE ?></span>
                                        </div>
                                        <div class="personal-card-number">
                                            <span class="black">Номер карты: </span>
                                            <span class="white"><?= $CARD_CODE . $CARD_NUMBER ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="personal-block-data personal-card-footer">
                                    <div class="pos-r">
    <? if (!empty($sBonusesRulesUrl)): ?>
                                            <div class="">
                                                <a href="<?= $sBonusesRulesUrl ?>" title="Условия бонусной программы" target="_blank">
                                                    Условия бонусной программы
                                                </a>
                                            </div>
    <? endif; ?>

                                        <a class="" href="<?= PATH_PERSONAL_ADDCARD ?>" title="Прикрепить другую карту">
                                            Прикрепить другую карту
                                        </a>

                                        <div
                                            class="personal-notes-card"
                                            data-target="#card-full"
                                            onmouseenter="App.showNoteTip(this)"
                                            onmouseleave="App.hideNoteTip(this)"
                                            >
                                            <span
                                                class="personal-notes-card-short noselect"
                                                data-target="#bonus-full"
                                                onclick="App.showNoteTip(this)"
                                                >
                                                <i class="ion-ios-help-outline"></i>
                                            </span>
                                            <span
                                                id="card-full"
                                                class="personal-notes-card-full">
    <?= \Axi::GT("kabinet/card-replace-note"); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="bold white">Есть вопросы по карте?</div>
                                        <a href="/faq/?CATEGORY=21" title="Напишите нам" target="_blank">
                                            Напишите нам!
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
<? else: ?>
                    <div class="col-12 col-md-24">
                        <div class="personal-block-wrap">
                            <div class="personal-title">Бонусная карта</div>
                            <div class="personal-block personal-card personal-block-right row">


                                <div class="personal-block-data personal-card-footer">
                                    <div class="white"><?= \Axi::GT("kabinet/card-empty") ?></div>

                                    <div class="pos-r">
                                        <a class="white bold" href="<?= PATH_PERSONAL_ADDCARD ?>" title="Прикрепить карту">Прикрепить карту</a>

                                        <div
                                            class="personal-notes-card"
                                            data-target="#card-full"
                                            onmouseenter="App.showNoteTip(this)"
                                            onmouseleave="App.hideNoteTip(this)"
                                            >
                                            <span
                                                class="personal-notes-card-short noselect"
                                                data-target="#bonus-full"
                                                onclick="App.showNoteTip(this)"
                                                >
                                                <i class="ion-ios-help-outline"></i>
                                            </span>
                                            <span
                                                id="card-full"
                                                class="personal-notes-card-full">
    <?= \Axi::GT("kabinet/card-new-note"); ?>
                                            </span>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
<? endif; ?>
            </div>

<? if ($USER->IsAdmin()): ?>
                <div class="row  hidden-xl-down">
                    <div class="col-24">
                        <div class="personal-block-wrap" style="border: 1px red solid">
                            <div class="personal-title">bitrix:sale.personal.profile</div>
                            <?
                            $APPLICATION->IncludeComponent("bitrix:sale.personal.profile", "", Array(
                                "SEF_MODE"           => "N",
                                "PER_PAGE"           => 20,
                                "USE_AJAX_LOCATIONS" => "Y",
                                "SET_TITLE"          => "N",
                                "SEF_FOLDER"         => "/",
                                "SEF_URL_TEMPLATES"  => Array(
                                    "list"   => "",
                                    "detail" => "?ID=#ID#"
                                ),
                                "VARIABLE_ALIASES"   => Array(
                                    "list"   => Array(),
                                    "detail" => Array(
                                        "ID" => "ID"
                                    ),
                                )
                                    )
                            );
                            ?>
                        </div>

                        <div class="personal-block-wrap" style="border: 1px red solid;">
                            <div class="personal-title">clearCacheCustom</div>
                                <? if (!empty($_REQUEST["clearCacheCustom"])): ?>
                                <div class="offset-1">
                                    <?
                                    $path             = htmlspecialcharsbx($_REQUEST["clearCacheCustom"]);
                                    $clearCacheResult = ClearCache("/" . $path . "/", true);
                                    ?>
                                    <span class="bold red uppercase">
        <?= $clearCacheResult ? "clearCacheCustom $path Success" : "clearCacheCustom $path Fail"; ?>
                                    </span>
                                    <br/><br/>
                                </div>
                            <? endif; ?>

                                <? if (!empty($_REQUEST["clearCacheCustomC"])): ?>
                                <div class="offset-1">
                                    <?
                                    $path             = htmlspecialcharsbx($_REQUEST["clearCacheCustomC"]);
                                    $clearCacheResult = ClearCache("/" . $path . "/");
                                    ?>
                                    <span class="bold red uppercase">
        <?= $clearCacheResult ? "clearCacheCustomC $path Success" : "clearCacheCustomC $path Fail"; ?>
                                    </span>
                                    <br/><br/>
                                </div>
    <? endif; ?>

                            <div class="offset-1">
                                <a href="?clearCacheCustom=menu">menu</a><br/>
                                <a href="?clearCacheCustom=menu.sections">menu.sections</a><br/>
                                <a href="?clearCacheCustom=catalog.element">catalog.element</a><br/>
                                <a href="?clearCacheCustom=catalog.section">catalog.section</a><br/>
                                <a href="?clearCacheCustom=catalog.section.list">catalog.section.list</a><br/>
                                <a href="?clearCacheCustom=form.result.new">form.result.new</a><br/>
                                <a href="?clearCacheCustom=news.detail">news.detail</a><br/>
                                <a href="?clearCacheCustom=news.list">news.list</a><br/>
                                <a href="?clearCacheCustom=search.page">search.page</a><br/>
                                <br/>
                                <a href="?clearCacheCustomC=ccache_catalog">ccache_catalog</a><br/>
                                <a href="?clearCacheCustomC=ccache_common">ccache_common</a><br/>
                                <a href="?clearCacheCustomC=ccache_filter">ccache_filter</a><br/>
                                <a href="?clearCacheCustomC=ccache_services">ccache_services</a><br/>
                                <a href="?clearCacheCustomC=ccache_stores">ccache_stores</a><br/>
                                <a href="?clearCacheCustomC=ccache_wizard">ccache_wizard</a><br/>
                                <br/>
                                <a href="?clearCacheCustomC=medialib">medialib</a><br/>
                            </div>

                            <br/>
                            <br/>
                        </div>
                    </div>
                </div>
<? endif; ?>
        </div>
    </div>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>