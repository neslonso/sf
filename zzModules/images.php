<?
ob_start();

//almacen
//fichero
//ancho
//alto
//modo
//formato
try {
	switch ($_GET["almacen"]) {
		case "LOREMPIXEL":
			/*
			http://lorempixel.com/400/200 to get a random picture of 400 x 200 pixels
			http://lorempixel.com/g/400/200 to get a random gray picture of 400 x 200 pixels
			http://lorempixel.com/400/200/sports to get a random picture of the sports category
			http://lorempixel.com/400/200/sports/1 to get picture no. 1/10 from the sports category
			http://lorempixel.com/400/200/sports/Dummy-Text...with a custom text on the random Picture
			*/
			try {
				$ancho=(isset($_GET["ancho"]))?$_GET["ancho"]:640;
				$alto=(isset($_GET["alto"]))?$_GET["alto"]:480;
				$categoria=(isset($_GET["fichero"]))?"/".$_GET["fichero"]:"";
				$url="http://lorempixel.com/".$ancho."/".$alto.$categoria;
				//$firephp->info($url,"URL: ");
				$objImg=Imagen::fromString(file_get_contents($url));
			} catch (Exception $e) {
				error_log ($e->getMessage());
				$file=BASE_IMGS_DIR.'imgErr.png';
				$objImg=Imagen::fromFile($file);
			}
		break;
		case "DB";
			try {
				$db=cDb::getInstance();
				list($tabla,$campoId,$valorId,$campoData)=explode($_GET["fichero"],'.');
				$sql="SELECT ".$campoId.", ".$campoData." FROM ".$tabla." WHERE id='".$db->real_Escape_String($valorId)."'";
				//$GLOBALS['firephp']->info($sql);
				$rslSet=$db->query($sql);
				if ($rslSet->num_rows>0) {
					$data=$rslSet->fetch_object();
					$data=$data->data;
				}
				$objImg=Imagen::fromString($data);
				//$objImg->marcaAgua("");
				//$objImg->marcaAgua("",1,1,"center");
			} catch (Exception $e) {
				error_log(print_r($e,true));
				$file=BASE_IMGS_DIR.'imgErr.png';
				$objImg=Imagen::fromFile($file);
			}
		break;
		default:
			try {
				if (defined($_GET['almacen'])) {
					$file=constant($_GET['almacen']).$_GET['fichero'];
				} else {
					$file=BASE_IMGS_DIR.$_GET['fichero'];
				}
				$objImg=Imagen::fromFile($file);
			} catch (Exception $e) {
				//error_log(print_r($e,true));
				error_log($e->getMessage());
				$file=BASE_IMGS_DIR.'imgErr.png';
				$objImg=Imagen::fromFile($file);
			}
	}


	ob_clean();//limpiamos el buffer antes de mandar la imagen, no queremos nada más que la imagen

	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 60*60*24*364));

	$ancho=(isset($_GET['ancho']) && is_numeric($_GET['ancho']))?$_GET['ancho']:NULL;
	$alto=(isset($_GET['alto']) && is_numeric($_GET['alto']))?$_GET['alto']:NULL;
	$modo=(isset($_GET['modo']) && is_numeric($_GET['modo']))?$_GET['modo']:Imagen::OUTPUT_MODE_SCALE;
	$formato=(isset($_GET['formato']))?$_GET['formato']:"png";

	$objImg->output($ancho,$alto,$modo,$formato);
} catch (Exception $e) {
	$firephp->info("Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine());
	$firephp->info($e->getTrace(),"trace");
	$firephp->info($e->getTraceAsString(),"traceAsString");
	error_log ("Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine());
	error_log ("TRACE: ".$e->getTraceAsString());
}
?>