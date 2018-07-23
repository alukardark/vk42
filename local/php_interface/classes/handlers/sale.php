<?php

namespace Axi\Handlers;

class Sale
{
    /**
     * Вызывается в компоненте bitrix:sale.order.ajax после создания заказа и всех его параметров,
     * после отправки письма, перед выводом страницы об успешно созданном заказе и оплате заказа.
     * @param string|int $ID Идентификатор заказа
     * @param array $arOrder Массив полей заказа
     * @param array $arParams Массив параметров компонента
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\NotImplementedException
     */
    function OnSaleComponentOrderOneStepFinal($ID, $arOrder, $arParams)
    {
        //разбиваем оплату на части, если выбрана оплата бонусами и/или предоплата
        \CSaleExt::splitOrderPayments($ID);

        //присвоим другой статус заказа, если выбран способ оплаты наличными при получении (ID=1, code = "C")
        if (intval($arOrder["PAY_SYSTEM_ID"]) === CPAYMENT_ID)
        {
            \CSaleOrder::StatusOrder($ID, STATUS_ACCEPTED); //Принят, формируется к отправке
        }

        if (intval($arOrder["PAY_SYSTEM_ID"]) === KPAYMENT_ID)
        {
            $obOrder         = \Bitrix\Sale\Order::load($ID);
            $ORDER_STATUS_ID = $obOrder->getField('STATUS_ID');

            if (!in_array($ORDER_STATUS_ID, array(STATUS_KREDIT_NEW, STATUS_KREDIT_ACCEPT, STATUS_KREDIT_DECLINE)))
            {
                \CSaleOrder::StatusOrder($ID, STATUS_KREDIT_NEW); //Принят, ожидается решение по кредиту
            }
        }
    }

    /**
     * Вызывается в компоненте bitrix:sale.order.ajax после формирования всех данных компонента на этапе заполнения формы заказа,
     *  может быть использовано для модификации данных. 
     * 
     * @param type $arResult Массив arResult компонента
     * @param type $arUserResult Массив arUserResult компонента, содержащий текущие выбранные пользовательские данные
     * @param type $arParams Массив параметров компонента
     */
    function OnSaleComponentOrderOneStepProcess(&$arResult, &$arUserResult, $arParams)
    {
        
    }

    function OnSaleBeforeCancelOrder(&$ID, &$val)
    {
        //$val = "Y";
    }

    function OnSaleOrderBeforeSaved(\Bitrix\Main\Event $event)
    {
        if ($_REQUEST['mode'] == 'import' && $_REQUEST['type'] == 'sale')
        {
            /* $ORDER     = $event->getParameter("ENTITY");
              $NAME      = $event->getParameter('NAME');
              $VALUE     = $event->getParameter('VALUE');
              $OLD_VALUE = $event->getParameter('OLD_VALUE');

              $ID = $ORDER->getId();

              printra($ORDER); */
        }
    }

    function OnBeforeSaleOrderSetField(\Bitrix\Main\Event $event)
    {
        if ($_REQUEST['mode'] == 'import') return;

        $NAME  = $event->getParameter('NAME');
        $VALUE = $event->getParameter('VALUE');
    }

    function OnSaleOrderSetField(\Bitrix\Main\Event $event)
    {
        if ($_REQUEST['mode'] == 'import') return;

        $ORDER     = $event->getParameter("ENTITY");
        $NAME      = $event->getParameter('NAME');
        $VALUE     = $event->getParameter('VALUE');
        $OLD_VALUE = $event->getParameter('OLD_VALUE');

        $ID = $ORDER->getId();

        if ($NAME == "CANCELED" && $VALUE == "Y" && $OLD_VALUE == "N" && isPost())
        {
            if (!empty($_REQUEST['REASON_CANCELED']))
            {
                $res = \CSaleOrder::CancelOrder($ID, "Y", $_REQUEST['REASON_CANCELED']);
                return true;
            }
            //echo time() . '<br/>';
            //\CSaleOrder::CancelOrder($ID, "Y", "Потому что передумал");
        }
    }

    /**
     * Вызывается после создания и расчета обьекта заказа.
     * 
     * @param type $order
     * @param type $arUserResult
     * @param type $request
     * @param type $arParams
     * @param type $arResult
     * @param type $arDeliveryServiceAll
     * @param type $arPaySystemServiceAll
     */
    function OnSaleComponentOrderCreated($order, &$arUserResult, $request, &$arParams, &$arResult, &$arDeliveryServiceAll, &$arPaySystemServiceAll)
    {
        //echo 'OnSaleComponentOrderCreated<br/>';
    }

