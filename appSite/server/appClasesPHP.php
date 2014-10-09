<?
//Carga automatica de clases que no estén requeridas,
//busca clase en los directorios Logic y Pages de la APP y en appZzSahred/server/clases
spl_autoload_register(function ($clase) {
	//error_log("Intentando carga automatica de clase: ".$clase);
	$clase = @end(explode("\\",$clase));//namespaces, genera: PHP Strict Standards:  Only variables should be passed by reference, pq end recibe el resultado de explode por referencia
	//error_log("Intentando carga automatica de clase: ".$clase);
	//
	$fileList=Filesystem::folderSearch(dirname(__FILE__).'/'.'clases/','/.*\/'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		//error_log('require: '.$filePath);
		if (file_exists($filePath)) {
			require_once($filePath);
		}
	}

	$fileList=Filesystem::folderSearch(dirname(__FILE__).'/'.'../../appZzShared/server/clases/','/.*\/'.$clase.'.php$/');
	foreach ($fileList as $filePath) {
		//error_log('require: '.$filePath);
		if (file_exists($filePath)) {
			require_once($filePath);
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