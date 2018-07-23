<?

//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
//ini_set('display_errors', 1);
ini_set('max_execution_time', 90);

class CURL
{

    const VK_LOGIN = "Client1C";
    const VK_PASSW = "F9KS6g@";

    private static $VK_URL, $VK_PORT;
    private static $arConfig = array(
        CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; web-axioma-curl-bot)',
        CURLOPT_REFERER        => 'http://cs42.ru',
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_MAXREDIRS      => 4,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_BINARYTRANSFER => false,
        CURLOPT_HEADER         => false,
        CURLOPT_NOBODY         => false,
    );

    /**
     * 
     * @param type $sMethod
     * @param type $arData
     * @param type $onlyPost
     * @param type $debug
     * @param type $long
     * @param type $hard_work принудительно отправить запрос в боевую базу
     * @return type
     */
    public static function getReplay($sMethod = '', $arData = array(), $onlyPost = false, $debug = false, $long = false, $hard_work = false, $servise = "Exchange", $ttt = false)
    {
        if (SITE_TEST) return;

        $test = $_SERVER['HTTP_HOST'] == "vk.axiomatest.ru";

        $SERVICE_URL  = $test && !$hard_work ? "http://46.181.49.50/Copy_KA/hs/$servise/" : "http://46.181.49.50/KA/hs/$servise/";
        $CURLOPT_PORT = $test && !$hard_work ? 60080 : 60081;

        $CURLOPT_URL = $onlyPost ?
                $SERVICE_URL . $sMethod :
                $SERVICE_URL . $sMethod . '?' . http_build_query($arData);

        $obCurl = curl_init();

        //if ($ttt)printra();

        curl_setopt($obCurl, CURLOPT_URL, $CURLOPT_URL);
        curl_setopt($obCurl, CURLOPT_PORT, $CURLOPT_PORT);
        curl_setopt($obCurl, CURLOPT_USERPWD, self::VK_LOGIN . ':' . self::VK_PASSW);
        curl_setopt($obCurl, CURLOPT_POST, true);
        curl_setopt($obCurl, CURLOPT_POSTFIELDS, http_build_query($arData));

        if ($long)
        {
            self::$arConfig[CURLOPT_TIMEOUT]        = 180; //10 min
            self::$arConfig[CURLOPT_CONNECTTIMEOUT] = 180; //10 min
        }

        foreach (self::$arConfig as $key => $value)
        {
            curl_setopt($obCurl, $key, $value);
        }

        AddMessage2Log($CURLOPT_URL . ':' . $CURLOPT_PORT, "", 2);

        $replay = curl_exec($obCurl);

        $info    = curl_getinfo($obCurl);
        $error   = curl_error($obCurl);
        $version = curl_version();

        curl_close($obCurl);

        if ($debug != false)
        {
            return array(
                'replay_original' => $replay,
                'replay'          => json_decode($replay, true),
                'curl_error'      => $error,
                'curl_getinfo'    => $info,
                'curl_version'    => $version,
                'post'            => $arData
            );
        }

        return json_decode($replay, true);
    }
    
    public static function getReplayTest($sMethod = '', $arData = array(), $onlyPost = false, $debug = false, $long = false, $hard_work = false, $servise = "Exchange", $ttt = false)
    {
        //if (SITE_TEST) return;

        $test = $_SERVER['HTTP_HOST'] == "vk.axiomatest.ru";

        $SERVICE_URL  = $test && !$hard_work ? "http://46.181.49.50/Test_KA/hs/$servise/" : "http://46.181.49.50/KA/hs/$servise/";
        $CURLOPT_PORT = $test && !$hard_work ? 60080 : 60081;

        $CURLOPT_URL = $onlyPost ?
                $SERVICE_URL . $sMethod :
                $SERVICE_URL . $sMethod . '?' . http_build_query($arData);

        $obCurl = curl_init();

        //if ($ttt)printra();

        curl_setopt($obCurl, CURLOPT_URL, $CURLOPT_URL);
        curl_setopt($obCurl, CURLOPT_PORT, $CURLOPT_PORT);
        curl_setopt($obCurl, CURLOPT_USERPWD, self::VK_LOGIN . ':' . self::VK_PASSW);
        curl_setopt($obCurl, CURLOPT_POST, true);
        curl_setopt($obCurl, CURLOPT_POSTFIELDS, http_build_query($arData));

        if ($long)
        {
            self::$arConfig[CURLOPT_TIMEOUT]        = 180; //10 min
            self::$arConfig[CURLOPT_CONNECTTIMEOUT] = 180; //10 min
        }

        foreach (self::$arConfig as $key => $value)
        {
            curl_setopt($obCurl, $key, $value);
        }

        AddMessage2Log($CURLOPT_URL . ':' . $CURLOPT_PORT, "", 2);

        $replay = curl_exec($obCurl);

        $info    = curl_getinfo($obCurl);
        $error   = curl_error($obCurl);
        $version = curl_version();

        curl_close($obCurl);

        if ($debug != false)
        {
            return array(
                'replay_original' => $replay,
                'replay'          => json_decode($replay, true),
                'curl_error'      => $error,
                'curl_getinfo'    => $info,
                'curl_version'    => $version,
                'post'            => $arData
            );
        }

        return json_decode($replay, true);
    }

    public static function sendPost($sUrl, $arData = false, $auth = false, $debug = false)
    {
        if (SITE_TEST) return;

        $obCurl = curl_init();

        curl_setopt($obCurl, CURLOPT_URL, $sUrl);
        curl_setopt($obCurl, CURLOPT_POST, true);
        curl_setopt($obCurl, CURLOPT_POSTFIELDS, http_build_query($arData));
        curl_setopt($obCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($obCurl, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($obCurl, CURLOPT_HEADER, false);
        curl_setopt($obCurl, CURLOPT_NOBODY, false);

        if (!empty($auth))
        {
            curl_setopt($obCurl, CURLOPT_USERPWD, $auth["LOGIN"] . ':' . $auth["PASSWORD"]);
        }

        $replay = curl_exec($obCurl);

        $info    = curl_getinfo($obCurl);
        $error   = curl_error($obCurl);
        $version = curl_version();

        curl_close($obCurl);

        if ($debug)
        {
            return array(
                'replay_original' => $replay,
                'replay_decode'   => json_decode($replay, true),
                'curl_error'      => $error,
                'curl_getinfo'    => $info,
                'curl_version'    => $version,
                'post'            => $arData
            );
        }

        return $replay;
    }

    public static function sendMoneyCare($sUrl, $arData, $auth, $get = false, $debug = false)
    {
        if (SITE_TEST) return;

        $json = json_encode($arData);

        $headers = array(
            "Content-Type: application/json",
            'Content-Length: ' . strlen($json),
        );

        $obCurl = curl_init();
        curl_setopt($obCurl, CURLOPT_USERPWD, $auth["LOGIN"] . ':' . $auth["PASSWORD"]);

        if (!$get)
        {
            curl_setopt($obCurl, CURLOPT_URL, $sUrl);
            curl_setopt($obCurl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($obCurl, CURLOPT_POST, true);
            curl_setopt($obCurl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($obCurl, CURLOPT_HEADER, false);
            curl_setopt($obCurl, CURLOPT_HTTPHEADER, $headers);
        }
        else
        {
            curl_setopt($obCurl, CURLOPT_URL, $sUrl . "?" . http_build_query($arData));
            curl_setopt($obCurl, CURLOPT_CUSTOMREQUEST, "GET");
        }

        curl_setopt($obCurl, CURLOPT_TIMEOUT, 90);
        curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, 90);
        curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($obCurl, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($obCurl, CURLOPT_NOBODY, false);

        $replay = curl_exec($obCurl);

        $info    = curl_getinfo($obCurl);
        $error   = curl_error($obCurl);
        $version = curl_version();

        curl_close($obCurl);

        if ($debug)
        {
            return array(
                'replay_original' => $replay,
                'replay_decode'   => json_decode($replay, true),
                'curl_error'      => $error,
                'curl_getinfo'    => $info,
                'curl_version'    => $version,
                'post'            => $arData
            );
        }

        return json_decode($replay, true);
    }

    public static function sendSbl($sUrl, $arData, $auth = false, $get = true, $debug = false)
    {
        //if (SITE_TEST) return;

        $json = json_encode($arData);

        $headers = array(
            "Content-Type: application/json",
            'Content-Length: ' . strlen($json),
        );

        $obCurl = curl_init();

        if ($auth)
        {
            curl_setopt($obCurl, CURLOPT_USERPWD, $auth["LOGIN"] . ':' . $auth["PASSWORD"]);
        }

        if (!$get)
        {
            curl_setopt($obCurl, CURLOPT_URL, $sUrl);
            curl_setopt($obCurl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($obCurl, CURLOPT_POST, true);
            curl_setopt($obCurl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($obCurl, CURLOPT_HEADER, false);
            curl_setopt($obCurl, CURLOPT_HTTPHEADER, $headers);
        }
        else
        {
            curl_setopt($obCurl, CURLOPT_URL, $sUrl . "?" . http_build_query($arData));
            curl_setopt($obCurl, CURLOPT_CUSTOMREQUEST, "GET");
        }

        curl_setopt($obCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($obCurl, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($obCurl, CURLOPT_NOBODY, false);

        $referer = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER[HTTP_HOST] . "" . $_SERVER[REQUEST_URI];
        curl_setopt($obCurl, CURLOPT_REFERER, $referer);

        $replay = curl_exec($obCurl);

        $info    = curl_getinfo($obCurl);
        $error   = curl_error($obCurl);
        $version = curl_version();

        curl_close($obCurl);

        if ($debug)
        {
            return array(
                'replay_original' => $replay,
                'replay_decode'   => json_decode($replay, true),
                'curl_error'      => $error,
                'curl_getinfo'    => $info,
                'curl_version'    => $version,
                'post'            => $arData
            );
        }

        return json_decode($replay, true);
    }

}
