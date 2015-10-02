<?
$ARR_CLIENT_LIBS=array();
array_push($ARR_CLIENT_LIBS,SKEL_ROOT_DIR."includes/cliente/bowerComponents.php");
array_push($ARR_CLIENT_LIBS,RUTA_APP."cliente/appBowerComponents.php");

array_push($ARR_CLIENT_LIBS,SKEL_ROOT_DIR."includes/cliente/composerAssetPluginComponents.php");
array_push($ARR_CLIENT_LIBS,RUTA_APP."cliente/appComposerAssetPluginComponents.php");

array_push($ARR_CLIENT_LIBS,SKEL_ROOT_DIR."includes/cliente/clasesJS.php");
array_push($ARR_CLIENT_LIBS,RUTA_APP."cliente/appClasesJS.php");

define ('BOOTSTRAP',true);
?>