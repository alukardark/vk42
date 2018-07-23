<?php

/**
 * 
 * @global $USER
 * @param $var
 * @param $only_admin
 * @param $exit
 * @param $return
 * @return boolean|string
 */
function dmp($var, $only_admin = true, $exit = true, $return = false)
{
    global $USER;
    if ($only_admin && (empty($USER) || $USER->GetID() != 1)) return false;

    $backtrace = debug_backtrace();
    $id        = "dddump" . md5(mt_rand(0, 99999));
    $out       = '<script>function dspl(id){$("#"+id).slideToggle(0);}</script>';
    $out       .= '<div id="' . $id . '" style="position:fixed;top:10px;right:10px;z-index:99998;width:750px;height:500px;">';
    $out       .= '<a style="position:absolute;right:5px;top:5px;padding:5px;background-color:#000;color:red;" href="javascript:dspl(\'' . $id . '\');">x</a>';
    $out       .= "<div style='font-size:11px;background-color: #000;color:#fff;padding:5px 15px;'>" . $backtrace[0]['file'] . " (" . $backtrace[0]['line'] . ")</div>";
    $out       .= '<textarea style="font-family:Courier;line-height:1;font-size:14px;background-color:#000;color:#fff;'
            . 'width:100%;height:100%;padding:10px;white-space:nowrap;overflow:auto;">'
            . htmlspecialchars(print_r($var, true))
            . '</textarea>'
            . '</div>';
    if ($exit != false) die($out);
    if ($return) return $out;
    echo $out;
}

function ddmp($var, $only_admin = false, $die = false)
{
    global $USER;
    if ($only_admin && (empty($USER) || $USER->GetID() != 1)) return false;

    echo "<pre style='font-family: Verdana; background: black; color: white; white-space: pre-wrap; position: relative; z-index: 1000;'>";
    var_dump($var);
    echo "</pre>";
    if ($die) {
        die();
    }
}

/**
 * Переделка битриксовой функции check_bitrix_sessid
 * @return mixed
 */
function vk_check_bitrix_sessid($data)
{
    return $data[VK_SESSID] == bitrix_sessid();
}

function printra()
{
    global $APPLICATION;
    if (is_object($APPLICATION) && IntVal($APPLICATION->buffered))
    {
        $APPLICATION->RestartBuffer();
    }

    $backtrace = debug_backtrace();
    echo "<pre dddump>" . $backtrace[0]['file'] . " on line " . $backtrace[0]['line'] . "</pre>";

    printr(func_get_args());
    die();
}

function pprintra()
{
    if (isPost())
    {
        global $APPLICATION;
        if (is_object($APPLICATION) && IntVal($APPLICATION->buffered))
        {
            $APPLICATION->RestartBuffer();
        }

        $backtrace = debug_backtrace();
        echo "<pre dddump>" . $backtrace[0]['file'] . " on line " . $backtrace[0]['line'] . "</pre>";

        printr(func_get_args());
        die();
    }
}

function printrau()
{
    global $USER;
    $USER_ID = !empty($USER) ? (int) $USER->GetId() : null;

    if ($USER_ID === 1)
    {
        $backtrace = debug_backtrace();
        echo "<pre dddump>" . $backtrace[0]['file'] . " on line " . $backtrace[0]['line'] . "</pre>";

        printra(func_get_args());
    }
}

function printrak()
{
    if ($_REQUEST['k'] == 'k')
    {
        $backtrace = debug_backtrace();
        echo "<pre dddump>" . $backtrace[0]['file'] . " on line " . $backtrace[0]['line'] . "</pre>";

        printra(func_get_args());
    }
}

function pprintrau()
{
    if (isPost())
    {
        global $USER;
        $USER_ID = !empty($USER) ? (int) $USER->GetId() : null;

        if ($USER_ID === 1)
        {
            $backtrace = debug_backtrace();
            echo "<pre dddump>" . $backtrace[0]['file'] . " on line " . $backtrace[0]['line'] . "</pre>";

            printra(func_get_args());
        }
    }
}

function printr()
{
    echo "<pre dddump>";
    $arPrint = func_get_args();
    if (count($arPrint) == 1)
    {
        print_r($arPrint[0][0]);
    }
    else print_r($arPrint[0]);
    echo "</pre>";
}

