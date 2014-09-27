<?
class Home extends Error implements IPage {
	public function __construct(Usuario $objUsr=NULL) {
		parent::__construct();
	}
	public function pageValida () {
		//return "docHome";
		return true;
	}
	public function accionValida($metodo) {
		switch ($metodo) {
			default: $result=false;
		}
		return $result;
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
