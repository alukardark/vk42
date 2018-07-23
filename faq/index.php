<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «Континент шин» сервис-центры, легковые шины, грпузовые шины, мото шины");
$APPLICATION->SetPageProperty("description", "Сеть сервис-центров «Континент шин» предлагает: купить шины, диски, масла и аккумуляторы, а также пройти обслуживание своего автомобиля. Полный перечень услуг, гарантия качества.");
$APPLICATION->SetPageProperty("title", "Вопрос-ответ | Сервис-центры «Континент шин» — шины, диски, масла, технические жидкости, обслуживание автомобиля");
$APPLICATION->SetTitle("Вопрос-ответ");

/**
 * По умолчанию в сессии запоминается последняя открытая страница постраничной навигации.
 * Если вы хотите изменить такое поведение для данной текущей страницы,
 * то до вызова етода необходимо воспользоваться следующим кодом:
 * 
 * @see https://dev.1c-bitrix.ru/api_help/main/reference/cdbresult/navprint.php
 */
\CPageOption::SetOptionString("main", "nav_page_in_session", "N");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/faq.css");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/faq.js");
$assets->addJs(SITE_TEMPLATE_PATH . "/scripts/form.js");

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$countOnPage = 10;
$page        = intval($request['PAGEN_1']);
if (empty($page)) $page        = 1;

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_common/faq.arCategories/";
$cacheID   = "arCategories" . FORM_FAQ;

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arCategories"]))
    {
        $arCategories = $vars["arCategories"];
        $lifeTime     = 0;
    }
}

$currentCategoryId   = false;
$defaultCategoryName = "Все вопросы";
$currentCategoryName = $defaultCategoryName;

if ($lifeTime > 0)
{
    $arCategories[] = array(
        'ID'          => false,
        'FIELD_ID'    => false,
        'QUESTION_ID' => false,
        'MESSAGE'     => $defaultCategoryName,
        'FIELD_TYPE'  => 'dropdown',
    );

    \CForm::GetDataByID(FORM_FAQ, $arForm, $Q, $A, $D, $M); //sorry
    $arCategories = array_merge($arCategories, $A["CATEGORY"]);

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arCategories" => $arCategories,
    ));
}

//printra($arCategories);



if (isPost("get_list"))
{
    $postedCategoryId = (int) $request["CATEGORY"];

    foreach ($arCategories as $arCategory)
    {
        if ($arCategory['ID'] == $postedCategoryId)
        {
            $currentCategoryId   = $arCategory['ID'];
            $currentCategoryName = $arCategory['MESSAGE'];
            break;
        }
    }
}
elseif (!empty($request["CATEGORY"]))
{
    $postedCategoryId = (int) $request["CATEGORY"];

    foreach ($arCategories as $arCategory)
    {
        if ($arCategory['ID'] == $postedCategoryId)
        {
            $currentCategoryId   = $arCategory['ID'];
            $currentCategoryName = $arCategory['MESSAGE'];
            break;
        }
    }
}

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("15min", 0);
$cachePath = "/ccache_common/faq.arQuestions/";
$cacheID   = "arQuestions" .
        FORM_FAQ .
        FORM_FAQ_STATUS_PUBLIC .
        $currentCategoryName .
        $page .
        $countOnPage;

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arQuestions"]))
    {
        $arQuestions = $vars["arQuestions"];
        $lifeTime    = 0;
    }
}

