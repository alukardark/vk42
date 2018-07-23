<?
//заглушки ie 6-8
$sUserAgent = getUA();

$IE = stripos($sUserAgent, 'MSIE 6.0') ? 6 : false;
$IE = stripos($sUserAgent, 'MSIE 7.0') ? 7 : false;
$IE = stripos($sUserAgent, 'MSIE 8.0') ? 8 : false;

if ($IE)
{
    LocalRedirect("/ie67/ie{$IE}.html");
    die;
}

