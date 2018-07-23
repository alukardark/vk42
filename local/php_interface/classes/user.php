<?php

class CUserExt
{

    /**
     * Получаем профили текущего юзера
     * @global type $USER
     * @return type
     */
    public static function getProfile($USER_ID = false)
    {
        if (empty($USER_ID))
        {
            global $USER;
            $USER_ID = $USER->GetID();
        }

        $USER_PROFILES = array();

        $obListProps = \CSaleOrderUserProps::GetList(array("DATE_UPDATE" => "DESC"), array("USER_ID" => $USER_ID));
        while ($arFetch     = $obListProps->Fetch())
        {
            $USER_PROFILES[] = $arFetch;
            break;
        }

        foreach ($USER_PROFILES as &$USER_PROFILE)
        {
            $obListPropsValue = \CSaleOrderUserPropsValue::GetList(array(), Array("USER_PROPS_ID" => $USER_PROFILE["ID"]));
            while ($arPropVals       = $obListPropsValue->Fetch())
            {
                $USER_PROFILE["PROPS_VALUE"][$arPropVals["PROP_CODE"]] = $arPropVals;
            }
        }

        return $USER_PROFILES[0];
    }

    public static function getName($USER_ID = null, $short = false)
    {
        if ($USER_ID === null)
        {
            global $USER;
            $USER_ID = $USER->GetID();
        }

        if (empty($USER_ID))
        {
            return false;
        }

        $name = $USER->GetFullName();

        if (empty($name))
        {
            $name = $USER->GetLogin();
        }

        if ($short)
        {
            $arName = explode(" ", $name);
            $name   = $arName[0];
        }

        return $name;
    }

    /**
     * Возвращает телефон пользователя. Если телефон не установлен, то возвращает 
     * телефон из последнего профиля покупателя, иначе false
     * @return type
     */
    public static function getPhone($USER_ID = null)
    {
        if ($USER_ID === null)
        {
            global $USER;
            $USER_ID = $USER->GetID();
        }

        if (empty($USER_ID))
        {
            return false;
        }

        $obUser = \CUser::GetByID($USER_ID);

        if (empty($obUser))
        {
            return false;
        }

        $arUser = $obUser->Fetch();

        if (!empty($arUser['PERSONAL_PHONE']))
        {
            return fixPhoneNumber($arUser['PERSONAL_PHONE']);
        }

        $USER_PROFILE = self::getProfile();

        if (empty($USER_PROFILE) || empty($USER_PROFILE["PROPS_VALUE"]["PHONE"]["VALUE"]))
        {
            return false;
        }

        return fixPhoneNumber($USER_PROFILE["PROPS_VALUE"]["PHONE"]["VALUE"]);
    }

    public static function getUserProps($PERSON_TYPE_ID = FIZ_LICO)
    {
        $arFullProps = array();

        $arIgnoreProps  = array();
        $arIgnoreGroups = array(2, 5, 6, 7, 8, 9, 10, 11, 12, 13);

        $arFilter = array(
            "ACTIVE"         => "Y",
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID,
            "!CODE"          => $arIgnoreProps,
            "!GROUP_ID"      => $arIgnoreGroups,
        );

        $obLists = \CSaleOrderProps::GetList(array("SORT" => "ASC"), $arFilter, false, false, array("*"));
        while ($arFetch = $obLists->Fetch())
        {
            $arFullProps[$arFetch['CODE']] = array(
                "ID"          => $arFetch['ID'],
                "NAME"        => $arFetch['CODE'],
                "FIELD_TYPE"  => $arFetch['CODE'] == "SUBSCRIBE" ? "hidden" : "text",
                "REQUIRED"    => $arFetch['CODE'] == "SUBSCRIBE" ? "N" : "Y",
                "CAPTION"     => $arFetch['NAME'],
                "VALUE"       => $arFetch['CODE'] == "SUBSCRIBE" ? "EMAIL;" : "",
                "DESCRIPTION" => "",
            );

            $db_vars = \CSaleOrderPropsVariant::GetList(($by      = "SORT"), ($order   = "ASC"), Array("ORDER_PROPS_ID" => $arFetch["ID"]));
            while ($vars    = $db_vars->Fetch())
            {
                $arFullProps[$arFetch['CODE']]['OPTIONS'][] = $vars;
            }
        }

        return $arFullProps;
    }