function startsWith($haystack, $needle)
{
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

// разбиваем текст на "слова" по пробельным символам
function explodeText($text, $length, $striptags = false)
{
    if ($striptags) $text = strip_tags($text);

    preg_match_all("/[^\s]+/i", $text, $matches);
    $text_arr = $matches[0];

// объединяем те "слова", которые содержатся внутри тегов
    $text_arr2 = array();
    $text_tmp  = array();
    $tag_open  = $tag_close = 0;
    foreach ($text_arr as $word)
    {
        $tag_open   += preg_match_all("/<[^\/]+>/Ui", $word, $matches);
        $tag_close  += preg_match_all("/<\/.+>/Ui", $word, $matches);
        $text_tmp[] = $word;
        if ($tag_open == $tag_close)
        {
            $text_arr2[] = implode(" ", $text_tmp);
            $text_tmp    = array();
        }
    }

// вычисляем кол-во слов для каждой части текста
    $result_len          = mb_strlen($text_arr2[0]);
    $result_num_words[0] = 1;
    $part                = 0;
    foreach (array_slice($text_arr2, 1) as $word)
    {
        $word_len = mb_strlen($word);
        if ($result_len + $word_len + $result_num_words[$part] > $length)
        {
            $part++;
            $result_len              = $word_len;
            $result_num_words[$part] = 1;
            continue;
        }
        $result_len += $word_len;
        $result_num_words[$part] ++;
    }

// разбиваем текст на части
    $result = array();
    $offset = 0;
    foreach ($result_num_words as $rnw)
    {
        $result[] = implode(" ", array_slice($text_arr2, $offset, $rnw));
        $offset   += $rnw;
    }

    return implode(" ", $result);
}

function array_insert(&$array, $position, $insert)
{
    if (is_int($position)) array_splice($array, $position, 0, $insert);
    else
    {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(array_slice($array, 0, $pos), $insert, array_slice($array, $pos));
    }
}

function GetRelativePath($sPath)
{
    return substr_count($sPath, $_SERVER["DOCUMENT_ROOT"]) ? str_replace($_SERVER["DOCUMENT_ROOT"], "", $sPath) : $sPath;
}

/**
 * $arFilter должен быть таким, чтобы функция вернула только один результат.
 * По умолчанию возвращается массив, описывающий базовую цену
 * @param array $arFilter
 * @param bool $get что вернуть (ID, NAME, etc.)
 * @return array|string
 */
function getPrice($arFilter = null, $get = false)
{
    if (empty($arFilter))
    {
        $arFilter = array("BASE" => "Y");
    }

    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("30day", 0);
    $cachePath = "/ccache_common/getPrice/";
    $cacheID   = "getPrice" . serialize($arFilter);

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arGroup"]))
        {
            $arGroup  = $vars["arGroup"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $obList  = \CCatalogGroup::GetList(array(), $arFilter);
        $arGroup = $obList->Fetch();

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arGroup" => $arGroup,
        ));
    }

    return empty($get) || !array_key_exists($get, $arGroup) ? $arGroup : $arGroup[$get];
}

function basePrice($get = "ID")
{
    return getPrice(array("BASE" => "Y"), $get);
}

function F($s)
{
    return CurrencyFormat($s, "RUB");
}

function ruDate()
{
    $translation = array(
        "am"        => "дп",
        "pm"        => "пп",
        "AM"        => "ДП",
        "PM"        => "ПП",
        "Monday"    => "Понедельник",
        "Mon"       => "Пн",
        "Tuesday"   => "Вторник",
        "Tue"       => "Вт",
        "Wednesday" => "Среда",
        "Wed"       => "Ср",
        "Thursday"  => "Четверг",
        "Thu"       => "Чт",
        "Friday"    => "Пятница",
        "Fri"       => "Пт",
        "Saturday"  => "Суббота",
        "Sat"       => "Сб",
        "Sunday"    => "Воскресенье",
        "Sun"       => "Вс",
        "January"   => "Января",
        "Jan"       => "Янв",
        "February"  => "Февраля",
        "Feb"       => "Фев",
        "March"     => "Марта",
        "Mar"       => "Мар",
        "April"     => "Апреля",
        "Apr"       => "Апр",
        "May"       => "Мая",
        "May"       => "Мая",
        "June"      => "Июня",
        "Jun"       => "Июн",
        "July"      => "Июля",
        "Jul"       => "Июл",
        "August"    => "Августа",
        "Aug"       => "Авг",
        "September" => "Сентября",
        "Sep"       => "Сен",
        "October"   => "Октября",
        "Oct"       => "Окт",
        "November"  => "Ноября",
        "Nov"       => "Ноя",
        "December"  => "Декабря",
        "Dec"       => "Дек",
        "st"        => "ое",
        "nd"        => "ое",
        "rd"        => "е",
        "th"        => "ое",
        "01"        => 'Января',
        "02"        => 'Февраля',
        "03"        => 'Марта',
        "04"        => 'Апреля',
        "05"        => 'Мая',
        "06"        => 'Июня',
        "07"        => 'Июля',
        "08"        => 'Августа',
        "09"        => 'Сентября',
        "10"        => 'Октября',
        "11"        => 'Ноября',
        "12"        => 'Декабря',
    );
    if (func_num_args() > 1)
    {
        $timestamp = func_get_arg(1);
        return strtr(date(func_get_arg(0), $timestamp), $translation);
    }
    else
    {
        return strtr(date(func_get_arg(0)), $translation);
    }
}

