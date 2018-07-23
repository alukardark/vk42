<?php

/**
 * Данный класс применим на странице офрмления заказа, то есть тогда, <b>когда ORDER_ID еще не известен</b>.<br>
 * Поэтому на странице оплаты или в персональном разделе его не стоит использовать<br>
 * <br>
 * Product - это товар из инфоблока каталога<br>
 * Record - это запись в корзине<br>
 */
class COrderExt
{

    private static $request;
    private static $server;
    private static $isPost;
    private static $arPostedValues;
    private static $arOrderProps;
    private static $arTotal;
    private static $arBasketItems;
    private static $prepayValue; //PREPAY100 || PREPAY10 || etc.

    /**
     * Для каждой точки выдачи указывается POINT_ID
     * @return array
     */

    public static function getMoneyCarePoints()
    {
        if (SITE_TEST)
        {
            $arMoneyCarePoints = array(
                235 => 220520172347, //кем, карболитовскаая
                234 => 220520172352, //кем, марковцева
                243 => 220520172355, //нк, строителей
                244 => 220520172357, //нк, курако
            );
        }
        else
        {
            $arMoneyCarePoints = array(
                223 => 220520172347, //кем, карболитовскаая
                222 => 220520172352, //кем, марковцева
                231 => 220520172355, //нк, строителей
                230 => 220520172357, //нк, курако
                224 => 230520170007, //кем, аврора
                229 => 230520170004, //кем, октябрьский 30а
                225 => 230520170001, //кем, заводский
            );
        }

        return $arMoneyCarePoints;
    }

    /**
     * Для каждой точки выдачи указывается массив, где
     * первое значение - логин API,
     * второе значение - пароль API
     * 
     * @return array
     */
    public static function getMoneyCareApi()
    {
        if (SITE_TEST)
        {
            $arMoneyCareApi = array(
                235 => array("krist863all", "434695"), //кем, карболитовскаая
                236 => array("krist863all", "434695"), //кем, марковцева
                243 => array("krist863all", "434695"), //нк, строителей
                244 => array("krist863all", "434695"), //нк, курако
            );
        }
        else
        {
            $arMoneyCareApi = array(
                223 => array("krist863all", "434695"), //кем, карболитовскаая
                222 => array("krist863all", "434695"), //кем, марковцева
                231 => array("krist863all", "434695"), //нк, строителей
                230 => array("krist863all", "434695"), //нк, курако
                224 => array("krist863all", "434695"), //кем, аврора
                229 => array("krist863all", "434695"), //кем, октябрьский 30а
                225 => array("krist863all", "434695"), //кем, заводский
            );
        }

        return $arMoneyCareApi;
    }

    public static function getMoneyCareSettingsByStoreId($storeID)
    {
        $arMoneyCarePoints = self::getMoneyCarePoints();
        $arMoneyCareApi    = self::getMoneyCareApi();

        return array(
            "POINT_ID"       => $arMoneyCarePoints[$storeID],
            "API_CLIENT_ID"  => $arMoneyCareApi[$storeID][0],
            "API_CLIENT_PWD" => $arMoneyCareApi[$storeID][1],
        );
    }

    public function getPrepayKoeff()
    {
        $koeff = (int) str_replace("PREPAY", "", self::$prepayValue);
        if (empty($koeff)) $koeff = 100;
        return $koeff;
    }

    public function getPrepayPrice($iPrice, $koeff, $print)
    {
        $iPrice = $iPrice * $koeff / 100;

        return $print ? printPrice($iPrice) : $iPrice;
    }

    public function getPrice($type = 'total', $print = true)
    {
        $iPrice = self::$arTotal['ORDER_TOTAL_PRICE'];

        if ($type == 'prepay')
        {
            $koeff  = $this->getPrepayKoeff();
            $iPrice = $iPrice * $koeff / 100;
        }

        return $print ? printPrice($iPrice) : $iPrice;
    }

