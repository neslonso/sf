<?
class modulos extends docHome implements IPage {
	public function __construct(Usuario $objUsr=NULL) {
		parent::__construct();
	}
	public function pageValida () {
		return parent::pageValida();
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
	public function content() {
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/content.php");
	}
}
?>