    /**
     * Вызывается в компоненте bitrix:sale.order.ajax после формирования списка доступных служб доставки,
     *  может быть использовано для модификации данных. 
     * 
     * @param type $arResult Массив arResult компонента
     * @param type $arUserResult Массив arUserResult компонента, содержащий текущие выбранные пользовательские данные
     * @param type $arParams Массив параметров компонента
     */
    function OnSaleComponentOrderOneStepDelivery(&$arResult, &$arUserResult, $arParams)
    {
        global $APPLICATION;
        //echo 'OnSaleComponentOrderOneStepDelivery<br/>';

        $STORE_LIST     = $arResult["STORE_LIST"]; //список доступных складов самовывоза
        $BUYER_STORE    = $arUserResult["BUYER_STORE"];
        $ORDER_PROP     = $arUserResult["ORDER_PROP"];
        $PERSON_TYPE_ID = $arUserResult["PERSON_TYPE_ID"];
        $DELIVERY_LIST  = $arResult['DELIVERY'];
        
        //printrau($DELIVERY_LIST);


        $LOCATION_PROP_ID       = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "LOCATION");
        $LOCATION_SELECTED_CODE = $arUserResult["ORDER_PROP"][$LOCATION_PROP_ID];

        if (empty($LOCATION_SELECTED_CODE) && isPost())
        {
            //\CSaleExt::setDefaultOrderLocation($arUserResult);
            //$LOCATION_SELECTED_CODE = $arUserResult["ORDER_PROP"][$LOCATION_PROP_ID];
        }

        if (!empty($LOCATION_SELECTED_CODE))
        {
            $locationId = \Axi::getLocationIdByCode($LOCATION_SELECTED_CODE);
            $LOCATION   = \CSaleLocation::GetByID($locationId);

            $cityName     = $LOCATION["CITY_NAME"];
            $arStoresCity = \CCatalogExt::getStores($cityName, null, "Y");

            $arStoresCityIds = array(); //список ID складов в выбранном городе
            foreach ($arStoresCity as $arStoresCityItem)
            {
                $arStoresCityIds[] = $arStoresCityItem["ID"];
            }

            foreach ($STORE_LIST as $STORE_ID => $STORE_ITEM)
            {
                if (!in_array($STORE_ID, $arStoresCityIds)) unset($arResult["STORE_LIST"][$STORE_ID]);
            }

            if (!isPost() || $_REQUEST["DELIVERY_SET"] == "Y")
            {
                $BUYER_STORE = null; //отменяем автовыбор склада самовывоза

                foreach ($DELIVERY_LIST as $DELIVERY_ITEM)
                {
                    if ($DELIVERY_ITEM['CHECKED'] == "Y" && !empty($DELIVERY_ITEM['STORE']))
                    {
                        $minDeliveryDays = null;
                        $isOnAllStores   = true; //есть на всех складах
                        $isOffAllStores  = true; //нету ни на одном складе

                        foreach ($DELIVERY_ITEM['STORE'] as $iDeliveryStoreId)
                        {
                            if (!array_key_exists($iDeliveryStoreId, $STORE_LIST)) continue;
                            if (!in_array($iDeliveryStoreId, $arStoresCityIds)) continue;

                            $deliveryStoreDate = \COrderExt::getDeliveryDate($iDeliveryStoreId, false);

                            if ($deliveryStoreDate === null) continue;

                            if ($deliveryStoreDate > 0)
                            {
                                $isOnAllStores = false;
                            }

                            if ($deliveryStoreDate == 0)
                            {
                                $isOffAllStores = false;
                            }

                            if ($minDeliveryDays === null || $deliveryStoreDate < $minDeliveryDays)
                            {
                                $minDeliveryDays = $deliveryStoreDate;
                                $BUYER_STORE     = $iDeliveryStoreId;
                            }
                        }
                    }
                }

                if ($isOnAllStores === true || $isOffAllStores === true)
                {
                    $BUYER_STORE = $arStoresCityIds[0];
                }
                
                if ($minDeliveryDays > 0)
                {
                    $BUYER_STORE = $arStoresCityIds[0];
                }
            }

            if (empty($BUYER_STORE))
            {
                $BUYER_STORE = $arStoresCityIds[0];
            }
            else
            {
                if (!in_array($BUYER_STORE, $arStoresCityIds))
                {
                    $BUYER_STORE = $arStoresCityIds[0];
                }
            }
        }

