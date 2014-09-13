<?
require_once "./includes/server/lib/misc.php";//biblioteca de funciones varias
require_once "./includes/server/lib/returnInfo.php";//biblioteca de funciones para tratar $_SESSION['returnInfo']

require_once "./includes/server/clases/mysqliDB.php";
require_once "./includes/server/clases/Fecha.php";
require_once "./includes/server/clases/Cadena.php";
require_once "./includes/server/clases/Imagen.php";

require_once "./includes/server/clases/Page.php";

if (file_exists(RUTA_APP."server/appClasesPHP.php")) {
	require_once RUTA_APP."server/appClasesPHP.php";
}
?>