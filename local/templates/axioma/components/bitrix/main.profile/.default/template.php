<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;
$USER_ID = $USER->GetId();

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");

$QUESTIONS = $arResult["QUESTIONS"];

$USER_NFO  = \CUserExt::getById($USER_ID);
$firstName = $USER->GetFirstName();
$lastName  = $USER->GetLastName();
$fullName  = $USER->GetFullName();
?>

<div class="row auth reg" style="background-color: #fff;">
    <div class="offset-4 col-16">
        <div class="row auth-inner reg-inner">

            <form
                method="POST"
                autocomplete="off"
                onsubmit="Form.saveUser(event, this);">
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

                            if ($SID == "FIO")
                            {
                                $VALUE = $fullName;
                            }

                            if ($SID == "PHONE")
                            {
                                $VALUE = \CUserExt::getPhone();
                            }

                            if ($SID == "EMAIL")
                            {
                                $VALUE = $USER_NFO["EMAIL"];
                            }

                            /* if ($SID == "EMAIL")
                              {
                              $VALUE = $USER_NFO["EMAIL"];
                              } */

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

                        <div class="form-submit">
                            <?= bitrix_sessid_post() ?>
                            <?= bitrix_sessid_post(VK_SESSID) ?>
                            <input
                                type="submit"
                                class="form-submit-button"
                                name="web_form_apply"
                                title="Сохранить"
                                value="Сохранить"
                                />
                        </div>

                    </div>
                </div>

                <div class="col-12 col-lg-24 auth-col">

                    <div class="auth-subtitle">
                        Подписка на новости и акции от вк сервис:
                    </div>

                    <div class="auth-fakebox <?= $arResult["SUBSCRIBE_SMS"] ? "selected" : "" ?>" onclick="Form.toggleFakeCheckbox(this)">
                        <i></i><span>Согласие на SMS рассылку</span>
                        <input type="hidden" name="SUBSCRIBE_SMS" value="<?= $arResult["SUBSCRIBE_SMS"] ?>" />
                    </div>

                    <div class="auth-fakebox <?= $arResult["SUBSCRIBE_EMAIL"] ? "selected" : "" ?>" onclick="Form.toggleFakeCheckbox(this)">
                        <i></i><span>Согласие на E-mail рассылку</span>
                        <input type="hidden" name="SUBSCRIBE_EMAIL" value="<?= $arResult["SUBSCRIBE_EMAIL"] ?>" />
                    </div>

                    <?
                    $SUBSCRIBE = "";
                    if ($arResult["SUBSCRIBE_EMAIL"]) $SUBSCRIBE .= "EMAIL;";
                    if ($arResult["SUBSCRIBE_SMS"]) $SUBSCRIBE .= "SMS;";
                    ?>

                    <input type="hidden" name="SUBSCRIBE" value="<?= $SUBSCRIBE ?>" />
                </div>

            </form>
        </div>
    </div>
</div>

<div class="backlink">
    <a href="<?= PATH_PERSONAL ?>" title="Назад в кабинет">
        <i class="ion-ios-arrow-back"></i><span>Назад в кабинет</span>
    </a>
</div>