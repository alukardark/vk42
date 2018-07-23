<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if ($USER->IsAuthorized())
{
    LocalRedirect(PATH_PERSONAL);
    die;
}

//\COAuthExt::getNets();

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/personal.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/oauth.js");

$APPLICATION->SetTitle("Авторизация");

if ($request['bycard'] == "1")
{
    //$title    = "Авторизация на сайте с помощью бонусной карты";
    $altLink  = PATH_AUTH;
    $altTitle = "Войти с помощью E-mail или телефона";

    $QUESTIONS = array(
        "CARD"   => array(
            "NAME"        => "CARD",
            "FIELD_TYPE"  => "text",
            "REQUIRED"    => "Y",
            "CAPTION"     => "Номер бонусной карты",
            "VALUE"       => "",
            "DESCRIPTION" => "",
        ),
        "FIO"    => array(
            "NAME"        => "FIO",
            "FIELD_TYPE"  => "text",
            "REQUIRED"    => "Y",
            "CAPTION"     => "ФИО",
            "VALUE"       => "",
            "DESCRIPTION" => "",
        ),
        "PHONE"  => array(
            "NAME"        => "PHONE",
            "FIELD_TYPE"  => "text",
            "REQUIRED"    => "Y",
            "CAPTION"     => "Телефон",
            "VALUE"       => "",
            "DESCRIPTION" => "",
        ),
        "BYCARD" => array(
            "NAME"        => "BYCARD",
            "FIELD_TYPE"  => "hidden",
            "REQUIRED"    => "N",
            "CAPTION"     => "",
            "VALUE"       => "1",
            "DESCRIPTION" => "",
        ),
    );
}
else
{
    //$title    = "Авторизация на сайте";
    $altLink  = PATH_AUTH . "?bycard=1";
    $altTitle = "Войти с помощью бонусной карты";

    $QUESTIONS = array(
        "LOGIN"    => array(
            "NAME"             => "LOGIN",
            "FIELD_TYPE"       => "text",
            "REQUIRED"         => "Y",
            "CAPTION"          => "Телефон или E-mail",
            "VALUE"            => "",
            "DESCRIPTION"      => "Вводите телефон формате +7 (XXX) XXX-XX-XX",
            "MASKPHONEOREMAIL" => true,
        ),
        "PASSWORD" => array(
            "NAME"        => "PASSWORD",
            "FIELD_TYPE"  => "password",
            "REQUIRED"    => "Y",
            "CAPTION"     => "Пароль",
            "VALUE"       => "",
            "DESCRIPTION" => "",
        ),
        "BYCARD"   => array(
            "NAME"        => "BYCARD",
            "FIELD_TYPE"  => "hidden",
            "REQUIRED"    => "N",
            "CAPTION"     => "",
            "VALUE"       => "0",
            "DESCRIPTION" => "",
        ),
    );
}

$title = "Авторизация";
?>

<div class="row auth">
    <div class="offset-5 col-14 offset-xxl-4 col-xxl-16 offset-xl-2 col-xl-20 offset-lg-0 col-lg-24">
        <div class="row auth-inner">
            <div class="col-12 col-lg-24 auth-col">
                <h2 class="auth-col-title"><?= $title ?></h2>

                <?
                if (isPost("get_auth_tab"))
                {
                    $APPLICATION->RestartBuffer();
                }
                ?>
                <div class="auth-tabs" id="auth-tabs-wrap">

                    <div class="auth-tabs row">
                        <button
                            title="по e-mail или телефону"
                            class="auth-tabs-item col-13 <?= $request['bycard'] == "1" ? "" : "active" ?>"
                            data-bycard="0"
                            onclick="Personal.setTab(event, this)"
                            >
                            <span class="hidden-sm-down">по e-mail или телефону</span>
                            <span class="hidden-sm-up">по email/тел</span>
                        </button>
                        <div class="col-1"></div>
                        <button
                            title="по бонусной карте"
                            class="auth-tabs-item col-10 float-right <?= $request['bycard'] == "1" ? "active" : "" ?>"
                            data-bycard="1"
                            onclick="Personal.setTab(event, this)"
                            >
                            <span class="hidden-sm-down">по бонусной карте</span>
                            <span class="hidden-sm-up">по карте</span>
                        </button>
                    </div>

                <!--<a class="auth-col-link" href="<?= $altLink ?>" title="<?= $altTitle ?>"><?= $altTitle ?></a>-->

                    <div class="auth-form">
                        <div class="form-error"></div>
                        <div class="form-result"></div>

                        <form
                            method="POST"
                            autocomplete="off"
                            onsubmit="Form.auth(event, this);">
                            <figure class="form-spinner"><i></i><i></i></figure>

                            <?
                            foreach ($QUESTIONS as $SID => $arQuestion):
                                $NAME             = $arQuestion['NAME'];
                                $FIELD_TYPE       = $arQuestion['FIELD_TYPE']; //text, hidden, etc.
                                $REQUIRED         = $arQuestion['REQUIRED'];
                                $CAPTION          = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
                                $PARAMS           = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
                                $VALUE            = $arQuestion['VALUE'];
                                $DESCRIPTION      = $arQuestion['DESCRIPTION'];
                                $MASKPHONEOREMAIL = $arQuestion['MASKPHONEOREMAIL'] ? 'onkeyup="Form.maskPhoneOrEmail(event, this)"' : '';

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
                                            <?= $MASKPHONEOREMAIL ?>
                                            value="<?= $VALUE ?>"
                                            />
                                        <? endif; ?>

                                    <span class="form-question-error"></span>

                                    <? if (!empty($DESCRIPTION)): ?>
                                        <span class="form-question-description"><?= $DESCRIPTION ?></span>
                                    <? endif; ?>
                                </div>
                            <? endforeach; ?>

                            <div class="form-astericks">* – поля обязательные для заполнения</div>

                            <div class="form-submit nomt">
                                <?= bitrix_sessid_post() ?>
                                <?= bitrix_sessid_post(VK_SESSID) ?>
                                <input
                                    type="submit"
                                    class="form-submit-button"
                                    name="web_form_apply"
                                    title="Войти"
                                    value="Войти"
                                    />
                            </div>

                            <a
                                class="auth-col-link auth-col-link-key"
                                href="<?= PATH_RECOVERY ?>"
                                title="Восстановление пароля"
                                ><i></i>Забыли пароль?</a>
                        </form>
                    </div>

                    <div class="auth-reg-note">
                        <?= \Axi::GT("kabinet/reg-note"); ?>
                        <a
                            class="auth-reg-link bold"
                            href="<?= PATH_REGISTER ?>"
                            title="Регистрация"
                            >Регистрация</a>
                    </div>

                </div>

                <?
                if (isPost("get_auth_tab"))
                {
                    die;
                }
                ?>
            </div>

            <div class="col-12 col-lg-24 auth-col">
                <h2 class="auth-col-title">Вход через профиль в соц. сетях</h2>

                <div class="auth-reg-description">
                    <?= \Axi::GT("kabinet/reg-socnet-description"); ?>
                </div>

                <? $APPLICATION->IncludeFile("/include/kabinet/auth_socnets.php"); ?>
            </div>
        </div>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>