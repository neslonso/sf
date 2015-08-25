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

		putenv('HOME='.SKEL_ROOT_DIR);
		//putenv('BOWERPHP_TOKEN=' . <github api token>);
		$opts='-n -d"'.SKEL_ROOT_DIR.'" -V';
		$cmd='php '.SKEL_ROOT_DIR.'includes/server/vendor/bowerphp.phar '.$opts;
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
		$bowerphpVersion=$stdout;

		$arrInstalledLibs=$this->getInstalledLibs();

		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}

	public function acExec ($args) {
		echo "Args: <pre>".print_r ($args,true)."</pre>";
		echo "Request: <pre>".print_r ($_REQUEST,true)."</pre>";

		$pkgs=$args['pkgs'];
		$cCmd=$args['cCmd'];
		$arrLibsApps=$args['arrLibsApps'];

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

		echo "<hr />";
		echo "<h3>STDOUT</h3>";
		echo "<pre>".$stdout."</pre>";
		echo "<hr />";
		echo "<h3>STDERR</h3>";
		echo "<pre>".$stderr."</pre>";
		echo "<hr />";

		$this->generateBowerComponentsFiles($arrLibsApps);
	}

	private function getInstalledLibs() {
		$arrInstalledLibs=array();
		$arrBowerJsonFiles=\Filesystem::folderSearch(BOWER_PATH, '/.*bower.json/');
		foreach ($arrBowerJsonFiles as $bowerJsonFilePath) {
			if (!isset($arrInstalledLibs[basename(dirname($bowerJsonFilePath))])) {
				$dotBowerJsonFilePath=dirname($bowerJsonFilePath).'/.bower.json';
				if (file_exists($dotBowerJsonFilePath)) {
					$objBowerInfo=json_decode(file_get_contents($dotBowerJsonFilePath));
				} else {
					$objBowerInfo=json_decode(file_get_contents($bowerJsonFilePath));
				}

				$objLibData=new \stdClass();
				$objLibData->name=$objBowerInfo->name;
				$objLibData->version=(isset($objBowerInfo->version))?$objBowerInfo->version:'No definida';
				$objLibData->dependencies=(isset($objBowerInfo->dependencies))?(array)$objBowerInfo->dependencies:array();
				$objLibData->main=(is_array($objBowerInfo->main))?$objBowerInfo->main:array(0 => $objBowerInfo->main);

				$arrJsFiles=array();
				$arrCssFiles=array();
				$arrOtherFiles=array();
				$objLibData->js=array();
				$objLibData->css=array();
				$objLibData->otherFiles=array();

				foreach ($objLibData->main as $fileRelPath) {
					$baseURI="./".\Filesystem::find_relative_path(SKEL_ROOT_DIR,BOWER_PATH)."/";
					$includeFilePath=$baseURI.basename(dirname($bowerJsonFilePath)).DIRECTORY_SEPARATOR.$fileRelPath;
					$fileExt=pathinfo($includeFilePath, PATHINFO_EXTENSION);
					switch ($fileExt) {
						case 'js':
							if (!in_array($includeFilePath,$arrJsFiles)) {
								$objLibData->js[]=$includeFilePath;
							}
							break;
						case 'css':
							if (!in_array($includeFilePath,$arrCssFiles)) {
								$objLibData->css[]=$includeFilePath;
							}
							break;
						default:
							if (!in_array($includeFilePath,$arrOtherFiles)) {
								$objLibData->otherFiles[]=$includeFilePath;
							}
							break;
					}
				}

				$arrInstalledLibs[basename(dirname($bowerJsonFilePath))]=$objLibData;
			}
		}
		return $arrInstalledLibs;
	}

	private function generateBowerComponentsFiles ($arrLibsApps) {
		echo "<h2>Generando bowerComponents.php / appBowerComponents.php</h2>";
		//Quitamos todo lo que esté en global de las apps
		foreach ($arrLibsApps as $libName => $arrLibData) {
			if ($arrLibData['GLOBAL']==1) {
				foreach ($arrLibData as $scopeName => $siempre1) {
					if ($scopeName!='GLOBAL') {
						$arrLibsApps[$libName][$scopeName]=0;
					}
				}
			}
		}
		echo "arrLibsApps: <pre>".print_r ($arrLibsApps,true)."</pre>";

		echo "<h3>GLOBAL</h3>";
		try {
			$this->includeAppLibs(NULL,$this->getInstalledLibs(),$arrLibsApps);
		} catch (\Exception $e) {
			echo 'Excepcion incluyendo libs. Msg: '.$e->getMessage();
		}

		$arrApps=unserialize(APPS);
		foreach ($arrApps as $entryPoint => $arrAppData) {
			echo '<h3>'.$entryPoint.' ('.$arrAppData['NOMBRE_APP'].')</h3>';
			try {
				$this->includeAppLibs($arrAppData,$this->getInstalledLibs(),$arrLibsApps);
			} catch (\Exception $e) {
				echo 'Excepcion incluyendo libs. Msg: '.$e->getMessage();
			}
			echo "<hr />";
		}
	}

	private function includeAppLibs($arrAppData,$arrInstalledLibs,$arrLibsApps) {
		if (is_null($arrAppData)) {
			$scopeName="GLOBAL";
			$bowerComponentsFilePath=SKEL_ROOT_DIR.'includes/cliente/bowerComponents.php';
		} else {
			$scopeName=$arrAppData['FILE_APP'];
			$bowerComponentsFilePath=$arrAppData['RUTA_APP'].'cliente/appBowerComponents.php';
		}

		if (!file_exists($bowerComponentsFilePath)) {throw new \ActionException('Fichero bowerComponents no existe ('.$bowerComponentsFilePath.')', 1);return;}

		//file_put_contents($bowerComponentsFilePath,'');
		$arrBowerComponents=$arrInstalledLibs;
		$arrBowerComponentsProcessed=array();
		$bowerComponentsContent='<!-- Fichero generado automaticamente. No editar. -->'.PHP_EOL;
		$i=0;
		while (count($arrBowerComponents)>count($arrBowerComponentsProcessed)) {
			$i++;
			if ($i>1000) {
				file_put_contents($bowerComponentsFilePath,$bowerComponentsContent);
				throw new \ActionException('No se pudieron satisfacer las dependencias ['.$scopeName.']', 1);
			}
			foreach ($arrBowerComponents as $componentName => $objComponentInfo) {
				$dependenciesSatisfied=true;
				foreach ($objComponentInfo->dependencies as $dependencyName => $dependencyVersion) {
					if (!in_array($dependencyName, $arrBowerComponentsProcessed)) {
						$dependenciesSatisfied=false;
					}
				}
				if ($dependenciesSatisfied) {
					if (!in_array($componentName, $arrBowerComponentsProcessed)) {
						if (isset($arrLibsApps[$componentName])) {
							if (isset($arrLibsApps[$componentName][$scopeName])) {
								if ($arrLibsApps[$componentName][$scopeName]!=1) {
									echo "Excluido: ".$componentName."<br />\n";
									$arrBowerComponentsProcessed[]=$componentName;
									continue;
								}
							}
						}
						echo "Incluido: ".$componentName."<br />\n";
						$arrBowerComponentsProcessed[]=$componentName;
						foreach ($objComponentInfo->js as $jsFilePath) {
							$bowerComponentsContent.='<!-- '.$componentName.' --><script src="'.$jsFilePath.'"></script>'.PHP_EOL;
						}
						foreach ($objComponentInfo->css as $cssFilePath) {
							$bowerComponentsContent.='<!-- '.$componentName.' --><link href="'.$cssFilePath.'" rel="stylesheet">'.PHP_EOL;
						}
					}
				}
			}
		}
		file_put_contents($bowerComponentsFilePath,$bowerComponentsContent);
	}

	private function getArrLibsApps() {
		$arrLibsApps=array();
		$arrApps=unserialize(APPS);
		$arrApps['GLOBAL']=array('RUTA_APP' => SKEL_ROOT_DIR);


		$arrInstalledLibs=$this->getInstalledLibs();
		foreach ($arrInstalledLibs as $libName => $objLibData) {
			foreach ($arrApps as $entryPoint => $arrAppData) {
				if ($entryPoint=='GLOBAL') {
					$scopeName="GLOBAL";
					$bowerComponentsFilePath=$arrAppData['RUTA_APP'].'includes/cliente/bowerComponents.php';
				} else {
					$scopeName=$arrAppData['FILE_APP'];
					$bowerComponentsFilePath=$arrAppData['RUTA_APP'].'cliente/appBowerComponents.php';
				}
				$bowerComponentsContent='';
				try {
					$bowerComponentsContent=file_get_contents($bowerComponentsFilePath);
				} catch (\Exception $e) {
					$fp=\FirePHP::getInstance(true);
					$fp->error('Excepcion en getArrLibsApps. Msg: '.$e->getMessage());
				}
				if (
					strstr($bowerComponentsContent, "<!-- ".$libName." -->") ||
					strstr($bowerComponentsContent, "bowerVendor/".$libName."") ) {
					$arrLibsApps[$libName][$scopeName]=1;
				} else {
					$arrLibsApps[$libName][$scopeName]=0;
				}
			}
		}
		return $arrLibsApps;
	}
}
?>
