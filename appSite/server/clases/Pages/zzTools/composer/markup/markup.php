<?="\n<!-- ".get_class()." -->\n"?>
<h1>Composer</h1>
<hr />
<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data">
	<input name="MODULE" id="MODULE" type="hidden" value="actions"/>
	<input name="acClase" id="acClase" type="hidden" value="composer"/>
	<input name="acMetodo" id="acMetodo" type="hidden" value="acExec"/>
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>"/>

	<input type="radio" name="cCmd" value="install" <?=$cCmd['install']?> /> install
	<input type="radio" name="cCmd" value="update" <?=$cCmd['update']?> /> update
	<input type="radio" name="cCmd" value="require" <?=$cCmd['require']?> /> require
	<input type="radio" name="cCmd" value="remove" <?=$cCmd['remove']?> /> remove
	<input type="radio" name="cCmd" value="search" <?=$cCmd['search']?> /> search
	<input type="radio" name="cCmd" value="show" <?=$cCmd['show']?> /> show
	<br />
	Paquete: <input type="text" name="pkgs" value="<?=$pkgs?>"><br />
	(vendor/package1:version vendor/package2:version ...) (phpunit/phpunit:4.3.* mysql/autobackup:dev-master)
	<br />
	Dry run:
	<input type="hidden" name="dryRun" value="0" />
	<input type="checkbox" name="dryRun" value="1" checked="checked" />
	<br />
	<input type="submit" value="Ejecutar composer" />
</form>
<h2>composer.json</h2>
<!--<textarea style="width:100%; height:300px; background-color:#c0c0c0;" disabled="disabled"></textarea>-->
<pre><?=file_get_contents(SKEL_ROOT_DIR.'composer.json');?></pre>
<?="\n<!-- /".get_class()." -->\n"?>
