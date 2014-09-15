<?
class DBException extends Exception {}

class mysqliDB extends mysqli {
	const _DB_HOST_='localhost';
	const _DB_USER_='celorrio3';
	const _DB_PASSWD_='3c3l0rr10';
	const _DB_NAME_='celorrio3';

	public function __construct($host=NULL, $user=NULL, $pass=NULL, $db=NULL) {
		if (is_null($host)) {
			$host=self::_DB_HOST_;
			$user=self::_DB_USER_;
			$pass=self::_DB_PASSWD_;
			$db=self::_DB_NAME_;
		}
		parent::init();
		/*
		if (!parent::options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
			throw new exception('Setting MYSQLI_INIT_COMMAND failed');
		}

		if (!parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
			throw new exception('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
		}
		*/
		if (!parent::real_connect($host, $user, $pass, $db)) {
			//throw new exception($this->connect_error, $this->connect_errno);
			//$connect_error falla hasta PHP 5.3.0, asi que usamos la siguiente
			throw new DBException(mysqli_connect_error(), mysqli_connect_errno());
		}
		/* change character set to utf8 */
		if (!parent::set_charset("utf8")) {
			throw new DBException(mysqli_connect_error(), mysqli_connect_errno());
		}
	}
	public function __destruct() {
		if (!parent::close()) {
			throw new DBException(mysqli_connect_error(), mysqli_connect_errno());
		}
	}
	public function query($query) {
		$localeActual=setlocale(LC_ALL, 0);
	 	setlocale(LC_ALL,'en_US.utf8');

		$result=parent::query($query);

		setlocale(LC_ALL,$localeActual);
		/*
		if(mysqli_error($this)){
			throw new exception(mysqli_error($this), mysqli_errno($this));
		}
		*/
		if($this->errno!=0) {
			$sql=(strlen($query)<512)?$query:substr($query,0,512)."[RESTO DE LA CONSULTA ELIMINADA]";
			throw new DBException("SQL: ".$sql.". ".$this->error, $this->errno);
		}
		return $result;
	}

	public function get_var($query) {
		return $this->get_data($query,"var");
	}

	public function get_row($query) {
		return $this->get_data($query,"row");
	}

	public function get_arrVars($query) {
		return $this->get_data($query,"arrVars");
	}

	public function get_arrRows($query) {
		return $this->get_data($query,"arrRows");
	}
	public function get_results($query) {return $this->get_arrRows($query);}

	public function get_num_rows($query) {
		return $this->get_data($query,"num_rows");
	}

	private function get_data($query,$tipo="mysqli_result") {
		$qResult=$this->query($query);
		switch ($tipo) {
			case "mysqli_result":
				$result=$qResult;
			case "var":
				$row=$qResult->fetch_array();
				$result=$row[0];
				break;
			case "row":
				$result=$qResult->fetch_object();
				break;
			case "arrVars":
				$result=array();
				while ($row=$qResult->fetch_array(MYSQLI_ASSOC)) {
					array_push($result,$row[0]);
				}
				break;
			case "arrArrs":
				//$result=$qResult->fetch_all(MYSQLI_BOTH);//<- Según el manual esta disponible a partir de PHP 5.3.0 pero probé en dl333 con PHP 5.3.3 y undefined method mysqli_result::fetch_all(), quiza porque Available only with mysqlnd (nd=native driver)
				$result=array();
				while ($row=$qResult->fetch_array(MYSQLI_ASSOC)) {
					array_push($result,$row);
				}
				break;
			case "arrObjs":
				$result=array();
				while ($row=$qResult->fetch_object()) {
					array_push($result,$row);
				}
				break;
			case "num_rows":
				$result=$qResult->num_rows;
				break;
			case "html_table":
				$result=mysqliDB::mysqli_result_to_html_table($qResult);
				break;
		}
		$qResult->free();
		return $result;
	}

	/* nextId *****************************************************
	**************************************************************/
	public function nextId($tabla,$campo) {
		$maxCampo=$this->get_var ("SELECT max($campo) as maxCampo FROM $tabla");
		$max=$maxCampo+1;
		if (class_exists('contador')) {
			/*
			CREATE TABLE IF NOT EXISTS `contador` (
				`id` INT NOT NULL,
				`insert` TIMESTAMP NULL DEFAULT NULL,
				`update` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
				`tabla` VARCHAR(255) NOT NULL,
				`campo` VARCHAR(255) NOT NULL,
				`valor` INT NOT NULL DEFAULT 0,
			PRIMARY KEY (`id`),
			UNIQUE INDEX `tablaCampo` (`tabla` ASC, `campo` ASC))
			*/
			$sqlContador="SELECT * FROM contador WHERE tabla='".$tabla."' AND campo='".$campo."'";
			$dataContador=$this->get_row($sqlContador);
			if ($dataContador) {
				$contador = new Contador($dataContador->id);
				if ($contador->GETvalor()>$maxCampo) {
					$max=($contador->GETvalor())+1;
				}
				$contador->SETvalor($max);
				$contador->grabar();
			}
		}
		return ($max);
	}

	/* mysqli_result_to_html_table ********************************
	**************************************************************/
	public static function result_to_html_table ($mysqli_result,$afterTrCallback=NULL) {
		$result='';
		$result.='<table border="1" style="width:100%;">';
		$i=0;
		while ($columnInfo = $mysqli_result->fetch_array(MYSQLI_ASSOC)) {
			if ($i==0) {
				$result.='	<tr>';
				if (!is_null($afterTrCallback)) {$result.=$afterTrCallback($i,$columnInfo);}
				foreach ($columnInfo as $key => $value) {
					$result.='		<th>'.$key.'</th>';
				}
				$result.='	</tr>';
			}
			$i++;
			$result.='	<tr>';
			if (!is_null($afterTrCallback)) {$result.=$afterTrCallback($i,$columnInfo);}
			foreach ($columnInfo as $key => $value) {
				$result.='		<td>'.$value.'</td>';
			}
			$result.='	</tr>';
		}
		$result.='</table>';
		return $result;
	}

}

class cDb extends mysqliDB {
	private static $singleton;
	public static function getInstance() {
		if(!self::$singleton instanceof self) self::$singleton = new self();
		return self::$singleton;
	}
	public static function gI() {
		return self::getInstance();
	}
}
?>