        $arResult['BUYER_STORE']     = $BUYER_STORE;
        $arUserResult['BUYER_STORE'] = $BUYER_STORE;
    }

    /**
     * Вызывается перед добавлением заказа, может быть использовано для отмены или модификации данных.
     * @param $arUserResult
     * @param $request
     * @param $arParams
     * @param $arResult
     */
    function OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
    {
        /**
         * Удалим из корзины товары запрещенные к доставке в выбранный город
         */
        $userCity = $request->getCookie('USER_CITY');
        if ($userCity == ANOTHER_CITY_CODE) {
            $obBasketExt = \CBasketExt::get();
            $obBasketExt::deleteRestrictedRecords();
        }

        global $USER;

        $USER_ID        = $USER->GetId();
        $ORDER_PROP     = $arUserResult["ORDER_PROP"];
        $PERSON_TYPE_ID = $arUserResult["PERSON_TYPE_ID"];

        /**
         * УСтановим местоположение, если не задано
         */
        if (!isPost())
        {
            \CSaleExt::setDefaultOrderLocation($arUserResult);
        }

        /**
         * значения по умолчанию для подписки и оповещений
         */
        if (!isPost())
        {
            $NOTIFICATION_PROP_ID = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "NOTIFICATION");
            $SUBSCRIBE_PROP_ID    = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, "SUBSCRIBE");

            if (empty($arUserResult["ORDER_PROP"][$NOTIFICATION_PROP_ID]))
            {
                $arUserResult["ORDER_PROP"][$NOTIFICATION_PROP_ID] = "EMAIL;SMS;";
            }

            if (empty($arUserResult["ORDER_PROP"][$SUBSCRIBE_PROP_ID]))
            {
                $arUserResult["ORDER_PROP"][$SUBSCRIBE_PROP_ID] = "EMAIL;";
            }
        }

        //массив с свойств заказа для email и phone
        $arCheckedProps = array();
        $obLists        = \CSaleOrderProps::GetList(array(), array('CODE' => array("EMAIL", "PHONE")));
        while ($arFetch        = $obLists->Fetch())
        {
            $arCheckedProps[$arFetch["ID"]] = $arFetch;
        }

        //проверяем тел и почту на уникальность
        //ошибки добавляются в массив $arResult["ERROR"]
        foreach ($ORDER_PROP as $id => $value)
        {
            if (!array_key_exists($id, $arCheckedProps) || empty($value)) continue;

            $code = $arCheckedProps[$id]["CODE"];

            if ($code == "PHONE")
            {
                if (!\CUserExt::isUniquePhone($value, $USER_ID))
                {
                    $message = "Телефон $value уже зарегистрирован на сайте";

                    //$arResult["ERROR"][]                    = $message;
                    //$arResult["ERROR_SORTED"]["PROPERTY"][] = $message;
                }
            }

            if ($code == "EMAIL")
            {
                if (!\CUserExt::isUniqueEmail($value, $USER_ID))
                {
                    $message = "E-mail $value уже зарегистрирован на сайте";

                    //$arResult["ERROR"][]                    = $message;
                    //$arResult["ERROR_SORTED"]["PROPERTY"][] = $message;
                }
            }
        }
    }

    function OnSaleComponentOrderShowAjaxAnswer(&$result)
    {
        
    }

    /**
     * Вы можете дополнить стандартный набор ограничений своими собственными ограничениями.
     *  Для этого следует в зависимости от ваших нужд использовать события инициализирования ограничений
     * @see https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=7352
     * 
     * @return \Bitrix\Main\EventResult
     */
    function onSaleDeliveryRestrictionsClassNamesBuildList()
    {
        return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::SUCCESS, array(
            '\VKDeliveryRestriction' => '/local/php_interface/include/restrictions/delivery.php',
                )
        );
    }

    function onSalePaySystemRestrictionsClassNamesBuildList()
    {
        return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::SUCCESS, array(
            '\VKPayRestriction' => '/local/php_interface/include/restrictions/pay.php',
                )
        );
    }

}