function array_key_istrue_multi($key, $array)
{
    if (!is_array($array)) return false;
    foreach ($array as $item)
    {
        if (isset($item[$key]) && $item[$key]) return true;
    }
    return false;
}

/**
 * передвигает элемент массива в его начало
 */
function array_move_key_first($key, &$array)
{
    $array = array($key => $array[$key]) + $array;
}

/**
 * @param type $value
 * @param type $texts array("день", "дня", "дней")
 * @return type
 */
function wordPlural($value, $texts)
{
    $value = intval($value);

    if ($value % 10 === 1 && ($value < 10 || $value > 20)) return $texts[0];
    if (($value % 10 === 2 || $value % 10 === 3 || $value % 10 === 4) && ($value < 10 || $value > 20)) return $texts[1];
    return $texts[2];
}

/**
 * Определяет юзерагент
 * @return string
 */
function getUA()
{
    $keyname_ua_arr = array(
        'HTTP_X_ORIGINAL_USER_AGENT',
        'HTTP_X_DEVICE_USER_AGENT',
        'HTTP_X_OPERAMINI_PHONE_UA',
        'HTTP_X_BOLT_PHONE_UA',
        'HTTP_X_MOBILE_UA',
        'HTTP_USER_AGENT');
    foreach ($keyname_ua_arr as $keyname_ua)
    {
        if (!empty($_SERVER[$keyname_ua]))
        {
            return $_SERVER[$keyname_ua];
        }
    }
    return 'Unknown';
}

function isBot()
{
    $user_agent    = getUA();
    $mobile_agents = array("bot", "spider", "archiver", "php", "python", "perl", "wordpress", "crawl", "vkexport");
    foreach ($mobile_agents as $device)
    {
        if (stristr($user_agent, $device))
        {
            return true;
        }
    }
    return false;
}

function is_iphone()
{
    $user_agent    = getUA();
    $mobile_agents = array("iphone", "ipad", "ipod");
    foreach ($mobile_agents as $device)
    {
        if (stristr($user_agent, $device))
        {
            return true;
        }
    }
    return false;
}

function is_winphone()
{
    $user_agent    = getUA();
    $mobile_agents = array("Windows Phone", "IEMobile", "Edge/1");
    foreach ($mobile_agents as $device)
    {
        if (stristr($user_agent, $device))
        {
            return true;
        }
    }
    return false;
}

function is_android()
{
    $user_agent    = getUA();
    $mobile_agents = array("android");
    foreach ($mobile_agents as $device)
    {
        if (stristr($user_agent, $device))
        {
            return true;
        }
    }
    return false;
}

function getViewportMetaTag()
{
    if (is_iphone())
    {
        return '<meta name="viewport" content="width=1024" />' . PHP_EOL;
    }

    if (is_winphone())
    {
        return;
    }

    if (is_android())
    {
        return '<meta name="viewport" content="width=100%" />' . PHP_EOL;
    }

    return '<meta name="viewport" content="width=device-width">' . PHP_EOL;
}

function download($file, $fileName)
{
    if (ob_get_level())
    {
        ob_end_clean();
    }

    // Определим IE
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
    {
        $fileName = rawurlencode($fileName);
    }
    $fileName = '"' . $fileName . '"';

    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream;');
    header('Content-Disposition: attachment; filename=' . $fileName);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    // читаем файл и отправляем его пользователю
    readfile($file);
    exit;
}

