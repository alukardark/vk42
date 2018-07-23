<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if ($USER->IsAuthorized())
{
    LocalRedirect(PATH_PERSONAL);
    die;
}

$context = Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$assets = Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/oauth.js");

$APPLICATION->SetTitle("Восстановление пароля");

//$APPLICATION->IncludeComponent("bitrix:main.profile", "", Array(
//    "SET_TITLE"     => "N",
//    "USER_PROPERTY" => "N"
//        ), false
//);

$title = "Восстановление пароля";

$QUESTIONS = array(
    "LOGIN"     => array(
        "NAME"        => "LOGIN",
        "FIELD_TYPE"  => "hidden",
        "REQUIRED"    => "N",
        "CAPTION"     => "",
        "VALUE"       => $_REQUEST['USER_LOGIN'],
        "DESCRIPTION" => "",
    ),
    "WORD"      => array(
        "NAME"        => "WORD",
        "FIELD_TYPE"  => "hidden",
        "REQUIRED"    => "N",
        "CAPTION"     => "",
        "VALUE"       => $_REQUEST['USER_CHECKWORD'],
        "DESCRIPTION" => "",
    ),
    "PASSWORD"  => array(
        "NAME"        => "PASSWORD",
        "FIELD_TYPE"  => "password",
        "REQUIRED"    => "Y",
        "CAPTION"     => "Новый пароль",
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

<div class="row auth">
    <div class="offset-5 col-14 offset-xxl-4 col-xxl-16 offset-xl-2 col-xl-20 offset-lg-0 col-lg-24">
        <div class="row auth-inner">
            <div class="col-12 col-lg-24 auth-col">
                <h2 class="auth-col-title"><?= $title ?></h2>
                <a class="auth-col-link" href="<?= $altLink ?>" title="<?= $altTitle ?>"><?= $altTitle ?></a>

                <div class="auth-form recovery-form">
                    <div class="form-error"></div>
                    <div class="form-result"></div>

                    <form
                        method="POST"
                        autocomplete="off"
                        onsubmit="Form.change(event, this);">
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
                                        />
                                    <? endif; ?>

                                <span class="form-question-error"></span>

                                <? if (!empty($DESCRIPTION)): ?>
                                    <span class="form-question-description"><?= $DESCRIPTION ?></span>
                                <? endif; ?>
                            </div>
                        <? endforeach; ?>

                        <div class="form-submit">
                            <?= bitrix_sessid_post() ?>
                            <?= bitrix_sessid_post(VK_SESSID) ?>

                            <input
                                type="submit"
                                class="form-submit-button"
                                name="web_form_apply"
                                title="Восстановить"
                                value="Восстановить"
                                />
                        </div>

                        <a
                            class="auth-col-link auth-col-link-key"
                            href="<?= PATH_AUTH ?>"
                            title="Авторизация"
                            ><i></i>Авторизация</a>
                    </form>
                </div>
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