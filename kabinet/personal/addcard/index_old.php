<?
$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/personal.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/personal.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");


$USER_NFO = \CUserExt::getById();
$NUMBER   = getUF("USER", $USER_ID, "UF_CARD_NUMBER");

if (empty($NUMBER))
{
    $title    = "Прикрепить карту";
    $infofile = "kabinet/card-new-info";
}
else
{
    $title    = "Прикрепить новую карту";
    $infofile = "kabinet/card-replace-info";
}

$APPLICATION->SetTitle($title);
?>

<div class="row">
    <div class="offset-4 col-16 offset-xxl-3 col-xxl-18 offset-xl-2 col-xl-20 offset-lg-1 col-lg-22">
        <?
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
            "BYCARD" => array(
                "NAME"        => "EMAIL",
                "FIELD_TYPE"  => "hidden",
                "REQUIRED"    => "N",
                "CAPTION"     => "",
                "VALUE"       => $USER_NFO['$USER_NFO'],
                "DESCRIPTION" => "",
            ),
        );
        ?>

        <div class="row">
            <div class="col-12 col-lg-24" style="padding: 50px 0;">
                <h2 class="auth-col-title"><?= $title ?></h2>

                <div class="auth-col-info ve"><?= \Axi::GT($infofile) ?></div>

                <div class="auth-form">
                    <div class="form-error"></div>
                    <div class="form-result"></div>

                    <form
                        method="POST"
                        autocomplete="off"
                        onsubmit="Form.addcard(event, this);">
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
                                title="Прикрепить"
                                value="Прикрепить"
                                />
                        </div>

                    </form>
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