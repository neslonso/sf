<?
//Carga automatica de clases que no estén requeridas,
//busca clase en los directorios Logic y Pages de la APP y en appZzSahred/server/clases
spl_autoload_register(function ($clase) {
	//error_log("Intentando carga automatica de clase: ".$clase);
	$clase = end(explode("\\",$clase));//namespaces
	//error_log("Intentando carga automatica de clase: ".$clase);

	$file=str_replace('//','/',dirname(__FILE__).'/') .'clases/Logic/'.$clase.'.php';
	if (file_exists($file)) {
		require_once($file);
	}

	$file=str_replace('//','/',dirname(__FILE__).'/') .'clases/Pages/'.$clase.'/'.$clase.'.php';
	if (file_exists($file)) {
		require_once($file);
	}

	$path=str_replace('//','/',dirname(__FILE__).'/') .'clases/Pages/';
	$file=$clase.'.php';
	$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
	foreach($objects as $name => $object){
		if($object->getFilename() === $file) {
			require_once($object->getPathname());
		}
	}

	$file=str_replace('//','/',dirname(__FILE__).'/') .'../../../appZzShared/server/clases/'.$clase.'.php';
	if (file_exists($file)) {
		require_once($file);
	}
	/* No podemos lanzar una excepcion aunque la clase no existe, porque entonces class_exists deja de funionar
	ob_start();
		var_dump($clase);
	$infoClase=ob_get_clean();
	throw new Exception("No se encontro la clase '".$clase."' en Logic, ni es Page ni en Shared/clases. var_dump: ".$infoClase, 1);
	*/
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