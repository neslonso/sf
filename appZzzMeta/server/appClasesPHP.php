<?
//Carga automatica de clases que no estén requeridas,
//busca clase en el directorio clases y en appZzSahred/server/clases
spl_autoload_register(function ($clase) {
	$clase = @end(explode('\\',$clase));//namespaces, genera: PHP Strict Standards:  Only variables should be passed by reference, pq end recibe el resultado de explode por referencia
	$fileList=Filesystem::folderSearch(dirname(__FILE__).DIRECTORY_SEPARATOR.'clases'.DIRECTORY_SEPARATOR,'/.*'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		if (file_exists($filePath)) {
			require_once($filePath);
			return;
		}
	}

	$fileList=Filesystem::folderSearch(dirname(__FILE__).DIRECTORY_SEPARATOR.
		'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'appZzShared'.DIRECTORY_SEPARATOR.'server'.DIRECTORY_SEPARATOR.'clases'.DIRECTORY_SEPARATOR,
		'/.*'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		if (file_exists($filePath)) {
			require_once($filePath);
			return;
		}
	}
});

/******************************************************************************/
/* Libs */
/**/
/* Logic */
/**/
/* Pages */
/**/
	require_once( str_replace('//','/',dirname(__FILE__).'/') .'clases/Pages/zzTools/Creacion/Creacion.php');
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'clases/Pages/zzTools/Creacion/claseCreadora.mysqliDB.php');

?>