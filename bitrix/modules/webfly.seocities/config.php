<?
define("WF_SEO_IBLOCK", "webfly_seo");
define("WF_CITIES_IBLOCK", "webfly_cities");
define("WF_SEOCITIES_CACHEFOLDER", "/webfly/seocities/");

if ($_SERVER["HTTPS"] == "on")
    $protocol = "https://";
else
    $protocol = "http://";
define("WF_SC_PROTOCOL", $protocol);
?>
