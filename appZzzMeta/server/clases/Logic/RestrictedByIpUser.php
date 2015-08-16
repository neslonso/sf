<?
use Sintax\Core\IUser;
use Sintax\Core\User;
use Sintax\Core\Page;
class RestrictedByIpUser extends User implements IUser {
	protected $userId='Anonimo';
	public function pagePermitida (Page $objPage) {
		$result=false;
		if (in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
			$result=true;
		}
		if (!$result) {
			throw new Exception("Su IP (".$_SERVER['REMOTE_ADDR'].") no está permitida", 1);
		}
		return "Cualquier cosa que no sea una clase que exista";
	}
	public function accionPermitida (Page $objPage,$metodo) {
		$result=false;
		if (in_array($_SERVER['REMOTE_ADDR'],unserialize(IPS_DEV))) {
			$result=true;
		}
		return $result;
	}
}
?>