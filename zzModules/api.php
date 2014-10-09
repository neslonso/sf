<?
ob_start();
?>
<?
$uniqueId=uniqid("api.");
error_log ('');
error_log ('----------------------');
error_log ('/********************/');
error_log ('LLAMADA A API.PHP: '.$uniqueId);
?>
<?
try {
	try {
		//Tratamos de identificar el servicio en base a lo que venga en $_REQUEST
		//Como esta es una URL que se dará a servicios exteriores, podemos contar con un parametro GET
		$service=$_REQUEST['service'];
		$key=(isset($_REQUEST['key']))?$_REQUEST['key']:'';
		if (defined('ARR_API_SERVICES')) {
			$arrServices=unserialize(ARR_API_SERVICES);
			if (isset($arrServices[$service])) {
				$arrServiceData=$arrServices[$service];
				if ($arrServiceData['active']) {
					$granted=false;
					if (count($arrServiceData['keys'])>0) {
						if (in_array($key, $arrServiceData['keys'])) {
							$granted=true;
						}
					} else {
						$granted=true;
					}
					if ($granted) {
						eval ($arrServiceData['comando']);
					} else {
						throw new ApiException("Service key not valid");
					}
				} else {
					throw new ApiException("Service '".$service."' inactive.");
				}
			} else {
				throw new ApiException("Service '".$service."' desconocido.");
			}
		}
	} catch (ApiException $e) {
		//throw new Exception("ApiException: [".$e->getMessage()."]",1,$e);
		throw $e;
	}
} catch (Exception $e) {
	echo $e->getMessage();
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	error_log ($infoExc);
	error_log ("TRACE: ".$e->getTraceAsString());

	mail (DEBUG_EMAIL,SITE_NAME.". API.PHP",
		$infoExc."\n\n--\n\n".$e->getTraceAsString()."\n\n--\n\n".print_r($GLOBALS,true));
}
?>
<?
error_log ("FIN LLAMADA A API.PHP: ".$uniqueId);
error_log ('/********************/');
error_log ('----------------------');
error_log ('');
?>
<?
class ApiException extends Exception {}
?>