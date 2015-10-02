<?="\n<!-- ".get_class()." -->\n"?>
<h1>
	Composer<br />
	<span style="font-size:small;">(<?=$composerVersion?>)</span>
</h1>
<a href="https://packagist.org/">https://packagist.org/</a>
<hr />
<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data">
	<input name="MODULE" id="MODULE" type="hidden" value="actions"/>
	<input name="acClase" id="acClase" type="hidden" value="composer"/>
	<input name="acMetodo" id="acMetodo" type="hidden" value="acUpdate"/>
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>"/>

	<input type="submit" value="self-update composer" />
</form>
<hr />
Primero Require y luego Install.

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
	Paquete: <input type="text" name="pkgs" id="pkgs" value="<?=$pkgs?>">
	<input type="button" value="Buscar en packagist"
		onclick="window.open('https://packagist.org/search/?q='+document.getElementById('pkgs').value,'_blank');" /><br />
	(vendor/package1:version vendor/package2:version ...) (phpunit/phpunit:4.3.* mysql/autobackup:dev-master)
	<br />
	Dry run (ejecución simulada):
	<input type="hidden" name="dryRun" value="0" />
	<input type="checkbox" name="dryRun" value="1" />
	<br />
	Verbose (--verbose):
	<input type="hidden" name="verbose" value="0" />
	<input type="checkbox" name="verbose" value="1" />
	<br />
	<input type="submit" value="Ejecutar composer" />
</form>
<hr />
<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data">
	<input name="MODULE" id="MODULE" type="hidden" value="actions" />
	<input name="acClase" id="acClase" type="hidden" value="composer" />
	<input name="acMetodo" id="acMetodo" type="hidden" value="acGenerateComposerAssetPluginComponentsFiles" />
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc" />
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>" />

	APPs:<br />
<?
$arrLibsApps=\Sintax\Pages\bower::getArrLibsApps(COMPOSER_ASSET_PLUGIN_PATH,'composerAssetPluginComponents.php','appComposerAssetPluginComponents.php');
$arrApps=unserialize(APPS);
$arrApps['GLOBAL']=array('NOMBRE_APP' => 'composerAssetPluginComponents.php');
//echo "<pre>".print_r ($arrLibsApps,true)."</pre>";
?>
	<table border="1">
		<tr>
			<td>Componente</td>
<?
foreach ($arrApps as $entryPoint => $arrAppConstants) {
	$name=$arrAppConstants['NOMBRE_APP'].'<br /> ('.$entryPoint.')';
?>
			<td style="text-align:center;"><?=$name?></td>
<?
}
?>
		</tr>
<?
if (count($arrInstalledLibs)>0) {
	foreach ($arrInstalledLibs as $libName => $objLibData) {
		echo "<tr>";
		echo '<td title="'.print_r($objLibData,true).'">'.$objLibData->name.' ('.$objLibData->version.')</td>';
		foreach ($arrApps as $entryPoint => $arrAppConstants) {
			$checked=($arrLibsApps[$libName][$entryPoint]==1)?'checked="checked"':'';
			$name=$entryPoint.' ('.$arrAppConstants['NOMBRE_APP'].')';
?>
			<td style="text-align:center;">
				<input type="hidden" name="arrLibsApps[<?=$libName?>][<?=$entryPoint?>]" value="0">
				<input type="checkbox" name="arrLibsApps[<?=$libName?>][<?=$entryPoint?>]" <?=$checked?> value="1">
			</td>
<?
		}
		echo "</tr>\n";
	}
} else {
	echo '<tr><td colspan="99">No se encontraron assets de composer instalados</td></tr>';
}
?>
	</table>
	<input type="submit" value="(Re)Generar ficheros" />
</form>
<hr />
<h2>composer.json</h2>
<!--<textarea style="width:100%; height:300px; background-color:#c0c0c0;" disabled="disabled"></textarea>-->
<pre><?=file_get_contents(SKEL_ROOT_DIR.'composer.json');?></pre>
<?="\n<!-- /".get_class()." -->\n"?>
