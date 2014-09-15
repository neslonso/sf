<?="\n<!-- ".get_class()." -->\n"?>
<form action="./<?=FILE_APP?>" method="post" enctype="multipart/form-data"
 	style="background-color: #FFF;" class="sobreAnterior">
	<input name="MODULE" id="MODULE" type="hidden" value="actions"/>
	<input name="acClase" id="acClase" type="hidden" value="Creacion"/>
	<input name="acMetodo" id="acMetodo" type="hidden" value="CrearPagina"/>
	<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>
	<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER['REQUEST_URI']?>"/>

	<fieldset>
		<legend>Creación de Page Class</legend>

		<label for="ruta">Ruta:</label> <input type="text" name="ruta" id="ruta" value="<?=RUTA_APP?>server/clases/Pages/" /> (Ruta donde crear la carpeta de la clase de Pagina (en adelante la Page), <strong>¡¡Atención!!</strong> se sobreescribirá si existe)<br />
		<label for="page">Nombre:</label> <input type="text" name="page" id="page" value="" /> (Nombre para la Page,  corresponde al valor del parametro GET 'page' para acceder a ella)<br />
		<label for="extends">Extends:</label> <input type="text" name="extends" id="extends" value="Home" /> (Page a la que extiende, se usará para herencias de css, js y marcado)<br />
		<label for="markupFunc">MarkupFunc:</label> <input type="text" name="markupFunc" id="markupFunc" value="cuerpo" /> (Función de marcado, puede usarse para sobreescribir parte del marcado de la Page base y reutilizar el resto)<br />
		<label for="markupFile">MarkupFile:</label> <input type="text" name="markupFile" id="markupFile" value="" /> (Fichero del que copiar el marcado original, util para convertir un php standalone en una Page)<br />

		<!--Table(Class): (CRUD)<input type="text" name="class" value="" /><br />-->
		<label for="pageType">Tipo:</label>
		<select name="pageType" id="pageType" onchange="
			if (this.value!='Blank') {
				document.getElementById('divClass').style.display='block';
			} else {
				document.getElementById('divClass').style.display='none';
				document.getElementById('class').value='';
				document.getElementById('class').onchange();
			}
		">
			<option value="Blank">Blank</option>
			<option value="CRUD">CRUD</option>
			<option value="DBdataTable">DBdataTable</option>
		</select> (Tipo de Page a crear: Blank => Ficheros en blanco, CRUD => Formulario de campos validados, DBdataTable => Listado datatables server side)

		<div style="display:none;" id="divClass">
			<label for="class">Tabla (Class):</label>
			<select name="class" id="class" onchange="
				var children = document.getElementById('divTables').childNodes;
				for (var i = 0; i < children.length; i++) {
					if (children[i].style) {
						children[i].style.display='none';
					}
				};
				var divTable=document.getElementById('div'+this.value);
				if (divTable) {
					divTable.style.display='block';
				}
			">
				<option value="">Seleecionar tabla</option>
<?
foreach ($arrStdObjTableInfo as $stdObjTableInfo) {
?>
				<option value="<?=$stdObjTableInfo->tableName?>"><?=$stdObjTableInfo->tableName?></option>
<?
}
?>
			</select> (Tabla de la BD en la que se basa la Page, tambien se creará en '<?=RUTA_APP?>server/clases/Logic/' la clase de lógica (Clase Logic o Clase ORM) asociada si no existe)
		</div>
		<div id="divTables">
