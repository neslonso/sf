<?
try {
	require_once "./includes/server/start.php";
	$module=(isset($_REQUEST['MODULE']))?strtolower($_REQUEST['MODULE']):"render";
	if (isset($_REQUEST['MODULE'])) {unset ($_REQUEST['MODULE']);}
	if (isset($_POST['MODULE'])) {unset ($_POST['MODULE']);}
	if (isset($_GET['MODULE'])) {unset ($_GET['MODULE']);}
	$arrModules=unserialize(MODULES);
	if (array_key_exists($module,$arrModules)) {
		require $arrModules[$module];
	} else {
		throw new Exception("El módulo '".$module."' no se encuentra.", 1);
	}
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	error_log ($infoExc);
	header('HTTP/1.1 500 Internal Server Error',true,500);
	echo ($infoExc);
}
?>