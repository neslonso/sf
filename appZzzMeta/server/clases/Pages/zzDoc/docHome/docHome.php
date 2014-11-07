<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

class docHome extends Error implements IPage {

	public function __construct (User $objUsr=NULL) {
		parent::__construct($objUsr);
	}

	public function pageValida () {
		//return $this->objUsr->pagePermitida($this);
		$result=true;
		return $result;
	}
	public function accionValida($metodo) {
		//return $this->objUsr->accionPermitida($this,$metodo);
		switch ($metodo) {
			case "acPackCode": $result=true;break;
			default: $result=false;
		}
		return $result;
	}

	public function title() {

	}

	public function head() {
		parent::head();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/head.php');
	}

	public function js() {
		parent::js();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/js.php');
	}

	public function css() {
		parent::css();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/css.php');
	}

	public function markup() {
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/markup.php');
	}

	public function nav() {
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/nav.php');
	}

	public function header() {
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/header.php');
	}


	public function content() {
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/content.php');
	}


	public function fragment($indice) {
		switch ($indice) {
			case '1':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/features.php');
			break;
			case '2':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/quickstart.php');
			break;
			case '3':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/workflow.php');
			break;
			case '4':
				$excludingRexEx=array (
					"/",
					"vendor\/.*", "|",
					"aaReferences", "|",
					"(css|jsMin)\.(.+)\.(css|js)", "|",
					"zzWorkspace", "|",
					"google", "|",
					//excluimos tambien las direcorios de APPs
					preg_quote(BASE_DIR,'/')."app(?!Zz)(.*)", "|",
					//excluimos ficheros varios
					"(\.htaccess|LICENSE|README\.md|crossdomain\.xml|humans\.txt|robots\.txt|.*\.lock)", "|",
					//carpeta de code coverage de phpunit
					"(1)",
					"/"
				);
				$arrSintaxFiles=\Filesystem::path2array(SKEL_ROOT_DIR."./",implode('',$excludingRexEx));
				$excludingRexEx=array (
					"/",
					preg_quote(RUTA_APP,'/')."server\/clases\/Logic\/.*",
					"|",preg_quote(RUTA_APP,'/')."server\/clases\/Pages\/(?!Home|Error)",
					"|","markup\/parts",
					"|",".htaccess",
					"/"
				);

				$arrAppFiles=\Filesystem::path2array(RUTA_APP,implode('',$excludingRexEx));
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/structure.php');
			break;
			case '5':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/download.php');
			break;
		}
	}

	public function ulPages ($dir) {
?>
<ul>
<?
		$arrFilenames = scandir($dir);
		foreach($arrFilenames as $filename) {
			if ($filename=="." || $filename=="..") {continue;}
			if (is_dir($dir.$filename)) {
				if (file_exists($dir.$filename."/".$filename.".php")) {
?>
										<li><a href="<?=BASE_URL?><?=FILE_APP?>?page=<?=$filename?>"><?=$filename?></a></li>
<?
				} else {
					$subdir=$dir.$filename."/";
?>
										<li>
											Carpeta de Pages (<?=basename($subdir)?>):
											<ul>
<?
					$arrFilenamesSubdir = scandir($subdir);
					foreach($arrFilenamesSubdir as $filenameSubdir) {
						if ($filenameSubdir=="." || $filenameSubdir=="..") {continue;}
						if (is_dir($subdir.$filenameSubdir)) {
							if (file_exists($subdir.$filenameSubdir."/".$filenameSubdir.".php")) {

?>
												<li><a href="<?=BASE_URL?><?=FILE_APP?>?page=<?=$filenameSubdir?>"><?=$filenameSubdir?></a></li>
<?
							}else {
?>
												<li><?=$filenameSubdir?> (No es una page)</li>
<?
							}
						}
					}
?>
											</ul>
										</li>
<?

				}
			}
		}
?>
									</ul>
<?
	}

	public function acPackCode ($type='noVendorCode') {
		$dir=SKEL_ROOT_DIR.'./zCache/tmpUpload/';
		$arrFilesOrFalse=glob($dir."Sintax*");
		if ($arrFilesOrFalse) {
			foreach ($arrFilesOrFalse as $file) {
				if (file_exists($file)) {unlink($file);}
			}
		}
		error_log($type);
		if ($type=='withVendorCode') {
			$excludingRexEx=array (
				"/",
				"aaReferences",
				"|",
				"(css|jsMin)\.(.+)\.(css|js)",
				"|",
				"zzWorkspace",
				"|",
				".zip$",
				"/"
			);
		} else {
			$excludingRexEx=array (
				"/",
				"vendor\/.*",
				"|",
				"aaReferences",
				"|",
				"(css|jsMin)\.(.+)\.(css|js)",
				"|",
				"zzWorkspace",
				"|",
				".zip$",
				"/"
			);
		}
		$arr=\Filesystem::path2array(SKEL_ROOT_DIR."./",implode('',$excludingRexEx));
		$file=$dir.'Sintax.'.SKEL_VERSION.'.zip';
		\Filesystem::array2zip($arr,$file);

		ob_clean();//limpiamos el buffer antes de mandar el fichero, no queremos nada más que el fichero
		header ("Content-Disposition: attachment; filename=".basename($file)."\n\n");
		header ('Content-Transfer-Encoding: binary');
		header ("Content-Type: application/octet-stream");
		header ("Content-Length: ".filesize($file));
		readfile($file);
	}
}
?>