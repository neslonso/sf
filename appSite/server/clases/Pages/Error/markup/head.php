<?="\n<!-- ".get_class()." -->\n"?>
<script type="text/javascript">
$(document).ready(function() {
<?
	/* BLOQUE PARA MOSTRAR returnInfo SI EXISTE ***************************/
	$sri=\Sintax\Core\ReturnInfo::ensureArray();
	if (count($sri)>0) {
		foreach ($sri as $arrInfo) {
			$title=(isset($arrInfo['title']))?$arrInfo['title']:'';
			$msg=(isset($arrInfo['msg']))?$arrInfo['msg']:'';
			$title=preg_replace("/\r?\n/", "\\n", addslashes($title));
			$msg=preg_replace("/\r?\n/", "\\n", addslashes($msg));
			$llamadaJsMuestraMsgModal='muestraMsgModal(\''.$title.'\',\''.$msg.'\');';
			echo $llamadaJsMuestraMsgModal;
		}
	}
	\Sintax\Core\ReturnInfo::clear();
?>
});
</script>
<?="\n<!-- /".get_class()." -->\n"?>