/**
 * Очищает все элементы конкретного инфоблока
 * @param int $ID
 * @param int $nTopCount ограничить количество сверху
 */
function clear_ib($ID, $nTopCount = false)
{
    $res = false;

    $arFilter = array('IBLOCK_ID' => $ID);
    if ($nTopCount != false)
    {
        $nTopCount = array("nTopCount" => $nTopCount);
    }
    $rsItems = \CIBlockElement::GetList(array(), $arFilter, false, $nTopCount, array('IBLOCK_ID', 'ID'));
    while ($arItem  = $rsItems->Fetch())
    {
        $res = true;
        \CIBlockElement::Delete($arItem['ID']);
    }

    return $res;
}

/**
 * Деактивирует все элементы и разделы инфоблока
 * @param int $IBLOCK_ID
 */
function deactivateIBlock($IBLOCK_ID)
{
    $IBlockSection = new \CIBlockSection;
    $IBlockElement = new \CIBlockElement;

    $arFilter = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y");
    $obList   = \CIBlockSection::GetList(array(), $arFilter, false, array('ID', 'IBLOCK_ID'));
    while ($arFetch  = $obList->Fetch())
    {
        $IBlockSection->Update($arFetch["ID"], array("ACTIVE" => "N"));
    }

    $arFilter = array('IBLOCK_ID' => $IBLOCK_ID, "ACTIVE" => "Y");
    $obList   = \CIBlockElement::GetList(array(), $arFilter, false, false, array('ID', 'IBLOCK_ID'));
    while ($arFetch  = $obList->Fetch())
    {
        $IBlockElement->Update($arFetch["ID"], array("ACTIVE" => "N"));
    }
}

function getCountElements($ID)
{
    $arFilter  = array('IBLOCK_ID' => $ID);
    $res_count = \CIBlockElement::GetList(Array(), $arFilter, Array(), false, Array());

    return $res_count;
}

function json_result($success, $result = null)
{
    \Bitrix\Main\Application::getInstance()->getContext()->getResponse()->flush();
    die(json_encode(array('success' => $success, 'result' => $result)));
}

function NormalizeLink($sURL = '')
{
    $sNewURL = preg_replace('#\/{2,}#', '/', $sURL);

    return $sNewURL;
}

function SplitSQL($file, $delimiter = ';')
{
    set_time_limit(0);
    global $DB;

    if (is_file($file) === true)
    {
        $file = fopen($file, 'r');

        if (is_resource($file) === true)
        {
            $query = array();

            while (feof($file) === false)
            {
                $query[] = fgets($file);

                if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
                {
                    $query = trim(implode('', $query));

                    /* if (mysql_query($query) === false)
                      {
                      echo '<h3>ERROR: ' . $query . '</h3>' . "\n";
                      }
                      else
                      {
                      echo '<h3>SUCCESS: ' . $query . '</h3>' . "\n";
                      } */

                    $DB->Query($query);

                    while (ob_get_level() > 0)
                    {
                        ob_end_flush();
                    }

                    flush();
                }

                if (is_string($query) === true)
                {
                    $query = array();
                }
            }

            return fclose($file);
        }
    }

    return false;
}

/**
 * Сохраняет base64 строку в картинку с регистрацией в БД через API битрикса
 * @param type $base64String
 * @param string $path руть до папки внутри /upload/
 * @param string $fileName имя файла
 * @return int ID созданного файла
 */
function base64ToFile($base64String, $path, $fileName, $oldId = false)
{
    //полный  путь до временного файла
    $dir = $_SERVER["DOCUMENT_ROOT"] . '/upload/' . $path;

    if (!file_exists($dir))
    {
        mkdir($dir, 0777, true);
    }

    $fullPath = NormalizeLink($dir . '/' . $fileName);

    $fp = fopen($fullPath, "wb");
    fwrite($fp, base64_decode($base64String));
    fclose($fp);

    $arFile = \CFile::MakeFileArray($fullPath);

    $arIMAGE = (array) $arFile + array(
        "old_file"  => $oldId,
        "del"       => "Y",
        "MODULE_ID" => "iblock"
    );

    $fid = \CFile::SaveFile($arIMAGE, $path);

    return $fid;
}

