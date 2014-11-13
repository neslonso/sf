<?
/*
Néstor
neslonso@gmail.com
ClaseCreadora 2.0
20110110 - 20141009

/* History
/* v 2.0 (20141009)
	* Remodelada para su integración en S!nt@x
	* Estrcuturada en varias metodos

/* v 1.3 (20140807)
	* Añadido $arrFksTo para la generación de funciones que devuelven arr de elementos de otras clases que referencian a la clase (e.g. Categoria->arrProdcutos())
	* Añadidas funciones borrar y noReferenciado a la clase
	* Añadido $arrFksFrom para la generación de funciones que devuelven objs de elementos de a otras clases a las que hace referencia esta (e.g. Producto->objCategoria())
/* v 1.2 (20120502)
	* Añadidos los parametros segundo y tercero a htmlentities en la funcion cargarId para que maneje bien las conversiones UTF-8: ENT_QUOTES y "UTF-8"
/* v 1.1 (20120403)
	* Empezamos la history
	* Añadida llamada a htmlentities en cargarId, ya que los valores cargados de la BD serán usados en HTML y
	* los caracteres convertidos a entidades HTML para que no produzcan problemas.
/**************************************************************************************************************/
//Crea un fichero php con el codigo basico que solemos usar para las clases
//El fichero creado tira de las funciones de MysqliDB.php
//Se supone que el primer atributo de arrAtributos es la clave primaria de la tabla
class Creadora {
	private $ruta;
	private $nombreClase;
	private $arrAtributos;
	private $nombreTabla;
	private $arrFksFrom;
	private $arrFksTo;
	private $sl;//saltoLinea
	private $sg;//sangrado

	function creadora($ruta,$nombreClase, $arrAtributos, $nombreTabla, $arrFksFrom,$arrFksTo) {
		$this->ruta=$ruta;
		$this->nombreClase=$nombreClase;
		$this->arrAtributos=$arrAtributos;
		$this->nombreTabla=$nombreTabla;
		$this->arrFksFrom=$arrFksFrom;
		$this->arrFksTo=$arrFksTo;
		$this->sl="\n";
		$this->sg="\t";
		$sl=$this->sl;
		$sg=$this->sg;

		$classCode='';
		$classCode.="<?".$sl;
		//Apertura de la clase
		$classCode.="class ".$nombreClase." {".$sl;

		//Main
			$classCode.=$this->declaraciones($arrAtributos);
			$classCode.=$this->constructor();
			$classCode.=$this->db();
			$classCode.=$this->cargarId($arrAtributos,$nombreTabla);
			$classCode.=$this->grabar($arrAtributos,$nombreTabla);
			$classCode.=$this->borrar($nombreTabla,$nombreTabla);
			$classCode.=$this->cargarArray();
			$classCode.=$this->cargarObj();
			$classCode.=$this->toJson();
			$classCode.=$this->toArray();
			$classCode.=$this->settersGetters($arrAtributos);
		//estaticas
			$classCode.=$sl;
			$classCode.="/* Funciones estaticas ********************************************************/".$sl;
			$classCode.=$sl;
			$classCode.=$this->existeID($nombreTabla);
			$classCode.=$this->allToArray($nombreTabla);
			$classCode.=$this->ls($nombreTabla);
		//dinamicas
			$classCode.=$sl;
			$classCode.="/* Funciones dinamicas ********************************************************/".$sl;
			$classCode.=$sl;
			$classCode.=$this->noReferenciado($arrFksTo);
		//FkFrom
			$classCode.=$sl;
			$classCode.="/* Funciones FkFrom ***********************************************************/".$sl;
			$classCode.=$sl;
			//Inicio funciones FkFrom
			$classCode.=$this->FkFrom($arrFksFrom);
			//Fin funciones FkFrom
		//FkTo
			$classCode.=$sl;
			$classCode.="/* Funciones FkTo *************************************************************/".$sl;
			$classCode.=$sl;
			//Inicio funciones FkTo
			$classCode.=$this->FkTo($arrFksTo);
			//Fin funciones FkTo

		//Llave de cierre de la clase
		$classCode.="}".$sl;
		$classCode.="?>".$sl;
		//$file="./creacion/".$nombreClase.".php";
		$file=$ruta."/".$nombreClase.".php";
		$fp=fopen ($file,"w");
		fwrite ($fp,$classCode);
		fclose ($fp);
		chmod ($file,0777);
	}

