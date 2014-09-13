<?
/*
Néstor
neslonso@gmail.com
ClaseCreadora 1.2
20110110

/* History
/* v 1.3 (20140807)
	* Añadido $arrFksTo para la generación de funciones que devuelven arr de elementos de otras clases que referencian a la clase (e.g. Categoria->arrProdcutos())
	* Añadidas funciones borrar y noReferenciado a la clase
	* Añadido $arrFksFrom para la generación de funciones que devuelven objs de elementos de aotras clases a las que hace referencia esta (e.g. Producto->objCategoria())

/* v 1.2 (20120502)
	* Añadidos los parametros segundo y tercero a htmlentities en la funcion cargarId para que maneje bien las conversiones UTF-8: ENT_QUOTES y "UTF-8"
/* v 1.1 (20120403)
	* Empezamos la history
	* Añadida llamada a htmlentities en cargarId, ya que los valores cargados de la BD serán usados en HTML y
	* los caracteres convertidos a entidades HTML para que no produzcan problemas.

TODO:
- Parametrizar CORRECTAMTE la referencia a la clase de Acceso a datos.
   NOTA :  Néstor, de momento, como no sabemos como quieres parametrizar esto, hemos definido unas constantes
   para ti y otras para nosotros con las diferencias este nivel .. hemos cambiado cosa de nuestra clase
   de accesos a datos para minimizar esas diferencias, pero mas no podemos minimizarlas.
	Queda de tu mano el poner esto aqui como merjor creas, pero a través de estas constantes ya sabes lo que nos
	hace falta.
*/


/* DEFINES PARA CLASE DE ACCESO A DATOS mysqliDB */
define("CAD_CLASE_DATOS","\$GLOBALS['db']->");
define("CAD_LLAMADA_QUERY_INSERT_UPDATE","\$GLOBALS['db']->query (\$sql);");



/* DEFINES PARA CLASE DE ACCESO A DATOS cDB */
/*
define("CAD_CLASE_DATOS","cDb::");
define("CAD_LLAMADA_QUERY_INSERT_UPDATE","cDb::dbQuery(\$sql,false);");
*/




/**************************************************************************************************************/
//Crea un fichero php con el codigo basico que solemos usar para las clases
//El fichero creado tira de las funciones de mysqliDB.php
//Se supone que el primer atributo de arrAtributos es la clave primaria de la tabla
class Creadora {
	function creadora($ruta,$nombreClase, $arrAtributos, $nombreTabla, $arrFksFrom,$arrFksTo) {
		$sl="\n";
		$sg="\t";
		//$file="./creacion/".$nombreClase.".php";
		$file=$ruta."/".$nombreClase.".php";
		$fp=fopen ($file,"w");
		fwrite ($fp,"<?".$sl);
		fwrite ($fp,"class ".$nombreClase." {".$sl);
		//Declaración de varialbes
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			fwrite ($fp,$sg."private $".$nombreAtributo.";".$sl);
		}
		fwrite ($fp,$sl);
		//Funcion Constructor
		fwrite ($fp,$sg."public function __construct (\$id=\"\") {".$sl);
		fwrite ($fp,$sg.$sg."if (\$id!=\"\") {".$sl);
		fwrite ($fp,$sg.$sg.$sg."\$this->cargarId (\$id);".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);
		//fwrite ($fp,$sg.$sg."return \$result;".$sl);//<- El constructor no puede devolver nada
		fwrite ($fp,$sg."}".$sl);
		fwrite ($fp,$sl);

		//fwrite ($fp,$sg."//Incluida para compatibilidad con versiones anteriores de PHP;".$sl);
		//fwrite ($fp,$sg."public function ".$nombreClase." (\$id=\"\") {".$sl);
		//fwrite ($fp,$sg.$sg."return __construct(\$id);".$sl);
		//fwrite ($fp,$sg."}".$sl);
		//Fin Funcion Constructor
		//fwrite ($fp,$sl);

