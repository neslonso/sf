<?
namespace Sintax\Core;

interface IUser {
	/**
	 * Comprueba si un usuario tiene acceso permitido a una page
	 * @param  object $objPage Instancia de Sintax\Core\Page objeto de la clase de página que se desea comprobar
	 * @return boolean | string Boolean true si el acceso está permitido o nombre de la page a la que continuar en caso contrario
	 */
	public function pagePermitida (Page $objPage);
	/**
	 * Comprueba si un usuario tiene acceso permitido a una accion
	 * @param  string $objPage Nombre de la clase de página a la que pertenece la acción que se desea comprobar
	 * @param  string $metodo Nombre del metodo de acción que se desea comprobar
	 * @return boolean | string Boolean true si el acceso está permitido o mensaje para el usuario en caso contrario
	 */
	public function accionPermitida (Page $objPage,$metodo);
}

abstract class User implements IUser {
	/**
	 * Valor que identifica univocamente al usuario de la aplicacion
	 * @var mixed
	 */
	protected $userId;
	public function pagePermitida (Page $objPage) {return true;}
	public function accionPermitida (Page $objPage,$metodo) {return true;}
}

class AnonymousUser extends User implements IUser {
	protected $userId='Anonimo';
	public function pagePermitida (Page $objPage) {return true;}
	public function accionPermitida (Page $objPage,$metodo) {
		$result=true;
		switch (get_class($objPage)) {
			case 'value':
				switch ($metodo) {
					case 'value':
						# code...
						break;
					default:
						# code...
						break;
				}
				break;
			default:
				# code...
				break;
		}
		return $result;
	}
}
?>
