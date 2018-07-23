<?php

namespace Axi\Handlers;

class Main
{

    /**
     * сжимает вывод html
     * @global type $USER
     * @param string $buffer вывод html
     * @return void
     */
    function OnEndBufferContent(&$buffer)
    {
        if ($_REQUEST['mode'] == 'import') return;
        global $USER;

        //отключим сжатие для админов и в случае отладки
        if (strstr($buffer, "dddump") || $USER->IsAuthorized() || !empty($_REQUEST['pdf']) || !empty($_REQUEST['DOWNLOAD']) || !empty($_REQUEST['is_ajax_post']))
        {
            return $buffer;
        }
        $search  = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
        $replace = array('>', '<', '\\1');
        $buffer  = preg_replace($search, $replace, $buffer);
        $buffer  = str_replace("> <", "><", $buffer);

        /* if (!empty($_POST['ajaxLoad']))
          {
          //$APPLICATION->RestartBuffer();
          define("PUBLIC_AJAX_MODE", true);
          die($buffer);
          } */
    }

    /**
     * Событие "OnBeforeUserRegister" вызывается до попытки регистрации нового пользователя методом 
     * CUser::Register и может быть использовано для прекращения процесса регистрации или переопределения некоторых полей.
     * Примечание: функция будет вызываться также при подтверждении регистрации (событие OnBeforeUserUpdate), где ключа LOGIN нет.
     * 
     * @param type $arArgs
     */
    function OnBeforeUserRegister(&$arFields)
    {
        //printra($arFields);
    }

    function OnBeforeUserSimpleRegister(&$arFields)
    {
        //printra($arFields);
    }

    function OnBeforeUserAdd(&$arFields)
    {
        $arFields['PERSONAL_MOBILE'] = fixPhoneNumber($arFields["PERSONAL_PHONE"]);
    }

    function OnAfterUserAdd(&$arFields)
    {
        
    }

    function OnBeforeUserUpdate(&$arFields)
    {
        if (!empty($_REQUEST['user_edit_active_tab'])) return;
        if ($_REQUEST['mode'] == 'import') return;

        global $APPLICATION;
        $arErrors = array();

        $USER_ID        = $arFields["ID"];
        $EMAIL          = $arFields["EMAIL"];
        $PERSONAL_PHONE = $arFields["PERSONAL_PHONE"];

        $arFields['PERSONAL_MOBILE'] = fixPhoneNumber($arFields["PERSONAL_PHONE"]);
        //die("test");
        
        //printra($arFields);

        if (CHECK_UNIQUE_ENABLE_ON_REG && $_POST['ACTION'] != "CHANGE")
        {
            if (!\CUserExt::isUniquePhone($PERSONAL_PHONE, $USER_ID))
            {
                $arErrors[] = "Номер $PERSONAL_PHONE уже зарегистрирован на сайте";
            }

            if (!\CUserExt::isUniqueEmail($EMAIL, $USER_ID))
            {
                $arErrors[] = "E-mail $EMAIL уже зарегистрирован на сайте";
            }
        }

        if (!empty($arErrors))
        {
            $obAdminException = new \CAdminException();
            foreach ($arErrors as $sError)
            {
                $obAdminException->AddMessage(array("text" => $sError));
            }

            $APPLICATION->ThrowException($obAdminException);
            return false;
        }
    }

    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;

        $USER_GROUPS = \CUser::GetUserGroup($USER->GetID());

        if (!in_array(13, $USER_GROUPS))
        {
            return;
        }

        $hidden_menus = array("global_menu_marketing", "global_menu_store", "global_menu_settings");
        $hidden_items = array("iblock_import", "iblock_admin", "iblock_redirect", "menu_fileman_site_s1_");


        foreach ($aGlobalMenu as $key => &$aGlobalMenuItem)
        {
            if (in_array($key, $hidden_menus))
            {
                unset($aGlobalMenu[$key]);
            }
        }

        foreach ($aModuleMenu as $key => &$aModuleMenuItem)
        {
            $parent_menu = $aModuleMenuItem["parent_menu"];
            $items       = $aModuleMenuItem["items"];

            if (in_array($parent_menu, $hidden_menus))
            {
                unset($aModuleMenu[$key]);
                continue;
            }

            foreach ($items as $key_item => $item)
            {
                if (in_array($item["items_id"], $hidden_items))
                {
                    unset($aModuleMenu[$key]["items"][$key_item]);
                    continue;
                }

//                if ($item["items_id"] == "menu_fileman_file_s1_")
//                {
//                    foreach ($item["items"] as $key_item_blya => $item_blya)
//                    {
//                        
//                    }
//                }
            }
        }

        //printra($aModuleMenu);
    }

//    function OnAdminTabControlBegin(&$form)
//    {
//        if ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/form_edit.php")
//        {
//            $form->tabs[] = array(
//                "DIV"     => "my_edit",
//                "TAB"     => "Настройка SMS",
//                "ICON"    => "main_user_edit",
//                "TITLE"   => "Настройка SMS",
//                "CONTENT" =>
//                '<tr class="adm-detail-required-field">
//		<td width="40%" class="adm-detail-content-cell-l">Телефоны через запятую:</td>
//		<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="PHONES" size="60" value=""></td>
//	</tr>'
//            );
//        }
//    }
//    function OnBeforeProlog()
//    {
//        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/form_edit.php' && $_REQUEST['ID'] > 0)
//        {
//            COption::SetOptionString('main', 'asd_ib_send_' . $_REQUEST['ID'], $_REQUEST['asd_ib_send_' . $_REQUEST['ID']] == 'Y' ? 'Y' : 'N');
//        }
//    }
}
