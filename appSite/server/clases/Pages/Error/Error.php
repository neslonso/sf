<?
use Sintax\Core\IPage;
use Sintax\Core\Page;
use Sintax\Core\Usuario;

class Error extends Page implements IPage {

	private $msg;

	public function __construct (Usuario $objUsr=NULL) {
		parent::__construct($objUsr);
		$this->msg="Descripcion no especificada.";
	}

	public function setMsg($msg) {
		$this->msg=$msg;
	}

	//En head y markup no usamos require_once, pq si salta un error durante la ejecución de una Page
	//no se volverá a requerir el fichero, ya que todas las Pages descienden de Error (via extends Home).
	//Esto tambien significa que ninguna de las funciones pueden contener nada que no pueda ser
	//redeclarado (funciones, clases...)
	public function head() {
		require( str_replace('//','/',dirname(__FILE__).'/') .'markup/head.php');
	}

	public function js() {
		require( str_replace('//','/',dirname(__FILE__).'/') .'markup/js.php');
	}

	public function css() {
		require( str_replace('//','/',dirname(__FILE__).'/') .'markup/css.php');
	}

	public function markup() {
		try {
			if ($this->msg=="") {
				if (liMsgsReturnInfo()!="") {
					$this->setMsg('<ul class="sriMsgs">'.liMsgsReturnInfo().'</ul>');
				} else {
					if (!in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
						$this->setMsg('Error no especificado, use IPS_DEV');
					} else {
						$this->setMsg('<pre>'.print_r(debug_backtrace(),true).'</pre>');
					}
				}
			}
		} catch (Exception $e) {
			$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
			$this->setMsg('Error mostrando detalles de error:<br />'.$infoExc);
		}
		require( str_replace('//','/',dirname(__FILE__).'/') .'markup/markup.php');
	}
}
?>