<?
use Sintax\Core\IPage;
use Sintax\Core\Usuario;

class HomeSample extends Error implements IPage {

	public function __construct (Usuario $objUsr=NULL) {
		parent::__construct($objUsr);
	}

	public function pageValida () {
		$result=true;
		return $result;
	}
	public function accionValida($metodo) {
		switch ($metodo) {
			default: $result=false;
		}
		return $result;
	}

	public function title() {

	}

	public function head() {
		parent::head();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/head.php');
	}

	public function js() {
		parent::js();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/js.php');
	}

	public function css() {
		parent::css();
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/css.php');
	}

	public function markup() {
		require_once( str_replace('//','/',dirname(__FILE__).'/') .'markup/markup.php');
	}

	public function fragment($indice) {
		switch ($indice) {
			case '1':
			case '3':
			case '5':
				require( str_replace('//','/',dirname(__FILE__).'/') .'markup/fragment1.php');
			break;
			case '2':
			case '4':
			case '6':
				echo '<div class="titulo"><div>Titulo grande de sección</div></div>';
			break;
			case '7':
			break;
		}
	}
}
?>