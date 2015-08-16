<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

class bower extends Error implements IPage {
	public function __construct(User $objUsr=NULL) {
		parent::__construct($objUsr);
	}
	public function pageValida () {
		return $this->objUsr->pagePermitida($this);
		//return parent::pageValida();
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
		$pkgs=(isset($_GET['pkgs']))?$_GET['pkgs']:'';

		$cCmd['install']=$cCmd['update']=$cCmd['uninstall']=$cCmd['search']=$cCmd['info']=$cCmd['list']='';

		if (isset($_GET['cCmd'])) {
			$cCmd[$_GET['cCmd']]='checked="checked"';
		} else {
			$cCmd['search']='checked="checked"';
		}

		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}

	public function acExec ($args) {
		echo "Args: <pre>".print_r ($args,true)."</pre>";
		echo "Request: <pre>".print_r ($_REQUEST,true)."</pre>";

		$pkgs=$args['pkgs'];
		$cCmd=$args['cCmd'];

		$opts='-n -d"'.SKEL_ROOT_DIR.'"';
		switch ($cCmd) {
			case "install":$opts.='';break;
			case "update":$opts.='';break;
			case "uninstall":$opts.='';break;
			case "list":$opts.='';break;
			break;
			case "search":
			case "info":
				if ($pkgs=='') {
					throw new \ActionException('Debe introducir el paquete.', 1);
				}
			break;
			default:
				throw new \ActionException('Comando "'.htmlspecialchars($cCmd).'" no válido o no implementado', 1);
			break;
		}

		putenv('HOME='.SKEL_ROOT_DIR);
		//putenv('BOWERPHP_TOKEN=' . <github api token>);
		$cmd='php '.SKEL_ROOT_DIR.'includes/server/vendor/bowerphp.phar '.$opts.' '.$cCmd.' '.$pkgs;
		echo "<h2>Ejecutando: ".$cmd."</h2>";
		echo "<h3>Working dir: ".getcwd ()." :: HOME=".getenv('HOME')."</h3>";

		$descriptorspec = array(
			//0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		);

		$process = proc_open($cmd, $descriptorspec, $pipes);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		unset($pipes);

		echo "<h3>STDOUT</h3>";
		echo "<pre>".$stdout."</pre>";
		echo "<h3>STDERR</h3>";
		echo "<pre>".$stderr."</pre>";

		$this->generateBowerComponentsFile();
	}

	private function generateBowerComponentsFile () {
		echo "<h2>Generando bowerComponents.php</h2>";
		define ('BOWER_PATH',SKEL_ROOT_DIR.'includes/cliente/vendor/bowerVendor/');
		$bowerComponentsFilePath=SKEL_ROOT_DIR.'includes/cliente/bowerComponents.php';

		$arrBowerJsonFiles=\Filesystem::folderSearch(BOWER_PATH, '/.*bower.json/');
		$arrJsFiles=array();
		$arrCssFiles=array();
		echo "<pre>";
		$arrBowerComponents=array();
		foreach ($arrBowerJsonFiles as $bowerJsonPath) {
			$objBowerInfo=json_decode(file_get_contents($bowerJsonPath));
			/*
			echo '/****************************************************************************'.PHP_EOL;
			echo basename(dirname($bowerJsonPath)).PHP_EOL;
			echo basename($bowerJsonPath).PHP_EOL;
			echo "objBowerInfo:".print_r ($objBowerInfo,true).PHP_EOL;
			echo '/****************************************************************************'.PHP_EOL;
			*/

			$arrBowerComponents[$objBowerInfo->name]=new \stdClass();
			$arrBowerComponents[$objBowerInfo->name]->js=array();
			$arrBowerComponents[$objBowerInfo->name]->css=array();
			$arrBowerComponents[$objBowerInfo->name]->dependencies=(isset($objBowerInfo->dependencies))?(array)$objBowerInfo->dependencies:array();
			$arrObjBowerInfoMain=(is_array($objBowerInfo->main))?$objBowerInfo->main:array(0 => $objBowerInfo->main);
			foreach ($arrObjBowerInfoMain as $fileRelPath) {
				$baseURI="./".\Filesystem::find_relative_path(SKEL_ROOT_DIR,BOWER_PATH)."/";
				$includeFilePath=$baseURI.basename(dirname($bowerJsonPath)).DIRECTORY_SEPARATOR.$fileRelPath;
				$fileExt=pathinfo($includeFilePath, PATHINFO_EXTENSION);
				switch ($fileExt) {
					case 'js':
						if (!in_array($includeFilePath,$arrJsFiles)) {
							$arrBowerComponents[$objBowerInfo->name]->js[]=$includeFilePath;
						}
						break;
					case 'css':
						if (!in_array($includeFilePath,$arrCssFiles)) {
							$arrBowerComponents[$objBowerInfo->name]->css[]=$includeFilePath;
						}
						break;
				}
			}
		}
		//print_r ($arrBowerComponents);

		$arrBowerComponentsIncluded=array();
		$bowerComponentsContent='';
		$i=0;
		while (count($arrBowerComponents)>count($arrBowerComponentsIncluded)) {
			$i++;
			if ($i>1000) {
				file_put_contents($bowerComponentsFilePath,$bowerComponentsContent);
				throw new \ActionException("No se pudieron satisfacer las dependencias", 1);
			}
			foreach ($arrBowerComponents as $componentName => $objComponentInfo) {
				$dependenciesSatisfied=true;
				foreach ($objComponentInfo->dependencies as $dependencyName => $dependencyVersion) {
					if (!in_array($dependencyName, $arrBowerComponentsIncluded)) {
						$dependenciesSatisfied=false;
					}
				}
				if ($dependenciesSatisfied) {
					if (!in_array($componentName, $arrBowerComponentsIncluded)) {
						echo "Incluyendo: ".$componentName."<br />\n";
						$arrBowerComponentsIncluded[]=$componentName;
						foreach ($objComponentInfo->js as $jsFilePath) {
							$bowerComponentsContent.='<script src="'.$jsFilePath.'"></script>'.PHP_EOL;
						}
						foreach ($objComponentInfo->css as $cssFilePath) {
							$bowerComponentsContent.='<link href="'.$cssFilePath.'" rel="stylesheet">'.PHP_EOL;
						}
					}
				}
			}
		}
		file_put_contents($bowerComponentsFilePath,$bowerComponentsContent);
		//$arrBowerFiles=array_merge($arrJsFiles,$arrCssFiles);
		//echo json_encode($arrBowerFiles);
		echo "</pre>";
		echo "<hr>";
	}
}
?>
