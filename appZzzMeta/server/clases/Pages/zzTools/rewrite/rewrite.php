<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

class rewrite extends Error implements IPage {
	public function __construct(User $objUsr=NULL) {
		parent::__construct($objUsr);
	}
	public function pageValida () {
		return $this->objUsr->pagePermitida($this);
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
		//require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
		$requestHeaders=array();
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === 0) {
				$requestHeaders[$key]=$value;
				/*
				$chunks = explode('_', $key);
				$header = '';
				for ($i = 1; $y = sizeof($chunks) - 1, $i < $y; $i++) {
					$header .= ucfirst(strtolower($chunks[$i])).'-';
				}
				$header .= ucfirst(strtolower($chunks[$i])).': '.$value;
				echo $header.'<br>';
				*/
			}
		}
		echo "<pre>".
			"ENV: <br />".print_r($_ENV,true)."<hr />".
			"requestHeaders: <br />".print_r($requestHeaders,true)."<hr />".
			"SERVER: <br />".print_r($_SERVER,true)."<hr />".
		"</pre>";
	}
}
?>