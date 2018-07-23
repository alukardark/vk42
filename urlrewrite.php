<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/(info|poleznaya\\-informaciya)/([a-zA-Z0-9\\-/]+)/#",
		"RULE" => "/\$1/\$2.php",
		"ID" => "",
		"PATH" => "",
	),
	array(
		"CONDITION" => "#^/web-service-order/([0-9a-z]+)(/?)([^/]*)#",
		"RULE" => "token=\$1",
		"ID" => "",
		"PATH" => "/web-service/order.php",
	),
	array(
		"CONDITION" => "#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#",
		"RULE" => "alias=\$1",
		"ID" => "",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/kabinet/personal/orders/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/kabinet/personal/orders/index.php",
	),
	array(
		"CONDITION" => "#^={PATH_PERSONAL_ORDERS}#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/kabinet/personal/index.php",
	),
	array(
		"CONDITION" => "#^/online/(/?)([^/]*)#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/prochie_tovary/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/prochie_tovary/index.php",
	),
	array(
		"CONDITION" => "#^/akkumulyatory/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/akkumulyatory/index.php",
	),
	array(
		"CONDITION" => "#^/articles/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/articles/index.php",
	),
	array(
		"CONDITION" => "#^/novosti/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/novosti/index.php",
	),
	array(
		"CONDITION" => "#^/katalog/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/katalog/index.php",
	),
	array(
		"CONDITION" => "#^/uslugi/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/uslugi/index.php",
	),
	array(
		"CONDITION" => "#^/masla/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/masla/index.php",
	),
	array(
		"CONDITION" => "#^/akcii/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/akcii/index.inc.php",
	),
	array(
		"CONDITION" => "#^/akcii/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/akcii/index.php",
	),
	array(
		"CONDITION" => "#^/diski/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/diski/index.php",
	),
);

?>