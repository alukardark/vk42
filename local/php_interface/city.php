<?
//global $APPLICATION;
//$cuurentCity = $APPLICATION->get_cookie("CURRENT_CITY");
//$APPLICATION->set_cookie("RUSSIAN_VISITOR_ID", 156, time()+60*60*24*30*12*2, "/ru/");

$arSiteCities = array(
    'Kemerovo'     => "Кемерово",
    'Novokuznetsk' => "Новокузнецк",
);

$sDefaultCity = 'Kemerovo';


if (!empty($_SESSION['current_city']) && !array_key_exists($_SESSION['current_city'], $arSiteCities))
{
    unset($_SESSION['current_city']);
}



//detect city
if (empty($_SESSION['current_city']))
{
    CModule::IncludeModule('statistic');
    $cityObj    = new CCity();
    $arThisCity = $cityObj->GetFullInfo();
    $sCity      = $arThisCity['CITY_NAME']['VALUE'];

    if (!empty($sCity) && array_key_exists($sCity, $arSiteCities))
    {
        $_SESSION['current_city'] = $sCity;
    }
}

if (empty($_SESSION['current_city']))
{
    $_SESSION['current_city'] = $sDefaultCity;
}

//var_dump($_SESSION['current_city']); die;
