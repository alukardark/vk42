<?php

class CFormExt
{

    public static function showQuestions($QUESTIONS)
    {
        foreach ($QUESTIONS as $SID => $arQuestion)
        {
            $NAME         = $arQuestion['NAME'];
            $FIELD_TYPE   = $arQuestion['FIELD_TYPE']; //text, hidden, etc.
            $REQUIRED     = $arQuestion['REQUIRED'];
            $CAPTION      = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
            $CAPTION2     = $arQuestion['CAPTION'] . ":";
            $PARAMS       = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
            $VALUE        = $arQuestion['VALUE'];
            $DESCRIPTION  = $arQuestion['DESCRIPTION'];
            $AUTOCOMPLETE = empty($arQuestion['AUTOCOMPLETE']) ? "off" : $arQuestion['AUTOCOMPLETE'];
            $MAXLENGTH    = empty($arQuestion['maxlength']) ? 100 : $arQuestion['MAXLENGTH'];

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

                <? if (!in_array($FIELD_TYPE, array("datepicker", "passport"))): ?>
                    <span
                        class="form-question-placeholder <?= !empty($VALUE) ? "active" : "" ?> "
                        onclick="Form.onClickPlaceholder(this)"
                        ><?= $CAPTION ?>
                    </span>
                <? endif; ?>

                <? if ($FIELD_TYPE == "textarea"): ?>

                    <textarea
                        name="<?= $NAME ?>"
                        <?= $PARAMS ?>
                        maxlength="1000"
                        title="<?= $CAPTION ?>"
                        ></textarea>

                    <span class="form-question-error"></span>

                <? elseif ($FIELD_TYPE == "datepicker"): ?>

                    <div class="row">
                        <div class="float-left">
                            <span class="form-question-title"><?= $CAPTION2 ?></span>
                        </div>
                        <div class="float-left">
                            <input
                                class="form-question-input-field form-question-input-field-datepicker"
                                type="<?= $FIELD_TYPE ?>"
                                name="<?= $NAME ?>"
                                <?= $PARAMS ?>
                                maxlength="100"
                                title="<?= $CAPTION ?>"
                                value="<?= $VALUE ?>"
                                autocomplete="off"
                                />
                        </div>
                    </div>

                    <span class="form-question-error form-question-error-left"></span>

                <? elseif ($FIELD_TYPE == "passport"): ?>

                    <div class="row">
                        <div class="float-left">
                            <span class="form-question-title"><?= $CAPTION2 ?></span>
                        </div>
                        <div class="float-left">
                            <input
                                class="form-question-input-field form-question-input-field-passport"
                                type="<?= $FIELD_TYPE ?>"
                                name="<?= $NAME ?>"
                                <?= $PARAMS ?>
                                maxlength="100"
                                title="<?= $CAPTION ?>"
                                value="<?= $VALUE ?>"
                                autocomplete="off"
                                />
                        </div>
                    </div>

                    <span class="form-question-error form-question-error-left"></span>

                <? else: ?>

                    <input
                        class="form-question-input-field"
                        type="<?= $FIELD_TYPE ?>"
                        name="<?= $NAME ?>"
                        <?= $PARAMS ?>
                        maxlength="<?= $MAXLENGTH ?>"
                        title="<?= $CAPTION ?>"
                        onfocus="Form.onInputFocus(this)"
                        onblur="Form.onInputBlur(this)"
                        value="<?= $VALUE ?>"
                        autocomplete="<?= $AUTOCOMPLETE ?>"
                        />

                    <span class="form-question-error"></span>
                <? endif; ?>

                <? if (!empty($DESCRIPTION)): ?>
                    <span class="form-question-description"><?= $DESCRIPTION ?></span>
                <? endif; ?>
            </div>
            <?
        }
    }

    public static function showSubmit($title = "Зарегистрироваться", $wrapClass = "")
    {
        ?>
        <div class="form-submit <?= $wrapClass ?>">
            <?= bitrix_sessid_post() ?>
            <?= bitrix_sessid_post(VK_SESSID) ?>
            <input
                type="submit"
                class="form-submit-button"
                name="web_form_apply"
                title="<?= $title ?>"
                value="<?= $title ?>"
                />
        </div>
        <?
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get()
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init();
        }
        return self::$_instance;
    }

    private static function init()
    {
        
    }

}
