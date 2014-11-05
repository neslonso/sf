<?if (false) {?><style><?}?>
<?="\n/*".get_class()."*/\n"?>
/*phpinfo() styles*/
<?
ob_start();
phpinfo();
$phpinfoResult=ob_get_clean();
preg_match_all("=<style[^>]*>(.*)</style>=siU", $phpinfoResult, $a);
$phpinfo = $a[1][0];
echo $phpinfo
?>
/**/