    /**
     * @see https://dev.1c-bitrix.ru/user_help/store/sale/components_2/order/sale_order_ajax.php
     * Если пользователь не зарегистрирован на сайте, то при отмеченной опции он будет автоматически
     * зарегистрирован для оформления заказа.
     * Если флаг с данной опции снят, то при оформлении заказа будет отображена форма регистрации и незарегистрированный пользователь
     * должен будет зарегистрироваться самостоятельно.
     * Поле работает при условии, что в ядре включена самостоятельная регистрация 
     * и отключено подтверждение регистрации по E-mail (поле ниже).
     * @global type $USER
     * @param type $VALUES
     * @return type
     */
    public static function register($VALUES)
    {
        global $USER;

        $context     = \Bitrix\Main\Application::getInstance()->getContext();
        $request     = $context->getRequest();
        $ACTION      = (string) $request->get("ACTION"); //регистрация через веб-сервис
        $USER_XML_ID = (string) $request->get("USER_XML_ID");

        if ($USER->IsAuthorized() && empty($ACTION))
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $PERSON_TYPE_ID = (int) $VALUES['PERSON_TYPE_ID'];

        if ($PERSON_TYPE_ID != FIZ_LICO && $PERSON_TYPE_ID != UR_LICO)
        {
            return array('success' => false, 'message' => '<p>Неверный тип плательщика</p>', 'ecodes' => 'bad_person_type');
        }

        $PROPS = self::getUserProps($PERSON_TYPE_ID);

        $SUBSCRIBE_PROP_ID  = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "SUBSCRIBE");
        $PROPS["SUBSCRIBE"] = array(
            "ID"   => $SUBSCRIBE_PROP_ID,
            "NAME" => "SUBSCRIBE",
        );

        $QUESTIONS = $PROPS + array(
            "PASSWORD"  => array(
                "NAME"        => "PASSWORD",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "Y",
                "CAPTION"     => "Пароль",
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

        $errors = false;
        $ecodes = false;
        //быстрая проверка на заполненность обязательных полей
        if (empty($ACTION))
        {
            foreach ($QUESTIONS as $code => $question)
            {
                $value = trim($VALUES[$code]);
                if ($question['REQUIRED'] == "Y" && empty($value))
                {
                    $errors .= '<p>Поле ' . $question['CAPTION'] . ' должно быть заполнено</p>';
                    $ecodes .= 'empty_' . $question['CAPTION'] . ';';
                }
            }
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors, 'ecodes' => $ecodes);
        }

        //поля, обязательные для любого типа плательщика
        $sUserName  = trim($VALUES['FIO']);
        $sPhone     = trim($VALUES['PHONE']);
        $sUserEmail = trim($VALUES['EMAIL']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if (!empty($ACTION))
        {
            $sPassword  = $sPassword2 = randString(6, array("0123456789"));
            $sPhone     = NormalizePhone($sPhone, 6);
        }

        if ($sPassword != $sPassword2)
        {
            $errors .= '<p>пароли не совпадают</p>';
            $ecodes .= 'not_equal_passwords;';
        }

        if (!self::isUniquePhone($sPhone))
        {
            $errors .= '<p>Телефон ' . $sPhone . ' уже зарегистрирован</p>';
            $ecodes .= 'phone_already_exist;';
        }

        if (!self::isUniqueEmail($sUserEmail))
        {
            $errors .= '<p>E-mail ' . $sUserEmail . ' уже зарегистрирован</p>';
            $ecodes .= 'email_already_exist;';
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors, 'ecodes' => $ecodes);
        }

        //добавляем нового юзера
        $regResult = $USER->Register($sUserEmail, $sUserName, "", $sPassword, $sPassword2, $sUserEmail);
        $USER_ID   = $USER->GetID();


        if (empty($USER_ID))
        {
            return array('success' => false, 'message' => '<p>' . $regResult['MESSAGE'] . '</p>', 'ecodes' => 'cant_create_user;');
        }

        $arUserFields  = array();
        $BYCARD_XML_ID = false;

        //привяжем бонусную карту, если указана. Актутализируем потом инфу о юзере в 1С
        if (!empty($VALUES['HASH']) && $VALUES['FROM'] == "BYCARD")
        {
            $BYCARD_XML_ID = getHash('decrypt', $VALUES['HASH']);
            self::setBonusCardFrom1C($USER_ID, $BYCARD_XML_ID);
        }

        //устанавливаем некоторые свойства юзера
        $arUserFields["NAME"]           = $sUserName;
        $arUserFields["EMAIL"]          = $sUserEmail;
        $arUserFields["PERSONAL_PHONE"] = $sPhone;

        if (!empty($VALUES['UF']))
        {
            foreach ($VALUES['UF'] as $UF_KEY => $UF_VALUE)
            {
                $arUserFields[$UF_KEY] = $UF_VALUE;
            }
        }

        if (!empty($USER_XML_ID))
        {
            $arUserFields["XML_ID"] = $USER_XML_ID;
        }

        $USER->Update($USER_ID, $arUserFields);

        //создаем профиль покупателя
        $arFields      = array(
            "NAME"           => $sUserName,
            "USER_ID"        => $USER_ID,
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID
        );
        $USER_PROPS_ID = \CSaleOrderUserProps::Add($arFields);

        //свойства заказа для созданног опрофиля покупателя
        $arProfileFields = array();
        foreach ($PROPS as $code => $prop)
        {
            $arProfileFields[$prop['ID']] = $VALUES[$code];
        }
        \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROPS_ID, $sUserName, $PERSON_TYPE_ID, $arProfileFields, $arResult);

