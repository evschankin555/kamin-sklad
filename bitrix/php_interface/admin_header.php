<?php
CJSCore::Init(array("jquery"));
if (strpos($_SERVER["REQUEST_URI"], "/bitrix/admin/") !== false)
{
?>
<style>
    .updprice{
        margin-top: 0px!important;
        margin-left: 10px!important;
    }
</style>
<?
}
