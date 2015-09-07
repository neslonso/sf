<?
$lessParse=(class_exists('Less_Parser') && true)?true:false;
$arrLessParserOptions = array( 'compress'=>false );
ob_start();
?>
<?
try {
	header('Content-type: text/css; charset=utf-8');
	session_cache_limiter('public');
	session_start();
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() - 60*60*24*364));

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

?>
<?
	/* css Js Libs ********************************************************************/
	$arrFilesModTime=array();
	$arrFilesModTime[$_SERVER['SCRIPT_FILENAME']]=getlastmod();//Fecha de modificacion del punto de entrada
	$arrFilesModTime[__FILE__]=filemtime(__FILE__);//Fecha de modificacion de este fichero
	$arrFilesModTime[SKEL_ROOT_DIR."includes/server/start.php"]=filemtime(SKEL_ROOT_DIR."includes/server/start.php");
	ob_start();
		foreach ($ARR_CLIENT_LIBS as $libPath) {
			$arrFilesModTime[$libPath]=filemtime($libPath);
			require $libPath;
		}
	$cssLinkTags=ob_get_clean();

	if ($cssLinkTags!="") {
		$hrefs = array();
		try {
			$doc = new DOMDocument();
			$doc->loadHTML($cssLinkTags);
			$linkElements = $doc->getElementsByTagName('link');

			for($i = 0; $i < $linkElements->length; $i++) {
				$hrefs[]=$linkElements->item($i)->getAttribute('href');
				$href=$linkElements->item($i)->getAttribute('href');
				try {
					if (substr($href, 0,1)=='/' && substr($href, 0,2)!='//') {
						$href=realpath($_SERVER["DOCUMENT_ROOT"].$href);
					} elseif (substr($href, 0,2)=='./') {
						$href=realpath(SKEL_ROOT_DIR.$href);
					} elseif (substr($href, 0,2)=='//') {
						$href=PROTOCOL.":".$href;
					}
					$arrFilesModTime[$href]=filemtime($href);
				} catch (Exception $e) {
					$firephp->warn($e->getMessage(),"Error obteniendo fecha de ".$href );
				}
			}
		} catch (Exception $e) {
			$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
			error_log ($infoExc);
			error_log ("TRACE: ".$e->getTraceAsString());
			$firephp->info($infoExc);
		}

		$cssFile=CACHE_DIR.str_replace('/', '-',dirname($_SERVER['SCRIPT_NAME']))."-css.".md5(serialize($arrFilesModTime)).".css";
		$firephp->info($cssFile,'cssFile:');
		$firephp->group('Fechas de ficheros', array('Collapsed' => true, 'Color' => '#FF9933'));
		foreach ($arrFilesModTime as $filePath => $modTimeStamp) {
			$firephp->info(gmdate('D, d M Y H:i:s \G\M\T',$modTimeStamp),$filePath);
		}
		$firephp->groupend();

		if (file_exists($cssFile)) {
			$firephp->info('cssFile exite: Devolviendo CSS cacheado');
			/*
			$firephp->info($cssFile,'Devolviendo CSS cacheado:');
			$firephp->info(gmdate('D, d M Y H:i:s \G\M\T',time()),'Fecha de ejecución:');
			$firephp->info(array_map(function ($elto) {
				return gmdate('D, d M Y H:i:s \G\M\T',$elto);
			},$arrFilesModTime),'Fechas de modificacion:');
			*/
			echo file_get_contents($cssFile);
		} else {
			$cssLibs="/* <css Libs> */\n";
			$firephp->group('Carga de HREFs CSS', array('Collapsed' => true, 'Color' => '#FF9933'));
			foreach ($hrefs as $href) {
				try {
					$origHref=$href;
					$firephp->info($origHref,'origHref:');

					$baseURI=preg_replace('#[^/]*$#', '', $href);//Quitamos todo desde el último slash (exclusive)

					if (substr($href, 0,1)=='/' && substr($href, 0,2)!='//') {
						$basePath=realpath($_SERVER["DOCUMENT_ROOT"].$baseURI);
						$baseURI=\Filesystem::find_relative_path(dirname($_SERVER['SCRIPT_FILENAME']),$basePath)."/";
						//$firephp->info($baseURI,'baseURI:');
						$href=realpath($_SERVER["DOCUMENT_ROOT"].$href);
						if (!$href) {throw new Exception ('realpath ('.$_SERVER["DOCUMENT_ROOT"].$origHref.') devolvió false, ¿existe el fichero?.');}
					} elseif (substr($href, 0,2)=='./') {
						//$firephp->info(SKEL_ROOT_DIR.' + '.$baseURI,'SKEL_ROOT_DIR y baseURI:');
						//$firephp->info(realpath(SKEL_ROOT_DIR.$baseURI),'realpath SKEL_ROOT_DIR y baseURI:');
						$basePath=realpath(SKEL_ROOT_DIR.$baseURI);
						$baseURI=\Filesystem::find_relative_path(dirname($_SERVER['SCRIPT_FILENAME']),$basePath)."/";
						//$firephp->info($baseURI,'baseURI:');

						$href=realpath(SKEL_ROOT_DIR.$href);
						if (!$href) {throw new Exception ('realpath ('.SKEL_ROOT_DIR.$origHref.') devolvió false, ¿existe el fichero?.');}
					} elseif (substr($href, 0,2)=='//') {
						$href=PROTOCOL.":".$href;
						$firephp->info($baseURI,'baseURI:');
					}

					$firephp->info($href,'Cargando HREF y reescribiendo URLs css:');
					$fileContent=file_get_contents($href)."\n\n";
					$patron="/(url ?\( ?['\"]?)(?!['\"]?data:)(?!['\"]?https?:)([^'\")]+)/";
					$fileContent=preg_replace($patron,'$1'.$baseURI.'$2', $fileContent);
					$cssLibs.="/* <Lib: ".$href."> */\n";
					$cssLibs.=$fileContent;
					$cssLibs.="/* </lib> */\n";
					//$cssLibs.="@import url('".$href."');"."\n";
				} catch (Exception $e) {
					error_log ("css.php:: Imposible cargar '".$href."'");
					$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine().". origHref: ".$origHref;
					error_log ($infoExc);
					$firephp->error($infoExc,'Error cargando href "'.$href.'"');
				}
				//var_dump($http_response_header);
			}
			$firephp->groupend();
			$cssLibs.="/* </css Libs> */";
			ob_start();
				echo $cssLibs;
			$cssLibs=ob_get_clean();
			if ($lessParse) {
				$lessParser = new Less_Parser($arrLessParserOptions);
				$lessParser->parse($cssLibs);
				$cssLibs=$lessParser->getCss();
			}
			file_put_contents($cssFile, $cssLibs);
			echo $cssLibs;
		}
	}
	/******************************************************************************/
?>
<?
	/* css local ******************************************************************/
	ob_start();
		require SKEL_ROOT_DIR."includes/cliente/base.css";
		require RUTA_APP."cliente/appCss.php";

		$Page=new $page($objUsr);
		$Page->css();
	$cssLocal=ob_get_clean();
	if ($lessParse) {
		$lessParser = new Less_Parser($arrLessParserOptions);
		$lessParser->parse($cssLocal);
		$cssLocal=$lessParser->getCss();
	}
	echo "/*CSS LOCAL*/\n".$cssLocal;
	/******************************************************************************/
?>
<?
} catch (Exception $e) {
	$msg='Error durante la generación de css.php.';
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