        //создадим или обновляем юзера в 1С

        $createOrUpdateUserIn1CResult = self::createOrUpdateUserIn1C($USER_ID, $BYCARD_XML_ID);

        return array('success' => true, 'result' => $createOrUpdateUserIn1CResult["RESULT"], 'USER_ID' => $USER_ID);
    }

    public static function setBonusCardFrom1C($USER_ID, $XML_ID)
    {
        //получаем инфу о юзере из 1С
        $arData = array(
            "XML_ID" => $XML_ID,
        );

        $ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);

        $arUserFields["UF_CARD_ACTIVATED"] = !empty($ar1CUser["ACTIVATED"]) ? "Y" : "N";
        $arUserFields["UF_CARD_TYPE"]      = $ar1CUser["TYPE_OF_CARD"];
        $arUserFields["UF_CARD_NUMBER"]    = cutAllButNumbers($ar1CUser["CARD"]);
        $arUserFields["UF_CARD_BALANCE"]   = (int) $ar1CUser["BONUSES"];

        $obUser = new \CUser;
        $obUser->Update($USER_ID, $arUserFields);
    }

    public static function auth($VALUES)
    {
        global $APPLICATION, $USER;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $LOGIN    = trim($VALUES['LOGIN']);
        $PASSWORD = $VALUES['PASSWORD'];

        if (empty($LOGIN) || empty($PASSWORD))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        $arUsers = array();
        $arUsers += self::getByPhone(getPhoneFromString($LOGIN));
        $arUsers += self::getByEmail(array($LOGIN));
        $arUsers += self::getByLogin($LOGIN, 'array');
        //printra($arUsers);

        if (empty($arUsers))
        {
            return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
        }

        $arAuthResult = false;
        foreach ($arUsers as $arUser)
        {
            $arAuthResult = $USER->Login($arUser['LOGIN'], $PASSWORD, "Y", "Y");

            if ($arAuthResult === true)
            {
                break;
            }
        }

        $APPLICATION->arAuthResult = $arAuthResult;

        if ($arAuthResult === true) return array('success' => true);
        else return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
    }

    public static function authByCard($VALUES)
    {
        global $USER, $APPLICATION;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы уже авторизованы</p>');
        }

        $CARD  = cutAllButNumbers($VALUES["CARD"]);
        $FIO   = trim(htmlspecialcharsbx($VALUES["FIO"]));
        $PHONE = trim(htmlspecialcharsbx($VALUES["PHONE"]));

        if (empty($CARD) || empty($FIO) || empty($PHONE))
        {
            return array('success' => false, 'message' => '<p>Необходимо ввести корректные данные.</p>');
        }

        //ищем юзера в 1С
        $arData = array(
            "CARD"  => $CARD,
            "FIO"   => $FIO,
            "PHONE" => $PHONE,
        );

        $APPLICATION->set_cookie("REG_BY_CARD_DATA", serialize($arData), time() + 60 * 60 * 24 * 365);

        $arReplay = \CURL::getReplay("getUserByCard", $arData, true, false, true);
        //printra($arReplay);


        if (empty($arReplay))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден</p>');
        }

        //напутал Векшин местами ID и XML_ID
        $userXML_ID = $arReplay["ID"]; //это XML_ID юзера. Он должен быть всегда заполнен, если юзер в 1С найден.
        $userID     = $arReplay["XML_ID"]; //это внутренний ID юзера на сайте. Он заполнен, если такой юзер есть на сайте. НА ДЕЛЕ ЭТО ПОЛЕ ВСЕГДА ПРИХОДИТ ПУСТЫМ :( поэтому будет проверять UF_CARD_NUMBER

        if (empty($userXML_ID))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден.</p>');
        }
        else
        {
            //1С нам заявляет, что такой юзер на сайте уже есть. Проверяем.
            //ищем юзера на сайте
            $userIDByCard  = self::getByField("UF_CARD_NUMBER", $CARD);
            $userIDByXmlId = self::getByXMLId($userXML_ID);

            //printra($userIDByXmlId);

            if (!empty($userIDByXmlId) && $userIDByCard == $userIDByXmlId)
            {
                //все ок, авторизируем юзера
                self::authByUserId($userIDByCard);
                $redirect = PATH_PERSONAL . "?FROM=BYCARD";
                return array('success' => true, 'redirect' => $redirect);
            }
            else
            {
                //что то пошло не так
                //return array('success' => false, 'message' => '<p>Не удалось произвести авторизацию.</p>');
                //в 1С такой юзер есть, но на сайте его нету. Производим регистрацию на сайте
                //$arData["XML_ID"] = $userXML_ID;
                $encrypted_hash = getHash('encrypt', $userXML_ID);

                $redirect = PATH_REGISTER . "?FROM=BYCARD&HASH=" . urlencode($encrypted_hash);
                return array('success' => true, 'redirect' => $redirect);
            }
        }
        /* elseif (empty($userID))
          {
          //в 1С такой юзер есть, но на сайте его нету. Производим регистрацию на сайте
          //$arData["XML_ID"] = $userXML_ID;
          $encrypted_hash = getHash('encrypt', $userXML_ID);

          $redirect = PATH_REGISTER . "?FROM=BYCARD&HASH=" . urlencode($encrypted_hash);
          return array('success' => true, 'redirect' => $redirect);
          } */


        return array('success' => false, 'message' => '<p>Ошибка</p>');
    }

    public static function addCard($VALUES)
    {
        global $USER;

        if (!$USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Ошибка авторизации</p>');
        }

        $USER_ID = $USER->GetId();

        $NUMBER = getUF("USER", $USER_ID, "UF_CARD_NUMBER");

        if (!empty($NUMBER))
        {
            //return array('success' => false, 'message' => '<p>К вашему аккаунту уже привязана карта</p>');
        }

        $USER_PROFILE = \CUserExt::getProfile();
        $USER_NFO     = \CUserExt::getById();

        $PERSON_TYPE_ID = $USER_PROFILE["PERSON_TYPE_ID"];
        //$PROPS          = \CUserExt::getUserProps($PERSON_TYPE_ID);

        $CARD  = cutAllButNumbers($VALUES["CARD"]);
        $FIO   = trim(htmlspecialcharsbx($VALUES["FIO"]));
        $PHONE = trim(htmlspecialcharsbx($VALUES["PHONE"]));

        if (empty($CARD) || empty($FIO) || empty($PHONE))
        {
            return array('success' => false, 'message' => '<p>Необходимо ввести корректные данные.</p>');
        }

        //ищем юзера в 1С
        $arData = array(
            "CARD"   => $CARD,
            "FIO"    => $FIO,
            "PHONE"  => $PHONE,
            "EMAIL"  => $USER_NFO["EMAIL"],
            "XML_ID" => $USER_NFO["XML_ID"],
        );

        $arReplay = \CURL::getReplay("getUserByCard", $arData, true, false, true);
        //pprintra($arReplay);

        if (empty($arReplay))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден</p>');
        }

        //напутал Векшин местами ID и XML_ID
        $ChangeOwner = $arReplay["ChangeOwner"];
        $userXML_ID  = $arReplay["ID"]; //это XML_ID юзера. Он должен быть всегда заполнен, если юзер в 1С найден.
        $userID      = $arReplay["XML_ID"]; //это внутренний ID юзера на сайте. Он заполнен, если такой юзер есть на сайте. НА ДЕЛЕ ЭТО ПОЛЕ ВСЕГДА ПРИХОДИТ ПУСТЫМ :( поэтому будет проверять UF_CARD_NUMBER

        if (empty($userXML_ID))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден.</p>');
        }
        elseif ($ChangeOwner === "")
        {
            return array('success' => false, 'message' => '<p>Карта успешно привязана.</p>');
        }
        elseif ($ChangeOwner === false)
        {
            return array('success' => false, 'message' => '<p>Не удалось привязать карту.</p>');
        }
        else
        {
            //1С нам заявляет, что такой юзер на сайте уже есть. Проверяем.
            //if ($USER_NFO['XML_ID'] == $userXML_ID)
            {
                $arData = array(
                    "XML_ID" => $userXML_ID,
                );

                $ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true);

                $arUserFields                      = array();
                $arUserFields["UF_CARD_ACTIVATED"] = !empty($ar1CUser["ACTIVATED"]) ? "Y" : "N";
                $arUserFields["UF_CARD_TYPE"]      = $ar1CUser["TYPE_OF_CARD"];
                $arUserFields["UF_CARD_NUMBER"]    = cutAllButNumbers($ar1CUser["CARD"]);
                $arUserFields["UF_CARD_BALANCE"]   = (int) $ar1CUser["BONUSES"];

                $obUser = new \CUser;
                $obUser->Update($USER_ID, $arUserFields);

                return array('success' => false, 'message' => '<p>Карта успешно привязана.</p>');
            }
            //else
            {
                //return array('success' => false, 'message' => '<p>Эта карта привязана к другому пользователю.</p>');
            }
        }

        return array('success' => false, 'message' => '<p>Ошибка</p>');
    }

    public static function authByUserId($USER_ID)
    {
        global $USER;
        $USER->Authorize($USER_ID);
    }

    public static function recovery($VALUES)
    {
        global $USER;

        if ($USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Вы уже авторизированы</p>');
        }

        $LOGIN = trim($VALUES['LOGIN']);

        if (empty($LOGIN))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        $arUsers = array();
        $arUsers += self::getByPhone(getPhoneFromString($LOGIN));
        $arUsers += self::getByEmail(array($LOGIN));
        $arUsers += self::getByLogin($LOGIN, 'array');

        if (empty($arUsers))
        {
            return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
        }


        foreach ($arUsers as $arUser)
        {
            $arResult = $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL']);
        }

        if ($arResult["TYPE"] == "OK")
                return array('success' => true, 'message' => '<p>Ссылка для восстановления пароля отправлена на указанный почтовый ящик</p>');
        else return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
    }

    public static function change($VALUES)
    {
        global $USER;

        if ($USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Вы уже авторизированы</p>');
        }

        $LOGIN      = trim($VALUES['LOGIN']);
        $WORD       = trim($VALUES['WORD']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if (empty($LOGIN) || empty($WORD))
        {
            return array('success' => false, 'message' => '<p>Ошибка безопасности. Попробуйте еще раз.</p>');
        }

        if (empty($sPassword) || empty($sPassword2))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        if ($sPassword != $sPassword2)
        {
            return array('success' => false, 'message' => '<p>Пароли не совпадают</p>');
        }

        $arResult = $USER->ChangePassword($LOGIN, $WORD, $sPassword, $sPassword2);

        if ($arResult["TYPE"] == "OK")
        {
            $rsUser = $USER->GetByLogin($LOGIN);
            $arUser = $rsUser->Fetch();
            $USER->Authorize($arUser['ID']);

            return array('success' => true, 'message' => '<p>Пароль успешно изменен</p>');
        }
        else
        {
            return array('success' => false, 'message' => '<p>' . $arResult['MESSAGE'] . '</p>');
        }
    }

    public static function saveUser($VALUES)
    {
        global $USER;

        if (!$USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Ошибка авторизации</p>');
        }

        $USER_ID      = $USER->GetId();
        $USER_PROFILE = \CUserExt::getProfile();

        $PERSON_TYPE_ID = $USER_PROFILE["PERSON_TYPE_ID"];
        $PROPS          = \CUserExt::getUserProps($PERSON_TYPE_ID);

        $SUBSCRIBE_PROP_ID  = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "SUBSCRIBE");
        $PROPS["SUBSCRIBE"] = array(
            "ID"   => $SUBSCRIBE_PROP_ID,
            "NAME" => "SUBSCRIBE",
        );

        $QUESTIONS = $PROPS + array(
            "PASSWORD"  => array(
                "NAME"        => "PASSWORD",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
            "PASSWORD2" => array(
                "NAME"        => "PASSWORD2",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Повторите пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
        );

        $errors = false;
        //быстрая проверка на заполненность обязательных полей
        foreach ($QUESTIONS as $code => $question)
        {
            $value = trim($VALUES[$code]);
            if ($question['REQUIRED'] == "Y" && empty($value))
            {
                $errors .= '<p>Поле ' . $question['CAPTION'] . ' должно быть заполнено</p>';
            }
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }

        //поля, обязательные для любого типа плательщика
        //$sUserFisrtName = trim($VALUES['FIO']);
        $FIO           = explode(" ", trim($VALUES['FIO']));
        $sUserLastName = "";
        $sUserFullName = "";

        foreach ($FIO as $k => $FIO_PART)
        {
            $sUserFullName .= $FIO_PART . " ";

            if ($k == 0)
            {
                $sUserFisrtName = $FIO_PART;
                continue;
            }

            $sUserLastName .= $FIO_PART . " ";
        }

        $sPhone     = trim($VALUES['PHONE']);
        $sUserEmail = trim($VALUES['EMAIL']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if (!empty($sPassword) && $sPassword != $sPassword2)
        {
            $errors .= '<p>пароли не совпадают</p>';
        }

        if (!self::isUniquePhone($sPhone, $USER_ID))
        {
            $errors .= '<p>Телефон ' . $sPhone . ' уже зарегистрирован</p>';
        }

        if (!self::isUniqueEmail($sUserEmail, $USER_ID))
        {
            $errors .= '<p>E-mail ' . $sUserEmail . ' уже зарегистрирован</p>';
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }

        $arUserFields = array(
            "LOGIN"          => $sUserEmail,
            "NAME"           => $sUserFisrtName,
            "LAST_NAME"      => rtrim($sUserLastName, " "),
            "EMAIL"          => $sUserEmail,
            "PERSONAL_PHONE" => $sPhone,
            "ACTIVE"         => "Y",
        );

        if (!empty($sPassword))
        {
            $arUserFields["PASSWORD"]         = $sPassword;
            $arUserFields["CONFIRM_PASSWORD"] = $sPassword2;
        }

        $USER->Update($USER_ID, $arUserFields);

        //свойства заказа для созданног опрофиля покупателя
        $arProfileFields = array();
        foreach ($PROPS as $code => $prop)
        {
            $arProfileFields[$prop['ID']] = $VALUES[$code];
        }

        \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROFILE["ID"], rtrim($sUserFullName, " "), $PERSON_TYPE_ID, $arProfileFields, $arResult);

        //обновим юзера в 1С
        self::createOrUpdateUserIn1C($USER_ID);

        return array('success' => true, 'message' => "<p>Данные сохранены</p>");
    }

    /**
     * Обновляет инфу о юзере на сайте по данным из 1С
     * @global type $USER
     * @return boolean
     */
    public static function updateUserFrom1C()
    {
        global $USER;
        $USER_ID = $USER->GetId();

        if (empty($USER_ID))
        {
            return false;
        }

        $arFilter = Array("ACTIVE" => "Y", "ID" => $USER_ID);

        $XML_ID  = false;
        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            $XML_ID = $arFetch['XML_ID'];
        }

        //получаем инфу о юзере из 1С
        $arData = array(
            "XML_ID" => $XML_ID,
        );

        $ar1CUser = \CURL::getReplay("getUserDataByXmlId", $arData, true, false, true, false);

        //$sUserFisrtName  = trim($ar1CUser['FIO']);
        $FIO           = explode(" ", trim($ar1CUser['FIO']));
        $sUserLastName = "";
        $sUserFullName = "";
        foreach ($FIO as $k => $FIO_PART)
        {
            $sUserFullName .= $FIO_PART . " ";

            if ($k == 0)
            {
                $sUserFisrtName = $FIO_PART;
                continue;
            }

            $sUserLastName .= $FIO_PART . " ";
        }
        $sPhone     = trim($ar1CUser['PHONE']);
        $sUserEmail = trim($ar1CUser['EMAIL']);

        $arFields = array(
            "NAME"           => $sUserFisrtName,
            "LAST_NAME"      => rtrim($sUserLastName, " "),
            "EMAIL"          => $sUserEmail,
            "PERSONAL_PHONE" => $sPhone,
            "XML_ID"         => $ar1CUser["XML_ID"],
            'COMPANY'        => $ar1CUser['COMPANY'],
            'COMPANY_ADR'    => $ar1CUser['COMPANY_ADR'],
            'INN'            => $ar1CUser['INN'],
            'KPP'            => $ar1CUser['KPP'],
            'CONTACT_PERSON' => $ar1CUser['CONTACT_PERSON'],
            'CURRACC'        => $ar1CUser['CURRACC'],
        );

        $USER->Update($USER_ID, $arFields);

        $USER_PROFILE      = \CUserExt::getProfile();
        $PERSON_TYPE_ID    = $USER_PROFILE["PERSON_TYPE_ID"];
        $SUBSCRIBE_PROP_ID = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "SUBSCRIBE");

        $arProfileFields[$SUBSCRIBE_PROP_ID] = $ar1CUser['SUBSCRIBE'];
        \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROFILE["ID"], rtrim($sUserFullName, " "), $PERSON_TYPE_ID, $arProfileFields, $arResult);

        if (empty($ar1CUser["TYPE_OF_CARD"]) || !in_array($ar1CUser["TYPE_OF_CARD"], array("REGULAR", "PREMIUM")))
        {
            $ar1CUser["TYPE_OF_CARD"] = "REGULAR";
        }


        $arUserFields = array(
            "UF_CARD_ACTIVATED" => !empty($ar1CUser["ACTIVATED"]) ? "Y" : "N",
            "UF_CARD_TYPE"      => $ar1CUser["TYPE_OF_CARD"],
            "UF_CARD_NUMBER"    => cutAllButNumbers($ar1CUser["CARD"]),
            "UF_CARD_BALANCE"   => $ar1CUser["BONUSES"],
        );

        setUF("USER", $USER_ID, $arUserFields);
        \COrderExt::createUpdateUserAccount($ar1CUser["BONUSES"]);
    }

    /**
     * Проверяет на уникальность номер телефона
     * @param string $value номер телефона
     * @param array $exclude ID юзеров, которые надо исключить из проверки
     * @return boolean TRUE если телефон уникален
     */
    public static function isUniquePhone($value, $exclude = null, $allowEmpty = false, $check1C = false)
    {
        if (!CHECK_UNIQUE_ENABLE) return true;

        $phones = getPhoneFromString($value);

        if (!empty($exclude) && !is_array($exclude))
        {
            $exclude = array($exclude);
        }

        if (!empty($exclude))
        {
            $allowEmpty = true;
        }

        if (!$allowEmpty && empty($phones))
        {
            return false;
        }



        foreach ($phones as $phone)
        {
            if (!$allowEmpty && empty($phone))
            {
                return false;
            }

            $arFilter = array(
                "PERSONAL_PHONE" => $phone,
            );

            if (!empty($exclude))
            {
                //$arFilter["LOGIC"] = "AND";
                //$arFilter["!ID"]   = $exclude;
            }

            $obList  = \CUser::GetList($by      = "", $order   = "", $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (in_array($arFetch['ID'], $exclude)) continue;
                return false;
            }
        }

        if ($check1C)
        {
            $arData   = array(
                "PHONE" => $phones[0],
            );
            $ar1CUser = \CURL::getReplay("getUsersByEmailOrPhone", $arData, true, false, true, false);
            $XML_IDs  = $ar1CUser["XML_IDs"];

            if (!empty($exclude))
            {
                foreach ($exclude as $exclude_user_id)
                {
                    $USER_NFO = \CUserExt::getById($exclude_user_id);

                    if (!empty($USER_NFO))
                    {
                        foreach ($XML_IDs as $XML_ID)
                        {
                            if ($XML_ID == $USER_NFO["XML_ID"])
                            {
                                return false;
                            }
                        }
                    }
                }
            }
            else
            {
                if (!empty($XML_IDs))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Проверяет на уникальность почту
     * @param string $value адрес почты
     * @param array $exclude ID юзеров, которые надо исключить из проверки
     * @return boolean TRUE - если почта уникальна
     */
    public static function isUniqueEmail($value, $exclude = null, $allowEmpty = false, $check1C = false)
    {
        if (!CHECK_UNIQUE_ENABLE) return true;

        $email = trim(htmlspecialcharsbx($value));


        if (!empty($exclude) && !is_array($exclude))
        {
            $exclude = array($exclude);
        }

        if (!empty($exclude))
        {
            $allowEmpty = true;
        }

        if (!$allowEmpty && empty($email))
        {
            return false;
        }

        if (!empty($email))
        {
            $arFilter = array(
                "=EMAIL" => $email,
            );


            if (!empty($exclude))
            {
                //$arFilter["LOGIC"] = "AND";
                //$arFilter["!ID"]   = $exclude;
            }

            $obList  = \CUser::GetList($by      = "", $order   = "", $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (in_array($arFetch['ID'], $exclude)) continue;

                return false;
            }
        }

        if ($check1C && !empty($email))
        {
            $arData   = array(
                "EMAIL" => $email,
            );
            $ar1CUser = \CURL::getReplay("getUsersByEmailOrPhone", $arData, true, false, true, false);
            $XML_IDs  = $ar1CUser["XML_IDs"];

            if (!empty($exclude))
            {
                foreach ($exclude as $exclude_user_id)
                {
                    $USER_NFO = \CUserExt::getById($exclude_user_id);

                    if (!empty($USER_NFO))
                    {
                        foreach ($XML_IDs as $XML_ID)
                        {
                            if ($XML_ID == $USER_NFO["XML_ID"])
                            {
                                return false;
                            }
                        }
                    }
                }
            }
            else
            {
                if (!empty($XML_IDs))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * получает инфу о юзере по его ID
     * @global type $USER
     * @param type $USER_ID
     * @return type
     */
    public static function getById($USER_ID = false)
    {
        global $USER;

        if (empty($USER_ID))
        {
            $USER_ID = $USER->GetId();
        }

        $rsUser = \CUser::GetByID($USER_ID);
        $arUser = $rsUser->Fetch();

        return $arUser;
    }

    public static function getByPhone($arPhones)
    {
        $arUsers = array();

        foreach ($arPhones as $phone)
        {
            if (empty($phone)) continue;

            $arFilter = Array("ACTIVE" => "Y", "PERSONAL_PHONE" => $phone);

            $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (!array_key_exists($arFetch['ID'], $arUsers)) $arUsers[$arFetch['ID']] = $arFetch;
            }
        }

        return $arUsers;
    }

    public static function getByEmail($arEmails)
    {
        $arUsers = array();

        foreach ($arEmails as $email)
        {
            if (empty($email)) continue;

            $arFilter = Array("ACTIVE" => "Y", "EMAIL" => $email);

            $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (!array_key_exists($arFetch['ID'], $arUsers)) $arUsers[$arFetch['ID']] = $arFetch;
            }
        }

        return $arUsers;
    }

    public static function getByLogin($login, $return = 'string')
    {
        $arUsers = Array();

        $arFilter = Array("ACTIVE" => "Y", "LOGIN_EQUAL" => $login);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            if ($return == 'string') return $arFetch['ID'];

            $arUsers = array($arFetch['ID'] => $arFetch);
        }


        return $arUsers;
    }

    public static function getByXMLId($XML_ID, $active = "Y")
    {
        $arFilter = Array("ACTIVE" => $active, "XML_ID" => $XML_ID);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch['ID'];
        }

        return false;
    }

    public static function getByField($field, $value)
    {
        if (empty($field) || empty($value))
        {
            return false;
        }

        $arFilter = Array("ACTIVE" => "Y", "!" . $field => false, "=" . $field => $value);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch['ID'];
        }

        return false;
    }

    public static function createOrUpdateUserIn1C($USER_ID, $XML_ID = false)
    {
        global $USER;

        //обновим XML_ID юзера на сайте
        if (!empty($XML_ID))
        {
            $arUserFields = array(
                "XML_ID" => $XML_ID,
            );
            $USER->Update($USER_ID, $arUserFields);
        }

        $USER_NFO = \CUserExt::getById($USER_ID);

        if (isAdmin())
        {
            //printra($USER_NFO);
        }
        
        $USER_PROFILE = \CUserExt::getProfile($USER_ID);

        $PROPS_VALUE = $USER_PROFILE["PROPS_VALUE"];

        //добавляем или обновляем юзера в 1С
        $arData = array(
            //"ID"             => $USER_ID,
            "ID"             => $XML_ID,
            "EMAIL"          => $USER_NFO["EMAIL"],
            "PHONE"          => $USER_NFO["PERSONAL_PHONE"],
            "SUBSCRIBE"      => $PROPS_VALUE["SUBSCRIBE"]["VALUE"],
            "PERSON_TYPE_ID" => $USER_PROFILE["PERSON_TYPE_ID"] == FIZ_LICO ? 0 : 1,
            "FIO"            => $PROPS_VALUE["FIO"]["VALUE"],
            "COMPANY"        => $PROPS_VALUE["COMPANY"]["VALUE"],
            "COMPANY_ADR"    => $PROPS_VALUE["COMPANY_ADR"]["VALUE"],
            "INN"            => $PROPS_VALUE["INN"]["VALUE"],
            "KPP"            => $PROPS_VALUE["KPP"]["VALUE"],
            "CONTACT_PERSON" => $PROPS_VALUE["CONTACT_PERSON"]["VALUE"],
            "CURRACC"        => $PROPS_VALUE["CURRACC"]["VALUE"],
            "XML_ID"         => $USER_NFO["XML_ID"],
        );

        //ищем юзера в 1С
        $arData2 = array(
            "CARD"   => false,
            "FIO"    => $PROPS_VALUE["FIO"]["VALUE"],
            "PHONE"  => $USER_NFO["PERSONAL_PHONE"],
            "EMAIL"  => $USER_NFO["EMAIL"],
            "XML_ID" => $USER_NFO["XML_ID"],
        );


        //ищем юзера в 1с
        $arReplayHZ = \CURL::getReplay("getUserByCard", $arData, true, false, true);
        //pprintra($arReplayHZ);



        $ar1CnewUser = \CURL::getReplay("newUser", $arData, true, 0, true, false);
        //printra($ar1CnewUser);

        if ($ar1CnewUser["RESULT"])
        {
            $XML_ID_1C = $ar1CnewUser["XML_ID"];

            //если полученный из 1С XML_ID отлючается от того, 
            //что есть у юзера на сайте - обновим XML_ID на сайте
            if ($XML_ID_1C != $USER_NFO["XML_ID"])
            {
                $arUserFields = array(
                    "XML_ID" => $XML_ID_1C,
                );

                //$USER->SetParam("XML_ID", $XML_ID_1C);

                $USER->Update($USER_ID, $arUserFields);

                //die;
            }
        }

        return $ar1CnewUser;
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
