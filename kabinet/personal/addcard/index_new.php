<?
$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/personal.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/card.js");


$USER_NFO     = \CUserExt::getById();
$USER_PROFILE = \CUserExt::getProfile($USER_ID);
$phone        = \CUserExt::getPhone();
$NUMBER       = getUF("USER", $USER_ID, "UF_CARD_NUMBER");

//
//$arData   = array(
//    "XML_ID" => $USER_NFO["XML_ID"],
//);
//$ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);
//printra($ar1CUser);

$fullName = $USER->GetFullName();
if (empty($fullName)) $fullName = $USER_PROFILE["PROPS_VALUE"]["FIO"]["VALUE"];

if (empty($NUMBER))
{
    $title    = "Прикрепить карту";
    $infofile = "kabinet/card-add-info";
}
else
{
    $title    = "Прикрепить новую карту";
    $infofile = "kabinet/card-readd-info";
}

$APPLICATION->SetTitle($title);


$arStep1 = array(
    "CARD"  => array(
        "NAME"        => "CARD",
        "FIELD_TYPE"  => "text",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Номер бонусной карты",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
    "PHONE" => array(
        "NAME"        => "PHONE",
        "FIELD_TYPE"  => "text",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Телефон",
        "VALUE"       => $phone,
        "DESCRIPTION" => "",
    ),
    "FIO"   => array(
        "NAME"        => "FIO",
        "FIELD_TYPE"  => "text",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Ф. И. О.",
        "VALUE"       => $fullName,
        "DESCRIPTION" => "",
    ),
    "CITY"  => array(
        "NAME"        => "CITY",
        "FIELD_TYPE"  => "text",
        "REQUIRED"    => "N",
        "CAPTION"     => "Город",
        "VALUE"       => $USER_NFO["PERSONAL_CITY"],
        "DESCRIPTION" => "",
    ),
    "BDAY"  => array(
        "NAME"        => "BDAY",
        "FIELD_TYPE"  => "datepicker",
        "REQUIRED"    => "N",
        "CAPTION"     => "Дата рождения",
        "VALUE"       => $USER_NFO["PERSONAL_BIRTHDAY"],
        "DESCRIPTION" => "",
    ),
);

$arStep2 = array(
    "CODE" => array(
        "NAME"        => "CODE",
        "FIELD_TYPE"  => "text",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Код из SMS",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
    "CARD" => array(
        "NAME"        => "CARD",
        "FIELD_TYPE"  => "hidden",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Номер бонусной карты",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
);

$infofile2 = "kabinet/card-enter-code";
$infofile3 = "kabinet/card-saved";
?>

<div class="row">
    <div class="offset-4 col-16 offset-xxl-3 col-xxl-18 offset-xl-2 col-xl-20 offset-lg-1 col-lg-22">
        <div class="row">
            <div class="col-12 col-lg-24" style="padding: 50px 0;">
                <h2 class="auth-col-title"><?= $title ?></h2>

                <div class="addcard-step active" id="addcard-step1">
                    <div class="auth-col-info addcard-step-info ve"><?= \Axi::GT($infofile) ?></div>

                    <div class="auth-form">
                        <div class="form-error"></div>
                        <div class="form-result"></div>

                        <form
                            method="POST"
                            autocomplete="off"
                            onsubmit="Card.sendSmsAndSaveUser(event, this);">
                            <figure class="form-spinner"><i></i><i></i></figure>

                            <? showFormQuestions($arStep1); ?>

                            <div class="form-astericks">* – поля обязательные для заполнения</div>

                            <div class="form-submit nomt">
                                <?= bitrix_sessid_post() ?>
                                <?= bitrix_sessid_post(VK_SESSID) ?>
                                <input
                                    type="submit"
                                    class="form-submit-button"
                                    name="web_form_apply"
                                    title="Активировать"
                                    value="Активировать"
                                    />
                            </div>
                        </form>
                    </div>
                </div>




                <div class="addcard-step"  id="addcard-step2">
                    <div class="auth-col-info addcard-step-info ve"><?= \Axi::GT($infofile2) ?></div>

                    <div class="auth-col-info addcard-step-info addcard-resend">
                        <span id="resend-descr">
                            Отправить SMS еще раз можно через 
                            <mark id="resend-timer"></mark>
                            секунд
                        </span>

                        <a
                            href="#"
                            title="Отправить код повторно"
                            id="resend-link"
                            onclick="Card.goToStep(event, 1)"
                            >Отправить код повторно</a>
                    </div>

                    <div class="auth-form">
                        <div class="form-error"></div>
                        <div class="form-result"></div>

                        <form
                            method="POST"
                            autocomplete="off"
                            onsubmit="Card.activate(event, this);">
                            <figure class="form-spinner"><i></i><i></i></figure>

                            <? showFormQuestions($arStep2); ?>

                            <div class="form-astericks">* – поля обязательные для заполнения</div>

                            <div class="form-submit nomt">
                                <?= bitrix_sessid_post() ?>
                                <?= bitrix_sessid_post(VK_SESSID) ?>
                                <input
                                    type="submit"
                                    class="form-submit-button"
                                    name="web_form_apply"
                                    title="Готово"
                                    value="Готово"
                                    />
                            </div>
                        </form>
                    </div>
                </div>




                <div class="addcard-step"  id="addcard-step3">
                    <div class="auth-col-info addcard-step-info ve"><?= \Axi::GT($infofile3) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="backlink">
    <a href="<?= PATH_PERSONAL ?>" title="Назад в кабинет">
        <i class="ion-ios-arrow-back"></i><span>Назад в кабинет</span>
    </a>
</div>