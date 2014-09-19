<?
require_once "./includes/server/lib/misc.php";//biblioteca de funciones varias
require_once "./includes/server/lib/returnInfo.php";//biblioteca de funciones para tratar $_SESSION['returnInfo']

require_once "./includes/server/clases/mysqliDB.php";
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
?>