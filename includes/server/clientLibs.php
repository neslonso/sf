<?
/******************************************************************************/
/* JS LIBS ********************************************************************/
/******************************************************************************/
define('MODERNIZR_LATEST',false);//true o false

//define('ANGULAR_JS',false);//false o version
define('ANGULAR_JS','1.2.14');//false o version

//define('JQUERY',false);//false o version
define('JQUERY','2.1.0');//false o version (Obligatorio incluirlo, sin el no funciona la app de ejemplo)
	//define('JQUERY_UI',false);//false o version
	define('JQUERY_UI','1.10.4');//false o version
		define('JQUERY_UI_THEME','cupertino');//nombre del tema -> jqueryui.com/themeroller
	//define('BOOTSTRAP',false);//false o version
	define('BOOTSTRAP','3.1.1');//false o version
		define('BOOTSWATCH_THEME',false);//false para tema por defecto o nombre del tema -> bootswatch.com
/******************************************************************************/
/* CSS LIBS *******************************************************************/
/******************************************************************************/
define ('GRID_960',false);
	define ('GRID_960_COLS',16);//12 (que incluye el de 16) o 24
define ('FONT_AWESOME','4.2.0');//false o version
/******************************************************************************/

$ARR_CLIENT_LIBS=array();
	if (MODERNIZR_LATEST!=false) {array_push($ARR_CLIENT_LIBS,"./includes/cliente/modernizr.php");}
	if (ANGULAR_JS!=false) {array_push($ARR_CLIENT_LIBS,"./includes/cliente/angularJS.php");}
	if (JQUERY!=false) {
		array_push($ARR_CLIENT_LIBS,"./includes/cliente/jquery.php");
		array_push($ARR_CLIENT_LIBS,"./includes/cliente/jqueryPlugins.php");
		if (JQUERY_UI!=false) {
			array_push($ARR_CLIENT_LIBS,"./includes/cliente/jqueryUI.php");
			array_push($ARR_CLIENT_LIBS,"./includes/cliente/jqueryUIplugins.php");
		}
		if (BOOTSTRAP!=false) {
			array_push($ARR_CLIENT_LIBS,"./includes/cliente/bootstrap.php");
			array_push($ARR_CLIENT_LIBS,"./includes/cliente/bootstrapPlugins.php");
		}
	}
	if (FONT_AWESOME!=false) {
		array_push($ARR_CLIENT_LIBS,"./includes/cliente/fontAwesome.php");
	}
	array_push($ARR_CLIENT_LIBS,"./includes/cliente/clasesJS.php");
/******************************************************************************/
?>