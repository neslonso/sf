<?
ob_start();
?>
<?
header('Content-Type: text/html; charset=utf-8');

$uniqueId=uniqid("auto.");
//error_log ('----------------------');
//error_log ('/********************/');
//error_log ('LLAMADA A AUTO.PHP: '.$uniqueId);

mb_internal_encoding("UTF-8");
//El UTF-8 para la conexion a la db se establece en el constructor de mysqliDB

date_default_timezone_set('Europe/Madrid');

//No utilizo setlocale, en cuestion de numeros, hace que los floats tengan la , por separador decimal al
//convertirlos a cadena, lo que da problemas al construir sentencias SQL o mandar JSON a JS
//setlocale(LC_ALL,'es_ES');
?>
<?
try {
	session_start();

	if (isset($_GET['sitemap'])) {
		sitemap();
	}
?>
<?
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	mail (DEBUG_EMAIL,SITE_NAME.". AUTO.PHP",
		$infoExc."\n\n--\n\n".$e->getTraceAsString()."\n\n--\n\n".print_r($GLOBALS,true));
}
?>