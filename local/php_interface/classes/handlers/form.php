<?php

namespace Axi\Handlers;

class Form
{

    /**
     * Обработчики события вызываются после добавления нового результата веб-формы. 
     * Может быть использовано для совершения каких-либо дополнительных операций с результатом веб-формы, 
     * например, для рассылки дополнительных уведомлений посредством электронной почты. 
     * Для изменения полей результата веб-формы стоит использовать CFormResult::SetField(). 
     * Возврат обработчиком каких-либо значений не предполагается.
     */
    function onAfterResultAdd($WEB_FORM_ID, $RESULT_ID)
    {
        if (isset($_REQUEST['lang']))
        {
            //это запрос из админки
            return;
        }

        if (!isPost())
        {
            return;
        }

        global $USER;

        $arParams = array();
        $arResult = array();

        //получаем данные формы
        $arForm        = array();
        $arQuestions   = array();
        $arAnswers     = array();
        $arDropdown    = array();
        $arMultiselect = array();
        \CForm::GetDataByID($WEB_FORM_ID, $arForm, $arQuestions, $arAnswers, $arDropdown, $arMultiselect);

        $WEB_FORM_CODE = $arForm['SID'];
        $MAIL_EVENT    = $arForm['MAIL_EVENT_TYPE'];

        //получаем ответы
        $arFormResult  = array();
        $arFormAnswers = array();
        \CFormResult::GetDataByID($RESULT_ID, array(), $arFormResult, $arFormAnswers);

        $arFields                   = array();
        $arFields['RS_RESULT_ID']   = $RESULT_ID;
        $arFields['RS_FORM_ID']     = $WEB_FORM_ID;
        $arFields['RS_FORM_NAME']   = $arForm['NAME'];
        $arFields['RS_DATE_CREATE'] = $arFormResult['DATE_CREATE'];

        $USER_ID = $USER->GetId();

        if (!empty($USER_ID))
        {
            $arFields['RS_USER_ID']   = $USER_ID;
            $arFields['RS_USER_NAME'] = \CUserExt::getName();
        }

        $arSendFields = array();
        foreach ($arFormAnswers as $arFormAnswer)
        {
            foreach ($arFormAnswer as $arAnswer)
            {
                if ($arAnswer["FIELD_TYPE"] == "checkbox")
                {
                    $USER_TEXT = $arAnswer["ANSWER_VALUE"] ? "Да" : "Нет";
                }
                else
                {
                    $USER_TEXT = $arAnswer['USER_TEXT'];
                }
                $arFields[$arAnswer['SID']]     = $arSendFields[$arAnswer['SID']] = $USER_TEXT;
            }
        }

        //получаем настройки ФОС
        $arSelect = Array("ID", "NAME", "IBLOCK_ID", 'CODE', 'ACTIVE', 'PROPERTY_*');
        $arFilter = Array(
            "IBLOCK_ID" => SETTINGS_FORMS,
            "CODE"      => $WEB_FORM_CODE,
            "ACTIVE"    => "Y");

        $obList    = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($obElement = $obList->GetNextElement())
        {
            $arProps = $obElement->GetProperties();

            $arParams["YAGOAL"]     = $arProps["YAGOAL"]['VALUE'];
            $arParams["PHONES"]     = $arProps["PHONES"]['VALUE'];
            $arParams["SMS_ENABLE"] = $arProps["SMS_ENABLE"]['VALUE_XML_ID'];
            $arParams["RESPONSE"]   = $arProps["RESPONSE"]['VALUE'];
            break;
        }

        if ($arParams["RESPONSE"])
        {
            $arResult["RESPONSE"] = 'RESULT_OK' . htmlspecialchars_decode($arParams["RESPONSE"]["TEXT"]);
        }

        if ($WEB_FORM_ID == FORM_BUY_ONE_CLICK)
        {
            \CURL::getReplay('OneClick', $arSendFields, true, true);
        }

        if ($WEB_FORM_ID == FORM_HELP_AKB)
        {
            \CEvent::SendImmediate("FORM_FILLING_HELPAKB", SITE_ID, $arFields);
            //\CURL::getReplay('OneClick', $arSendFields, true, true);
        }

        if ($WEB_FORM_ID == FORM_SUPPORT)
        {
            \CEvent::SendImmediate($MAIL_EVENT, SITE_ID, $arFields);
        }

        if ($WEB_FORM_ID == FORM_FAQ)
        {
            \CEvent::SendImmediate($MAIL_EVENT, SITE_ID, $arFields);
            ClearCache("/ccache_common/faq.arQuestions/");

            global $APPLICATION;
            $APPLICATION->RestartBuffer();
        }

        die($arResult["RESPONSE"]);
    }

    function onBeforeResultDelete($WEB_FORM_ID, $RESULT_ID, $CHECK_RIGHTS)
    {
        if ($WEB_FORM_ID == FORM_FAQ)
        {
            ClearCache("/ccache_common/faq.arQuestions/");
        }
    }

}
