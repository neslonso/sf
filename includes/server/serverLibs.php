<?
define('PHP_UNIT',true);//true o false

require_once SKEL_ROOT_DIR."includes/server/lib/misc.php";//biblioteca de funciones varias
require_once SKEL_ROOT_DIR."includes/server/lib/returnInfo.php";//biblioteca de funciones para tratar $_SESSION['returnInfo']

require_once SKEL_ROOT_DIR."includes/server/clases/MysqliDB.php";
require_once SKEL_ROOT_DIR."includes/server/clases/Fecha.php";
require_once SKEL_ROOT_DIR."includes/server/clases/Cadena.php";
require_once SKEL_ROOT_DIR."includes/server/clases/Imagen.php";
require_once SKEL_ROOT_DIR."includes/server/clases/Filesystem.php";

require_once SKEL_ROOT_DIR."includes/server/clases/User.php";
require_once SKEL_ROOT_DIR."includes/server/clases/Page.php";

require_once SKEL_ROOT_DIR."includes/server/vendor/PHPMailer_v5.1/class.phpmailer.php";
require_once SKEL_ROOT_DIR."includes/server/vendor/blueimp-jQuery-File-Upload/UploadHandler.php";
require_once SKEL_ROOT_DIR."includes/server/vendor/jsmin-1.1.1.php";

//CronExpression: https://github.com/mtdowling/cron-expression
require_once SKEL_ROOT_DIR."includes/server/vendor/cron-expression-1.0.3/src/Cron/FieldInterface.php";
foreach (glob(SKEL_ROOT_DIR."includes/server/vendor/cron-expression-1.0.3/src/Cron/*.php") as $filename) {require_once $filename;}

//PHP Token Reflection: https://github.com/Andrewsville/PHP-Token-Reflection
set_include_path(
	realpath(__DIR__ . '/vendor/PHP-Token-Reflection-1.4.0') . PATH_SEPARATOR . // Library
	get_include_path()
);
spl_autoload_register(function($className) {
	$file = strtr($className, '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '.php';
	//error_log($file);
	if (!function_exists('stream_resolve_include_path') || false !== stream_resolve_include_path($file)) {
		@include_once $file;
	}
});
//PHPUnit: https://phpunit.de/  & https://github.com/sebastianbergmann/phpunit
if (PHP_UNIT) {
	$fileList=Filesystem::pharSearch (SKEL_ROOT_DIR.'includes/server/vendor/phpunit.phar',"/.*\.php/");
	spl_autoload_register(function($className) use (&$fileList) {
		//static $sg='';
		//static $sgLen=2;
		//$sg.='  ';
		//error_log($sg."Intentando autoload: ".$className);
		$className = @end(explode("\\",$className));//namespaces, genera: PHP Strict Standards:  Only variables should be passed by reference, pq end recibe el resultado de explode por referencia
		$regEx='/.*(interface|class) '.$className.'\b/';
		foreach ($fileList as $key => $path) {
			$contents=file_get_contents($path);
			if (preg_match($regEx, $contents)===1) {
				//error_log($sg."+ ".$className." encontrada en: ".basename($path));
				require_once ($path);
				//unset ($fileList[$key]);
				//error_log(count($fileList));
				break;
			}
		}
		//error_log($sg."Fin autoload: ".$className);
		//$sg=substr($sg, 0,-$sgLen);
	});
}
// Composer:
require 'phar://'.realpath(SKEL_ROOT_DIR.'includes/server/vendor/composer.phar').'/vendor/autoload.php'; // require composer dependencies
?>