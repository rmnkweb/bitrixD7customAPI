<?
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();
?>
<? $APPLICATION->IncludeComponent(
    "rmnk:testapi",
    ".default",
    Array(),
    false
);?>