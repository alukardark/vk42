<?

function showFormQuestions($QUESTIONS)
{

    foreach ($QUESTIONS as $SID => $arQuestion)
    {
        $NAME             = $arQuestion['NAME'];
        $FIELD_TYPE       = $arQuestion['FIELD_TYPE']; //text, hidden, etc.
        $REQUIRED         = $arQuestion['REQUIRED'];
        $CAPTION          = $arQuestion['CAPTION'] . ($REQUIRED == "Y" ? "*" : "");
        $PARAMS           = ' data-type="' . $SID . '"  data-required="' . $REQUIRED . '"';
        $VALUE            = $arQuestion['VALUE'];
        $DESCRIPTION      = $arQuestion['DESCRIPTION'];
        $MASKPHONEOREMAIL = $arQuestion['MASKPHONEOREMAIL'] ? 'onkeyup="Form.maskPhoneOrEmail(event, this)"' : '';

        if ($FIELD_TYPE == 'hidden')
        {
            echo <<<EOF
                <input
                type="{$FIELD_TYPE}"
                name="{$NAME}"
                {$PARAMS}
                value="{$VALUE}"
                />
EOF;
        }
        else
        {

            $PH_ACTIVE = !empty($VALUE) ? "active" : "";

            echo <<<EOF
                <div class="form-question form-question-{$FIELD_TYPE}">
                <span
                class="form-question-placeholder {$PH_ACTIVE}"
                onclick="Form.onClickPlaceholder(this)"
                >{$CAPTION}</span>
EOF;


            if ($FIELD_TYPE == "textarea")
            {
                echo <<<EOF
                <textarea
                name="{$NAME}"
                {$PARAMS}
                maxlength="1000"
                title="{$CAPTION}"
                ></textarea>
EOF;
            }
            else
            {
                echo <<<EOF
                <input
                class="form-question-input-field"
                type="{$FIELD_TYPE}"
                name="{$NAME}"
                {$PARAMS}
                maxlength="100"
                title="{$CAPTION}"
                onfocus="Form.onInputFocus(this)"
                onblur="Form.onInputBlur(this)"
                {$MASKPHONEOREMAIL}
                value="{$VALUE}"
                />
EOF;
            }


            echo <<<EOF
                <span class="form-question-error"></span>
EOF;

            if (!empty($DESCRIPTION))
            {
                echo <<<EOF
                <span class="form-question-description">{$DESCRIPTION}</span>
EOF;
            }

            echo <<<EOF
                </div>
EOF;
        }
    }
}
