<?
spl_autoload_register(function ($clase) {
	$clase = @end(explode('\\',$clase));//namespaces, genera: PHP Strict Standards:  Only variables should be passed by reference, pq end recibe el resultado de explode por referencia
	$fileList=Filesystem::folderSearch(dirname(__FILE__).'/'.'clases/','/.*\/'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		if (file_exists($filePath)) {
			require_once($filePath);
		}
	}
	$fileList=Filesystem::folderSearch(dirname(__FILE__).'/'.'../../appZzShared/server/clases/','/.*\/'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		if (file_exists($filePath)) {
			require_once($filePath);
		}
	}
});
?>
