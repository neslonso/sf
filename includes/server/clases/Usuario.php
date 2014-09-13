<?
interface IUser {
	public function __construct ($id="");
	public function GETnombre();
	public static function compruebaLogin($user,$pass);
}

abstract class Usuario implements IUser {
	protected $id;
	private $insert;
	private $update;
	private $idTipoUsuario;
	private $login;
	private $pass;
	private $email;

	const ADMINISTRADOR = 1;
	const CLIENTE = 2;

	public function __construct ($id="") {
		if ($id!="") {
			$this->cargarId ($id);
		}
	}

	public function cargarId ($id) {
		$result=false;
		$sql="SELECT * FROM usuario WHERE id='".$GLOBALS['db']->real_escape_string($id)."'";
		$data=$GLOBALS['db']->get_row($sql);
		if ($data) {
			$this->id=$data->id;
			$this->insert=$data->insert;
			$this->update=$data->update;
			$this->idTipoUsuario=$data->idTipoUsuario;
			$this->login=$data->login;
			$this->pass=$data->pass;
			$this->email=$data->email;
			$result=true;
		}
		return $result;
	}

	public function grabar () {
		$result=false;
		$sqlValue_id=(is_null($this->id))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->id)."'";
		$sqlValue_insert=(is_null($this->insert))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->insert)."'";
		$sqlValue_update=(is_null($this->update))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->update)."'";
		$sqlValue_idTipoUsuario=(is_null($this->idTipoUsuario))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->idTipoUsuario)."'";
		$sqlValue_login=(is_null($this->login))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->login)."'";
		$sqlValue_pass=(is_null($this->pass))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->pass)."'";
		$sqlValue_email=(is_null($this->email))?"NULL":"'".$GLOBALS['db']->real_escape_string($this->email)."'";
		if ($this->id!="") { //UPDATE
			$sql="UPDATE usuario SET ".
				//"`id`=".$sqlValue_id.", ".
				//"`insert`=".$sqlValue_insert.", ".
				//"`update`=".$sqlValue_update.", ".
				"`idTipoUsuario`=".$sqlValue_idTipoUsuario.", ".
				"`login`=".$sqlValue_login.", ".
				"`pass`=".$sqlValue_pass.", ".
				"`email`=".$sqlValue_email." ".
				"WHERE id='".$this->id."'";
		} else { //INSERT
			$this->id=$sqlValue_id=$GLOBALS['db']->nextId ("usuario","id");
			$this->insert=$sqlValue_insert=$this->update=$sqlValue_update=date("YmdHis");
			$sql="INSERT INTO usuario ( ".
				"`id`, ".
				"`insert`, ".
				"`update`, ".
				"`idTipoUsuario`, ".
				"`login`, ".
				"`pass`, ".
				"`email`) VALUES (".
				$sqlValue_id.", ".
				$sqlValue_insert.", ".
				$sqlValue_update.", ".
				$sqlValue_idTipoUsuario.", ".
				$sqlValue_login.", ".
				$sqlValue_pass.", ".
				$sqlValue_email.")";
		}
		$result=$GLOBALS['db']->query ($sql);
		return $result;
	}

	public function cargarArray ($array,$usingSetters=true) {
		foreach($this as $key => $value) {
			if (isset($array[$key])) {
				if ($usingSetters) {
					$func="SET".$key;
					$this->$func($array[$key]);
				} else {
					$this->$key=$array[$key];
				}
			}
		}
	}

	public function cargarObj ($obj,$usingSetters=true) {
		foreach($this as $key => $value) {
			if (isset($obj->$key)) {
				if ($usingSetters) {
					$func="SET".$key;
					$this->$func($obj->$key);
				} else {
					$this->$key=$obj->$key;
				}
			}
		}
	}

	public function toJson () {
		$result=json_encode(get_object_vars($this));
		return $result;
	}

	public function toArray () {
		$result=get_object_vars($this);
		return $result;
	}

	public function GETid ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->id,ENT_QUOTES,"UTF-8"):$this->id;}
	public function SETid ($id,$entity_encode=false) {$this->id=($entity_encode)?htmlentities($id,ENT_QUOTES,"UTF-8"):$id;}

	public function GETinsert ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->insert,ENT_QUOTES,"UTF-8"):$this->insert;}
	public function SETinsert ($insert,$entity_encode=false) {$this->insert=($entity_encode)?htmlentities($insert,ENT_QUOTES,"UTF-8"):$insert;}

	public function GETupdate ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->update,ENT_QUOTES,"UTF-8"):$this->update;}
	public function SETupdate ($update,$entity_encode=false) {$this->update=($entity_encode)?htmlentities($update,ENT_QUOTES,"UTF-8"):$update;}

	public function GETidTipoUsuario ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->idTipoUsuario,ENT_QUOTES,"UTF-8"):$this->idTipoUsuario;}
	public function SETidTipoUsuario ($idTipoUsuario,$entity_encode=false) {$this->idTipoUsuario=($entity_encode)?htmlentities($idTipoUsuario,ENT_QUOTES,"UTF-8"):$idTipoUsuario;}

	public function GETlogin ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->login,ENT_QUOTES,"UTF-8"):$this->login;}
	public function SETlogin ($login,$entity_encode=false) {$this->login=($entity_encode)?htmlentities($login,ENT_QUOTES,"UTF-8"):$login;}

	public function GETpass ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->pass,ENT_QUOTES,"UTF-8"):$this->pass;}
	public function SETpass ($pass,$entity_encode=false) {$this->pass=($entity_encode)?htmlentities($pass,ENT_QUOTES,"UTF-8"):$pass;}

	public function GETemail ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->email,ENT_QUOTES,"UTF-8"):$this->email;}
	public function SETemail ($email,$entity_encode=false) {$this->email=($entity_encode)?htmlentities($email,ENT_QUOTES,"UTF-8"):$email;}

	public function GETidUsuario ($entity_decode=false) {return ($entity_decode)?html_entity_decode($this->idUsuario,ENT_QUOTES,"UTF-8"):$this->idUsuario;}


	public function esAdministrador(){
		if ($this->idTipoUsuario==self::ADMINISTRADOR){
			return true;
		} else {
			return false;
		}
	}

	public function esCliente(){
		if ($this->idTipoUsuario==self::CLIENTE){
			return true;
		} else {
			return false;
		}
	}