/**
 * 
 * пример для складов
 * @param type $entityCode код объекта ("CAT_STORE")
 * @param type $id id склада
 * @param type $ufCode символьный код поля (всегда начинается с "UF_" )
 * @return type
 */
function getUF($entityCode, $id, $ufCode)
{
    $arUF = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields($entityCode, $id);
    return $arUF[$ufCode]["VALUE"];
}

//function getUFEnum($id)
//{
//    $UserField   = \CUserFieldEnum::GetList(array(), array("ID" => $id));
//    if ($UserFieldAr = $UserField->GetNext())
//    {
//        return $UserFieldAr["VALUE"];
//    }
//    else return false;
//}

function setUF($entityCode, $id, $arFields)
{
    $GLOBALS["USER_FIELD_MANAGER"]->Update($entityCode, $id, $arFields);
}

function getHLEelementByXML_ID($HB, $XML_ID)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getHLEelementByXML_ID/";
    $cacheID   = "getHLEelementByXML_ID" . $HB . $XML_ID;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arFields"]))
        {
            $arFields = $vars["arFields"];
            //$lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HB)->fetch();
        $entity  = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock)->getDataClass();

        $rsData = $entity::getList(array(
                    //'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
                    'limit'  => '1',
                    'filter' => array('UF_XML_ID' => $XML_ID),
        ));

        $arFields = $rsData->Fetch();

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arFields" => $arFields,
        ));
    }


    return $arFields;
}

function timerGet()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

/**
 * выводит время работы куска кода от предыдущего вызова timerFlag() до текущего
 */
function timerFlag($round = 4, $debug = true, $adminOnly = false)
{
    global $timer, $timer_iteration, $USER;

    if ($adminOnly && !$USER->IsAdmin()) return;

    if (empty($timer)) $timer           = timerGet();
    if (empty($timer_iteration)) $timer_iteration = 0;
    $old_timer       = $timer;
    $timer           = timerGet();

    if ($debug)
    {
        $backtrace = debug_backtrace();
        $dbg_nfo   = " " . $backtrace[0]['file'] . " (" . $backtrace[0]['line'] . ") [i = " . $timer_iteration . "]";
    }
    $timer_iteration++;

    $razn = round($timer - $old_timer, $round);
    if ($razn > 0.5) $razn = "<b>$razn</b>";
    echo $razn;

    if ($debug)
    {
        echo $dbg_nfo;
    }

    echo '<br/>';
}

if (!function_exists('mb_ucfirst'))
{

    function mb_ucfirst($string, $encoding = null)
    {
        $encoding  = is_null($encoding) ? mb_detect_encoding($string) : $encoding;
        $strlen    = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then      = mb_substr($string, 1, $strlen - 1, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

}

function is_array_has_next($array)
{
    !is_array($array) ? false : (next($array) === false ? false : true);
}

function is_array_has_prev($array)
{
    !is_array($array) ? false : (prev($array) === false ? false : true);
}

function get_last_array_key($array)
{
    end($array);         // move the internal pointer to the end of the array
    return key($array);  // fetches the key of the element pointed to by the internal pointer
}

function isPost($action = false)
{
    if ($action === false)
    {
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isPost();
    }
    elseif ($action === true)
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        return $request->isPost() && ($request->getPost("AJAX") == "Y" || $request->getPost("VK_AJAX") == "Y");
    }
    else
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        return $request->isPost() &&
                ($request->getPost("AJAX") == "Y" || $request->getPost("VK_AJAX") == "Y") &&
                ($request->getPost("ACTION") == $action || $request->getPost("VK_ACTION") == $action);
    }
}

function fixPhoneNumber($sPhone)
{
    return NormalizePhone($sPhone, 6);
    //return getPhoneFromString($sPhone, true);
    //return $sPhone;
}

/**
 * удаляет начальные и конечные пробелы, неразрывные пробелы, табуляции, переносы строк и т.д. и т.п.
 * @param type $str
 * @return type
 */
function allTrim($str)
{
    $str = str_replace("\xA0", " ", $str);
    $str = preg_replace("/\s+/", " ", $str);
    $str = preg_replace('~\x{00a0}~siu', ' ', $str);

    return trim($str);
}

