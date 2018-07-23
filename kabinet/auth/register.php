<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

//$USER_NFO     = \CUserExt::getById();
//$USER_PROFILE = \CUserExt::getProfile();
//printra($USER_NFO);
//
//
//
//добавляем или обновляем юзера в 1С
//
//
//
//$arData          = array(
//    "XML_ID" => "2582af27-7d7b-11e7-98c8-00155d1a0c00",
//);
//$BYCARD_ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);
//printra($BYCARD_ar1CUser);
//
//
//
//
//$arData = array(
//    "ID"             => 77017,
//    "EMAIL"          => "koshkin@mail.ru",
//    "PHONE"          => "+7 (844) 444-11-11",
//    "SUBSCRIBE"      => NULL,
//    "PERSON_TYPE_ID" => 0,
//    "FIO"            => "Мошкин Роман Владимирович",
//    "COMPANY"        => NULL,
//    "COMPANY_ADR"    => NULL,
//    "INN"            => NULL,
//    "KPP"            => NULL,
//    "CONTACT_PERSON" => NULL,
//    "CURRACC"        => NULL,
//    "XML_ID"         => "2582af27-7d7b-11e7-98c8-00155d1a0c00",
//    //"XML_ID"         => "6a4c11d3-8714-11e7-98c8-00155d1a0c00",
//);
//$ar1CnewUser = \CURL::getReplay("newUser", $arData, true, 0, true, false);
//printra($ar1CnewUser);

if ($_REQUEST["getUserByCard"] == 1)
{
    if (empty($_REQUEST["CARD"]))
    {
        $arData = array(
            "CARD"  => "996",
            "FIO"   => "Кузькин кузьма",
            "PHONE" => "+73333333333",
        );
    }
    else
    {
        $arData = array(
            "CARD"  => $_REQUEST["CARD"],
            "FIO"   => $_REQUEST["FIO"],
            "PHONE" => $_REQUEST["PHONE"],
        );
    }

    $arReplay = \CURL::getReplay("getUserByCard", $arData, true, false, true);
    printra($arReplay);
}


if ($USER->IsAuthorized())
{
    LocalRedirect(PATH_PERSONAL);
    die;
}

/**
 * Бонусная "BKBONUS"_0000018
 * Каноныхин Владимир Михайлович
 * 9043753600
 */
$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$FROM = $request['FROM'];
$HASH = urldecode($request['HASH']);

if (!empty($HASH))
{
    $XML_ID = getHash('decrypt', $HASH);

    //получаем инфу о юзере из 1С
    $arData = array(
        "XML_ID" => $XML_ID,
    );

    $ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);

    $PERSON_TYPE_ID = $arUserInfo["PERSON_TYPE_ID"] ? UR_LICO : FIZ_LICO;

    if ($FROM == "BYCARD")
    {
        $CARD         = $arUserInfo["CARD"];
        $TYPE_OF_CARD = $arUserInfo["TYPE_OF_CARD"];
        $BONUSES      = $arUserInfo["BONUSES"];

        $REG_BY_CARD_DATA = @unserialize($APPLICATION->get_cookie("REG_BY_CARD_DATA")); //это те данные, которые юзер САМ ввел в форму
    }
    else
    {
        $APPLICATION->set_cookie("REG_BY_CARD_DATA", NULL, time() + 60 * 60 * 24 * 365);
    }
}

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/oauth.js");

$APPLICATION->SetTitle("Регистрация");

$title = "Зарегистрироваться как:";
$descr = "";

if (!empty($FROM))
{
    $title = "Завершить регистрацию";

    if ($FROM != "BYCARD")
    {
        $title .= " как:";
        $descr = "В дальнейшем вы сможете входить на сайт через соцсеть без ввода пароля";
    }
    else
    {
        $title .= ":";
        $descr = "В дальнейшем вы сможете входить на сайт по номеру карты без ввода пароля";
    }
}

$netsCodes = \COAuthExt::getNetsCodes();
foreach ($netsCodes as $NET)
{
    if ($FROM == $NET)
    {
        $UF_CODE   = "UF_" . $NET . "_USER_ID";
        $arNetUser = unserialize($APPLICATION->get_cookie($UF_CODE));
        break;
    }
}

$PERSONS = array(
    FIZ_LICO => array(
        'LINK'     => PATH_REGISTER . "?FROM=" . $FROM,
        'TITLE'    => "Физическое лицо",
        'SELECTED' => false,
    ),
    UR_LICO  => array(
        'LINK'     => PATH_REGISTER . "?law=1&FROM=" . $FROM,
        'TITLE'    => "Юридическое лицо",
        'SELECTED' => false,
    ),
);

