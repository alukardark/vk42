<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
/**
 * 
 * 
 * ВСЕ ЭТИ ФУНКЦИИ ПЕРЕНЕСЕНЫ В classes/order.php
 * ЭТОТ СКРИПТ НЕ ИСПОЛЬЗУЕТСЯ
 * 
 * 
 * 
 */
//define('PREPAY_PROP_ID_FIZ_LICO', 22);
//define('PREPAY_PROP_ID_UR_LICO', 22);

function getPrepayPrice($arTotal, $arFullProps)
{
    $iTotalPrice = $arTotal['ORDER_TOTAL_PRICE'];
    dmp($arFullProps);
}

function showProps($arOrderProps, $arFullProps, $arExcludeGroups = array(), $arIncludeGroups = array())
{
    $context        = Bitrix\Main\Application::getInstance()->getContext();
    $request        = $context->getRequest();
    //$server         = $context->getServer();
    $isPost         = $request->isPost();
    $arPostedValues = getPropertyValuesFromRequest();
    $arGroups       = $arOrderProps['groups'];
    $arProps        = $arOrderProps['properties'];

    /* $arOrderProps = array();
      $arPropsId    = array();
      //формируем список свойств заказа
      foreach ($arGroups as $arGroup)
      {
      if (!empty($arExcludeGroups) && in_array($arGroup['ID'], $arExcludeGroups)) continue;
      if (!empty($arIncludeGroups) && !in_array($arGroup['ID'], $arIncludeGroups)) continue;

      $arOrderProps[$arGroup['ID']]['GROUP'] = $arGroup;

      foreach ($arProps as $arProp)
      {
      if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

      $arOrderProps[$arGroup['ID']]['PROPS'][] = $arProp;
      $arPropsId[]                             = $arProp['ID'];
      }
      } */

    foreach ($arOrderProps as $iGroupId => $arOrderProp):
        $arGroup = $arOrderProp['GROUP'];
        $arProps = $arOrderProp['PROPS'];
        ?>
        <div class="order-block order-props-block">
            <div class="order-block-title"><?= $arGroup['NAME'] ?></div>

            <?
            foreach ($arProps as $arProp):
                if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;
                ?>

                <? if ($arProp['TYPE'] == 'STRING'): ?>
                    <? showPropsString($arProp, $arFullProps[$arProp['ID']], $isPost, $arPostedValues) ?>
                <? endif; ?>

                <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] == 'Y'): ?>
                    <? showPropsCheckBox($arProp, $arFullProps[$arProp['ID']], $isPost, $arPostedValues) ?>
                <? endif; ?>

                <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] != 'Y'): ?>
                    <? showPropsRadio($arProp, $arFullProps[$arProp['ID']], $isPost, $arPostedValues) ?>
                <? endif; ?>
            <? endforeach; ?>
        </div>
        <?
    endforeach;
}

function showPropsString($arProp, $arFullProp, $isPost, $arPostedValues)
{
    ?>
    <div class="order-props-block-input">
        <input
            type="text"
            data-property-code="<?= $arProp['CODE'] ?>"
            name="ORDER_PROP_<?= $arProp['ID'] ?>"
            placeholder="<?= $arProp['NAME'] ?>"
            value="<?= $isPost ? $arPostedValues[$arProp['ID']] : $arProp['VALUE'][0] ?>"
            />
    </div>
    <?
}

function showPropsCheckBox($arProp, $arFullProp, $isPost, $arPostedValues)
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

        $selected = $isPost ? in_array($sOptionCode, $arPostedValues[$arProp['ID']]) : in_array($sOptionCode, $arProp['VALUE']);
        ?>
        <button
            class="order-checkbox <?= $selected == true ? "selected" : "" ?>"
            data-multiple="<?= $arProp['MULTIPLE'] ?>"
            data-property-code="<?= $sOptionCode ?>"
            data-property-value="<?= $sOptionName ?>"
            name="ORDER_PROP_<?= $arProp['ID'] ?>"
            onclick="Order.setCheckbox(this)"
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

function showPropsRadio($arProp, $arFullProp, $isPost, $arPostedValues)
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

        $selected = $isPost ? in_array($sOptionCode, $arPostedValues[$arProp['ID']]) : in_array($sOptionCode, $arProp['VALUE']);
        ?>
        <button
            class="order-radio <?= $selected ? "selected" : "" ?>"
            data-multiple="<?= $arProp['MULTIPLE'] ?>"
            data-property-code="<?= $sOptionCode ?>"
            data-property-value="<?= $sOptionName ?>"
            name="ORDER_PROP_<?= $arProp['ID'] ?>"
            onclick="Order.setRadio(this)"
            >
            <i></i><span>
                <?= $sOptionName ?>
            </span>

            <? if (!empty($sOptionDescription)): ?>
                <div class="order-radio-description"><?= $sOptionDescription ?></div>
            <? endif; ?>
        </button>
        <?
    endforeach;
}

function getPropertyValuesFromRequest()
{
    global $request;
    $orderProperties = array();

    foreach ($request as $k => $v)
    {
        if (strpos($k, "ORDER_PROP_") !== false)
        {
            if (strpos($k, "[]") !== false) $orderPropId = intval(substr($k, strlen("ORDER_PROP_"), strlen($k) - 2));
            else $orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

            if ($orderPropId > 0) $orderProperties[$orderPropId] = $v;
        }
    }

    /* foreach ($this->request->getFileList() as $k => $arFileData)
      {
      if (strpos($k, "ORDER_PROP_") !== false)
      {
      $orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

      if (is_array($arFileData))
      {
      foreach ($arFileData as $param_name => $value)
      {
      if (is_array($value))
      {
      foreach ($value as $nIndex => $val)
      {
      if (strlen($arFileData["name"][$nIndex]) > 0)
      $orderProperties[$orderPropId][$nIndex][$param_name] = $val;
      }
      }
      else $orderProperties[$orderPropId][$param_name] = $value;
      }
      }
      }
      } */

    return $orderProperties;
}

function getFullProperies($arPropsId)
{
    //из компонента приходят не все данные о свойствах. Поэтому здесь получаем полные данные о свойствах
    $arFullProps = array();
    $obLists     = CSaleOrderProps::GetList(array(), array('ID' => $arPropsId), false, false, array("*"));
    while ($arFetch     = $obLists->Fetch())
    {
        $arFullProps[$arFetch['ID']] = $arFetch;

        $db_vars = CSaleOrderPropsVariant::GetList(($by      = "SORT"), ($order   = "ASC"), Array("ORDER_PROPS_ID" => $arFetch["ID"]));
        while ($vars    = $db_vars->Fetch())
        {
            $arFullProps[$arFetch['ID']]['OPTIONS'][] = $vars;
        }
    }

    return $arFullProps;
}

function getGroupedProperties($arOrderProps, $arExcludeGroups, $arIncludeGroups)
{
    $arGroups = $arOrderProps['groups'];
    $arProps  = $arOrderProps['properties'];

    $arResult  = array();
    $arPropsId = array();
    //формируем список свойств заказа
    foreach ($arGroups as $arGroup)
    {
        if (!empty($arExcludeGroups) && in_array($arGroup['ID'], $arExcludeGroups)) continue;
        if (!empty($arIncludeGroups) && !in_array($arGroup['ID'], $arIncludeGroups)) continue;

        $arOrderProps[$arGroup['ID']]['GROUP'] = $arGroup;

        foreach ($arProps as $arProp)
        {
            if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

            $arResult[$arGroup['ID']]['PROPS'][] = $arProp;
            $arPropsId[]                         = $arProp['ID'];
        }
    }
}
