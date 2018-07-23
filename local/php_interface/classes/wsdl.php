<?

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
//ini_set('display_errors', 1);
ini_set('max_execution_time', 90);

class WSDL
{

    private static $_instance;
    private static $obClient;
    private static $LAST_ERROR;

    const VK_SOAP_WSDL     = 'http://46.181.49.50:60080/test_ka/ws/exchange.1cws?wsdl';
    //const VK_SOAP_WSDL     = 'test_ka/ws/exchange.1cws?wsdl';
    const VK_SOAP_LOGIN    = "Client1C";
    const VK_SOAP_PASSWORD = "F9KS6g@";

    public function GetResidue($XML_ID)
    {
        if (empty($XML_ID))
        {
            return "empty XML_ID";
        }

        $arParams = array(
            'XML_ID' => $XML_ID,
        );

        $obReplay = self::getReplay("GetResidue", $arParams);



        //$arDeliveryVariantEstimationTime = $obReplay->Items->DeliveryVariantEstimationTime;
        //return $arDeliveryVariantEstimationTime;
    }

    /**
     * Параметры ответного сообщения
     * @param type $sReplayObjectName
     * @param type $arParams
     * @param type $sFunctionName
     * @return type
     */
    private static function getReplay($sReplayObjectName, $arParams = array(), $sFunctionName = false)
    {
        if (!$sFunctionName)
        {
            //name of calling function
            $arDebug       = debug_backtrace();
            $sFunctionName = $arDebug[1]['function'];
        }

        /* $soapClient = self::getClient();

          try
          {
          $soapClient->GetResidue($arParams);

          echo '<pre style="background:#eee">';
          echo '<b>Result: Success</b><br/><br/>';

          echo "<br/><br/><b>Request:</b><br/>", htmlentities($soapClient->__getLastRequest()), "<br/>";
          echo "<br/><br/><b>Response:</b><br/>", htmlentities($soapClient->__getLastResponse()), "<br/>";
          }
          catch (SoapFault $soapFault)
          {
          echo '<pre style="background:#eee;padding:20px;">';
          echo '<b class="red">Result: Fault</b><br/><br/>';

          echo "<br/><br/><b>Trace:</b><br/>", var_dump($soapFault), "<br/>";
          echo "<br/><br/><b>Request:</b><br/>", htmlentities($soapClient->__getLastRequest()), "<br/>";
          echo "<br/><br/><b>Request headers:</b><br/>", htmlentities($soapClient->__getLastRequestHeaders()), "<br/>";
          echo "<br/><br/><b>Response:</b><br/>", htmlentities($soapClient->__getLastResponse()), "<br/>";
          echo "<br/><br/><b>Response headers:</b><br/>", htmlentities($soapClient->__getLastResponseHeaders()), "<br/>";
          echo "<br/><br/><b>Functions:</b><br/>", var_dump($soapClient->__getFunctions()), "<br/>";
          }

          echo '</pre><br/><br/><br/><br/><br/><br/>';

          die('exit.');
         */
        if ($obResult = self::getClient()->{$sFunctionName}($arParams))
        {
            //$xml   = simplexml_load_string($obResult);
            //$json  = json_encode($obResult);
            //$array = json_decode($json, TRUE);
            echo 'res:<pre>'; var_dump($obResult); die;

            $obReplay = $obResult->return->ТСЦ;



            if (!empty($obReplay))
            {
                foreach ($obReplay as $obReplayRow)
                {
                    echo 'res:<pre>'; var_dump($obReplayRow->Склад); die;
                }
            }

            //$obErrorMessage = $obReplay->ErrorMessage;


            switch ($obErrorMessage->Type)
            {
                case "E_OK":
                    return $obReplay;
                    break;
                default:
                    self::$LAST_ERROR = $sFunctionName . ": " . $obErrorMessage->Message;
                    break;
            }
        }
        else
        {
            self::$LAST_ERROR = $sFunctionName . ": Unknown error";
        }
    }

    private static function getClient()
    {
        return self::$obClient;
    }

    private static function setClient()
    {
        $arConfig = array(
            'login'        => self::VK_SOAP_LOGIN,
            'password'     => self::VK_SOAP_PASSWORD,
            'proxy_host'   => '46.181.49.50',
            'proxy_port'   => '60080',
            'soap_version' => SOAP_1_2,
            'cache_wsdl'   => WSDL_CACHE_NONE,
            'trace'        => true,
            'features'     => SOAP_USE_XSI_ARRAY_TYPE
        );

        self::$obClient = new SoapClient(self::VK_SOAP_WSDL, $arConfig);

        //$functions = self::$obClient->__getFunctions();
        //echo '<pre>'; var_dump(self::$obClient); die;
    }

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
        self::setClient();
    }

}
