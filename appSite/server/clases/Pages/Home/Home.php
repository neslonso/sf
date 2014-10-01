<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

class Home extends Error implements IPage {
	public function __construct(User $objUsr=NULL) {
		parent::__construct($objUsr);
	}
	public function pageValida () {
		//return $this->objUsr->pagePermitida($this);
		//return "docHome";
		return true;
	}
	public function accionValida($metodo) {
		return $this->objUsr->accionPermitida($this,$metodo);
	}
	public function title() {
		return parent::title();
	}
	public function metaTags() {
		return parent::metaTags();
	}
	public function head() {
		parent::head();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/head.php");
	}
	public function js() {
		parent::js();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/js.php");
	}
	public function css() {
		parent::css();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/css.php");
	}
	public function markup() {
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
		echo '<h1>Página intencionadamente vacía</h1>';
		echo '<a href="'.BASE_URL.'docHome">Ver documentación (zzDoc/docHome)</a>';
	}
}
?>
