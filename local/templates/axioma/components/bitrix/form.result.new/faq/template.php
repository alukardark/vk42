<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$isPost  = $request->isPost();
?>
<div class="form-faq-wrap" id="form-faq">

    <? if (!empty($arResult["FORM_ERRORS_TEXT"])): ?>
        <div class="form-errors-text"><?= $arResult["FORM_ERRORS_TEXT"] ?></div>
    <? endif; ?>

    <? if ($arResult["isFormNote"] == "Y"): ?>
        <div class="form-note">Крутяк!<?= $arResult["FORM_NOTE"] ?></div>
    <? endif; ?>

    <? if ($arResult["isFormNote"] != "Y"): ?>

        <? if (!empty($arResult['arForm']['DESCRIPTION'])): ?>
            <div class="form-description"><?= $arResult['arForm']['DESCRIPTION'] ?></div>
        <? endif; ?>

        <div class="form-error hidden"></div>
        <div class="form-result hidden"></div>

        <form
            class="form-faq"
            name="<?= $arResult["arForm"]["SID"] ?>"
            method="POST"
            data-form-id="<?= $arResult['arForm']['ID'] ?>"
            onsubmit="Form.submit(event, this);"
            >
            <figure class="form-spinner"><i></i><i></i></figure>
            <?
            //printra($arResult["QUESTIONS"]);
            foreach ($arResult["QUESTIONS"] as $SID => $arQuestion):
                $STRUCTURE = $arQuestion['STRUCTURE'];

                $ID         = $STRUCTURE[0]['ID'];
                //$ID         = $STRUCTURE[0]['FIELD_ID'];
                $FIELD_TYPE = $STRUCTURE[0]['FIELD_TYPE']; //text, hidden, etc.
                $REQUIRED   = $arQuestion['REQUIRED'];
                $CAPTION    = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
                $PARAMS     = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
                $NAME       = "form_{$FIELD_TYPE}_{$ID}";
                $VALUE      = $arResult['arrVALUES'][$NAME];

                if ($SID == "ANSWER") continue;

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
                            ><?= $CAPTION ?>
                        </span>
                    <? endif; ?>

                    <? if ($FIELD_TYPE == "textarea"): ?>
                        <textarea
                            name="<?= $NAME ?>"
                            <?= $PARAMS ?>
                            maxlength="1000"
                            title="<?= $CAPTION ?>"
                            onfocus="Form.onInputFocus(this)"
                            onblur="Form.onInputBlur(this)"
                            ><?= $VALUE ?></textarea>

                    <? elseif ($FIELD_TYPE == "checkbox"): ?>
                        <?
                        $NAME     = "form_{$FIELD_TYPE}_{$SID}[]";
                        $VALUE_ID = $STRUCTURE[0]['ID'];
                        $VALUE    = $STRUCTURE[0]['VALUE'];
                        ?>
                        <div class="form-question-fakebox <?= $VALUE == "1" ? "selected" : "" ?>" onclick="Form.toggleFakeCheckbox(this, <?= $VALUE_ID ?>)">
                            <i></i><span><?= $CAPTION ?></span>
                            <input type="hidden" name="<?= $NAME ?>" value="<?= $VALUE_ID ?>">
                        </div>
                    <? elseif ($FIELD_TYPE == "dropdown"): ?>
                        <?
                        $NAME       = "form_{$FIELD_TYPE}_{$SID}";
                        $VALUE_ID   = $STRUCTURE[0]['ID'];
                        $VALUE_MESS = $STRUCTURE[0]['MESSAGE'];

                        $DROPDOWN_TITLE = $arParams["CURRENT_CATEGORY_NAME"] == $arParams["DEFAULT_CATEGORY_NAME"] ?
                                "Выберите категорию" :
                                $arParams["CURRENT_CATEGORY_NAME"];
                        ?>
                        <div class="form-question-fakeselect" data-target="<?= $NAME ?>">
                            <div
                                class="form-question-fakeselect-current"
                                onclick="Form.toggleDropdown(this)"
                                >
                                <span><?= $DROPDOWN_TITLE ?></span>
                                <i class="ion-arrow-down-b"></i>
                            </div>

                            <ul  class="form-question-fakeselect-variants">
                                <? foreach ($STRUCTURE as $arDropdown): ?>
                                    <li
                                        class="form-question-fakeselect-variants-item <?= $arDropdown["ID"] == $VALUE_ID ? "selected" : "" ?> "
                                        data-dropdown-id="<?= $arDropdown["ID"] ?>"
                                        onclick="Form.setDropdown(this)"
                                        ><span><?= $arDropdown["MESSAGE"] ?></span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>

                        <input
                            type="hidden"
                            name="<?= $NAME ?>"
                            data-input-dropdown="Y"
                            value="<?= $arParams["CURRENT_CATEGORY_ID"] ?>"
                            />

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