if ($request['law'] == "1" || $PERSON_TYPE_ID == UR_LICO)
{
    //юр лицо
    $QUESTIONS                    = \CUserExt::getUserProps(UR_LICO);
    //printra($QUESTIONS);
    $PERSONS[UR_LICO]['SELECTED'] = true;

    $QUESTIONS += array(
        "PERSON_TYPE_ID" => array(
            "NAME"        => "PERSON_TYPE_ID",
            "FIELD_TYPE"  => "hidden",
            "REQUIRED"    => "N",
            "CAPTION"     => "",
            "VALUE"       => UR_LICO,
            "DESCRIPTION" => "",
        ),
    );

    if (!empty($arNetUser))
    {
        $QUESTIONS['CONTACT_PERSON']["VALUE"] = $arNetUser['user_name'];
    }

    if (!empty($ar1CUser))
    {
        $QUESTIONS['CONTACT_PERSON']["VALUE"] = $ar1CUser['CONTACT_PERSON'];
        $QUESTIONS['INN']["VALUE"]            = $ar1CUser['INN'];
        $QUESTIONS['KPP']["VALUE"]            = $ar1CUser['KPP'];
        $QUESTIONS['COMPANY']["VALUE"]        = $ar1CUser['COMPANY'];
        $QUESTIONS['COMPANY_ADR']["VALUE"]    = $ar1CUser['COMPANY_ADR'];
        $QUESTIONS['CURRACC']["VALUE"]        = $ar1CUser['CURRACC'];
        $QUESTIONS['KPP']["VALUE"]            = $ar1CUser['KPP'];
    }
}
else
{
    //физ лицо
    $QUESTIONS                     = \CUserExt::getUserProps(FIZ_LICO);
    $PERSONS[FIZ_LICO]['SELECTED'] = true;

    $QUESTIONS += array(
        "PERSON_TYPE_ID" => array(
            "NAME"        => "PERSON_TYPE_ID",
            "FIELD_TYPE"  => "hidden",
            "REQUIRED"    => "N",
            "CAPTION"     => "",
            "VALUE"       => FIZ_LICO,
            "DESCRIPTION" => "",
        ),
    );

    if (!empty($arNetUser))
    {
        $QUESTIONS['FIO']["VALUE"] = $arNetUser['user_name'];
    }

    if (!empty($ar1CUser))
    {
        $QUESTIONS['FIO']["VALUE"] = $ar1CUser['FIO'];
        //$QUESTIONS['FIO']["VALUE"] = $REG_BY_CARD_DATA['FIO'];
    }
}

if (!empty($arNetUser))
{
    $QUESTIONS['EMAIL']["VALUE"] = $arNetUser['email'];

    $QUESTIONS += array(
        $UF_CODE => array(
            "NAME"        => "UF[$UF_CODE]",
            "FIELD_TYPE"  => "hidden",
            "REQUIRED"    => "N",
            "CAPTION"     => "",
            "VALUE"       => $arNetUser['user_id'],
            "DESCRIPTION" => "",
        ),
    );
}

if (!empty($ar1CUser))
{
    $QUESTIONS['EMAIL']["VALUE"] = $ar1CUser['EMAIL'];
    //$QUESTIONS['PHONE']["VALUE"] = getPhoneFromString($ar1CUser['PHONE'], true);
    $QUESTIONS['PHONE']["VALUE"] = getPhoneFromString($REG_BY_CARD_DATA['PHONE'], true);
}

