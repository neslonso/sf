<?
$minify=false;//true no funciona del todo bien, debe existir algun problema en la clase JSMin
ob_start();
?>
<?
try {
	header('Content-type: text/javascript; charset=utf-8');
	session_cache_limiter('public');
	session_start();
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 60*60*24*364));

	$page=(isset($_GET['page']))?$_GET['page']:'Home';

	$objUsr=new Sintax\Core\AnonymousUser();
	if (isset($_SESSION['usuario'])) {
		//$objUsr=$_SESSION['usuario'];
		$usrClass=get_class($_SESSION['usuario']);
		if ($usrClass!="__PHP_Incomplete_Class") {
			$objUsr=$_SESSION['usuario'];
		} else {
			unset ($_SESSION['usuario']);
		}
	}

	/* Js Libs ********************************************************************/
	$arrFilesModTime=array();
	$arrFilesModTime[__FILE__]=getlastmod();//Fecha de modificacion de este fichero
	$arrFilesModTime[SKEL_ROOT_DIR."includes/server/start.php"]=filemtime(SKEL_ROOT_DIR."includes/server/start.php");
	ob_start();
		foreach ($ARR_CLIENT_LIBS as $libPath) {
			$arrFilesModTime[$libPath]=filemtime($libPath);
			require $libPath;
		}
	$jsScriptTags=ob_get_clean();

	$doc = new DOMDocument();
	$doc->loadHTML($jsScriptTags);
	$scriptElements = $doc->getElementsByTagName('script');

	$srcs = array();
	for($i = 0; $i < $scriptElements->length; $i++) {
		$srcs[]=$scriptElements->item($i)->getAttribute('src');
		$src=$scriptElements->item($i)->getAttribute('src');
		try {
			$arrFilesModTime[$src]=filemtime($src);
		} catch (Exception $e) {}
	}

	$jsMinFile=CACHE_DIR."jsMin.".md5(serialize($arrFilesModTime)).".js";
	//echo $jsMinFile;

	if (file_exists($jsMinFile)) {
		$firephp->info($jsMinFile,'devolviendo JS cacheado:');
		$firephp->info(array_map(function ($elto) {
			return gmdate('D, d M Y H:i:s \G\M\T',$elto);
		},$arrFilesModTime),'Fechas de modificacion:');
		echo file_get_contents($jsMinFile);
	} else {
		$jsLibs="// Js Libs \n";
		$firephp->group('Carga de SRCs JS', array('Collapsed' => true, 'Color' => '#FF9933'));
		foreach ($srcs as $src) {
			$origSrc=$src;
			if (substr($src, 0,2)=='./') {
				$src=realpath(SKEL_ROOT_DIR.$src);
			}
			if (substr($src, 0,2)=='//') {
				$src=PROTOCOL.":".$src;
			}
			$firephp->info($src,'Intentando file_get_contents:');
			try {
				$fileContent=file_get_contents($src)."\n\n";
				$jsLibs.=$fileContent."\n\n";
				//$jsLibs.=JSMin::minify($fileContent);
			} catch (Exception $e) {
				error_log ("js.php:: Imposible cargar '".$src."'");
				$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine().". origSrc: ".$origSrc;
				error_log ($infoExc);
				$firephp->error($infoExc,'Error cargando '.$src);
			}
			//var_dump($http_response_header);
		}
		$firephp->groupend();
		$jsLibs.="\n// Js Libs Fin";
		echo $jsLibs;
	}
	/******************************************************************************/
?>
<?
	//Leemos todo lo que hemos ido volcando al buffer raiz y lo guardamos en el fichero
	$jsLibs=ob_get_clean();
	//$jsLibs="";

	if ($minify) {
		$jsMin=JSMin::minify($jsLibs);
	} else {
		$jsMin=$jsLibs;
	}
	file_put_contents($jsMinFile, $jsMin);
	$jsMinFileContents=file_get_contents($jsMinFile)."\n\n\n\n";
?>
<?
	/* js local *******************************************************************/
	ob_start();
		echo "// <![CDATA[ \n";
		$Page=new $page($objUsr);
		$Page->js();
		echo "\n// ]]> ";
	$jsLocal=ob_get_clean();

	if ($minify) {
		$jsLocalMin=JSMin::minify($jsLocal);
	} else {
		$jsLocalMin=$jsLocal;
	}
	$jsLocalMin="// JS LOCAL\n".$jsLocalMin."\n// JS LOCAL FIN\n";
	/* js local *******************************************************************/
?>
<?
//Volcamos todo (Libs + Local)
echo $jsMinFileContents;
echo $jsLocalMin;
?>
<?
} catch (Exception $e) {
	$msg='Error durante la generación de js.php.';
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	error_log ($infoExc);
	error_log ("TRACE: ".$e->getTraceAsString());
	$firephp->group($msg, array('Collapsed' => false, 'Color' => '#FF6600'));
	$firephp->info($infoExc);
	$firephp->info($e->getTraceAsString(),"traceAsString");
	$firephp->info($e->getTrace(),"trace");
	$firephp->groupEnd();
	ob_clean();
	//echo '<h1>'.date("YmdHis").': '.$msg.'</h1>';
}
?>