/* Funciones estaticas *********************************************************/
	public static function existeId($id) {
		$sql="SELECT * FROM usuario WHERE id='".$GLOBALS['db']->real_escape_string($id)."'";
		$data=$GLOBALS['db']->get_row($sql);
		if ($data) {$result=true;} else {$result=false;}
		return $result;
	}

	public static function compruebaLogin($user,$pass){
		$sql="SELECT * FROM usuario WHERE login='".$user."'";
		$GLOBALS['firephp']->info($sql,'SQL DE USUARIO: ');
		$rsl=$GLOBALS['db']->query($sql);
		$data=$rsl->fetch_object();
		if ($data) {
			if ($data->pass==$pass){
	        	switch  ($data->idTipoUsuario) {
	        		case self::ADMINISTRADOR:
	        			$objUsr=new Administrador($data->id);
	        			break;
	        		case self::CLIENTE:
	        			$objUsr=new Cliente($data->id);
	        			break;
	        		default:
	        			throw new Exception('Tipos de usuario no definido.');
	        	}
				return $objUsr;
			} else {
				throw new ActionException('Datos de acceso incorrectos.');
				//return 0;
			}
        } else {
			//throw new ActionException('Datos de acceso incorrectos.');
			$_SESSION['returnInfo']['title']='Acceso no permitido';
			$_SESSION['returnInfo']['msg']='Los datos introducidos no son válidos';
        }
	}


	public static function existeLogin($user){
		$sql="SELECT * FROM usuario WHERE login='".$user."'";
		$rsl=$GLOBALS['db']->query($sql);
		$data=$rsl->fetch_object();
		if ($data) {
			return "1";
        } else {
			return "0";
        }
	}

/******************************************************************************/

	public function GETnombre() {
		return $this->login;
	}


}


?>
