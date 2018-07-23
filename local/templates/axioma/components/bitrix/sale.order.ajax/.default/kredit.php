<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (/* empty($USER_ID) || */ empty($ORDER_ID))
{
    //на этой странице юзер должен быть уже зареган и авторизован
    return;
}

if ($ORDER_STATUS_ID != STATUS_KREDIT_NEW)
{
    return;
}

$USER_NFO = empty($USER_ID) ? array() : \CUserExt::getById();

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");

$QUESTIONS1 = array(
    "ORDER_ID"   => array(
        "NAME"        => "ORDER_ID",
        "FIELD_TYPE"  => "hidden",
        "REQUIRED"    => "",
        "CAPTION"     => "",
        "VALUE"       => $ORDER_ID,
        "DESCRIPTION" => "",
    ),
    "SURNAME"    => array(
        "NAME"         => "SURNAME",
        "FIELD_TYPE"   => "",
        "REQUIRED"     => "Y",
        "CAPTION"      => "Фамилия",
        //"VALUE"       => $USER_NFO["LAST_NAME"],
        "VALUE"        => "",
        "DESCRIPTION"  => "",
        "AUTOCOMPLETE" => "on",
    ),
    "FIRSTNAME"  => array(
        "NAME"         => "FIRSTNAME",
        "FIELD_TYPE"   => "",
        "REQUIRED"     => "Y",
        "CAPTION"      => "Имя",
        //"VALUE"       => $USER_NFO["NAME"],
        "VALUE"        => "",
        "DESCRIPTION"  => "",
        "AUTOCOMPLETE" => "on",
    ),
    "PATRONYMIC" => array(
        "NAME"         => "PATRONYMIC",
        "FIELD_TYPE"   => "",
        "REQUIRED"     => "Y",
        "CAPTION"      => "Отчество",
        "VALUE"        => "",
        "DESCRIPTION"  => "",
        "AUTOCOMPLETE" => "on",
    ),
    "BIRTHDAY"   => array(
        "NAME"        => "BIRTHDAY",
        "FIELD_TYPE"  => "datepicker",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Дата рождения",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
);

$QUESTIONS2 = array(
    "PHONE"         => array(
        "NAME"        => "PHONE",
        "FIELD_TYPE"  => "PHONE",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Телефон",
        "VALUE"       => $USER_NFO["PERSONAL_PHONE"],
        "DESCRIPTION" => "",
    ),
    "EMAIL"         => array(
        "NAME"        => "EMAIL",
        "FIELD_TYPE"  => "email",
        "REQUIRED"    => "Y",
        "CAPTION"     => "E-mail",
        "VALUE"       => $USER_NFO["EMAIL"],
        "DESCRIPTION" => "",
    ),
    "PASSPORT"      => array(
        "NAME"        => "PASSPORT",
        "FIELD_TYPE"  => "passport",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Серия и номер паспорта",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
    "PASSPORT_DATE" => array(
        "NAME"        => "PASSPORT_DATE",
        "FIELD_TYPE"  => "datepicker",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Дата выдачи",
        "VALUE"       => "",
        "DESCRIPTION" => "",
    ),
);

//id заявки на кредит
$isWait = !empty($KREDIT_ID_PROP_VALUE);
?>

<div class="row kredit">
    <div>
        <div class="kredit-wait <?= $isWait ? "active" : "" ?>">
            <div class="kredit-wait-content">
                <figure class="kredit-wait-spinner"><i><i></i><i></i></i></figure>
                <div class="kredit-wait-note">Ожидание решения по заявке</div>
            </div>
        </div>

        <div class="row kredit-inner <?= $isWait ? "wait" : "" ?>">
            <form
                id="kredit-form"
                method="POST"
                autocomplete="off"
                onsubmit="Form.kreditSendRequest(event, this);"
                >

                <div class="col-24">
                    <div class="kredit-form reg-form">
                        <div class="form-error"></div>
                        <div class="form-result"></div>

                        <figure class="form-spinner"><i></i><i></i></figure>

                        <div class="kredit-form-questionsblock col-10 col-lg-11 col-md-24">
                            <? \CFormExt::showQuestions($QUESTIONS1); ?>
                        </div>

                        <div class="kredit-form-questionsblock col-10 col-lg-11 offset-4 offset-lg-2 col-md-24 offset-md-0">
                            <? \CFormExt::showQuestions($QUESTIONS2); ?>
                        </div>
                    </div>
                </div>


                <div class="col-10 col-lg-11 col-md-24">
                    <div style="min-height: 1px"></div>
                </div>

                <div class="col-10 col-lg-11 offset-4 offset-lg-2 col-md-24 offset-md-0">
                    <div class="kredit-footer">
                        <? \CFormExt::showSubmit("Отправить данные", "form-submit-kredit"); ?>

                        <div class="kredit-footer-note">
                            Нажимая на кнопку, вы даете <a href="/info/personal-information/" target="_blank" title="Подробнее">согласие на обработку персональных данных</a>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>