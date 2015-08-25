<?="\n<!-- ".get_class()." -->\n"?>
<h1>Bowerphp <span style="font-size:small;">(<?=$bowerphpVersion?>)</span></h1>
<a href="https://packagist.org/">https://packagist.org/</a>
<hr />
<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data">
	<input name="MODULE" id="MODULE" type="hidden" value="actions" />
	<input name="acClase" id="acClase" type="hidden" value="bower" />
	<input name="acMetodo" id="acMetodo" type="hidden" value="acExec" />
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc" />
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>" />

	APPs:<br />
<?
$arrLibsApps=$this->getArrLibsApps();
$arrApps=unserialize(APPS);
$arrApps['GLOBAL']=array('NOMBRE_APP' => 'bowerComponents.php');
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
	echo '<tr><td colspan="99">No se encontraron componentes bower instalados<td></tr>';
}
?>
	</table>
	<br /><br />
	<input type="radio" name="cCmd" value="install" <?=$cCmd['install']?> /> install
	<input type="radio" name="cCmd" value="update" <?=$cCmd['update']?> /> update
	<input type="radio" name="cCmd" value="uninstall" <?=$cCmd['uninstall']?> /> uninstall
	<input type="radio" name="cCmd" value="search" <?=$cCmd['search']?> /> search
	<input type="radio" name="cCmd" value="info" <?=$cCmd['info']?> /> info
	<input type="radio" name="cCmd" value="list" <?=$cCmd['list']?> /> list
	<br />

	Paquete: <input type="text" name="pkgs" value="<?=$pkgs?>"><br />
	nombre#version, e.j bootstrap#2.3.2
	<br />
	<input type="submit" value="Ejecutar bower" />
</form>
<?="\n<!-- /".get_class()." -->\n"?>
