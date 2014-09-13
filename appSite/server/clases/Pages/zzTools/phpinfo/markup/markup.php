<?="\n<!-- ".get_class()." -->\n"?>
<?
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Madrid');
?>
<?
echo "PHP_OS: ".PHP_OS."<br />";
echo "Interface con el servdor web (php_sapi_name()): ".php_sapi_name();
?>
<hr />
<?
echo "Propietario del fichero ejecutado: ".get_current_user();
?>
<hr />
<?
echo "Usuario bajo el que corre el script (whoami): ".exec('whoami');
?>
<hr />
<?
/* PHP 5.4.0
echo "Lista de cabeceras de request(var_dump(apache_request_headers())): ";
var_dump(apache_request_headers());
echo "Lista de cabeceras de response (var_dump(apache_request_headers())): ";
var_dump(apache_response_headers());
*/
echo "Lista de cabeceras de response (var_dump(headers_list())): ";
echo '<pre style="height:200px;">';
var_dump(headers_list());
echo "</pre>";
?>
<hr />
<?
echo "Configuración regional actual (setlocale(LC_ALL, 0)): ".setlocale(LC_ALL, 0);
echo " -> Prueba de locale (22/12/1978 13:14:15): ".strftime("%A %e %B %Y", mktime(13, 14, 15, 12, 22, 1978));
echo "<br />";
echo "Configuración regional: Cambiando a es_ES (setlocale(LC_ALL, 'es_ES')): ".setlocale(LC_ALL, 'es_ES');
echo " -> Prueba de locale (22/12/1978 13:14:15): ".strftime("%A %e %B %Y", mktime(13, 14, 15, 12, 22, 1978));
?>
<hr />
<?
echo "Proceso de hash salteado sha256: ";
$pass="12345678";
$salt=hash('sha256', uniqid(mt_rand(), true));
$hash=$salt.hash('sha256',$salt.$pass);
echo $hash."<br />";
//los primeros 64 caracteres del hash son el salt usado para este pass
//que deberemos recuperar para juntar con el pass introducido y comprobar

?>
<hr />
<?
ob_start();
phpinfo();
$phpinfoResult=ob_get_clean();
preg_match_all("=<body[^>]*>(.*)</body>=siU", $phpinfoResult, $a);
$phpinfo = $a[1][0];
/*
$phpinfo = str_replace( 'width="600"', 'width="750"', $phpinfo );
$phpinfo = str_replace( 'border="0" cellpadding', 'class="x" border="0" cellpadding', $phpinfo );
$phpinfo = str_replace( '<td>', '<td><div class="tt">', $phpinfo );
$phpinfo = str_replace( '<td class="e">', '<td class="e"><div class="te">', $phpinfo );
$phpinfo = str_replace( '<td class="v">', '<td class="v"><div class="tv">', $phpinfo );
$phpinfo = str_replace( '</td>', '</div></td>', $phpinfo );
*/
echo '<div class="phpinfo">'.PHP_EOL.$phpinfo.PHP_EOL.'</div>';
?>
<?="\n<!-- /".get_class()." -->\n"?>
