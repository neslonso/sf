<script type="text/javascript" src="./includes/cliente/clases/Function.js"></script>

<script type="text/javascript" src="./includes/cliente/clases/Date.js"></script>
<script type="text/javascript" src="./includes/cliente/clases/DateFormat.js"></script>
<script type="text/javascript" src="./includes/cliente/clases/Log.js"></script>
<script type="text/javascript" src="./includes/cliente/clases/Misc.js"></script>
<script type="text/javascript" src="./includes/cliente/clases/Post.js"></script>

<!--<script type="text/javascript" src="<?=RUTA_APP?>cliente/appClasesJS.php"></script>-->
<?
if (file_exists(RUTA_APP."cliente/appClasesJS.php")) {
	require_once RUTA_APP."cliente/appClasesJS.php";
}
?>