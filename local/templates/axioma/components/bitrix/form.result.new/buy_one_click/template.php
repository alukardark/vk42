<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$isPost  = $request->isPost();
?>
<div class="form-right-wrap">

    <? if (!empty($arResult["FORM_ERRORS_TEXT"])): ?>
        <div class="form-errors-text"><?= $arResult["FORM_ERRORS_TEXT"] ?></div>
    <? endif; ?>

    <? if ($arResult["isFormNote"] == "Y"): ?>
        <div class="form-note"><?= $arResult["FORM_NOTE"] ?></div>
    <? endif; ?>

    <? if ($arResult["isFormNote"] != "Y"): ?>

        <? if (!empty($arResult['arForm']['NAME'])): ?>
            <div class="form-title"><?= $arResult['arForm']['NAME'] ?></div>
        <? endif; ?>

        <? if (!empty($arResult['arForm']['DESCRIPTION'])): ?>
            <div class="form-description"><?= $arResult['arForm']['DESCRIPTION'] ?></div>
        <? endif; ?>

        <div class="form-error hidden"></div>
        <div class="form-result hidden"></div>

        <form
            class="form-right"
            name="<?= $arResult["arForm"]["SID"] ?>"
            method="POST"
            data-form-id="<?= $arResult['arForm']['ID'] ?>"
            onsubmit="Form.submit(event, this);"
            >
            <figure class="form-spinner"><i></i><i></i></figure>
            <?
            foreach ($arResult["QUESTIONS"] as $SID => $arQuestion):
                $STRUCTURE = $arQuestion['STRUCTURE'];

                $ID         = $arQuestion['STRUCTURE'][0]['ID'];
                $FIELD_TYPE = $arQuestion['STRUCTURE'][0]['FIELD_TYPE']; //text, hidden, etc.
                $REQUIRED   = $arQuestion['REQUIRED'];
                $CAPTION    = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
                $PARAMS     = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
                $NAME       = "form_{$FIELD_TYPE}_{$ID}";
                $VALUE      = $arResult['arrVALUES'][$NAME];

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

                    <? if ($FIELD_TYPE != "dropdown" && $FIELD_TYPE != "checkbox"): ?>
                        <span
                            class="form-question-placeholder <?= !empty($VALUE) ? "active" : "" ?> "
                            onclick="Form.onClickPlaceholder(this)"
                            ><?= $CAPTION ?></span>
                        <? endif; ?>

                    <? if ($FIELD_TYPE == "textarea"): ?>
                        <textarea
                            name="<?= $NAME ?>"
                            <?= $PARAMS ?>
                            maxlength="1000"
                            title="<?= $CAPTION ?>"
                            onfocus="Form.onInputFocus(this)"
                            onblur="Form.onInputBlur(this)"
                            ></textarea>

                    <? elseif ($FIELD_TYPE == "checkbox"): ?>
                        <?
                        $NAME     = "form_{$FIELD_TYPE}_{$SID}[]";
                        $VALUE_ID = $STRUCTURE[0]['ID'];
                        $VALUE    = $STRUCTURE[0]['VALUE'];
                        ?>
                        <div class="form-question-fakebox <?= $VALUE == 1 ? "selected" : "" ?>" onclick="Form.toggleFakeCheckbox(this, '<?= $VALUE_ID ?>')">
                            <i></i><span><?= $CAPTION ?></span>
                            <input type="hidden" name="<?= $NAME ?>" value="<?= $VALUE_ID ?>">
                        </div>
                    <? else: ?>
                        <input
                            class="form-question-input-field"
                            type="text"
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
                </div>
            <? endforeach; ?>

            <div class="form-submit">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="WEB_FORM_ID" value="<?= $arResult["arForm"]["ID"] ?>" />
                <input type="hidden" name="web_form_apply" value="Y" />
                <input
                    type="submit"
                    class="form-submit-button"
                    name="web_form_apply"
                    title="<?= $arResult['arForm']['BUTTON'] ?>"
                    value="<?= $arResult['arForm']['BUTTON'] ?>"
                    />
                <div class="form-submit-note"><? \Axi::GT("fz152", "fz152"); ?></div>
            </div>
        </form>
    <? endif; ?>
</div>