if ($lifeTime > 0)
{
    $arQuestions = array();

    $arFilter = array(
        "STATUS_ID" => FORM_FAQ_STATUS_PUBLIC,
    );

    if (!empty($currentCategoryId))
    {
        $arFilter["FIELDS"] = array(
            array(
                "CODE"           => "CATEGORY",
                "PARAMETER_NAME" => "ANSWER_TEXT",
                "VALUE"          => $currentCategoryName,
                "FILTER_TYPE"    => "text",
            ),
        );
    }

    $obList  = \CFormResult::GetList(FORM_FAQ, ($by      = "s_date_create"), ($order   = "desc"), $arFilter, $is_filtered, "N");
    while ($arFetch = $obList->Fetch())
    {
        $arAnswer = \CFormResult::GetDataByID(
                        $arFetch['ID'], array(), $arResults, $arAnsweres);

        $arQuestions[] = array(
            'RESULT' => $arResults,
            'ANSWER' => $arAnswer,
        );
    }

    $totalQuestions = count($arQuestions);
    $moreCount      = $totalQuestions - $page * $countOnPage;

    $navResult                 = new \CDBResult();
    $navResult->NavPageCount   = ceil($totalQuestions / $countOnPage);
    $navResult->NavPageNomer   = $page;
    $navResult->NavNum         = 1;
    $navResult->NavPageSize    = $countOnPage;
    $navResult->NavRecordCount = count($arQuestions);

    $arQuestions = array_slice($arQuestions, ($page - 1) * $countOnPage, $countOnPage);

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arQuestions" => $arQuestions,
    ));
}
?>
<div class="faq">
    <div class="faq-top">
        <div class="faq-inner">
            <?
            $APPLICATION->IncludeComponent(
                    "bitrix:form.result.new", "faq", Array(
                "CACHE_TIME"             => "360000",
                "CACHE_TYPE"             => "A",
                "CHAIN_ITEM_LINK"        => "",
                "CHAIN_ITEM_TEXT"        => "",
                "EDIT_URL"               => "",
                "IGNORE_CUSTOM_TEMPLATE" => "Y",
                "LIST_URL"               => "",
                "SEF_MODE"               => "N",
                "SUCCESS_URL"            => "",
                "USE_EXTENDED_ERRORS"    => "Y",
                "SHOW_LIST_PAGE"         => "N",
                "SHOW_EDIT_PAGE"         => "N",
                "VARIABLE_ALIASES"       => Array("RESULT_ID" => "RESULT_ID", "WEB_FORM_ID" => "WEB_FORM_ID"),
                "WEB_FORM_ID"            => FORM_FAQ,
                "CURRENT_CATEGORY_ID"    => $currentCategoryId,
                "CURRENT_CATEGORY_NAME"  => $currentCategoryName,
                "DEFAULT_CATEGORY_NAME"  => $defaultCategoryName,
                    )
            );
            ?>
        </div>
    </div>

    <div class="faq-content">
        <div class="faq-inner">
            <div class="faq-questions">
                <div class="faq-questions-header">
                    <div class="faq-questions-header-title">Ответы</div>

                    <div class="form-question-fakeselect" data-target="CATEGORY">
                        <div
                            class="form-question-fakeselect-current"
                            onclick="Form.toggleDropdown(this)"
                            >
                            <span><?= $currentCategoryName ?></span>
                            <i class="ion-chevron-down"></i>
                        </div>

                        <ul  class="form-question-fakeselect-variants">
                            <? foreach ($arCategories as $arCategory): ?>
                                <li
                                    class="form-question-fakeselect-variants-item <?= $arCategory["ID"] == $currentCategoryId ? "selected" : "" ?> "
                                    data-category-id="<?= $arCategory["ID"] ?>"
                                    onclick="Faq.setCategory(this)"
                                    ><span><?= $arCategory["MESSAGE"] ?></span>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </div>
                </div>

                <?
                if (isPost("get_list"))
                {
                    $APPLICATION->RestartBuffer();
                }
                ?>
                <div class="faq-list">
                    <? if (!empty($arQuestions)): ?>
                        <?
                        foreach ($arQuestions as $arQuestion):
                            $arResult = $arQuestion["RESULT"];
                            $arAnswer = $arQuestion["ANSWER"];

                            $DATE_CREATE = strtotime($arResult["DATE_CREATE"]);
                            $DATE        = date("d ", $DATE_CREATE) .
                                    strtolower(ruDate("F", $DATE_CREATE)) .
                                    date(", Y", $DATE_CREATE);

                            $NAME           = $arAnswer["NAME"][0]["USER_TEXT"];
                            $PHONE          = $arAnswer["PHONE"][0]["USER_TEXT"];
                            $EMAIL          = $arAnswer["EMAIL"][0]["USER_TEXT"];
                            $QUESTION       = $arAnswer["QUESTION"][0]["USER_TEXT"];
                            $QUESTION_SHORT = mb_substr($QUESTION, 0, 333);
                            $QUESTION_MORE  = mb_substr($QUESTION, 333);

                            $ANSWER = $arAnswer["ANSWER"][0]["USER_TEXT"];
                            //$ANSWER_SHORT = mb_substr($ANSWER, 0, 333);
                            //$ANSWER_MORE  = mb_substr($ANSWER, 333);
                            ?>
                            <div class="faq-questions-item">
                                <div class="faq-questions-item-header">
                                    <span class="faq-questions-item-header-name"><?= $NAME ?></span>
                                    <i class="ion-record"></i>
                                    <span class="faq-questions-item-header-date"><?= $DATE ?></span>
                                </div>

                                <? if ($QUESTION != $QUESTION_SHORT): ?>
                                    <pre class="faq-questions-item-question"><?= $QUESTION_SHORT
                                    ?><div class="faq-questions-item-question-button">... <button onclick="Faq.showMoreText(this)">подробнее</button><? ?></div><span class="faq-questions-item-question-more"><?= $QUESTION_MORE
                                    ?></span></pre>
                                <? else:
                                    ?><pre class="faq-questions-item-question"><span><?= $QUESTION
                                    ?></span></pre>
                                <? endif; ?>

                                <? if (!empty($ANSWER)): ?>
                                    <div class="faq-questions-item-answer row">
                                        <i></i>
                                        <pre><?= $ANSWER ?></pre>
                                    </div>
                                <? endif; ?>
                            </div>
                        <? endforeach; ?>
                    <? else: ?>
                        <div class="faq-questions-item">
                            Вопросов в этой категории никто не задавал. Будьте первыми!
                        </div>
                    <? endif; ?>
                </div>

                <div class="faq-questions-more <?= $moreCount > 0 ? "" : "hidden" ?>">
                    <button
                        title="Показать еще"
                        onclick="Faq.showMore(this);"
                        data-navnum="1"
                        data-pagenomer="<?= $page ?>"
                        >
                        <mark class='faq-questions-more-spinner'><i></i><i></i><i></i></mark>Показать еще
                    </button>
                </div>

                <? if ($totalQuestions > $countOnPage): ?>
                    <div class="faq-questions-pagination">
                        <?
                        $APPLICATION->IncludeComponent('bitrix:system.pagenavigation', 'catalog', array(
                            'NAV_RESULT' => $navResult,
                        ));
                        ?>
                    </div>
                <? endif; ?>

                <?
                if (isPost("get_list"))
                {
                    die();
                }
                ?>
            </div>
        </div>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>