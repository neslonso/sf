<?
class Filesystem {
	public static function path2array($path,$excludingRegEx='/^$/',$maxDepth=999,$nodesAsSplFileInfo=false) {
		//$path='./binaries/';
		$path=realpath($path)."/";
		$result=$arrDirs=$arrFiles=array();
		$arrFilenames = scandir($path);
		foreach($arrFilenames as $filename) {
			if ($filename=="." || $filename=="..") {continue;}
			$fileFullPath=$path.$filename;
			$exclude=false;
			if (preg_match($excludingRegEx, $fileFullPath)===1) {
				$exclude=true;
			}
			if ($exclude) {continue;}
			if ($nodesAsSplFileInfo) {
				$objSplFileInfo=new SplFileInfo ($fileFullPath);
				$isDir=$objSplFileInfo->isDir();
			} else {
				$isDir=is_dir($fileFullPath);
			}

			if ($isDir) {
				$newPath=$fileFullPath;
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

	public static function array2list($array, $cssClass='ulFiles', $recursiveCall=false) {
		$ulStyle='';
		if ($recursiveCall) {$ulStyle.='display:none;';}
		$result='<ul class="'.$cssClass.'" style="'.$ulStyle.'">';
		foreach ($array as $key => $value) {
			$nameForList=basename($key);
			if (is_array($value)) {
				$liStyle="cursor:pointer;";
				$liContent='<i class="fa fa-folder-o"></i> '.$nameForList.self::array2list($value,$cssClass,true);
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

	public static function folderSearch($folder, $pattern) {
		$fld = new RecursiveDirectoryIterator($folder);
		$ite = new RecursiveIteratorIterator($fld);
		$files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
		$fileList = array();
		foreach($files as $file) {
			$fileList = array_merge($fileList, $file);
		}
		return $fileList;
	}

	public static function pharSearch($phar, $pattern) {
		$phar=new Phar($phar);
		$ite = new RecursiveIteratorIterator($phar);
		$files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
		$fileList = array();
		foreach($files as $file) {
			$fileList = array_merge($fileList, $file);
		}
		return $fileList;
	}
}
?>