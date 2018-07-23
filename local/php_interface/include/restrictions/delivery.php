<?

use \Bitrix\Sale\Delivery\Restrictions;
use \Bitrix\Sale\Internals\Entity;

/**
 * класс не закончен.
 * @see https://it-round.ru/blog/itround/service-delivery-and-payment-systems-restriction-by-user-group/
 * @see https://dev.1c-bitrix.ru/api_d7/bitrix/sale/delivery/restrictions/
 * @see https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=7352
 * @see https://dev.1c-bitrix.ru/community/webdev/user/390462/blog/15579/
 */
class VKDeliveryRestriction extends Restrictions\Base
{

    public static function getClassTitle()
    {
        return 'по количеству товара в категории';
    }

    public static function getClassDescription()
    {
        return 'доставка будет выводится только в указанном диапазоне количества товара в категории';
    }

    public static function check($moonday, array $restrictionParams, $deliveryId = 0)
    {
        if ($moonday < $restrictionParams['MIN_MOONDAY'] || $moonday > $restrictionParams['MAX_MOONDAY']) return false;

        return true;
    }

    protected static function extractParams(Entity $shipment)
    {
        $json = file_get_contents('http://moon-today.com/api/index.php?get=moonday');
        $res  = json_decode($json, true);
        return !empty($res['moonday']) ? intval($res['moonday']) : 0;
    }

    public static function getParamsStructure($entityId = 0)
    {
//        return array(
//            "MIN_MOONDAY" => array(
//                'TYPE'    => 'NUMBER',
//                'DEFAULT' => "1",
//                'LABEL'   => 'Минимальные сутки'
//            ),
//            "MAX_MOONDAY" => array(
//                'TYPE'    => 'NUMBER',
//                'DEFAULT' => "30",
//                'LABEL'   => 'Максимальные сутки'
//            )
//        );

        $result = array(
            "CATEGORIES" => array(
                "TYPE"   => "DELIVERY_PRODUCT_CATEGORIES",
                "URL"    => "/bitrix/admin/cat_section_search.php?lang=ru&m=y&n=SECTIONS_IDS",
                "SCRIPT" => "window.InS" . md5('SECTIONS_IDS') . "=function(id, name){BX.Sale.Delivery.addRestrictionProductSection(id, name, this);};",
                "LABEL"  => "Категории товаров",
                "ID"     => 'sale-admin-delivery-restriction-cat-add'
            ),
            "MIN_COUNT"  => array(
                'TYPE'    => 'NUMBER',
                'DEFAULT' => "4",
                'LABEL'   => 'Минимальное количество'
            ),
        );

        return $result;
    }

}
