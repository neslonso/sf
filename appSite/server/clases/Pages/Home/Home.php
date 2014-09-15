<?
class Home extends Error implements IPage {

	public function __construct (Usuario $objUsr=NULL) {
		parent::__construct($objUsr);
	}

	public function pageValida () {
		$result=true;
		return $result;
	}
	public function accionValida($metodo) {
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
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/structure.php');
			break;
			case '5':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/parts/download.php');
			break;
			case '6':
				echo '<div class="titulo"><div>Titulo grande de sección</div></div>';
			break;
			case '7':
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
										<li><a href="<?=FILE_APP?>?page=<?=$filename?>"><?=$filename?></a></li>
<?
				} else {
					$subdir=$dir.$filename."/";
?>
										<li>
											Carpeta de Pages (<?=$subdir?>):
											<ul>
<?
					$arrFilenamesSubdir = scandir($subdir);
					foreach($arrFilenamesSubdir as $filenameSubdir) {
						if ($filenameSubdir=="." || $filenameSubdir=="..") {continue;}
						if (is_dir($subdir.$filenameSubdir)) {
							if (file_exists($subdir.$filenameSubdir."/".$filenameSubdir.".php")) {

?>
												<li><a href="<?=FILE_APP?>?page=<?=$filenameSubdir?>"><?=$filenameSubdir?></a></li>
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

	public static function path2array($path,$excludingRegEx='/^$/',$maxDepth=999,$nodesAsSplFileInfo=false) {
		//$path='./binaries/';
		$path=realpath($path)."/";
		$result=$arrDirs=$arrFiles=array();
		$arrFilenames = scandir($path);
		foreach($arrFilenames as $filename) {
			if ($filename=="." || $filename=="..") {continue;}
			$exclude=false;
			if (preg_match($excludingRegEx, $path.$filename)) {
				$exclude=true;
			}
			if ($exclude) {continue;}
			if ($nodesAsSplFileInfo) {
				$objSplFileInfo=new SplFileInfo ($path.$filename);
				$isDir=$objSplFileInfo->isDir();
			} else {
				$isDir=is_dir($path.$filename);
			}
			$fileFullPath=$path.$filename;
			if ($isDir) {
				$newPath=$path.$filename;
				$newDepth=$maxDepth-1;
				if ($newDepth>0) {
					$children=self::path2array($newPath,$excludingRegEx,$newDepth);
					if ($nodesAsSplFileInfo) {
						$objSplFileInfo->children=$children;
						$arrDirs[$fileFullPath."/"]=$objSplFileInfo;
					} else {
						$arrDirs[$fileFullPath."/"]=$children;
					}
					unset ($children);
				} else {
					if ($nodesAsSplFileInfo) {
						$arrDirs[$fileFullPath."/"]=$objSplFileInfo;
					} else {
						$arrDirs[$fileFullPath."/"]=$filename."/";
					}
				}
			} else {
				if ($nodesAsSplFileInfo) {
					$arrFiles[$fileFullPath]=$objSplFileInfo;
				} else {
					$arrFiles[$fileFullPath]=$filename;
				}
			}
		}
		$result=$arrDirs+$arrFiles;
		return $result;
	}

	public static function array2list($array,$recursiveCall=false) {
		$ulStyle='list-style-type:none;';
		if ($recursiveCall) {$ulStyle.='display:none;';}
		$result='<ul style="'.$ulStyle.'">';
		foreach ($array as $key => $value) {
			$nameForList=basename($key);
			if (is_array($value)) {
				$liStyle="cursor:pointer;";
				$liContent='<i class="fa fa-folder-o"></i> '.$nameForList.self::array2list($value,true);
				$liClick='
					$(this).children(\'ul\').toggle();
					$(this).children(\'i\').toggleClass(\'fa-folder-o\');
					$(this).children(\'i\').toggleClass(\'fa-folder-open-o\');
					arguments[0].stopPropagation();
				';
			} else {
				$liStyle="cursor:default;";
				$liContent='<i class="fa fa-file-code-o"></i> '.$nameForList;
				$liClick='';
			}
			$result.='<li style="'.$liStyle.'" onclick="'.$liClick.'">'. $liContent.'</li>';
		}
		$result.="</ul>";
		return $result;
	}

	public static function array2zip($array,$destino,$zip=NULL) {
		if (is_null($zip)) {
			if (!extension_loaded('zip')) {
				return false;
			}
			$zip=new ZipArchive();
			if (!$zip->open($destino,ZIPARCHIVE::OVERWRITE)) {
				return false;
			}
		}
		$scriptDir=dirname($_SERVER['SCRIPT_FILENAME']).'/';
		foreach ($array as $key => $value) {
			$fileToZip=str_replace($scriptDir, '', $key);
			if (is_array($value)) {
				$zip->addEmptyDir($fileToZip);
				$zip=self::array2zip($value,$destino,$zip);
			} else {
				$zip->addFile($fileToZip);
			}
		}
		if (is_null($zip)) {
			return $zip->close();
		} else {
			return $zip;
		}
	}

	public function acPackCode () {
		$excludingRexEx=array (
			"/",
			"vendor",
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
		$arr=self::path2array("./",implode('',$excludingRexEx));
		$file='./zCache/tmpUpload/Sintax.zip';
		if (file_exists($file)) {unlink($file);}
		self::array2zip($arr,$file);

		ob_clean();//limpiamos el buffer antes de mandar el fichero, no queremos nada más que el fichero
		header ("Content-Disposition: attachment; filename=".basename($file)."\n\n");
		header ('Content-Transfer-Encoding: binary');
		header ("Content-Type: application/octet-stream");
		header ("Content-Length: ".filesize($file));
		readfile($file);
	}
}
?>