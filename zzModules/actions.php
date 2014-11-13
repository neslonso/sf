<?
use Sintax\Core\ReturnInfo;
?>
<?
$tInicial=microtime(true);
ob_start();
?>
<?
$uniqueId=uniqid("actions.");
//error_log ('----------------------');
//error_log ('/********************/');
//error_log ('LLAMADA A ACTIONS.PHP: '.$uniqueId);
?>
<?
try {
	session_start();
?>
<?
	if (!isset($_POST['acReturnURI'])) {
		$_POST['acReturnURI']=(isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
	}

	//error_log ('$_SESSION='.print_r($_SESSION,true));
	//error_log ('$_POST='.print_r($_POST,true));
	$firephp->group('Llamada a actions.php 	clase: '.$_POST['acClase'].'. Metodo: '.$_POST['acMetodo'].'. Tipo: '.$_POST['acTipo'].'. ReturnURI: '.$_POST['acReturnURI'],
					array('Collapsed' => true,
						  'Color' => '#FF9933'));
	$firephp->group('_SESSION, _REQUEST, _POST y _FILES', array('Collapsed' => true, 'Color' => '#9933FF'));
	$firephp->info($_SESSION,'$_SESSION');
	$firephp->info($_POST,'$_REQUEST');
	$firephp->info($_POST,'$_POST');
	$firephp->info($_FILES,'$_FILES');
	$firephp->groupend();

	$result="";
	$acClase=$_POST['acClase'];
	$acClase="Sintax\\Pages\\".$_POST['acClase'];
	$acMetodo=$_POST['acMetodo'];
	$acTipo=(isset($_POST['acTipo']))?$_POST['acTipo']:"std";
	$acReturnURI=(isset($_POST['acReturnURI']))?$_POST['acReturnURI']:"./";
	unset ($_POST['acClase']);
	unset ($_POST['acMetodo']);
	unset ($_POST['acTipo']);
	unset ($_POST['acReturnURI']);

	$objUsr=new Sintax\Core\AnonymousUser();
	if (isset($_SESSION['usuario'])) {
		//$objUsr=$_SESSION['usuario'];
		$usrClass=get_class($_SESSION['usuario']);
		if ($usrClass!="__PHP_Incomplete_Class") {
			$objUsr=$_SESSION['usuario'];
		}
	}
?>
<?
/**/
?>
<?
	//Almacenamos lastAction
	$firephp->group('Almacenado lastAction', array('Collapsed' => true, 'Color' => '#3399FF'));
	$_SESSION['lastAction'][$acClase][$acMetodo]['TIMESTAMP']=time();
	$_SESSION['lastAction'][$acClase][$acMetodo]['URI']=(isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
	$_SESSION['lastAction'][$acClase][$acMetodo]['_POST']=$_POST;
	$firephp->group('Copiando _FILES', array('Collapsed' => true, 'Color' => '#3399FF'));
	foreach ($_FILES as $inputName => $arrFileData) {
		if (!is_array($arrFileData['error'])) {
			foreach ($arrFileData as $key => $value) {//si el input type file no era un array (name="xxx[]") convertimos los datos para tratarlo como si lo fuera
				$arrFileData[$key]=array($arrFileData[$key]);
			}
			$firephp->info($arrFileData,'Solo un file, convertido a array');
		}
		foreach ($arrFileData['error'] as $index => $error) {
			if ($error== UPLOAD_ERR_OK) {
				$tmp_name = $arrFileData["tmp_name"][$index];
				$name = $arrFileData["name"][$index];
				$destino=UPLOAD_DIR_TMP.basename($tmp_name);
				$firephp->info('('.$name.') => '.$destino,'Intentando copia de _FILES['.$inputName.']['.$index.']');
				if (copy($tmp_name, $destino)) {
				//if (move_uploaded_file($tmp_name, $destino)) {//No los movemos, por si hay que usarlos
					$firephp->info('('.$name.') => '.$destino,'_FILES['.$inputName.']['.$index.'] copiado');
					chmod ($destino,0777);
					//$_FILES[$inputName]['tmp_name'][$key]=$destino;
				} else {
					$firephp->info('('.$name.') !=> '.$destino,'Copia de _FILES['.$inputName.']['.$index.'] fallida');
				}
			}
		}
	}
	$firephp->groupEnd();
	$_SESSION['lastAction'][$acClase][$acMetodo]['_FILES']=$_FILES;
	$firephp->info($_SESSION['lastAction'],'_SESSION[\'lastAction\']:');
	$firephp->groupEnd();
?>
<?
/**/
?>
<?
	if (class_exists($acClase)) {
		$obj=new $acClase($objUsr);
		if (method_exists($obj,$acMetodo)) {

			$accionValida=$obj->accionValida($acMetodo);

			if ($accionValida===true) {
				$phpSentence="";
				switch ($acTipo) {
					case "std"://Los parametros vienen por POST, se pasan al metodo uno por uno y despues se redirige el navegador
					case "ajax"://Los parametros vienen por POST y se pasan al metodo uno por uno
					case "plain"://No hace nada, se llama a la acción y nada mas
						$args="";
						foreach ($_POST as $value) {
							$args.='"'.$value.'", ';
						}
						$args=substr($args,0,-2);
						$phpSentence='$resultSentence=$obj->'.$acMetodo.'('.$args.');';
						break;
					case "stdAssoc"://Los parametros vienen POST y se pasan al metodo como un array y despues se redirige el navegador
					case "ajaxAssoc"://Los parametros vienen POST y se pasan al metodo como un array
					case "plainAssoc"://No hace nada, se llama a la acción y nada mas
						$args=$_POST;
						$phpSentence='$resultSentence=$obj->'.$acMetodo.'($args);';
						break;
				}
				$phpSentence.='return true;';

				error_log('acClase: '.$acClase.' :: sentencia: '.$phpSentence);
				$firephp->group('sentencia: '.$phpSentence,
								array('Collapsed' => true,
									  'Color' => '#FF9933'));
				$firephp->info($args,'$args sentencia');

				$firephp->group('ejecucion sentencia',
							array('Collapsed' => true,
								  'Color' => '#FF9933'));

			$tAccion=microtime(true);
				$resultEval=eval ($phpSentence);
			$tAccion=microtime(true)-$tAccion;

				$firephp->groupEnd();

				$firephp->info($resultEval,'$result Eval');
				$firephp->info($resultSentence,'$result sentencia');
				$firephp->groupEnd();

				if ($resultEval===false) {
					$result="ERROR_EN_SENTENCIA";
					//throw new Exception($result);
				} else {
					$result=$resultSentence;
					//Todo fué bien, no hace falta conservar lastAction
					//Borramos los fichero temporales
					$firephp->group('Eliminando lastAction', array('Collapsed' => true, 'Color' => '#3399FF'));
					$firephp->group('Borrando _FILES de UPLOAD_DIR_TMP', array('Collapsed' => true, 'Color' => '#3399FF'));
					foreach ($_FILES as $inputName => $arrFileData) {
						if (!is_array($arrFileData['error'])) {
							foreach ($arrFileData as $key => $value) {//si el input type file no era un array (name="xxx[]") convertimos los datos para tratarlo como si lo fuera
								$arrFileData[$key]=array($arrFileData[$key]);
							}
							$firephp->info($arrFileData,'Solo un file, convertido a array');
						}
						foreach ($arrFileData['error'] as $index => $error) {
							if ($error== UPLOAD_ERR_OK) {
								$tmp_name = $arrFileData["tmp_name"][$index];
								$name = $arrFileData["name"][$index];
								$destino=UPLOAD_DIR_TMP.basename($tmp_name);
								$firephp->info('('.$name.') => '.$destino,'Intentando borrado de _FILES['.$inputName.']['.$index.']');
								if (unlink($destino)) {
								//if (move_uploaded_file($tmp_name, $destino)) {//No los movemos, por si hay que usarlos
									$firephp->info('('.$name.') => '.$destino,'_FILES['.$inputName.']['.$index.'] borrado');
									//$_FILES[$inputName]['tmp_name'][$key]=$destino;
								} else {
									$firephp->info('('.$name.') !=> '.$destino,'Borrado de _FILES['.$inputName.']['.$index.'] fallido');
								}
							}
						}
					}
					$firephp->groupEnd();
					$firephp->groupEnd();
					unset($_SESSION['lastAction'][$acClase][$acMetodo]);
					if (count($_SESSION['lastAction'][$acClase])==0) {
						unset($_SESSION['lastAction'][$acClase]);
					}
					if (count($_SESSION['lastAction'])==0) {
						unset($_SESSION['lastAction']);
					}
				}
			} else {
				$result="operación no permitida. (ERROR_NO_VALIDA)";
				$msg='operación no permitida.<br />'.$accionValida;
				$title='No es posbile realizar la operación.';
				ReturnInfo::add($msg,$title);
				throw new Exception($result);
			}
		} else {
			$result="accion no encontrada (ERROR_NO_METODO)";
			$msg='No se encontró la acción "'.$acMetodo.'".';
			$title='No es posbile realizar la operación.';
			ReturnInfo::add($msg,$title);
			throw new Exception($result);
		}
	} else {
		$result="clase no encontrada (ERROR_NO_CLASE)";
		$msg='No se encontró la clase "'.$acClase.'".';
		$title='No es posbile realizar la operación.';
		ReturnInfo::add($msg,$title);
		throw new Exception($result);
	}

	$firephp->group('Finalización de accion', array('Collapsed' => true, 'Color' => '#FF3399'));
	switch ($acTipo) {
		case "std":
		case "stdAssoc":
			$firephp->info($result,'$result');
			$location=$acReturnURI;
			$firephp->info($location,'redireccionando a ($location)');
			if (in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
				echo '<a href="'.$location.'">Continuar a: '.$location.'</a>';
			} else {
				header ("Location: ".$location);
			}
			break;
		case "ajax":
		case "ajaxAssoc":
			$actionData=new \stdClass();
			$actionData->exito=true;
			$actionData->data=$result;
			$actionData->msg='';
			$firephp->info($actionData,'Devolviendo actionData');
			$firephp->info(json_encode ($actionData),'JSON actionData');
			//header('Content-Type: application/json; charset=utf-8');
			echo json_encode ($actionData);
			break;
		case "plain":
		case "plainAssoc":
			break;
	}
	$firephp->groupend();
?>
<?
/**/
?>
<?
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	$firephp->info($infoExc);
	$firephp->info($e->getTrace(),"trace");
	$firephp->info($e->getTraceAsString(),"traceAsString");
	error_log ($infoExc);
	error_log ("TRACE: ".$e->getTraceAsString());

	switch ($acTipo) {
		case "std":
		case "stdAssoc":
			if (get_class($e)=="ActionException") {//Excepción lanzada voluntariamente
				if (!ReturnInfo::existe()) {
					$msg=$e->getMessage();
					$title="La operacion no ha sido completada";
					ReturnInfo::add($msg,$title);
				}
				$location=$acReturnURI;
			} else {//Excepcion no controlada
				$msg=$infoExc;
				$title="Situación de excepción no controlada";
				ReturnInfo::add($msg,$title);
				$location=BASE_DIR.FILE_APP."?page=error";
			}
			error_log('redireccionando a ('.$location.')');
			$firephp->info($location,'redireccionando a ($location)');
			if (in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
				echo '<a href="'.$location.'">Continuar a: '.$location.'</a>';
			} else {
				header ("Location: ".$location);
			}
			break;
		case "ajax":
		case "ajaxAssoc":
			ReturnInfo::clear();
			$actionData=new \stdClass();
			if (get_class($e)=="ActionException") {//Excepción lanzada voluntariamente
				$actionData->exito=true;
				$actionData->data='';
				$actionData->msg=$e->getMessage();
			} else {//Excepcion no controlada
				$actionData->exito=false;
				$actionData->data='';
				$actionData->msg=$infoExc;
			}
			//header('Content-Type: application/json; charset=utf-8');
			echo json_encode ($actionData);
			break;
		case "plain":
		case "plainAssoc":
			break;
	}
}
?>
<?
$tTotal=microtime(true)-$tInicial;
if (isset($tAccion)) {$firephp->info("Tiempo llamada acción: ".$tAccion." segundos");}
else {$firephp->info("Tiempo llamada acción no medido, no se ha realizado llamada a acción");}
$firephp->info("Ejecutado en: ".$tTotal." segundos");
$firephp->groupEnd();
//error_log ("FIN LLAMADA A ACTIONS.PHP: ".$uniqueId);
//error_log ('/********************/');
//error_log ('----------------------');
?>
<?
class ActionException extends Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code,$previous);
		$infoExc='';
		if (!is_null($previous)) {
			$infoExc=" :-: Excepcion de tipo: ".get_class($previous).". Mensaje: ".$previous->getMessage()." en fichero ".$previous->getFile()." en linea ".$previous->getLine();
		}
		$this->message=$message.$infoExc;
	}
}
?>