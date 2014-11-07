<?
ob_start();
?>
<?
try {
	header('Content-Type: text/html; charset=utf-8');
	session_start();

	/*mail (DEBUG_EMAIL,SITE_NAME.". RENDER.PHP ".FILE_APP.": ".$_SERVER['REMOTE_ADDR'],
		print_r($GLOBALS,true)."\n\n\n--\n\n\n".print_r($_SESSION,true));
	*/

	$pageHome=(defined('PAGE_HOME_APP'))?PAGE_HOME_APP:'Home';
	$page=(isset($_GET['page']))?$_GET['page']:$pageHome;
	$page='Sintax\\Pages\\'.$page;

	$objUsr=new Sintax\Core\AnonymousUser();
	if (isset($_SESSION['usuario'])) {
		//$objUsr=$_SESSION['usuario'];
		$usrClass=get_class($_SESSION['usuario']);
		if ($usrClass!="__PHP_Incomplete_Class") {
			$objUsr=$_SESSION['usuario'];
		}
	}

	logPageData('Creación de página');
	if (class_exists($page)) {
		do {
			$arrSustitucion=(isset($_SESSION['arrSustitucion']))?$_SESSION['arrSustitucion']:array();
			$Page=new $page($objUsr);
			$page=$Page->pageValida();
			if (class_exists($page)) {//pageValida devuelve una clase existente, hacemos la sustitucion
				$url=(count($arrSustitucion))?BASE_DIR.FILE_APP.'?page='.get_class($Page):$_SERVER['REQUEST_URI'];
				array_push($arrSustitucion,array('page' => get_class($Page), 'url' => $url));
				$_SESSION['arrSustitucion']=$arrSustitucion;
				$firephp->info('Page "'.get_class($Page).'" no valida, sustuticion: '.get_class($Page).' por '.$page.'. url sustituida: '.$url);
				unset($Page);
			} else {//no hay sustitucion
				$Page->arrSustitucion=$arrSustitucion;
				if (isset($_SESSION['arrSustitucion'])) {unset($_SESSION['arrSustitucion']);}
			}
		}
		while (class_exists($page));
		$page=get_class($Page);
		$firephp->info($arrSustitucion,'arrSustitucion');
	} else {
		throw new Exception('La clase de página solicitada "'.$page.'" no existe.');
	}
	$firephp->groupend();

	markup ($Page);

	logPageData('Finalización de página');

} catch (Exception $e) {//Excepcion durante la representación de la página
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	error_log ($infoExc);
	//error_log ("TRACE: ".$e->getTraceAsString());
	$firephp->info($infoExc);
	$firephp->info($e->getTraceAsString(),"traceAsString");
	$firephp->info($e->getTrace(),"trace");

	try {
		$Page=new Sintax\Pages\Error($objUsr);
		$Page->setMsg($e->getMessage());
		ob_clean();//limpiamos el buffer para eliminar lo que se haya podido meter antes de saltar la excepción
		markup($Page);
	} catch (Exception $ee) {//Excepción durante la representación del error usando la clase de página Error
		try {
			$Page=new Sintax\Pages\Error();
			$msg='Error recuperable durante el tratamiento de otro error recuperable.<ul><li>'.$ee->getMessage().'</li><li>'.$e->getMessage().'</li></ul>';
			$Page->setMsg($msg);
			ob_clean();
			markup($Page);
		} catch (Exception $eee) {//Excepción durante la representación del error usando la clase de página Error sin usuario
			$msg='Error no recuperable durante el tratamiento de un error recuperable.';
			$infoExc="Excepcion de tipo: ".get_class($eee).". Mensaje: ".$eee->getMessage()." en fichero ".$eee->getFile()." en linea ".$eee->getLine();
			error_log ($infoExc);
			//error_log ("TRACE: ".$eee->getTraceAsString());
			$firephp->group($msg, array('Collapsed' => false, 'Color' => '#FF6600'));
			$firephp->info($infoExc);
			$firephp->info($eee->getTraceAsString(),"traceAsString");
			$firephp->info($eee->getTrace(),"trace");
			$firephp->groupEnd();
			ob_clean();
			echo '<h1>'.date("YmdHis").': '.$msg.'</h1>';
		}
	}
}
?>
<?
/***********************************************************************************************************************************/
?>
<?
function markup ($Page) {
?>
<!DOCTYPE html>
<?
/*
?>
<!--<!DOCTYPE html>-->
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">-->
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">-->
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<?
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es"
	xmlns:og="http://opengraphprotocol.org/schema/"
	xmlns:fb="http://www.facebook.com/2008/fbml">
<!--
xmlns:og="http://ogp.me/ns#"
xmlns:fb="http://developers.facebook.com/schema/"
xmlns:og="http://opengraphprotocol.org/schema/"
xmlns:fb="http://www.facebook.com/2008/fbml"
-->
<head>
<title><?=$Page->title()?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Page Meta (Class <?=get_class($Page);?>)-->
<?=$Page->metaTags()?>
<!-- /Page Meta (Class <?=get_class($Page);?>)-->
<!--Favicon -->
<?=$Page->favIcon()?>
<!-- /Favicon -->
<link rel="stylesheet" href="<?=BASE_URL?><?=FILE_APP?>?MODULE=CSS&amp;APP=<?=FILE_APP?>&amp;page=<?=get_class($Page)?>" />
<script type="text/javascript" src="<?=BASE_URL?><?=FILE_APP?>?MODULE=JS&amp;APP=<?=FILE_APP?>&amp;page=<?=get_class($Page)?>"></script>
<!-- Page Head (Class <?=get_class($Page);?>)-->
<?=$Page->head()."\n";?>
<!-- /Page Head (Class <?=get_class($Page);?>)-->
</head>
<body>
	<div id="page" style="display:none;">
<!-- Page Markup (Class <?=get_class($Page);?>)-->
<?=$Page->markup()."\n";?>
<!-- /Page Markup (Class <?=get_class($Page);?>)-->
	</div>
	<noscript>
		Para utilizar las funcionalidades completas de este sitio es necesario tener
		JavaScript habilitado. Aquí están las <a href="http://www.enable-javascript.com/es/"
		target="_blank"> instrucciones para habilitar JavaScript en tu navegador web</a>.
	</noscript>
</body>
</html>
<?
}
?>