    public function getQuantity()
    {
        $iQuantity = 0;

        foreach (self::$arBasketItems as $arBasketItem)
        {
            $iQuantity += $arBasketItem['QUANTITY'];
        }

        return $iQuantity;
    }

    public function getDeliveryCost($addQuantity = false, $arDeliveries = null)
    {
        $arItems    = $this->getItems(true);
        $tiersCount = 0;
        foreach ($arItems as $item)
        {
            if ($item['IBLOCK_CODE'] == TIRES_IB_CODE)
            {
                $tiersCount += $item['QUANTITY'];
            }
        }

        $deliveryCost = $tiersCount >= 4 ? 0 : 500;

        foreach ($arDeliveries as $arDelivery)
        {
            if ($arDelivery["ID"] == KDELIVERY_ID && $arDelivery["CHECKED"] == "Y")
            {
                $deliveryCost = 200;
                break;
            }

            if ($arDelivery["ID"] == TKDELIVERY_ID && $arDelivery["CHECKED"] == "Y")
            {
                $deliveryCost = 0;
                break;
            }
        }

        return $addQuantity ? array(
            'delivery'       => $deliveryCost,
            'quantity_tiers' => $tiersCount
                ) : $deliveryCost;
    }

    public function getItems($includeExtraData = false)
    {
        $arItems = self::$arBasketItems;

        if ($includeExtraData)
        {
            foreach ($arItems as &$item)
            {
                $tree                      = $this->getTreeToRoot($item['PRODUCT_ID']);
                $item['ROOT_SECTION_ID']   = $tree[0]['ID'];
                $item['ROOT_SECTION_CODE'] = $tree[0]['CODE'];
                $item['IBLOCK_CODE']       = $tree[0]['IBLOCK_CODE'];
            }
        }

        return $arItems;
    }

    private function getTreeToRoot($id)
    {
        $tree = array();
        if ($e    = CIBlockElement::GetByID($id)->Fetch())
        {
            $sId = $e['IBLOCK_SECTION_ID'];
        }
        else
        {
            $sId = $id;
        }

        while ($sId)
        {
            $s      = CIBlockSection::GetByID($sId)->Fetch();
            $tree[] = $s;
            $sId    = $s['IBLOCK_SECTION_ID'];
        }

        return array_reverse($tree);
    }

    public static function getDeliveryDate($iBuyerStore, $return = "print")
    {
        if (isBot())
        {
            return '';
        }

        $BASKET_DATA = \CBasketExt::getBasketNew(false, false, false, $iBuyerStore);
        $arRecords   = $BASKET_DATA["RECORDS"];

        $maxDeliveryDate = null;
        foreach ($arRecords as $arRecord)
        {
            $deliveryDate = $arRecord['DELIVERY_DATE_BASKET'];

            if ($deliveryDate === null || $deliveryDate === false)
            {
                $maxDeliveryDate = null;
                break;
            }

            if ($maxDeliveryDate === null || $maxDeliveryDate < $deliveryDate)
            {
                $maxDeliveryDate = $deliveryDate;
            }
        }

        global $USER;
        $NEWYEAR_DELIVERY      = /* $USER->IsAdmin() && */ \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY", false);
        $NEWYEAR_DELIVERY_TEXT = \WS_PSettings::getFieldValue("NEWYEAR_DELIVERY_TEXT", "уточните у менеджера");

        if ($return == "print")
        {
            if ($maxDeliveryDate === 0)
            {
                return 'Готов к выдаче';
            }
            elseif ($NEWYEAR_DELIVERY && $maxDeliveryDate > 0)
            {
                return $NEWYEAR_DELIVERY_TEXT;
            }
            elseif ($maxDeliveryDate !== null)
            {
                return FormatDateFromDB(date('d.m.Y', strtotime("+$maxDeliveryDate days")), "DD MMMM YYYY");
            }
            else
            {
                return 'Неизвестно';
            }
        }
        else
        {
            return $maxDeliveryDate;
        }
    }