function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Метод возвращает либо массив либо строку в заисимости от параметра $oneResult<br/>
 * массив = [+7 (xxx) xxx-xx-xx, 7xxxxxxxxxx, 8xxxxxxxxxx]<br/>
 * строка = +7 (xxx) xxx-xx-xx
 * @param type $string
 * @param type $oneResult
 * @return boolean
 */
function getPhoneFromString($string, $oneResult = false, $strong = false)
{
    //сперва вырежем из строки все кроме цифр
    $string = preg_replace("/[^0-9]/", "", $string);

    //убираем первую цифру
    if (strlen($string) == 11)
    {
        $string = substr($string, 1);
    }

    $string = intval($string);

    if ($string > 0 && strlen($string) == 10)
    {
        if ($strong)
        {
            return $string;
        }

        //+7 (923) 666-55-44
        $phone1 = "+7 ("
                . substr($string, 0, 3) . ") "
                . substr($string, 3, 3) . "-"
                . substr($string, 6, 2) . "-"
                . substr($string, 8, 2);

        //79236665544
        $phone2 = "7" . $string;

        //89236665544
        $phone3 = "8" . $string;

        return $oneResult ? $phone1 : array($phone1, $phone2, $phone3);
    }

    return false;
}

function printPrice($iPrice, $sCode = PUBL_CURRENCY, $precision = 0)
{
    return CurrencyFormat(round($iPrice, $precision), $sCode);
}

function getSiteInfo($siteId = SITE_ID)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getSiteInfo/";
    $cacheID   = "getSiteInfo" . $siteId;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arSite"]))
        {
            $arSite   = $vars["arSite"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $arSite = \CSite::GetByID($siteId)->Fetch();

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arSite" => $arSite,
        ));
    }

    return $arSite;
}

function getIBlockInfo($IBLOCK_ID)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getIBlockInfo/";
    $cacheID   = "getIBlockInfo" . $IBLOCK_ID;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arProps"]))
        {
            $arProps  = $vars["arProps"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $obList  = \CIBlock::GetList(Array(), Array('ID' => $IBLOCK_ID));
        $arProps = $obList->Fetch();

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arProps" => $arProps,
        ));
    }

    return $arProps;
}

/**
 * 
 * @param type $path относительно папки /bitrix/cache/ (если не задан параметр $siteCache)
 * @param type $siteCache если TRUE, то $path относительно папки /bitrix/cache/SITE_ID/bitrix/
 */
function clearCache($path, $siteCache = false)
{
    $dir = $siteCache ? SITE_ID . '/bitrix/' . $path : $path;

    BXClearCache(true, $dir);
}

function ShowCondTitle()
{
    global $APPLICATION;


    $altTitle = $APPLICATION->GetTitle('alt_title', true);

    if (!empty($altTitle))
    {
        echo $altTitle;
    }
    else
    {
        $APPLICATION->ShowTitle(false, true);
    }
}

function getParentSection($SECTION, $iDepthLevel = 1, $arSelect = Array("ID", "CODE", "SECTION_PAGE_URL"))
{
    if (!in_array("ID", $arSelect)) $arSelect[] = "ID";

    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getParentSection/";
    $cacheID   = "getParentSection" . $SECTION . $iDepthLevel . $arSelect;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arParent"]))
        {
            $arParent = $vars["arParent"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $arParent  = false;
        $obSection = \CIBlockSection::GetList(Array(), Array(
                    "CODE" => $SECTION,
                        ), false, Array("IBLOCK_ID", "LEFT_MARGIN", "RIGHT_MARGIN"));

        if ($arSection = $obSection->Fetch())
        {
            $arFilter = Array(
                "IBLOCK_ID"      => $arSection["IBLOCK_ID"],
                "DEPTH_LEVEL"    => $iDepthLevel,
                "<=LEFT_BORDER"  => $arSection["LEFT_MARGIN"],
                ">=RIGHT_BORDER" => $arSection["RIGHT_MARGIN"]
            );

            $obParent = \CIBlockSection::GetList(Array("LEFT_MARGIN" => "ASC"), $arFilter, false, $arSelect);
            $arParent = $obParent->Fetch();
        }

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arParent" => $arParent,
        ));
    }

    return $arParent;
}

