<?="\n<!-- ".get_class()." -->\n"?>
<h1>Bower</h1>
<a href="https://packagist.org/">https://packagist.org/</a>
<hr />
<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data">
	<input name="MODULE" id="MODULE" type="hidden" value="actions"/>
	<input name="acClase" id="acClase" type="hidden" value="bower"/>
	<input name="acMetodo" id="acMetodo" type="hidden" value="acExec"/>
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>"/>


	<input type="radio" name="cCmd" value="install" <?=$cCmd['install']?> /> install
	<input type="radio" name="cCmd" value="update" <?=$cCmd['update']?> /> update
	<input type="radio" name="cCmd" value="uninstall" <?=$cCmd['uninstall']?> /> uninstall
	<input type="radio" name="cCmd" value="search" <?=$cCmd['search']?> /> search
	<input type="radio" name="cCmd" value="info" <?=$cCmd['info']?> /> info
	<input type="radio" name="cCmd" value="list" <?=$cCmd['list']?> /> list
	<br />

	Paquete: <input type="text" name="pkgs" value="<?=$pkgs?>"><br />
	nombre#verion, e.j bootstrap#2.3.2
	<br />
	<input type="submit" value="Ejecutar bower" />
</form>
<h2>bower.json</h2>
<!--<textarea style="width:100%; height:300px; background-color:#c0c0c0;" disabled="disabled"></textarea>-->
<pre><?=file_get_contents(SKEL_ROOT_DIR.'bower.json');?></pre>
<?="\n<!-- /".get_class()." -->\n"?>
