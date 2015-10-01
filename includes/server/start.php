<?
mb_internal_encoding("UTF-8");
//El UTF-8 para la conexion a la db se establece en el constructor de MysqliDB

date_default_timezone_set('Europe/Madrid');

//No utilizo setlocale, en cuestion de numeros, hace que los floats tengan la , por separador decimal al
//convertirlos a cadena, lo que da problemas al construir sentencias SQL o mandar JSON a JS
//setlocale(LC_ALL,'es_ES');

/**/
define ('SKEL_VERSION','1.0.0');
define('PROTOCOL',((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']))?'https':'http'));
define('BASE_DOMAIN',(substr($_SERVER['HTTP_HOST'],0,4)=="www.")?substr($_SERVER['HTTP_HOST'],4):$_SERVER['HTTP_HOST']);
define('BASE_DIR',
	(dirname($_SERVER['SCRIPT_NAME'])=='/')?
		dirname($_SERVER['SCRIPT_NAME']):
		dirname($_SERVER['SCRIPT_NAME']).'/'
);//Directorio del punto de entrada. Tiene que terminar en / obligatoriamente
define('BASE_URL',PROTOCOL.'://'.BASE_DOMAIN.BASE_DIR);//URL hasta el directorio del punto de entrada

define('CACHE_DIR',SKEL_ROOT_DIR.'zCache/');
define('TMP_UPLOAD_DIR',SKEL_ROOT_DIR.'zCache/tmpUpload/');
define('BASE_IMGS_DIR',SKEL_ROOT_DIR.'binaries/imgs/');

define ('DEBUG_EMAIL','nestor@parqueweb.com');

/* No implementado
define('SECS_PERSIST_LASTACTION',60*5);//Segundos durante los cuales se consideran validos los datos de lastAction
/**/

define ('IPS_DEV', serialize(array_merge(
	array('::1'),
	array(
		//'81.35.169.245',//León Carbajal
		//'83.53.147.57',//León Carbajal 20141118
		//'88.9.65.74',//León Carbajal 20141209
		//'79.145.230.48',//León Carbajal 20150216
		//'79.146.211.176',//León Carbajal 20150420
		//'88.14.233.166',//León Carbajal 20150625
		//'83.53.147.109',//León Carbajal 20150815
		//'88.14.237.3',//León Carbajal 20150818
		//'88.10.191.54',//León Carbajal 20150910
		'88.21.228.165',//León Carbajal 20150915
	),
	array(
		'193.146.109.133',//Unileon
	),
	array(
		'91.117.107.217',//Coruña oficna
	),
	array(
		//'47.62.0.55',//Diego Madrid
		//'47.62.0.219',//Diego Madrid 20150216
		'47.62.161.180',//Diego Madrid 20150831
	),
	array()
)));
define ('MODULES', serialize(array(
	'actions' => SKEL_ROOT_DIR.'zzModules/actions.php',
	'api' => SKEL_ROOT_DIR.'zzModules/api.php',
	'auto' => SKEL_ROOT_DIR.'zzModules/auto.php',
	'css' => SKEL_ROOT_DIR.'zzModules/css.php',
	'images' => SKEL_ROOT_DIR.'zzModules/images.php',
	'js' => SKEL_ROOT_DIR.'zzModules/js.php',
	'render' => SKEL_ROOT_DIR.'zzModules/render.php',
	'phpunit' => SKEL_ROOT_DIR.'zzModules/phpunit.php',
)));

//Listamos todas las aplicaciones del proyecto asociando cada punto de entrada a la ruta y nombre de la APP
define ('APPS', serialize(array(
	'index.php' => array(
		//'KEY_APP' => 'index.php',//siempre igual que la key (futuro)
		'FILE_APP' => 'index.php',
		'RUTA_APP' => SKEL_ROOT_DIR.'appZzzMeta/',
		'NOMBRE_APP' => 'Sintax tools (index del raiz)',
	),
	'sintax/index.php' => array(
		//'KEY_APP' => 'sintax/index.php',//siempre igual que la key (futuro)
		'FILE_APP' => 'index.php',
		'RUTA_APP' => SKEL_ROOT_DIR.'appZzzMeta/',
		'NOMBRE_APP' => 'Sintax tools',
	),
)));
//Definimos todas las constantes de la aplicacion correspondiente al punto de entrada
require_once SKEL_ROOT_DIR."includes/server/serverLibs.php";
require_once SKEL_ROOT_DIR."includes/server/FirePHP.php";

$arrApps=unserialize(APPS);
$appKey=\Filesystem::find_relative_path(SKEL_ROOT_DIR,$_SERVER['SCRIPT_FILENAME']);
if (isset($arrApps[$appKey])) {
	foreach ($arrApps[$appKey] as $key => $value) {
		define ($key,$value);
	}
} else {
	throw new \Exception("No se encontró APP con clave: ".$appKey,1);
}

require_once SKEL_ROOT_DIR."includes/server/clientLibs.php";

if (defined('RUTA_APP')) {
	if (file_exists(RUTA_APP."server/appDefines.php")) {
		require_once RUTA_APP."server/appDefines.php";
		if (file_exists(RUTA_APP."server/appClasesPHP.php")) {
			require_once RUTA_APP."server/appClasesPHP.php";
		}
		if (file_exists(RUTA_APP."server/appAuto.php")) {
			require_once RUTA_APP."server/appAuto.php";
		}
		if (file_exists(RUTA_APP."server/appApi.php")) {
			require_once RUTA_APP."server/appApi.php";
		}
	} else {
		error_log("/**/");
		error_log("/*".__FILE__.":".__LINE__."*/");
		error_log("URI: ".$_SERVER['REQUEST_URI']);
		error_log("RUTA_APP: ".RUTA_APP);
		error_log("/**/");
		error_log("/**/");
		throw new Exception("APP no encontrada en ".RUTA_APP);
	}
} else {
	error_log("/**/");
	error_log("/*".__FILE__.":".__LINE__."*/");
	error_log("URI: ".$_SERVER['REQUEST_URI']);
	error_log("/**/");
	error_log("/**/");
	//throw new Exception("RUTA_APP no definida. La App a utilizar debe estar asociada al nombre del script (index.php, admin.php...) o ser suministrada en el parametro APP ");
	throw new Exception("RUTA_APP no definida. SCRIPT_NAME: ".$_SERVER['SCRIPT_NAME']." :-: Basename: ".basename($_SERVER['SCRIPT_NAME']));
}
?>