function getSection($IBLOCK_ID, $SECTION)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getSection/";
    $cacheID   = "getSection" . $IBLOCK_ID . $SECTION;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arParent"]))
        {
            $arResult = $vars["arResult"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        if (is_numeric($SECTION))
        {
            $arFilter = array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "ID"        => $SECTION,
            );
        }
        else
        {
            $arFilter = array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "CODE"      => $SECTION,
            );
        }

        $arResult  = false;
        $obSection = \CIBlockSection::GetList(Array(), $arFilter);
        if ($arSection = $obSection->Fetch())
        {
            $arResult = $arSection;
        }

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arResult" => $arResult,
        ));
    }

    return $arResult;
}

function getIBlockByElement($ELEMENT_ID)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getIBlockByElement/";
    $cacheID   = "getIBlockByElement" . $ELEMENT_ID;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["IBLOCK_ID"]))
        {
            $IBLOCK_ID = $vars["IBLOCK_ID"];
            $lifeTime  = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $IBLOCK_ID = false;

        $obList = \CIBlockElement::GetList(Array(), Array(
                    "ID" => $ELEMENT_ID,
                        ), false, false, array("ID", "IBLOCK_ID"));

        if ($arFetch = $obList->Fetch())
        {
            $IBLOCK_ID = $arFetch["IBLOCK_ID"];
        }

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "IBLOCK_ID" => $IBLOCK_ID,
        ));
    }

    return $IBLOCK_ID;
}

function getIBlockProperties($IBLOCK_ID)
{
    $obCache   = \Bitrix\Main\Data\Cache::createInstance();
    $lifeTime  = strtotime("1day", 0);
    $cachePath = "/ccache_common/getIBlockProperties/";
    $cacheID   = "getIBlockProperties" . $IBLOCK_ID;

    if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
    {
        $vars = $obCache->GetVars();
        if (isset($vars["arProps"]))
        {
            $arProps  = $vars["arProps"];
            $lifeTime = 0;
        }
    }

    if ($lifeTime > 0)
    {
        $arProps = array();

        $obList  = \CIBlock::GetProperties($IBLOCK_ID);
        while ($arFetch = $obList->Fetch())
        {
            $arProps[$arFetch["ID"]] = $arFetch;
        }

        //кешируем
        $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
        $obCache->EndDataCache(array(
            "arProps" => $arProps,
        ));
    }

    return $arProps;
}

function AdminException($arErrors)
{
    global $APPLICATION;

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

    return true;
}

function getHash($action, $string)
{
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key     = 'Vf5dfddHEhSDfdgF';
    $secret_iv      = 'fdddgVHEhfSDf5dF';

    $key = hash('sha256', $secret_key);
    $iv  = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt')
    {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    }

    if ($action == 'decrypt')
    {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

/**
 * Удаляет все файлы из папки. Полный путь, в конце - слеш
 * @param type $dir
 */
function clearPath($dir)
{
    if (is_dir($dir))
    {
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if ($object != "." && $object != "..")
            {
                if (is_dir($dir . "/" . $object)) clearPath($dir . "/" . $object);
                else unlink($dir . "/" . $object);
            }
        }

        rmdir($dir);
    }
}

function if_in_array($needle, $haystack)
{
    return is_array($haystack) && in_array($needle, $haystack);
}

function if_array_key_exists($key, $array)
{
    return is_array($array) && array_key_exists($key, $array);
}

function isAdmin()
{
    global $USER;

    if (!$USER->IsAuthorized())
    {
        return false;
    }

    $USER_ID = $USER->GetID();

    $arIgnore = SITE_TEST ? array() : array(3, 76874, 77064, 76849,);
    $arAdmins = SITE_TEST ? array(1, 4, 76693) : array(1, 4, 76693,);

    if (in_array($USER_ID, $arIgnore))
    {
        return false;
    }

    if (in_array($USER_ID, $arAdmins))
    {
        return true;
    }

    if ($USER->IsAdmin())
    {
        return true;
    }
}

function cutAllButNumbers($string)
{
    return preg_replace("/[^0-9]/", "", $string);
}

function removeDuplicates($string)
{
    $arWords = explode(" ", $string);

    $alreadyUsed = array();
    foreach ($arWords as $k => &$word)
    {
        $wordLowerCase = mb_strtolower($word);

        if (!in_array($wordLowerCase, $alreadyUsed))
        {
            $alreadyUsed[] = $wordLowerCase;
        }
        else
        {
            unset($arWords[$k]);
        }
    }

    $result = implode(" ", $arWords);

    return $result;
}
