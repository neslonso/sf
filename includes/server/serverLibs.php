<?
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
require SKEL_ROOT_DIR."includes/server/vendor/composerVendor/autoload.php";
?>