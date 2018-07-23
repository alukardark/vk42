<?php

/**
 * Взято отсюда
 * https://www.olegpro.ru/post/1s_bitriks_dobavlyaem_v_morfologicheskiy_indeks_poiska_vozmozhnost_iskat_po_chasti_slova.html
 *
 * Дает возможность искать по части слова для указанных инфоблоков
 */
/**
 * Created by olegpro.ru
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 */

namespace Axi\Handlers;

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;

class Search
{

    /**
     * @var array
     */
    protected static $allowedIblockId = array(
        TIRES_IB
    );

    /**
     * @var
     */
    private static $element;

    /**
     * @param array $arFields
     * @return mixed
     */
    public static function beforeIndex($arFields)
    {
        return;
        static $allowedIblockId = null;

        if ($allowedIblockId === null)
        {
            $allowedIblockId = array_flip(self::$allowedIblockId);
        }

        if ($arFields['MODULE_ID'] == 'iblock' && isset($allowedIblockId[$arFields['PARAM2']]) && strlen($arFields['ITEM_ID']) > 0 && substr($arFields['ITEM_ID'], 0, 1) != 'S' && Loader::includeModule('iblock'))
        {
            if ($obElement = \CIBlockElement::GetList(array(), array('ID' => $arFields['ITEM_ID']), false, false, array())->GetNextElement())
            {
                $element               = $obElement->GetFields();
                $element['PROPERTIES'] = $obElement->GetProperties();
                self::$element         = $element;
            }
        }
        return $arFields;
    }

    /**
     * Events: OnBeforeIndexUpdate and OnAfterIndexAdd
     * @param $indexId
     * @param $arFields
     */
    public static function beforeIndexUpdate($indexId, $arFields)
    {
        return;
        if (empty(self::$element))
        {
            return;
        }

        if (!empty(self::$element) && is_array(self::$element) && !empty(self::$element['PROPERTIES']['CML2_ARTICLE']['VALUE']))
        {
            self::addCustomStems(self::$element['NAME'], $indexId);
            self::addCustomStems(self::$element['PROPERTIES']['CML2_ARTICLE']['VALUE'], $indexId);
            self::addCustomStems(self::$element['PROPERTIES']['MARKA']['VALUE'], $indexId);
            self::addCustomStems(self::$element['PROPERTIES']['MODEL']['VALUE'], $indexId);
            self::addCustomStems(self::$element['PROPERTIES']['RAZMER']['VALUE'], $indexId);
            self::addCustomStems(self::$element['PROPERTIES']['SEZON']['VALUE'], $indexId);
        }

        self::$element = null;
    }

    private static function addCustomStems($string, $indexId)
    {
        if (empty($string) || is_array($string))
        {
            return;
        }

        preg_match('~^.*?([0-9.,]+).*?$~', trim($string), $m);
        $word   = ToUpper($m[1]);
        $stemId = \CSearch::RegisterStem($word);

        if ($stemId > 0)
        {
            $connection = Application::getConnection();
            $sqlHelper  = $connection->getSqlHelper();

            try
            {
                $thereIs = $connection->queryScalar(sprintf("SELECT 1 FROM b_search_content_stem WHERE SEARCH_CONTENT_ID = '%s' AND STEM = '%s'", $sqlHelper->forSql($indexId), \CSearch::RegisterStem($word)));

                if ($thereIs === null)
                {
                    $connection->query(sprintf("INSERT IGNORE INTO `b_search_content_stem` (`SEARCH_CONTENT_ID`, `LANGUAGE_ID`, `STEM`, `TF`, `PS`) VALUES ('%s', '%s', '%s', '%s', '%s')", $sqlHelper->forSql($indexId), 'ru', \CSearch::RegisterStem($word), number_format(0.2, 4, ".", ""), number_format(1, 4, ".", "")));
                }
            }
            catch (SqlQueryException $e)
            {
                AddMessage2Log(sprintf("\\%s:\n%s", __METHOD__, $e->getMessage()));
            }
        }
    }

}