<?
	foreach ($arrStdObjTableInfo as $stdObjTableInfo) {
?>
			<div id="div<?=$stdObjTableInfo->tableName?>" style="display:none">
				<h3>Tabla: <?=$stdObjTableInfo->tableName?></h3>
				<div id="divFields<?=$stdObjTableInfo->tableName?>">
					<span title="<?=$stdObjTableInfo->arrCreateInfo['Create Table']?>">SQL Create Table (tooltip)</span>
					<h3>COLUMNAS</h3>
					<?=mysqliDB::result_to_html_table($stdObjTableInfo->rslColumns,function ($row,$columnInfo) use ($stdObjTableInfo) {
					if ($row==0) {
						return '
							<th>Excluir (La columna no se incluira en la Page)</th>
							<th>validators (Depende de <a href="bootstrapvalidator.com">bootstrapvalidator</a>)</th>
						';
					} else {
						$disabled=($columnInfo["Key"]=='PRI')?'disabled="disabled"':'';
						return '<td>
							<input type="checkbox" '.$disabled.'
									name="'.$stdObjTableInfo->tableName.'_excluir['.$columnInfo["Field"].']"
									id="'.$stdObjTableInfo->tableName.'_excluir_'.$columnInfo["Field"].'"
									value="'.$columnInfo["Field"].'" />
						</td><td>
							<select style="height:100px; overflow:auto; width:100%;" multiple="multiple" name="'.$stdObjTableInfo->tableName.'_validators['.$columnInfo["Field"].'][]">
								<option value="">Ninguno</option>
								<option value="base64"><b>base64</b>.-Validate a base64 encoded string</option>
								<option value="between"><b>between</b>.-Check if the input value is between (strictly or not) two given numbers</option>
								<option value="callback"><b>callback</b>.-Return the validity from a callback method</option>
								<option value="choice"><b>choice</b>.-Check if the number of checked boxes are less or more than a given number</option>
								<option value="creditCard"><b>creditCard</b>.-Validate a credit card number</option>
								<option value="cusip"><b>cusip</b>.-Validate a CUSIP</option>
								<option value="cvv"><b>cvv</b>.-Validate a CVV number</option>
								<option value="date"><b>date</b>.-Validate date</option>
								<option value="different"><b>different</b>.-Return true if the input value is different with given field\'s value</option>
								<option value="digits"><b>digits</b>.-Return true if the value contains only digits</option>
								<option value="ean"><b>ean</b>.-Validate an EAN (International Article Number)</option>
								<option value="emailAddress"><b>emailAddress</b>.-Validate an email address</option>
								<option value="file"><b>file</b>.-Validate file</option>
								<option value="greaterThan"><b>greaterThan</b>.-Return true if the value is greater than or equals to given number</option>
								<option value="grid"><b>grid</b>.-Validate a GRId (Global Release Identifier)</option>
								<option value="hex"><b>hex</b>.-Validate a hexadecimal number</option>
								<option value="hexColor"><b>hexColor</b>.-Validate a hex color</option>
								<option value="iban"><b>iban</b>.-Validate an International Bank Account Number (IBAN)</option>
								<option value="id"><b>id</b>.-Validate identification number. Support 25 countries</option>
								<option value="identical"><b>identical</b>.-Check if the value is the same as one of particular field</option>
								<option value="imei"><b>imei</b>.-Validate an IMEI (International Mobile Station Equipment Identity)</option>
								<option value="integer"><b>integer</b>.-Validate an integer number</option>
								<option value="ip"><b>ip</b>.-Validate an IP address. Support both IPv4 and IPv6</option>
								<option value="isbn"><b>isbn</b>.-Validate an ISBN (International Standard Book Number). Support both ISBN 10 and ISBN 13</option>
								<option value="isin"><b>isin</b>.-Validate an ISIN (International Securities Identification Number)</option>
								<option value="ismn"><b>ismn</b>.-Validate an ISMN (International Standard Music Number)</option>
								<option value="issn"><b>issn</b>.-Validate an ISSN (International Standard Serial Number)</option>
								<option value="lessThan"><b>lessThan</b>.-Return true if the value is less than or equals to given number</option>
								<option value="mac"><b>mac</b>.-Validate a MAC address</option>
								<option value="notEmpty"><b>notEmpty</b>.-Check if the value is empty</option>
								<option value="numeric"><b>numeric</b>.-Check if the value is numeric</option>
								<option value="phone"><b>phone</b>.-Validate a phone number</option>
								<option value="regexp"><b>regexp</b>.-Check if the value matches given Javascript regular expression</option>
								<option value="remote"><b>remote</b>.-Perform remote checking via Ajax request</option>
								<option value="rtn"><b>rtn</b>.-Validate a RTN (Routing transit number)</option>
								<option value="sedol"><b>sedol</b>.-Validate a SEDOL (Stock Exchange Daily Official List)</option>
								<option value="siren"><b>siren</b>.-Validate a Siren number</option>
								<option value="siret"><b>siret</b>.-Validate a Siret number</option>
								<option value="step"><b>step</b>.-Check if the value is valid step one</option>
								<option value="stringCase"><b>stringCase</b>.-Check if a string is a lower or upper case one</option>
								<option value="stringLength"><b>stringLength</b>.-Validate the length of a string</option>
								<option value="uri"><b>uri</b>.-Validate an URL address</option>
								<option value="uuid"><b>uuid</b>.-Validate an UUID, support v3, v4, v5</option>
								<option value="vat"><b>vat</b>.-Validate VAT number. Support 32 countries</option>
								<option value="vin"><b>vin</b>.-Validate an US VIN (Vehicle Identification Number)</option>
								<option value="zipCode"><b>zipCode</b>.-Validate a zip code</option>
							</select>
						</td>';
					}
				});?>
				</div>
				<div id="divIdx<?=$stdObjTableInfo->tableName?>">
					<h3>ÍNDICES</h3>
					<?=mysqliDB::result_to_html_table($stdObjTableInfo->rslIdx);?>
				</div>
				<div id="divFKs<?=$stdObjTableInfo->tableName?>">
					<h3>FKs FROM (Tablas a las que hace referencia <?=$stdObjTableInfo->tableName?>)</h3>
					<?=mysqliDB::result_to_html_table($stdObjTableInfo->rslFksFrom);?>
				</div>
				<div id="divFKs<?=$stdObjTableInfo->tableName?>">
					<h3>FKs TO (Tablas que contienen referencias a <?=$stdObjTableInfo->tableName?>)</h3>
					<?=mysqliDB::result_to_html_table($stdObjTableInfo->rslFksTo);?>
				</div>
			</div>
<?
	}
?>
		</div>
		<input type="submit" />
	</fieldset>
</form>
<?="\n<!-- /".get_class()." -->\n"?>
