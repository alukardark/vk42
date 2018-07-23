<?

use \Bitrix\Sale\Services\Base;
use \Bitrix\Sale\Internals\Entity;

class VKPayRestriction extends Base\Restriction
{

    public static function getClassTitle()
    {
        return 'по складам';
    }

    public static function getClassDescription()
    {
        return 'платежная система будет выводится только для указанных складов';
    }

    public static function check($iBuyerStore, array $restrictionParams, $serviceId = 0)
    {
        return 1;
        //printra($params);

        $stores = explode(" ", $restrictionParams["STRORES_ID"]);

        if (is_array($stores) and in_array($iBuyerStore, $stores))
        {
            return 1;
        }

        return 0;
    }

    protected static function extractParams(Entity $entity)
    {
        return 1;
        
        //global $APPLICATION;
        //$iBuyerStore = $APPLICATION->get_cookie("BUYER_STORE");
        //return $_SESSION['BUYER_STORE'] ;
        //printra($iBuyerStore);
        $collection  = $entity->getCollection();
        $obOrder     = $collection->getOrder();

        //pprintra($obOrder);

        $obShipmentCollection = $obOrder->getShipmentCollection();


        //$arStores = array();
        foreach ($obShipmentCollection as $shipment)
        {
            $storeId = $shipment->getStoreId();

            if (!empty($storeId))
            {
                return $storeId;
                //pprintra($shipment);
                //$arStores[] = $storeId;
            }
        }

        //printra($arStores);

        return 0;
    }

    public static function getParamsStructure($entityId = 0)
    {
        return array(
            "STRORES_ID" => array(
                'TYPE'    => 'STRING',
                'DEFAULT' => "",
                'LABEL'   => 'ID складов'
            ),
        );
    }

}