	private function declaraciones($arrAtributos) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$resultCode.=$sg."private $".$nombreAtributo.";".$sl;
		}
		return $resultCode;
	}
	private function constructor() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function __construct (\$id=\"\") {".$sl;
		$resultCode.=$sg.$sg."if (\$id!=\"\") {".$sl;
		$resultCode.=$sg.$sg.$sg."\$this->cargarId (\$id);".$sl;
		$resultCode.=$sg.$sg."}".$sl;
		$resultCode.=$sg."}".$sl;
		$resultCode.=$sl;
		return $resultCode;
	}
	private function db() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg.'private static function db() {'.$sl;
		$resultCode.=$sg.'	return cDb::gI();'.$sl;
		$resultCode.=$sg.'}'.$sl;
		$resultCode.=$sl;
		return $resultCode;
	}
	private function cargarId($arrAtributos,$nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function cargarId (\$id) {".$sl;
		$resultCode.=$sg.$sg."\$result=false;".$sl;
		$resultCode.=$sg.$sg."\$sql=\"SELECT * FROM ".$nombreTabla." WHERE id='\".self::db()->real_escape_string(\$id).\"'\";".$sl;
		$resultCode.=$sg.$sg."\$data=self::db()->get_obj(\$sql);".$sl;
		$resultCode.=$sg.$sg."if (\$data) {".$sl;
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			//$resultCode.=$sg.$sg.$sg."\$this->".$nombreAtributo."=htmlentities(\$data->".$nombreAtributo.",ENT_QUOTES,\"UTF-8\");".$sl;
			$resultCode.=$sg.$sg.$sg."\$this->".$nombreAtributo."=\$data->".$nombreAtributo.";".$sl;
		}
		$resultCode.=$sg.$sg.$sg."\$result=true;".$sl;
		$resultCode.=$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg."return \$result;".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function grabar($arrAtributos,$nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function grabar () {".$sl;
		$resultCode.=$sg.$sg."\$result=false;".$sl;
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$resultCode.=$sg.$sg."\$sqlValue_".$nombreAtributo."=(is_null(\$this->".$nombreAtributo."))?\"NULL\":\"'\".self::db()->real_escape_string(\$this->".$nombreAtributo.").\"'\";".$sl;
		}
		$resultCode.=$sg.$sg."if (\$this->id!=\"\") { //UPDATE".$sl;

		$resultCode.=$sg.$sg.$sg."\$this->update=\$sqlValue_update=date(\"YmdHis\");".$sl;
		$resultCode.=$sg.$sg.$sg."\$sql=\"UPDATE ".$nombreTabla." SET \".".$sl;
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\"`".$nombreAtributo."`=\".\$sqlValue_".$nombreAtributo.".\", \".".$sl;
		}
		$code=substr ($code,0,-5);
		$code.=" \".".$sl;
		$code.=$sg.$sg.$sg.$sg."\"WHERE id='\".\$this->id.\"'\";".$sl;
		$resultCode.=$code;

		$resultCode.=$sg.$sg."} else { //INSERT".$sl;

		$arrKeys=array_keys($arrAtributos);
		$resultCode.=$sg.$sg.$sg."\$this->id=\$sqlValue_id=self::db()->nextId (\"".$nombreTabla."\",\"".$arrKeys[0]."\");".$sl;
		$resultCode.=$sg.$sg.$sg."\$this->insert=\$sqlValue_insert=\$this->update=\$sqlValue_update=date(\"YmdHis\");".$sl;
		$resultCode.=$sg.$sg.$sg."\$sql=\"INSERT INTO ".$nombreTabla." ( \".".$sl;
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\"`".$nombreAtributo."`, \".".$sl;
		}
		$code=substr ($code,0,-5);
		$resultCode.=$code.") VALUES (\".".$sl;
		$code="";
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			$code.=$sg.$sg.$sg.$sg."\$sqlValue_".$nombreAtributo.".\", \".".$sl;
		}
		$code=substr ($code,0,-5);
		$resultCode.=$code.")\";".$sl;
		$resultCode.=$sg.$sg."}".$sl;

		$resultCode.=$sg.$sg."\$result=self::db()->query (\$sql);".$sl;

		$resultCode.=$sg.$sg."return \$result;".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function borrar($nombreTabla,$nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg.'public function borrar() {'.$sl;
		$resultCode.=$sg.$sg.'$result=false;'.$sl;
		$resultCode.=$sg.$sg.'if ($this->noReferenciado()) {'.$sl;
		$resultCode.=$sg.$sg.$sg.'$sql="DELETE FROM '.$nombreTabla.' WHERE id=\'".self::db()->real_escape_string($this->id)."\'";'.$sl;
		$resultCode.=$sg.$sg.$sg.'self::db()->query($sql);'.$sl;
		$resultCode.=$sg.$sg.$sg.'$result=true;'.$sl;
		$resultCode.=$sg.$sg.'}'.$sl;
		$resultCode.=$sg.$sg.'return $result;'.$sl;
		$resultCode.=$sg.'}'.$sl;
		return $resultCode;
	}
	private function cargarArray() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function cargarArray (\$array,\$usingSetters=true) {".$sl;
		$resultCode.=$sg.$sg."foreach(\$this as \$key => \$value) {".$sl;
		$resultCode.=$sg.$sg.$sg."if (isset(\$array[\$key])) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."if (\$usingSetters) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$func=\"SET\".\$key;".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$this->\$func(\$array[\$key]);".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."} else {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$this->\$key=\$array[\$key];".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg."}".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function cargarObj() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function cargarObj (\$obj,\$usingSetters=true) {".$sl;
		$resultCode.=$sg.$sg."foreach(\$this as \$key => \$value) {".$sl;
		$resultCode.=$sg.$sg.$sg."if (isset(\$obj->\$key)) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."if (\$usingSetters) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$func=\"SET\".\$key;".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$this->\$func(\$obj->\$key);".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."} else {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."\$this->\$key=\$obj->\$key;".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg."}".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function toJson() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function toJson(){".$sl;
		$resultCode.=$sg.$sg."\$var = get_object_vars(\$this);".$sl;
		$resultCode.=$sg.$sg."foreach(\$var as &\$value){".$sl;
		$resultCode.=$sg.$sg.$sg."if(is_object(\$value) && method_exists(\$value,'toJson')){".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."\$value = \$value->toJson();".$sl;
		$resultCode.=$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg.$sg."if (is_array(\$value)) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."foreach (\$value as &\$item) {".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."if(is_object(\$item) && method_exists(\$item,'toJson')){".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.$sg."\$item = \$item->toJson();".$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg."}".$sl;
		$resultCode.=$sg.$sg."return \$var;".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function toArray() {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg."public function toArray () {".$sl;
		$resultCode.=$sg.$sg."\$result=get_object_vars(\$this);".$sl;
		$resultCode.=$sg.$sg."return \$result;".$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function settersGetters($arrAtributos) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		foreach ($arrAtributos as $nombreAtributo => $sqlData) {
			//$resultCode.=$sg."public function GET".$nombreAtributo." () {";
			//$resultCode.="return \$this->".$nombreAtributo.";";
			$resultCode.=$sg."public function GET".$nombreAtributo." (\$entity_decode=false) {";
			$resultCode.="return (\$entity_decode)?html_entity_decode(\$this->".$nombreAtributo.",ENT_QUOTES,\"UTF-8\"):\$this->".$nombreAtributo.";";
			$resultCode.="}".$sl;

			//$resultCode.=$sg."public function SET".$nombreAtributo." (\$".$nombreAtributo.") {";
			//$resultCode.="\$this->".$nombreAtributo."=\$".$nombreAtributo.";";
			$resultCode.=$sg."public function SET".$nombreAtributo." (\$".$nombreAtributo.",\$entity_encode=false) {";
			$resultCode.="\$this->".$nombreAtributo."=(\$entity_encode)?htmlentities(\$".$nombreAtributo.",ENT_QUOTES,\"UTF-8\"):\$".$nombreAtributo.";";
			$resultCode.="}".$sl;
			$resultCode.=$sl;
		}
		return $resultCode;
	}
	private function existeID($nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg.'public static function existeId($id) {'.$sl;
		$resultCode.=$sg.$sg.'$sql="SELECT * FROM '.$nombreTabla.' WHERE id=\'".self::db()->real_escape_string($id)."\'";'.$sl;
		$resultCode.=$sg.$sg.'$data=self::db()->get_obj($sql);'.$sl;
		$resultCode.=$sg.$sg.'if ($data) {$result=true;} else {$result=false;}'.$sl;
		$resultCode.=$sg.$sg.'return $result;'.$sl;
		$resultCode.=$sg."}".$sl;
		return $resultCode;
	}
	private function allToArray($nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg.'public static function allToArray($where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl;
		$resultCode.=$sg.$sg.'$sqlWhere=($where!="")?" WHERE ".$where:"";'.$sl;
		$resultCode.=$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl;
		$resultCode.=$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl;
		$resultCode.=$sg.$sg.'$sql="SELECT * FROM '.$nombreTabla.'".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl;
		$resultCode.=$sg.$sg.'$arr=array();'.$sl;
		$resultCode.=$sg.$sg.'$rsl=self::db()->query($sql);'.$sl;
		$resultCode.=$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl;
		$resultCode.=$sg.$sg.$sg.'switch ($tipo) {'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'$obj=new self($data->id);'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'$obj=new stdClass();'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'}'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl;
		$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
		$resultCode.=$sg.$sg.$sg.'}'.$sl;
		$resultCode.=$sg.$sg.'}'.$sl;
		$resultCode.=$sg.$sg.'return $arr;'.$sl;
		$resultCode.=$sg.'}'.$sl;
		return $resultCode;
	}
	private function ls($nombreTabla) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$nombreVista='ls'.ucfirst($nombreTabla);
		$resultCode.=$sg.'public static function ls($where="",$order="",$limit="") {'.$sl;
		$resultCode.=$sg.'	$sqlView="CREATE OR REPLACE VIEW `'.$nombreVista.'` AS'.$sl;
		$resultCode.=$sg.'		SELECT * FROM '.$nombreTabla.';'.$sl;
		$resultCode.=$sg.'	";'.$sl;
		$resultCode.=$sg.'	self::db()->query($sqlView);'.$sl;
		$resultCode.=$sg.'	$sqlWhere=($where!="")?" WHERE ".$where:"";'.$sl;
		$resultCode.=$sg.'	$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl;
		$resultCode.=$sg.'	$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl;
		$resultCode.=$sg.'	$sql="SELECT * FROM '.$nombreVista.'".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl;
		$resultCode.=$sg.'	$arr=array();'.$sl;
		$resultCode.=$sg.'	$rsl=self::db()->query($sql);'.$sl;
		$resultCode.=$sg.'	while ($data=$rsl->fetch_object()) {'.$sl;
		$resultCode.=$sg.'		$objSeg=new self($data->id);'.$sl;
		$resultCode.=$sg.'		$obj=new \stdClass();'.$sl;
		$resultCode.=$sg.'		foreach ($data as $field => $value) {'.$sl;
		$resultCode.=$sg.'			$obj->$field=$value;'.$sl;
		$resultCode.=$sg.'		}'.$sl;
		$resultCode.=$sg.'		array_push($arr,$obj);'.$sl;
		$resultCode.=$sg.'		unset ($obj);'.$sl;
		$resultCode.=$sg.'	}'.$sl;
		$resultCode.=$sg.'	return $arr;'.$sl;
		$resultCode.=$sg.'}'.$sl;
		return $resultCode;
	}
	private function noReferenciado($arrFksTo) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		$resultCode.=$sg.'public function noReferenciado() {'.$sl;
		if (count($arrFksTo)>0) {
			foreach ($arrFksTo as $objFkInfo) {
				$fTable=$objFkInfo->TABLE_NAME;
				$fField=$objFkInfo->COLUMN_NAME;
				$resultCode.=$sg.$sg.'$sql="SELECT '.$fField.' FROM '.$fTable.' WHERE '.$fField.'=\'".self::db()->real_escape_string($this->id)."\'";'.$sl;
				$resultCode.=$sg.$sg.'$noReferenciadoEn'.ucfirst($fTable).'=(self::db()->get_num_rows($sql)==0)?true:false;'.$sl;
			}
			$strConds='';
			foreach ($arrFksTo as $objFkInfo) {
				$fTable=$objFkInfo->TABLE_NAME;
				$fField=$objFkInfo->COLUMN_NAME;
				$strConds.='$noReferenciadoEn'.ucfirst($fTable).' && ';
			}
			$strConds=substr($strConds, 0, -4);
			$resultCode.=$sg.$sg.'$result=('.$strConds.')?true:false;'.$sl;
			$resultCode.=$sg.$sg.'return $result;'.$sl;
		} else {
			$resultCode.=$sg.$sg.'$result=true;'.$sl;
			$resultCode.=$sg.$sg.'return $result;'.$sl;
		}
		$resultCode.=$sg.'}'.$sl;
		return $resultCode;
	}
	private function FkFrom($arrFksFrom) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';

		$arrTables=array();
		foreach ($arrFksFrom as $objFkInfo) {
			$fTable=$objFkInfo->REFERENCED_TABLE_NAME;
			$fField=$objFkInfo->COLUMN_NAME;
			if (!array_key_exists($fTable, $arrTables)) {
				$arrTables[$fTable]=$fTable;
			} else {
				if (!is_array($arrTables[$fTable])) {
					$tmp=$arrTables[$fTable];
					$arrTables[$fTable]=array();
					$arrTables[$fTable][]=$tmp;
				}
				$arrTables[$fTable][]=$fField;
			}
		}
		foreach ($arrFksFrom as $objFkInfo) {
			$fTable=$objFkInfo->REFERENCED_TABLE_NAME;
			$fField=$objFkInfo->COLUMN_NAME;
			$functionName=$fTable;
			if (is_array($arrTables[$fTable])) {
				$functionName=$fTable.'By'.ucfirst($fField);
			}
			$resultCode.=$sg.'public function obj'.ucfirst($functionName).'() {'.$sl;
			$resultCode.=$sg.$sg.'return new '.ucfirst($fTable).'($this->'.$fField.');'.$sl;
			$resultCode.=$sg.'}'.$sl;
		}
		return $resultCode;
	}
	private function FkTo($arrFksTo) {
		$sl=$this->sl;
		$sg=$this->sg;
		$resultCode='';
		define ('FUNCION_FKTO_UNICA',false);
		if (FUNCION_FKTO_UNICA) {
			//Alternativa sin probar
			$resultCode.=$sg.'public function arrFkTo($table,$field,$where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl;
			$resultCode.=$sg.$sg.'$sqlWhere=($where!="")?" WHERE ".$field."=\'".self::db()->real_escape_String($this->id)."\' AND ".$where:" WHERE ".$field."=\'".self::db()->real_escape_string($this->id)."\'";'.$sl;
			$resultCode.=$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl;
			$resultCode.=$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl;
			$resultCode.=$sg.$sg.'$sql="SELECT * FROM ".$table.$sqlWhere.$sqlOrder.$sqlLimit;'.$sl;
			$resultCode.=$sg.$sg.'$arr=array();'.$sl;
			$resultCode.=$sg.$sg.'$rsl=self::db()->query($sql);'.$sl;
			$resultCode.=$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl;
			$resultCode.=$sg.$sg.$sg.'switch ($tipo) {'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'$obj=new ucfirst($table)($data->id);'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'}'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl;
			$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
			$resultCode.=$sg.$sg.$sg.'}'.$sl;
			$resultCode.=$sg.$sg.'}'.$sl;
			$resultCode.=$sg.$sg.'return $arr;'.$sl;
			$resultCode.=$sg.'}'.$sl;
		} else {
			$arrTables=array();
			foreach ($arrFksTo as $objFkInfo) {
				$fTable=$objFkInfo->TABLE_NAME;
				$fField=$objFkInfo->COLUMN_NAME;
				if (!array_key_exists($fTable, $arrTables)) {
					$arrTables[$fTable]=$fTable;
				} else {
					if (!is_array($arrTables[$fTable])) {
						$tmp=$arrTables[$fTable];
						$arrTables[$fTable]=array();
						$arrTables[$fTable][]=$tmp;
					}
					$arrTables[$fTable][]=$fField;
				}
			}
			foreach ($arrFksTo as $objFkInfo) {
				$fTable=$objFkInfo->TABLE_NAME;
				$fField=$objFkInfo->COLUMN_NAME;
				$functionName=$fTable;
				if (is_array($arrTables[$fTable])) {
					$functionName=$fTable.'By'.ucfirst($fField);
				}

				$resultCode.=$sg.'public function arr'.ucfirst($functionName).'($where="",$order="",$limit="",$tipo="arrStdObjs") {'.$sl;
				$resultCode.=$sg.$sg.'$sqlWhere=($where!="")?" WHERE '.$fField.'=\'".self::db()->real_escape_String($this->id)."\' AND ".$where:" WHERE '.$fField.'=\'".self::db()->real_escape_string($this->id)."\'";'.$sl;
				$resultCode.=$sg.$sg.'$sqlOrder=($order!="")?" ORDER BY ".$order:"";'.$sl;
				$resultCode.=$sg.$sg.'$sqlLimit=($limit!="")?" LIMIT ".$limit:"";'.$sl;
				$resultCode.=$sg.$sg.'$sql="SELECT * FROM '.$fTable.'".$sqlWhere.$sqlOrder.$sqlLimit;'.$sl;
				$resultCode.=$sg.$sg.'$arr=array();'.$sl;
				$resultCode.=$sg.$sg.'$rsl=self::db()->query($sql);'.$sl;
				$resultCode.=$sg.$sg.'while ($data=$rsl->fetch_object()) {'.$sl;
				$resultCode.=$sg.$sg.$sg.'switch ($tipo) {'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.'case "arrIds": array_push($arr,$data->id);break;'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.'case "arrClassObjs":'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'$obj=new '.ucfirst($fTable).'($data->id);'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.'case "arrStdObjs":'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'foreach ($data as $field => $value) {'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.$sg.'$obj->$field=$value;'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'}'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'array_push($arr,$obj);'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.$sg.'unset ($obj);'.$sl;
				$resultCode.=$sg.$sg.$sg.$sg.'break;'.$sl;
				$resultCode.=$sg.$sg.$sg.'}'.$sl;
				$resultCode.=$sg.$sg.'}'.$sl;
				$resultCode.=$sg.$sg.'return $arr;'.$sl;
				$resultCode.=$sg.'}'.$sl;
			}
		}
		return $resultCode;
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