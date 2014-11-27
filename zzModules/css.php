<?
ob_start();
?>
<?
try {
	header('Content-type: text/css; charset=utf-8');
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

?>
<?
	/* css reset & 960 ********************/
	ob_start();
		if (GRID_960) {
			echo "/*<CSS 960>*/\n";
			$reset=SKEL_ROOT_DIR.'includes/cliente/vendor/960-Grid-System-master/code/css/min/reset.css';
			//echo "@import url('".$reset."');"."\n";
			echo file_get_contents($reset);
			$text=SKEL_ROOT_DIR.'includes/cliente/vendor/960-Grid-System-master/code/css/min/text.css';
			//echo "@import url('".$text."');"."\n";
			echo file_get_contents($text);
			if (GRID_960_COLS==12 || GRID_960_COLS==16) {
				$grid960=SKEL_ROOT_DIR.'includes/cliente/vendor/960-Grid-System-master/code/css/min/960.css';
			} else {
				$grid960=SKEL_ROOT_DIR.'includes/cliente/vendor/960-Grid-System-master/code/css/min/960_24_col.css';
			}
			//echo "@import url('".$grid960."');"."\n";
			echo file_get_contents($grid960);
			echo "/*</CSS 960>*/\n";
		}
	$css960=ob_get_clean();
?>
<?
	/* css Js Libs ********************************************************************/
	$cssFile=getlastmod();//Fecha de modificacion de este fichero
	$cssFile.=filemtime(SKEL_ROOT_DIR."includes/server/start.php");
	ob_start();
		foreach ($ARR_CLIENT_LIBS as $libPath) {
			$cssFile.=filemtime($libPath);
			require $libPath;
		}
	$cssLinkTags=ob_get_clean();

	$doc = new DOMDocument();
	$doc->loadHTML($cssLinkTags);
	$linkElements = $doc->getElementsByTagName('link');

	$hrefs = array();
	for($i = 0; $i < $linkElements->length; $i++) {
		$hrefs[]=$linkElements->item($i)->getAttribute('href');
		$href=$linkElements->item($i)->getAttribute('href');
		try {
			//echo "$href was last modified: " . gmdate('D, d M Y H:i:s \G\M\T', filemtime($href))."\n";
			$cssFile.=filemtime($href);
		} catch (Exception $e) {}
	}

	$cssFile=CACHE_DIR."css.".md5($cssFile).".css";
	//echo $cssFile;

	if (file_exists($cssFile)) {
		$firephp->info($cssFile,'devolviendo CSS cacheado:');
		echo file_get_contents($cssFile);
	} else {
		$cssLibs="/* <css Js Libs> */\n";
		$firephp->group('Carga de HREFs CSS', array('Collapsed' => true, 'Color' => '#FF9933'));
		foreach ($hrefs as $href) {
			if (substr($href, 0,2)=='./') {
				$href=realpath(SKEL_ROOT_DIR.$href);
			}
			if (substr($href, 0,2)=='//') {
				$href=PROTOCOL.":".$href;
			}
			$firephp->info($href,'Cargando HREF y reescribiendo URLs css:');
			try {
				$fileContent=file_get_contents($href)."\n\n";
				$baseURI=preg_replace('#[^/]*$#', '', $href);
				$patron="/(url ?\( ?['\"]?)(?!['\"]?data:)(?!['\"]?https?:)([^'\")]+)/";
				$fileContent=preg_replace($patron,'$1'.$baseURI.'$2', $fileContent);
				$cssLibs.="/* <Lib: ".$href."> */\n";
				$cssLibs.=$fileContent;
				$cssLibs.="/* </lib> */\n";
				//$cssLibs.="@import url('".$href."');"."\n";
			} catch (Exception $e) {
				error_log ("css.php:: Imposible cargar '".$href."'");
				$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
				error_log ($infoExc);
				$firephp->error($infoExc,'Error cargando '.$href);
			}
			//var_dump($http_response_header);
		}
		$firephp->groupend();
		$cssLibs.="/* </css Js Libs> */";
		echo $css960.$cssLibs;
		$css=ob_get_clean();
		file_put_contents($cssFile, $css);
		echo $css;
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