    public function showProps($arExcludeGroups = array(), $arIncludeGroups = array(), $description = false)
    {
        $arProps = self::$arOrderProps['properties'];

        $arGroupedProperties = self::getGroupedProperties($arExcludeGroups, $arIncludeGroups);
        $arGroupedProps      = $arGroupedProperties[0];
        $arPropsId           = $arGroupedProperties[1];
        $arFullProps         = self::getFullProperies($arPropsId);

        foreach ($arGroupedProps as $arOrderProp):
            $arGroup = $arOrderProp['GROUP'];
            $arProps = $arOrderProp['PROPS'];
            if (count($arProps)):
                ?>
                <div class="order-block order-props-block" data-property-group="<?= $arGroup['ID'] ?>">
                    <div class="order-block-title"><?= $arGroup['NAME'] ?></div>

                    <?
                    foreach ($arProps as $arProp):
                        if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

                        if ($arProp['CODE'] == 'PHONE')
                        {
                            $arProp["VALUE"][0] = fixPhoneNumber($arProp["VALUE"][0]);
                        }

                        if ($arProp['CODE'] == 'NOTIFICATION')
                        {
                            $arProp['TYPE']     = 'ENUM';
                            $arProp['MULTIPLE'] = 'Y';

                            $arFullProps[$arProp['ID']]['OPTIONS'] = array(
                                array('NAME' => 'по SMS', 'VALUE' => 'SMS',),
                                array('NAME' => 'по E-mail', 'VALUE' => 'EMAIL',),
                            );
                        }

                        if ($arProp['CODE'] == 'SUBSCRIBE')
                        {
                            $arProp['TYPE']     = 'ENUM';
                            $arProp['MULTIPLE'] = 'Y';

                            $arFullProps[$arProp['ID']]['OPTIONS'] = array(
                                array('NAME' => 'Согласие на SMS рассылку', 'VALUE' => 'SMS',),
                                array('NAME' => 'Согласие на E-mail рассылку', 'VALUE' => 'EMAIL',),
                            );
                        }
                        ?>

                        <? if ($arProp['TYPE'] == 'LOCATION'): ?>

                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'STRING'): ?>
                            <? self::showPropsString($arProp) ?>
                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] == 'Y'): ?>
                            <? self::showPropsCheckBox($arProp, $arFullProps[$arProp['ID']]) ?>
                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] != 'Y'): ?>
                            <? self::showPropsRadio($arProp, $arFullProps[$arProp['ID']]) ?>
                        <? endif; ?>
                    <? endforeach; ?>

                    <? if ($arGroup['ID'] == 1 || $arGroup['ID'] == 4): ?>
                        <div class="order-subscribe">
                            <?
                            //группы 9 и 10 - Хотите получать новости и акции от вк сервис?
                            $this->showProps(array(), array(9, 10));
                            ?>
                        </div>
                    <? endif; ?>

                    <? if (!empty($description)): ?>
                        <div class="order-block-description2"><?= $description ?></div>
                    <? endif; ?>
                </div>
                <?
            endif;
        endforeach;
    }

    private static function showPropsString($arProp)
    {
        global $USER;

        $CODE = $arProp['CODE'];

        if ($CODE == "ZIP" || $CODE == "CITY" || $CODE == "ADDRESS")
        {
            return;
        }


        $CAPTION = $arProp['NAME'];
        if ($arProp['REQUIRED'] == "Y") $CAPTION .= "*";
        $VALUE   = self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]) ? self::$arPostedValues[$arProp['ID']] : $arProp['VALUE'][0];
        
        if ($CODE == "PHONE" && !empty($VALUE))
        {
            $VALUE = substr($VALUE, 1);
        }
        ?>
        <div class="order-props-block-input order-props-block-input--<?= $CODE ?>">
            <span
                class="<?= !empty($VALUE) ? "active" : "" ?> "
                onclick="Order.onClickPlaceholder(this)"
                ><?= $CAPTION ?></span>
            <input
                type="text"
                data-property-code="<?= $CODE ?>"
                data-check-unique="<?= CHECK_UNIQUE_ENABLE_ON_ORDER ? "Y" : "" ?>"
                data-check-phone="<?= $CODE == "PHONE" ? "Y" : "" ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                id="<?= $CODE ?>_PROP"
                placeholder="<?= $CAPTION ?>"
                title="<?= $arProp['NAME'] ?>"
                value="<?= htmlspecialcharsbx($VALUE) ?>"
                onfocus="Order.onInputFocus(this)"
                onblur="Order.onInputBlur(this)"
                <?= $arProp['REQUIRED'] == "Y" ? 'required="required"' : '' ?>
                <?= $CODE == "PHONE" ? 'data-type="phone"' : '' ?>
        <?= $CODE == "EMAIL" ? 'data-type="email"' : '' ?>
                />

            <figure class="order-props-block-input-spinner js-enter-spinner"><i></i><i></i><i></i></figure>
            <i class="ion-checkmark-round order-props-block-input-checkmark js-enter-checkmark"></i>
            <i class="ion-close-round order-props-block-input-close js-enter-close"></i>

        <? if ($CODE == "PHONE" || $CODE == "EMAIL"): ?>
                <div class="order-props-block-input-enter js-enter-link">
                    <div>Этот <?= $arProp['CODE'] == "PHONE" ? "номер" : "e-mail" ?> уже зарегистрирован.</div>
            <? if (!$USER->IsAuthorized()): ?>
                        <a
                            href="<?= PATH_AUTH ?>"
                            title="Войти"
                            data-back-url="<?= PATH_BASKET ?>"
                            onclick="App.setBackUrl(event, this)"
                            >Войти на сайт
                        </a>
                <? endif; ?>
                </div>
        <? endif; ?>
        </div>
        <?
    }

    private static function showPropsCheckBox($arProp, $arFullProp)
    {
        if (!empty($arProp['DESCRIPTION'])):
            ?>
            <div class="order-block-description"><?= $arProp['DESCRIPTION'] ?></div>
            <?
        endif;

        foreach ($arFullProp['OPTIONS'] as $arOption):
            $sOptionCode        = $arOption['VALUE'];
            $sOptionName        = $arOption['NAME'];
            $sOptionDescription = $arOption['DESCRIPTION'];

            $selected = false;

            if (self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]))
            {
                if ($sOptionCode == "SMS" || $sOptionCode == "EMAIL")
                {
                    $selected = strstr(self::$arPostedValues[$arProp['ID']], $sOptionCode);
                }
                else
                {
                    $selected = in_array($sOptionCode, self::$arPostedValues[$arProp['ID']]);
                }
            }
            else
            {
                if ($sOptionCode == "SMS" || $sOptionCode == "EMAIL")
                {
                    $selected = strstr($arProp['VALUE'][0], $sOptionCode);
                }
                else
                {
                    $selected = in_array($sOptionCode, $arProp['VALUE']);
                }
            }
            ?>
            <button
                class="order-checkbox <?= $selected == true ? "selected" : "" ?>"
                data-multiple="<?= $arProp['MULTIPLE'] ?>"
                data-property-code="<?= $sOptionCode ?>"
                data-property-value="<?= $sOptionName ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                onclick="Order.setCheckbox(event, this)"
                >
                <i></i><span>
            <?= $sOptionName ?>
                </span>

                <? if (!empty($sOptionDescription)): ?>
                    <div class="order-checkbox-description"><?= $sOptionDescription ?></div>
            <? endif; ?>
            </button>
            <?
        endforeach;
    }

    private static function showPropsRadio($arProp, $arFullProp)
    {
        if (!empty($arProp['DESCRIPTION'])):
            ?>
            <div class="order-block-description"><?= $arProp['DESCRIPTION'] ?></div>
            <?
        endif;

        foreach ($arFullProp['OPTIONS'] as $arOption):
            $sOptionCode        = $arOption['VALUE'];
            $sOptionName        = $arOption['NAME'];
            $sOptionDescription = $arOption['DESCRIPTION'];

            $selected = self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]) ?
                    in_array($sOptionCode, self::$arPostedValues[$arProp['ID']]) :
                    in_array($sOptionCode, $arProp['VALUE']);
            ?>
            <button
                class="order-radio <?= $selected ? "selected" : "" ?>"
                data-multiple="<?= $arProp['MULTIPLE'] ?>"
                data-property-code="<?= $sOptionCode ?>"
                data-property-value="<?= $sOptionName ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                onclick="Order.setRadio(event, this)"
                >
                <i></i><span>
            <?= $sOptionName ?>
                </span>

                    <? if (!empty($sOptionDescription)): ?>
                    <mark class="order-radio-description"><?= $sOptionDescription ?></mark>
            <? endif; ?>
            </button>
            <?
        endforeach;
    }

    function getFullProperies($arPropsId = false)
    {
        //из компонента приходят не все данные о свойствах. Поэтому здесь получаем полные данные о свойствах
        $arFullProps = array();
        $obLists     = \CSaleOrderProps::GetList(array(), array('ID' => $arPropsId), false, false, array("*"));
        while ($arFetch     = $obLists->Fetch())
        {
            $arFullProps[$arFetch['ID']] = $arFetch;

            if (!self::$isPost)
            {
                if ($arFetch['ID'] == PREPAY_PROP_ID_FIZ_LICO || $arFetch['ID'] == PREPAY_PROP_ID_UR_LICO)
                {
                    if (!empty($arFetch['DEFAULT_VALUE']))
                    {
                        self::$prepayValue = $arFetch['DEFAULT_VALUE']; //PREPAY100 || PREPAY10 || etc.
                    }
                    elseif (!empty($arFetch['DEFAULT_VALUE_ORIG']))
                    {
                        self::$prepayValue = $arFetch['DEFAULT_VALUE_ORIG']; //PREPAY100 || PREPAY10 || etc.
                    }
                }
            }

            $db_vars = \CSaleOrderPropsVariant::GetList(($by      = "SORT"), ($order   = "ASC"), Array("ORDER_PROPS_ID" => $arFetch["ID"]));
            while ($vars    = $db_vars->Fetch())
            {
                $arFullProps[$arFetch['ID']]['OPTIONS'][] = $vars;
            }
        }

        return $arFullProps;
    }

    private function getGroupedProperties($arExcludeGroups, $arIncludeGroups)
    {
        $arGroups = self::$arOrderProps['groups'];
        $arProps  = self::$arOrderProps['properties'];

        $arResult  = array();
        $arPropsId = array();
        //формируем список свойств заказа
        foreach ($arGroups as $arGroup)
        {
            if (!empty($arExcludeGroups) && in_array($arGroup['ID'], $arExcludeGroups)) continue;
            if (!empty($arIncludeGroups) && !in_array($arGroup['ID'], $arIncludeGroups)) continue;

            $arResult[$arGroup['ID']]['GROUP'] = $arGroup;

            foreach ($arProps as $arProp)
            {
                if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

                $arResult[$arGroup['ID']]['PROPS'][] = $arProp;
                $arPropsId[]                         = $arProp['ID'];
            }
        }

        return array($arResult, $arPropsId);
    }

    public static function getCard()
    {
        global $USER;
        $USER_ID = $USER->GetId();

        if (empty($USER_ID))
        {
            return false;
        }

        $ACTIVATED = getUF("USER", $USER_ID, "UF_CARD_ACTIVATED");
        $TYPE      = getUF("USER", $USER_ID, "UF_CARD_TYPE");
        $NUMBER    = getUF("USER", $USER_ID, "UF_CARD_NUMBER");
        $BALANCE   = getUF("USER", $USER_ID, "UF_CARD_BALANCE");

        if (empty($NUMBER) || empty($TYPE) || $ACTIVATED !== "Y")
        {
            return false;
        }

        return array(
            'TYPE'    => htmlspecialchars($TYPE),
            'NUMBER'  => htmlspecialchars($NUMBER),
            'BALANCE' => (int) $BALANCE,
        );
    }

    public static function getCardTypeName($TYPE)
    {
        return \WS_PSettings::getFieldValue("BONUS_CARD_NAME_" . $TYPE, 0);
    }

    public static function getCardMaxPercent($TYPE)
    {
        return \WS_PSettings::getFieldValue("BONUS_MAX_PERCENT_" . $TYPE, 0);
    }

    public static function getCardSavePercent($TYPE)
    {
        return \WS_PSettings::getFieldValue("BONUS_SAVE_PERCENT_" . $TYPE, 0);
    }

    /**
     * особый процент накопления для цен, окончивающихся на 9
     * @param type $TYPE
     * @return type
     */
    public static function getCardSaveNinePercent($TYPE)
    {
        return \WS_PSettings::getFieldValue("BONUS_SAVE_PERCENT_NINES_" . $TYPE, 0);
    }

    /**
     * рассчитывает кол-во бонусов, которое можно накопить с заказа
     *
     * @param string $TYPE тип карты - REGULAR или PREMIUM
     * @return type
     */
    public static function calculateOrderSaveBonuses($CARD_TYPE, $GRID_ROWS)
    {
        $BONUS_SAVE_PERCENT      = \COrderExt::getCardSavePercent($CARD_TYPE);
        $BONUS_SAVE_NINE_PERCENT = \COrderExt::getCardSaveNinePercent($CARD_TYPE);

        $bonuses = 0;


        foreach ($GRID_ROWS as $GRID_ITEM)
        {
            $PRODUCT_ID = $GRID_ITEM['data']['PRODUCT_ID'];
            $QUANTITY   = $GRID_ITEM['data']['QUANTITY'];

            $PRICE_RETAIL = (int) \CBasketExt::getProductPrice($PRODUCT_ID, RETAIL_PRICE_ID);
            $PRICE        = (int) $GRID_ITEM['data']['PRICE'];

            $lastDigit = $PRICE_RETAIL % 10;
            if ($PRICE_RETAIL == $PRICE) $lastDigit = 0;

            $koeff   = $lastDigit === 9 ? $BONUS_SAVE_NINE_PERCENT : $BONUS_SAVE_PERCENT;
            $bonuses += $PRICE * $QUANTITY * $koeff / 100;
        }

        return $bonuses;
    }

    /**
     *
     * @global type $USER
     * @param int $balance тот баланс, который будет на счете юзера (внутренний счет)
     */
    public static function createUpdateUserAccount($balance = 0)
    {
        global $USER;
        $USER_ID = $USER->GetId();

        $userAccountId  = false;
        $currentBalance = false;
        //получаем ID счета текущего юзера
        if ($arUserAccount  = \CSaleUserAccount::GetByUserID($USER_ID, BASE_CURRENCY))
        {
            $userAccountId  = $arUserAccount["ID"];
            $currentBalance = $arUserAccount["CURRENT_BUDGET"];
        }
        else
        {
            //при необходимости создаем счет
            $userAccountId  = \CSaleUserAccount::UpdateAccount($USER_ID, 0, BASE_CURRENCY, "MANUAL", 0, "Создание счета");
            $currentBalance = 0;
        }

        $diffBalance = $balance - $currentBalance;

        if ($diffBalance != 0)
        {
            \CSaleUserAccount::UpdateAccount($USER_ID, $diffBalance, BASE_CURRENCY, "MANUAL", 0, "Обновление счета по запросу из 1С");
        }
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get($arResult)
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init($arResult);
        }
        return self::$_instance;
    }

    private static function init($arResult)
    {
        //printra($arResult);
        $context              = \Bitrix\Main\Application::getInstance()->getContext();
        self::$request        = $context->getRequest();
        self::$server         = $context->getServer();
        self::$isPost         = self::$request->isPost();
        self::$arPostedValues = self::getPropertyValuesFromRequest();
        self::$arOrderProps   = $arResult['JS_DATA']['ORDER_PROP'];
        self::$arTotal        = $arResult['JS_DATA']['TOTAL'];
        self::$arBasketItems  = $arResult['BASKET_ITEMS'];
    }

    /**
     * Значения свойств заказа на странице фофрмления заказа
     * @return type
     */
    private static function getPropertyValuesFromRequest()
    {
        $postedProperties = array();
        //dmp(self::$request);

        foreach (self::$request as $k => $v)
        {
            if (strpos($k, "ORDER_PROP_") !== false)
            {
                if (strpos($k, "[]") !== false)
                        $orderPropId = intval(substr($k, strlen("ORDER_PROP_"), strlen($k) - 2));
                else $orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

                if ($orderPropId > 0) $postedProperties[$orderPropId] = $v;

                if (self::$isPost)
                {
                    if ($orderPropId == PREPAY_PROP_ID_FIZ_LICO || $orderPropId == PREPAY_PROP_ID_UR_LICO)
                    {
                        if (!empty($v[0]))
                        {
                            self::$prepayValue = $v[0]; //PREPAY100 || PREPAY10 || etc.
                        }
                    }
                }
            }
        }

        return $postedProperties;
    }

    /**
     * возвращает массив целей для метрики, если в заказе есть соответствубщие акционные товары
     */
    public function getActionGoals()
    {
        $arGoals       = array();
        $arBasketItems = \CBasketExt::getBasketNew();

        foreach ($arBasketItems["RECORDS"] as $arItem)
        {
            $PRODUCT_ID = $arItem['PRODUCT_ID'];
            $IBLOCK_ID  = getIBlockByElement($PRODUCT_ID);

            $goal = null;

            if ($IBLOCK_ID == TIRES_IB)
            {
                $goal = "order_conf_shiny";
            }
            elseif ($IBLOCK_ID == AKB_IB)
            {
                $goal = "order_conf_akb";
            }
            elseif ($IBLOCK_ID == DISCS_IB)
            {
                $goal = "order_conf_disks";
            }
            elseif ($IBLOCK_ID == OILS_IB)
            {
                $goal = "order_conf_oils";
            }

            if (!empty($goal) && !in_array($goal, $arGoals))
            {
                $arGoals[] = $goal;
            }

            if (!empty($arItem["ACTION_GOAL_BUY"]) && !in_array($arItem["ACTION_GOAL_BUY"], $arGoals))
            {
                $arGoals[] = $arItem["ACTION_GOAL_BUY"];
            }
        }

        return $arGoals;
    }

    public static function serviceSetProperty()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $ORDER_ID       = (int) $request->get("ORDER_ID");
        $PROPERTY_CODE  = (string) $request->get("PROPERTY_CODE");
        $PROPERTY_VALUE = (string) $request->get("PROPERTY_VALUE");

        if (empty($PROPERTY_CODE))
        {
            json_result(false, array('error_message' => "bad PROPERTY_CODE", 'server_time' => time()));
        }

        $order = \Bitrix\Sale\Order::load($ORDER_ID);

        if (empty($order))
        {
            json_result(false, array('error_message' => "order $ORDER_ID not found", 'server_time' => time()));
        }

        $PERSON_TYPE_ID = $order->getPersonTypeId();
        $PROPERTY_ID    = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, $PROPERTY_CODE);

        $propertyCollection = $order->getPropertyCollection();
        $property           = $propertyCollection->getItemByOrderPropertyId($PROPERTY_ID);

        if (empty($property))
        {
            json_result(false, array('error_message' => "property $PROPERTY_CODE not found", 'server_time' => time()));
        }

        $property->setValue($PROPERTY_VALUE);
        $order->save();

        json_result(true, array('message' => "saved", 'server_time' => time()));
    }

    public static function serviceSetProperties()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $ORDER_ID   = (int) $request->get("ORDER_ID");
        $PROPERTIES = (array) @unserialize($request->get("PROPERTIES"));

        if (empty($PROPERTIES) || !is_array($PROPERTIES))
        {
            json_result(false, array('error_message' => "bad PROPERTIES", 'server_time' => time()));
        }

        $order = \Bitrix\Sale\Order::load($ORDER_ID);

        if (empty($order))
        {
            json_result(false, array('error_message' => "order $ORDER_ID not found", 'server_time' => time()));
        }

        $PERSON_TYPE_ID     = $order->getPersonTypeId();
        $propertyCollection = $order->getPropertyCollection();

        $msg = '';
        foreach ($PROPERTIES as $PROPERTY_CODE => $PROPERTY_VALUE)
        {
            $PROPERTY_ID = \CSaleExt::getPropertyIdByCode($PERSON_TYPE_ID, $PROPERTY_CODE);
            $property    = $propertyCollection->getItemByOrderPropertyId($PROPERTY_ID);

            if (empty($property))
            {
                $msg .= "property $PROPERTY_CODE not found; ";
                continue;
            }

            $property->setValue($PROPERTY_VALUE);
        }

        $order->save();

        json_result(true, array('message' => "saved", 'server_time' => time()));
    }

    public static function serviceSetStore()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $ORDER_ID     = (int) $request->get("ORDER_ID");
        $STORE_XML_ID = (string) $request->get("STORE_XML_ID");

        if (empty($STORE_XML_ID))
        {
            json_result(false, array('error_message' => "bad STORE_XML_ID", 'server_time' => time()));
        }

        $arStore = \CCatalogExt::getStoreByXML_ID($STORE_XML_ID);

        if (empty($arStore))
        {
            json_result(false, array('error_message' => "store $STORE_XML_ID not found", 'server_time' => time()));
        }

        $STORE_ID = $arStore['ID'];
        $order    = \Bitrix\Sale\Order::load($ORDER_ID);

        if (empty($order))
        {
            json_result(false, array('error_message' => "order $ORDER_ID not found", 'server_time' => time()));
        }

        $obShipmentCollection = $order->getShipmentCollection();

        foreach ($obShipmentCollection as $shipment)
        {
            $shipment->setStoreId($STORE_ID);
        }

        //$order->doFinalAction(true);
        $order->save();

        json_result(true, array('message' => "saved", 'server_time' => time()));
    }

    public static function serviceSetUser()
    {
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();

        $ORDER_ID    = (int) $request->get("ORDER_ID");
        $USER_XML_ID = (string) $request->get("USER_XML_ID");
        $PROPERTIES  = (array) @unserialize($request->get("PROPERTIES"));

        if (empty($USER_XML_ID))
        {
            json_result(false, array('error_message' => "bad USER_XML_ID", 'server_time' => time()));
        }

        $USER_ID = \CUserExt::getByXMLId($USER_XML_ID);

        if (empty($USER_ID))
        {
            $regRes  = \CUserExt::register($PROPERTIES);
            $USER_ID = (int) $regRes['USER_ID'];

            if (empty($USER_ID) || $regRes['success'] != true)
            {
                json_result(false, array('error_message' => $regRes['ecodes'], 'server_time' => time()));
            }
            else
            {
                $USER_ID = $regRes['USER_ID'];
            }
        }

        $USER_PROFILE_ID = \CUserExt::getProfile($USER_ID);

        if (empty($USER_PROFILE_ID))
        {
            //json_result(false, array('error_message' => "user $USER_XML_ID dont have any bayer profiles", 'server_time' => time()));
        }

        $order = \Bitrix\Sale\Order::load($ORDER_ID);

        if (empty($order))
        {
            json_result(false, array('error_message' => "order $ORDER_ID not found", 'server_time' => time()));
        }

        $arFields = array(
            "USER_ID" => $USER_ID,
        );
        \CSaleOrder::Update($ORDER_ID, $arFields);

        $propertyCollection = $order->getpropertyCollection();
        foreach ($propertyCollection as $property)
        {
            if ($property->getField('CODE') == 'SUBUSER_ID')
            {
                $property->setValue($USER_PROFILE_ID);
            }
        }

        $order->save();

        self::serviceSetProperties();

        json_result(true, array('message' => "saved", 'server_time' => time()));
    }

}