$QUESTIONS += array(
    "PASSWORD"  => array(
        "NAME"        => "PASSWORD",
        "FIELD_TYPE"  => "password",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Пароль",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
    "PASSWORD2" => array(
        "NAME"        => "PASSWORD2",
        "FIELD_TYPE"  => "password",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Повторите пароль",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
);
?>

<div class="row auth reg">
    <div class="offset-5 col-14 offset-xxl-4 col-xxl-16 offset-xl-2 col-xl-20 offset-lg-0 col-lg-24">
        <div class="row auth-inner reg-inner">
            <div class="col-24 auth-col auth-col-header">
                <h2 class="auth-col-title"><?= $title ?></h2>

                <? if (!empty($descr)): ?>
                    <div class="auth-col-descr"><?= $descr ?></div>
                <? endif; ?>

                <? if ($FROM != "BYCARD"): ?>
                    <div class="auth-fakeselect">
                        <? foreach ($PERSONS as $PERSON_TYPE_ID => $SELECT): ?>
                            <a
                                class="auth-fakeselect-link <?= $SELECT['SELECTED'] ? "selected" : "" ?>"
                                href="<?= $SELECT['LINK'] ?>"
                                title="<?= $SELECT['TITLE'] ?>"
                                >
                                <i></i><span><?= $SELECT['TITLE'] ?></span>
                            </a>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
            </div>

            <form
                method="POST"
                autocomplete="off"
                onsubmit="Form.register(event, this);">
                <div class="col-12 col-lg-24 auth-col">
                    <div class="auth-form reg-form">
                        <div class="form-error"></div>
                        <div class="form-result"></div>
                        <figure class="form-spinner"><i></i><i></i></figure>

                        <?
                        foreach ($QUESTIONS as $SID => $arQuestion):
                            $NAME        = $arQuestion['NAME'];
                            $FIELD_TYPE  = $arQuestion['FIELD_TYPE']; //text, hidden, etc.
                            $REQUIRED    = $arQuestion['REQUIRED'];
                            $CAPTION     = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
                            $PARAMS      = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
                            $VALUE       = $arQuestion['VALUE'];
                            $DESCRIPTION = $arQuestion['DESCRIPTION'];

                            if ($FIELD_TYPE == 'hidden'):
                                ?>
                                <input
                                    type="<?= $FIELD_TYPE ?>"
                                    name="<?= $NAME ?>"
                                    <?= $PARAMS ?>
                                    value="<?= $VALUE ?>"
                                    />
                                    <?
                                    continue;
                                endif;
                                ?>

                            <div class="form-question form-question-<?= $FIELD_TYPE ?>">
                                <span
                                    class="form-question-placeholder <?= !empty($VALUE) ? "active" : "" ?> "
                                    onclick="Form.onClickPlaceholder(this)"
                                    ><?= $CAPTION ?></span>

                                <? if ($FIELD_TYPE == "textarea"): ?>
                                    <textarea
                                        name="<?= $NAME ?>"
                                        <?= $PARAMS ?>
                                        maxlength="1000"
                                        title="<?= $CAPTION ?>"
                                        ></textarea>
                                    <? else: ?>
                                    <input
                                        class="form-question-input-field"
                                        type="<?= $FIELD_TYPE ?>"
                                        name="<?= $NAME ?>"
                                        <?= $PARAMS ?>
                                        maxlength="100"
                                        title="<?= $CAPTION ?>"
                                        onfocus="Form.onInputFocus(this)"
                                        onblur="Form.onInputBlur(this)"
                                        value="<?= $VALUE ?>"
                                        autocomplete="off"
                                        />
                                    <? endif; ?>

                                <span class="form-question-error"></span>

                                <? if (!empty($DESCRIPTION)): ?>
                                    <span class="form-question-description"><?= $DESCRIPTION ?></span>
                                <? endif; ?>
                            </div>
                        <? endforeach; ?>

                    </div>
                </div>

                <div class="col-12 col-lg-24 auth-col">

                    <div class="auth-fakebox selected" onclick="Form.toggleFakeCheckbox(this)">
                        <i></i><span><a href="/info/personal-information/" target="_blank" title="Подробнее">Согласие</a> на обработку персональных данных</span>
                        <input type="hidden" name="CONSENT" value="1" />
                    </div>

                    <div class="auth-subtitle">
                        Хотите получать новости и акции от вк сервис?
                    </div>

                    <div class="auth-fakebox" onclick="Form.toggleFakeCheckbox(this)">
                        <i></i><span>Согласие на SMS рассылку</span>
                        <input type="hidden" name="SUBSCRIBE_SMS" value="0" />
                    </div>

                    <div class="auth-fakebox selected" onclick="Form.toggleFakeCheckbox(this)">
                        <i></i><span>Согласие на E-mail рассылку</span>
                        <input type="hidden" name="SUBSCRIBE_EMAIL" value="1" />
                    </div>

                    <div class="form-submit">
                        <input type="hidden" name="SUBSCRIBE" value="" />
                        <input type="hidden" name="FROM" value="<?= htmlspecialchars($FROM) ?>" />
                        <input type="hidden" name="HASH" value="<?= htmlspecialchars($HASH) ?>" />
                        <?= bitrix_sessid_post() ?>
                        <?= bitrix_sessid_post(VK_SESSID) ?>
                        <input
                            type="submit"
                            class="form-submit-button"
                            name="web_form_apply"
                            title="Зарегистрироваться"
                            value="Зарегистрироваться"
                            />
                    </div>

                    <a
                        class="auth-col-link auth-col-link-key m0"
                        href="<?= PATH_AUTH ?>"
                        title="Авторизация"
                        ><i></i>Авторизация</a>
                    <a
                        class="auth-col-link auth-col-link-key"
                        href="<?= PATH_RECOVERY ?>"
                        title="Восстановление пароля"
                        ><i></i>Забыли пароль?</a>
                </div>

            </form>
        </div>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>