		//Funcion cargarId
		fwrite ($fp,$sg."public function cargarId (\$id) {".$sl);
		fwrite ($fp,$sg.$sg."\$result=false;".$sl);
		//fwrite ($fp,$sg.$sg."\$sql=\"SELECT * FROM ".$nombreTabla." WHERE id='\".\$GLOBALS['db']->real_escape_string(\$id).\"'\";".$sl);
		fwrite ($fp,$sg.$sg."\$sql=\"SELECT * FROM ".$nombreTabla." WHERE id='\".".CAD_CLASE_DATOS."real_escape_string(\$id).\"'\";".$sl);

		/*por parametrizacion de clase de accesos a datos */
		//fwrite ($fp,$sg.$sg."\$data=\$GLOBALS['db']->get_row(\$sql);".$sl);
		fwrite ($fp,$sg.$sg."\$data=".CAD_CLASE_DATOS."get_row(\$sql);".$sl);


		fwrite ($fp,$sg.$sg."if (\$data) {".$sl);
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			//fwrite ($fp,$sg.$sg.$sg."\$this->".$nombreAtributo."=htmlentities(\$data->".$nombreAtributo.",ENT_QUOTES,\"UTF-8\");".$sl);
			fwrite ($fp,$sg.$sg.$sg."\$this->".$nombreAtributo."=\$data->".$nombreAtributo.";".$sl);
		}
		fwrite ($fp,$sg.$sg.$sg."\$result=true;".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg."return \$result;".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin funcion cargarId
		fwrite ($fp,$sl);
		//Funcion grabar
		fwrite ($fp,$sg."public function grabar () {".$sl);
		fwrite ($fp,$sg.$sg."\$result=false;".$sl);
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			/*por parametrizacion de clase de accesos a datos */
			//fwrite ($fp,$sg.$sg."\$sqlValue_".$nombreAtributo."=(is_null(\$this->".$nombreAtributo."))?\"NULL\":\"'\".\$GLOBALS['db']->real_escape_string(\$this->".$nombreAtributo.").\"'\";".$sl);
			fwrite ($fp,$sg.$sg."\$sqlValue_".$nombreAtributo."=(is_null(\$this->".$nombreAtributo."))?\"NULL\":\"'\".".CAD_CLASE_DATOS."real_escape_string(\$this->".$nombreAtributo.").\"'\";".$sl);
		}
		fwrite ($fp,$sg.$sg."if (\$this->id!=\"\") { //UPDATE".$sl);

		fwrite ($fp,$sg.$sg.$sg."\$this->update=\$sqlValue_update=date(\"YmdHis\");".$sl);
		fwrite ($fp,$sg.$sg.$sg."\$sql=\"UPDATE ".$nombreTabla." SET \".".$sl);
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\"`".$nombreAtributo."`=\".\$sqlValue_".$nombreAtributo.".\", \".".$sl;
		}
		//restamos a code los 5 ultimos caracteres (", ".) porque son del ultimo atributo
		//y su linea de codigo acaba de manera diferente al resto
		$code=substr ($code,0,-5);
		$code.=" \".".$sl;
		$code.=$sg.$sg.$sg.$sg."\"WHERE id='\".\$this->id.\"'\";".$sl;
		fwrite ($fp,$code);

		fwrite ($fp,$sg.$sg."} else { //INSERT".$sl);

		$arrKeys=array_keys($arrAtributos);

		/*por parametrizacion de clase de accesos a datos */
		//fwrite ($fp,$sg.$sg.$sg."\$this->id=\$sqlValue_id=\$GLOBALS['db']->nextId (\"".$nombreTabla."\",\"".$arrKeys[0]."\");".$sl);
		fwrite ($fp,$sg.$sg.$sg."\$this->id=\$sqlValue_id=".CAD_CLASE_DATOS."nextId (\"".$nombreTabla."\",\"".$arrKeys[0]."\");".$sl);

		fwrite ($fp,$sg.$sg.$sg."\$this->insert=\$sqlValue_insert=\$this->update=\$sqlValue_update=date(\"YmdHis\");".$sl);
		fwrite ($fp,$sg.$sg.$sg."\$sql=\"INSERT INTO ".$nombreTabla." ( \".".$sl);
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\"`".$nombreAtributo."`, \".".$sl;
		}
		//restamos a code los 5 ultimos caracteres (", ".) porque son del ultimo atributo
		//y su linea de codigo acaba de manera diferente al resto
		$code=substr ($code,0,-5);
		fwrite ($fp,$code.") VALUES (\".".$sl);
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\$sqlValue_".$nombreAtributo.".\", \".".$sl;
		}
		//restamos a code los 5 ultimos caracteres (", ".) porque son del ultimo atributo
		//y su linea de codigo acaba de manera diferente al resto
		$code=substr ($code,0,-5);
		fwrite ($fp,$code.")\";".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);

		/*por parametrizacion de clase de accesos a datos */
		// fwrite ($fp,$sg.$sg."\$result=\$GLOBALS['db']->query (\$sql);".$sl);
		fwrite ($fp,$sg.$sg."\$result=".CAD_LLAMADA_QUERY_INSERT_UPDATE.$sl);


		fwrite ($fp,$sg.$sg."return \$result;".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin Funcion grabar
		fwrite ($fp,$sl);
		//Inicio Funcion borrar
		fwrite ($fp,$sg.'public function borrar() {'.$sl);
		fwrite ($fp,$sg.$sg.'$result=false;'.$sl);
		fwrite ($fp,$sg.$sg.'if ($this->noReferenciado()) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$sql="DELETE FROM '.$nombreTabla.' WHERE id=\'".'.CAD_CLASE_DATOS.'real_escape_string($this->id)."\'";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.''.CAD_CLASE_DATOS.'query($sql);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$result=true;'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.'return $result;'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
		//Fin Funcion borrar
		fwrite ($fp,$sl);
		//Inicio Funcion cargarArray
		fwrite ($fp,$sg."public function cargarArray (\$array,\$usingSetters=true) {".$sl);
		fwrite ($fp,$sg.$sg."foreach(\$this as \$key => \$value) {".$sl);
		fwrite ($fp,$sg.$sg.$sg."if (isset(\$array[\$key])) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."if (\$usingSetters) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$func=\"SET\".\$key;".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$this->\$func(\$array[\$key]);".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."} else {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$this->\$key=\$array[\$key];".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin Funcion cargarArray
		fwrite ($fp,$sl);
		//Inicio Funcion cargarObj
		fwrite ($fp,$sg."public function cargarObj (\$obj,\$usingSetters=true) {".$sl);
		fwrite ($fp,$sg.$sg."foreach(\$this as \$key => \$value) {".$sl);
		fwrite ($fp,$sg.$sg.$sg."if (isset(\$obj->\$key)) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."if (\$usingSetters) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$func=\"SET\".\$key;".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$this->\$func(\$obj->\$key);".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."} else {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."\$this->\$key=\$obj->\$key;".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin Funcion cargarObj
		fwrite ($fp,$sl);
		//Inicio Funcion toJson
		fwrite ($fp,$sg."public function toJson(){".$sl);
		fwrite ($fp,$sg.$sg."\$var = get_object_vars(\$this);".$sl);
		fwrite ($fp,$sg.$sg."foreach(\$var as &\$value){".$sl);
		fwrite ($fp,$sg.$sg.$sg."if(is_object(\$value) && method_exists(\$value,'toJson')){".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."\$value = \$value->toJson();".$sl);
		fwrite ($fp,$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg.$sg."if (is_array(\$value)) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."foreach (\$value as &\$item) {".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."if(is_object(\$item) && method_exists(\$item,'toJson')){".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.$sg."\$item = \$item->toJson();".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg."}".$sl);
		fwrite ($fp,$sg.$sg."return \$var;".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin Funcion toJson
		fwrite ($fp,$sl);
		//Inicio Funcion toArray
		fwrite ($fp,$sg."public function toArray () {".$sl);
		fwrite ($fp,$sg.$sg."\$result=get_object_vars(\$this);".$sl);
		fwrite ($fp,$sg.$sg."return \$result;".$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin Funcion toArray
		fwrite ($fp,$sl);
		//Inicio Funciones de atributos
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			//fwrite ($fp,$sg."public function GET".$nombreAtributo." () {");
			//fwrite ($fp,"return \$this->".$nombreAtributo.";");
			fwrite ($fp,$sg."public function GET".$nombreAtributo." (\$entity_decode=false) {");
			fwrite ($fp,"return (\$entity_decode)?html_entity_decode(\$this->".$nombreAtributo.",ENT_QUOTES,\"UTF-8\"):\$this->".$nombreAtributo.";");
			fwrite ($fp,"}".$sl);

			//fwrite ($fp,$sg."public function SET".$nombreAtributo." (\$".$nombreAtributo.") {");
			//fwrite ($fp,"\$this->".$nombreAtributo."=\$".$nombreAtributo.";");
			fwrite ($fp,$sg."public function SET".$nombreAtributo." (\$".$nombreAtributo.",\$entity_encode=false) {");
			fwrite ($fp,"\$this->".$nombreAtributo."=(\$entity_encode)?htmlentities(\$".$nombreAtributo.",ENT_QUOTES,\"UTF-8\"):\$".$nombreAtributo.";");
			fwrite ($fp,"}".$sl);
			fwrite ($fp,$sl);
		}
		//Fin funciones de atributos
		//Funciones estaticas
		fwrite ($fp,"/* Funciones estaticas ********************************************************/".$sl);
		fwrite ($fp,$sl);
		//Inicio funcion existeID
		fwrite ($fp,$sg.'public static function existeId($id) {'.$sl);
		fwrite ($fp,$sg.$sg.'$sql="SELECT * FROM '.$nombreTabla.' WHERE id=\'".'.CAD_CLASE_DATOS.'real_escape_string($id)."\'";'.$sl);
		fwrite ($fp,$sg.$sg.'$data='.CAD_CLASE_DATOS.'get_row($sql);'.$sl);
		fwrite ($fp,$sg.$sg.'if ($data) {$result=true;} else {$result=false;}'.$sl);
		fwrite ($fp,$sg.$sg.'return $result;'.$sl);
		fwrite ($fp,$sg."}".$sl);
		//Fin funcion existeID
		fwrite ($fp,$sl);
		//Inicio funcion allToArray
		fwrite ($fp,$sg.'public static function allToArray($where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl);
		fwrite ($fp,$sg.$sg.'$sqlWhere=($where!="")?" WHERE ".$where:"";'.$sl);
		fwrite ($fp,$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl);
		fwrite ($fp,$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl);
		fwrite ($fp,$sg.$sg.'$sql="SELECT * FROM cliente".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl);
		fwrite ($fp,$sg.$sg.'$arr=array();'.$sl);
		fwrite ($fp,$sg.$sg.'$rsl='.CAD_CLASE_DATOS.'query($sql);'.$sl);
		fwrite ($fp,$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'switch ($tipo) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$obj=new self($data->id);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$obj=new stdClass();'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.'return $arr;'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
		//Fin funcion allToArray
		fwrite ($fp,$sl);
		//Inicio funcion ls
		$nombreVista='ls'.ucfirst($nombreTabla);
		fwrite ($fp,$sg.'public static function ls($where="",$order="",$limit="") {'.$sl);
		fwrite ($fp,$sg.'	$sqlView="CREATE OR REPLACE VIEW `'.$nombreVista.'` AS'.$sl);
		fwrite ($fp,$sg.'		SELECT * FROM '.$nombreTabla.';'.$sl);
		fwrite ($fp,$sg.'	";'.$sl);
		fwrite ($fp,$sg.'	'.CAD_CLASE_DATOS.'query($sqlView);'.$sl);
		fwrite ($fp,$sg.'	$sqlWhere=($where!="")?" WHERE ".$where:"";'.$sl);
		fwrite ($fp,$sg.'	$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl);
		fwrite ($fp,$sg.'	$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl);
		fwrite ($fp,$sg.'	$sql="SELECT * FROM '.$nombreVista.'".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl);
		fwrite ($fp,$sg.'	$arr=array();'.$sl);
		fwrite ($fp,$sg.'	$rsl='.CAD_CLASE_DATOS.'query($sql);'.$sl);
		fwrite ($fp,$sg.'	while ($data=$rsl->fetch_object()) {'.$sl);
		fwrite ($fp,$sg.'		$objSeg=new self($data->id);'.$sl);
		fwrite ($fp,$sg.'		foreach ($data as $field => $value) {'.$sl);
		fwrite ($fp,$sg.'			$obj->$field=$value;'.$sl);
		fwrite ($fp,$sg.'		}'.$sl);
		fwrite ($fp,$sg.'		array_push($arr,$obj);'.$sl);
		fwrite ($fp,$sg.'		unset ($obj);'.$sl);
		fwrite ($fp,$sg.'	}'.$sl);
		fwrite ($fp,$sg.'	return $arr;'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
		//Fin funcion ls
		fwrite ($fp,$sl);
		//Fin Funciones estaticas
		fwrite ($fp,"/* Funciones dinamicas ********************************************************/".$sl);
		fwrite ($fp,$sl);
		//Inicio funcion noReferenciado
		fwrite ($fp,$sg.'public function noReferenciado() {'.$sl);
		foreach ($arrFksTo as $objFkInfo) {
			$fTable=$objFkInfo->TABLE_NAME;
			$fField=$objFkInfo->COLUMN_NAME;
			fwrite ($fp,$sg.$sg.'$sql="SELECT '.$fField.' FROM '.$fTable.' WHERE '.$fField.'=\'".'.CAD_CLASE_DATOS.'real_escape_string($this->id)."\'";'.$sl);
			fwrite ($fp,$sg.$sg.'$noReferenciadoEn'.ucfirst($fTable).'=('.CAD_CLASE_DATOS.'get_num_rows($sql)==0)?true:false;'.$sl);
		}
		$strConds='';
		foreach ($arrFksTo as $objFkInfo) {
			$fTable=$objFkInfo->TABLE_NAME;
			$fField=$objFkInfo->COLUMN_NAME;
			$strConds.='$noReferenciadoEn'.ucfirst($fTable).' && ';
		}
		$strConds=substr($strConds, 0, -4);
		fwrite ($fp,$sg.$sg.'$result=('.$strConds.')?true:false;'.$sl);
		fwrite ($fp,$sg.$sg.'return $result;'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
		//Fin funcion noReferenciado
		fwrite ($fp,$sl);
		fwrite ($fp,"/* Funciones FkFrom ***********************************************************/".$sl);
		fwrite ($fp,$sl);
		//Inicio funciones FkFrom
		foreach ($arrFksFrom as $objFkInfo) {
			$fTable=$objFkInfo->TABLE_NAME;
			$fField=$objFkInfo->COLUMN_NAME;
			fwrite ($fp,$sg.'public function obj'.ucfirst($fTable).'() {'.$sl);
			fwrite ($fp,$sg.$sg.'return new '.ucfirst($fTable).'($this->'.$fField.');'.$sl);
			fwrite ($fp,$sg.'}'.$sl);
		}
		//Fin funciones FkFrom
		fwrite ($fp,$sl);
		fwrite ($fp,$sl);
		fwrite ($fp,"/* Funciones FkTo *************************************************************/".$sl);
		fwrite ($fp,$sl);
		//Inicio funciones FkTo
		define ('FUNCION_FKTO_UNICA',false);
		if (FUNCION_FKTO_UNICA) {
			//Alternativa sin probar
			fwrite ($fp,$sg.'public function arrFkTo($table,$field,$where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl);
			fwrite ($fp,$sg.$sg.'$sqlWhere=($where!="")?" WHERE ".$field."=\'".'.CAD_CLASE_DATOS.'real_escape_String($this->id)."\' AND ".$where:" WHERE ".$field."=\'".'.CAD_CLASE_DATOS.'real_escape_string($this->id)."\'";'.$sl);
			fwrite ($fp,$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl);
			fwrite ($fp,$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl);
			fwrite ($fp,$sg.$sg.'$sql="SELECT * FROM ".$table.$sqlWhere.$sqlOrder.$sqlLimit;'.$sl);
			fwrite ($fp,$sg.$sg.'$arr=array();'.$sl);
			fwrite ($fp,$sg.$sg.'$rsl='.CAD_CLASE_DATOS.'query($sql);'.$sl);
			fwrite ($fp,$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl);
			fwrite ($fp,$sg.$sg.$sg.'switch ($tipo) {'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$obj=new ucfirst($table)($data->id);'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'}'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl);
			fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
			fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
			fwrite ($fp,$sg.$sg.'}'.$sl);
			fwrite ($fp,$sg.$sg.'return $arr;'.$sl);
			fwrite ($fp,$sg.'}'.$sl);
		} else {
			foreach ($arrFksTo as $objFkInfo) {
				$fTable=$objFkInfo->TABLE_NAME;
				$fField=$objFkInfo->COLUMN_NAME;

				fwrite ($fp,$sg.'public function arr'.ucfirst($fTable).'($where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl);
				fwrite ($fp,$sg.$sg.'$sqlWhere=($where!="")?" WHERE '.$fField.'=\'".'.CAD_CLASE_DATOS.'real_escape_String($this->id)."\' AND ".$where:" WHERE '.$fField.'=\'".'.CAD_CLASE_DATOS.'real_escape_string($this->id)."\'";'.$sl);
				fwrite ($fp,$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl);
				fwrite ($fp,$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl);
				fwrite ($fp,$sg.$sg.'$sql="SELECT * FROM '.$fTable.'".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl);
				fwrite ($fp,$sg.$sg.'$arr=array();'.$sl);
				fwrite ($fp,$sg.$sg.'$rsl='.CAD_CLASE_DATOS.'query($sql);'.$sl);
				fwrite ($fp,$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl);
				fwrite ($fp,$sg.$sg.$sg.'switch ($tipo) {'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$obj=new '.ucfirst($fTable).'($data->id);'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'}'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl);
				fwrite ($fp,$sg.$sg.$sg.$sg.'break;'.$sl);
				fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
				fwrite ($fp,$sg.$sg.'}'.$sl);
				fwrite ($fp,$sg.$sg.'return $arr;'.$sl);
				fwrite ($fp,$sg.'}'.$sl);
			}
		}
		//Fin funciones FkTo
		//Llave de cierre de la clase
		fwrite ($fp,"}".$sl);
		fwrite ($fp,"?>".$sl);
		fclose ($fp);
		chmod ($file,0777);
	}
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
/* Codigo obsoleto sin actualizar ********************************************/
/*****************************************************************************/
/*****************************************************************************/
/*****************************************************************************/
	function creadoraJS($nombreClase, $arrAtributos, $nombreTabla) {
		$sl="\n";
		$sg="\t";
		$file="./creacion/".$nombreClase.".js";
		$fp=fopen ($file,"w");
		fwrite ($fp,"// JavaScript Document".$sl);
		fwrite ($fp,"function ".$nombreClase." (id) {".$sl);
		//Codigo Constructor
		fwrite ($fp,$sg."var result=false;".$sl);
		fwrite ($fp,$sg."if (!(typeof id ===\"undefined\")) {".$sl);
		fwrite ($fp,$sg.$sg."result=this.cargarId (id);".$sl);
		fwrite ($fp,$sg."}".$sl);
		fwrite ($fp,$sg."return result;".$sl);
		fwrite ($fp,$sl);
		//Fin Codigo Constructor
		fwrite ($fp,"}".$sl);
		//Llave de cierre de la clase
		fwrite ($fp,$sl);

		//Funcion CargarId
		fwrite ($fp,$nombreClase.".prototype.cargarId=function (id) {".$sl);
		fwrite ($fp,$sg."var result=false;".$sl);
		fwrite ($fp,$sg."return result;".$sl);
		fwrite ($fp,"}".$sl);
		//Fin funcion CargarId
		fwrite ($fp,$sl);
		//Funcion Grabar
		fwrite ($fp,$nombreClase.".prototype.grabar=function () {".$sl);
		fwrite ($fp,$sg."var result=false;".$sl);
		fwrite ($fp,$sg."return result;".$sl);
		fwrite ($fp,"}".$sl);
		//Fin Funcion Grabar
		fwrite ($fp,$sl);
		//Funcion fomObj
		fwrite ($fp,$nombreClase.".prototype.fromObj=function (obj) {".$sl);
		fwrite ($fp,$sg."for (var prop in this) {".$sl);
		fwrite ($fp,$sg.$sg.'if (!(typeof obj[prop] ==="undefined")) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'this[prop]=obj[prop];'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
		fwrite ($fp,"}".$sl);
		//Fin Funcio fromObj
		fwrite ($fp,$sl);
		//Inicio Funciones de atributos
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			fwrite ($fp,$nombreClase.".prototype.".$nombreAtributo.'="";'.$sl);
			fwrite ($fp,$nombreClase.".prototype.GET".$nombreAtributo."=function () {");
			fwrite ($fp,"return this.".$nombreAtributo.";");
			fwrite ($fp,"}".$sl);

			fwrite ($fp,$nombreClase.".prototype.SET".$nombreAtributo."=function (".$nombreAtributo.") {");
			fwrite ($fp,"this.".$nombreAtributo."=".$nombreAtributo.";");
			fwrite ($fp,"}".$sl);
			fwrite ($fp,$sl);
		}
		//Fin funciones de atributos
		fwrite ($fp,"/******************************************************************************/".$sl);
		fclose ($fp);
	}

	function frmSimple($arrAtributos,$nombreClase) {
		/********************************************/
		/* SIN PROBAR */
		/********************************************/
		$sl="\n";
		$sg="\t";
		$file="./creacion/".$nombreClase.".frm.php";
		$fp=fopen ($file,"w");
		/*
		fwrite ($fp,"<?".$sl);
		fwrite ($fp,'if (isset($_POST["id"])) {'.$sl);
		fwrite ($fp,$sg.'$obj'.$nombreClase.'=new '.$nombreClase.'($_POST["id"]);'.$sl);
		fwrite ($fp,$sg.'$obj'.$nombreClase.'->cargarArray($_POST);'.$sl);
		fwrite ($fp,$sg.'$obj'.$nombreClase.'->grabar();'.$sl);
		fwrite ($fp,$sg.'header ("Location: ./".$_SERVER["PHP_SELF"]."?id=".$_POST["id"]);'.$sl);
		fwrite ($fp,'} else {'.$sl);
		fwrite ($fp,$sg.'$obj'.$nombreClase.'=new '.$nombreClase.'($_GET["id"]);'.$sl);
		fwrite ($fp,'}'.$sl);
		fwrite ($fp,"?>".$sl);
		*/

		fwrite ($fp,'<form action="./actions.php" method="post" enctype="multipart/form-data">'.$sl);
		fwrite ($fp,$sg.'<input name="APP" id="APP" type="hidden" value=""/>'.$sl);
		fwrite ($fp,$sg.'<input name="acClase" id="acClase" type="hidden" value=""/>'.$sl);
		fwrite ($fp,$sg.'<input name="acMetodo" id="acMetodo" type="hidden" value=""/>'.$sl);
		fwrite ($fp,$sg.'<input name="acTipo" id="acTipo" type="hidden" value="[std|stdAssoc|ajax|ajaxAssoc]"/>'.$sl);
		fwrite ($fp,$sg.'<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER[\'REQUEST_URI\']?>"/>'.$sl);
		fwrite ($fp,$sg.'<fieldset>'.$sl);
		fwrite ($fp,$sg.$sg.'<legend>Campos de '.$nombreClase.'</legend>'.$sl);

		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$tipoCampo="";
			if(strpos($sqlData,"varchar")!==false) {$tipoCampo="texto";}
			if(strpos($sqlData,"tinyint(1)")!==false) {$tipoCampo="checkbox";}
			if(strpos($sqlData,"enum")!==false) {
				$tipoCampo="select";
				preg_match('/enum\((.*)\)$/', $sqlData, $matches);
				$arrSelectValues = explode(',', $matches[1]);
			}
			if(strpos($sqlData,"timestamp")!==false) {$tipoCampo="fecha";}
			if(strpos($sqlData,"datetime")!==false) {$tipoCampo="fecha";}
			if(strpos($sqlData,"date")!==false) {$tipoCampo="fecha";}
			switch ($tipoCampo) {
				case "texto":
					$code.=$sg.$sg.'<label for="'.$nombreAtributo.'" accesskey="">'.$nombreAtributo.'</label><br />'.$sl;
					$code.=$sg.$sg.'<input type="text" name="'.$nombreAtributo.'" id="'.$nombreAtributo.'" value="<?=(isset($'.$nombreAtributo.'))?$'.$nombreAtributo.':"";?>" /><br />'.$sl;
				break;
				case "checkbox":
					$code.=$sg.$sg.'<input name="'.$nombreAtributo.'" id="'.$nombreAtributo.'.Dummy" type="hidden" value="0" />'.$sl;
					$code.=$sg.$sg.'<input name="'.$nombreAtributo.'" id="'.$nombreAtributo.'" type="checkbox" value="1" <?=(isset($'.$nombreAtributo.'))?$'.$nombreAtributo.':"";?>/>'.$sl;
					$code.=$sg.$sg.'<label for="'.$nombreAtributo.'" accesskey="">'.$nombreAtributo.'</label>'.$sl;
					$code.=$sg.$sg.'<br />'.$sl;
				break;
				case "select":
					$code.=$sg.$sg.'<label for="'.$nombreAtributo.'" accesskey="">'.$nombreAtributo.':</label>'.$sl;
					$code.=$sg.$sg.'<br />'.$sl;
					$code.=$sg.$sg.'<select name="'.$nombreAtributo.'" id="'.$nombreAtributo.'">'.$sl;
					foreach ($arrSelectValues as $key => $value) {
						$value=trim($value, "'");
						$code.=$sg.$sg.'<option value="'.$value.'" <?=(isset($'.$nombreAtributo.$key.'))?$'.$nombreAtributo.$key.':"";?>>'.$value.'</option>'.$sl;
					}
					$code.=$sg.$sg.'</select>'.$sl;
					$code.=$sg.$sg.'<br />'.$sl;
				break;
				case "fecha":
					$code.=$sg.$sg.'<label for="'.$nombreAtributo.'" accesskey="">'.$nombreAtributo.':</label>'.$sl;
					$code.=$sg.$sg.'<br />'.$sl;
					$code.=$sg.$sg.'<select name="'.$nombreAtributo.'Dia" id="'.$nombreAtributo.'Dia">'.$sl;
					$code.=$sg.$sg.$sg.'<option value="-1">Día:</option>'.$sl;
					$code.=$sg.$sg.'<? for ($i=1;$i<32;$i++) {?>'.$sl;
					$code.=$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$nombreAtributo.'Dia[$i]))?$'.$nombreAtributo.'Dia[$i]:"";?>><?=$i?></option>'.$sl;
					$code.=$sg.$sg.'<? }?>'.$sl;
					$code.=$sg.$sg.'</select>'.$sl;
					$code.=$sg.$sg.'<select id="'.$nombreAtributo.'Mes" name="'.$nombreAtributo.'Mes">'.$sl;
					$code.=$sg.$sg.$sg.'<option value="-1">Mes:</option>'.$sl;
					$code.=$sg.$sg.'<? for ($i=1;$i<13;$i++) {?>'.$sl;
					$code.=$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$nombreAtributo.'Mes[$i]))?$'.$nombreAtributo.'Mes[$i]:"";?>><?=$i?></option>'.$sl;
					$code.=$sg.$sg.'<? }?>'.$sl;
					$code.=$sg.$sg.'</select>'.$sl;
					$code.=$sg.$sg.'<select name="'.$nombreAtributo.'Ano" id="'.$nombreAtributo.'Ano">'.$sl;
					$code.=$sg.$sg.$sg.'<option value="-1">Año:</option>'.$sl;
					$code.=$sg.$sg.'<? for ($i=date("Y");$i>1900;$i--) {?>'.$sl;
					$code.=$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$nombreAtributo.'Ano[$i]))?$'.$nombreAtributo.'Ano[$i]:"";?>><?=$i?></option>'.$sl;
					$code.=$sg.$sg.'<? }?>'.$sl;
					$code.=$sg.$sg.'</select>'.$sl;
					$code.=$sg.$sg.'<br />'.$sl;
				break;
				default:
					$code.=$sg.$sg.'<label for="'.$nombreAtributo.'" accesskey="">'.$nombreAtributo.'</label><br />'.$sl;
					$code.=$sg.$sg.'<input type="text" name="'.$nombreAtributo.'" id="'.$nombreAtributo.'" value="<?=(isset($'.$nombreAtributo.'))?$'.$nombreAtributo.':"";?>" /><br />'.$sl;
			}
		}
		fwrite ($fp,$code);
		fwrite ($fp,$sg.'</fieldset>'.$sl);
		fwrite ($fp,"</form>".$sl);
		fclose ($fp);
	}
}
?>