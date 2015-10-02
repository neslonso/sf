<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;
use Sintax\Core\ReturnInfo;
class roadmap extends Error implements IPage {
	public function __construct(User $objUsr) {
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
		$dDocRoadmap = new \DOMDocument();
		$dDocRoadmap->load (SKEL_ROOT_DIR.'roadmap.xml');
		//$sXmlEltoVersiones = simplexml_import_dom($dDocRoadmap);
		$fp = \FirePHP::getInstance(true);
		//$fp->info($sXmlEltoVersiones);
		//$fp->info($dDocRoadmap);
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}
	public function acUpdateAttr ($args) {
		$fp = \FirePHP::getInstance(true);
		$xpath=$args['xpath'];
		$attrName=$args['attrName'];
		$attrValue=$args['attrValue'];
		$dDocRoadmap = new \DOMDocument();
		$dDocRoadmap->load (SKEL_ROOT_DIR.'roadmap.xml');
		$sXmlEltoVersion=simplexml_import_dom($dDocRoadmap);
		$arr=$sXmlEltoVersion->xpath($xpath);
		$arr[0]->attributes()->$attrName=$attrValue;
		$sXmlEltoVersion->asXml(SKEL_ROOT_DIR.'roadmap.xml');
	}
}
?>
