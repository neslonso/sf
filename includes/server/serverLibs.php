<?
require_once "./includes/server/lib/misc.php";//biblioteca de funciones varias
require_once "./includes/server/lib/returnInfo.php";//biblioteca de funciones para tratar $_SESSION['returnInfo']

require_once "./includes/server/clases/MysqliDB.php";
require_once "./includes/server/clases/Fecha.php";
require_once "./includes/server/clases/Cadena.php";
require_once "./includes/server/clases/Imagen.php";

require_once "./includes/server/clases/Page.php";

require_once "./includes/server/vendor/PHPMailer_v5.1/class.phpmailer.php";
require_once "./includes/server/vendor/blueimp-jQuery-File-Upload/UploadHandler.php";
require_once "./includes/server/vendor/jsmin-1.1.1.php";

//CronExpression: https://github.com/mtdowling/cron-expression
require_once "./includes/server/vendor/cron-expression-1.0.3/src/Cron/FieldInterface.php";
foreach (glob("./includes/server/vendor/cron-expression-1.0.3/src/Cron/*.php") as $filename) {require_once $filename;}

//PHP Token Reflection: https://github.com/Andrewsville/PHP-Token-Reflection
//error_log(get_include_path());
set_include_path(
	realpath(__DIR__ . '/vendor/PHP-Token-Reflection-1.4.0') . PATH_SEPARATOR . // Library
	get_include_path()
);
//error_log("/***/");
//error_log(get_include_path());

spl_autoload_register(function($className) {
	$file = strtr($className, '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) . '.php';
	//error_log($file);
	if (!function_exists('stream_resolve_include_path') || false !== stream_resolve_include_path($file)) {
		@include_once $file;
	}
});
?>