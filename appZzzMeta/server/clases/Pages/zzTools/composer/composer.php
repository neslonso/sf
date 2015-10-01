<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class composer extends Error implements IPage {
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
		$cCmd['install']=$cCmd['update']=$cCmd['require']=$cCmd['remove']=$cCmd['search']=$cCmd['show']='';

		if (isset($_GET['cCmd'])) {
			$cCmd[$_GET['cCmd']]='checked="checked"';
		} else {
			$cCmd['search']='checked="checked"';
		}

		putenv('COMPOSER_HOME=' . SKEL_ROOT_DIR);
		$descriptorspec = array(
			//0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		);
		$cmd='php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar --version';
		$process = proc_open($cmd, $descriptorspec, $pipes);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);
		unset($pipes);
		$composerVersion=$stdout;

		$arrInstalledLibs=\Sintax\Pages\bower::getInstalledLibs(COMPOSER_ASSET_PLUGIN_PATH);

		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}

	public function acUpdate ($args) {
		putenv('COMPOSER_HOME=' . SKEL_ROOT_DIR);
		$descriptorspec = array(
			//0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		);
		$cmd='php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar self-update';
		echo "<h2>Ejecutando: ".$cmd."</h2>";
		$process = proc_open($cmd, $descriptorspec, $pipes);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		echo "<h3>STDOUT</h3>";
		echo "<pre>".$stdout."</pre>";
		echo "<h3>STDERR</h3>";
		echo "<pre>".$stderr."</pre>";
	}

	public function acExec ($args) {
		$pkgs=$args['pkgs'];
		$cCmd=$args['cCmd'];
		$dryRun=($args['dryRun']==1)?' --dry-run ':'';
		$verbose=($args['verbose']==1)?' --verbose ':'';
		$opts=' --no-interaction -d "'.SKEL_ROOT_DIR.'" ';
		switch ($cCmd) {
			case "install":$opts.='--optimize-autoloader '.$dryRun.$verbose;break;
			case "update":$opts.='--with-dependencies '.$dryRun;break;
			break;
			case "require":
			case "remove":
				$opts.='--update-with-dependencies ';break;
			case "search":
			case "show":
				if ($pkgs=='') {
					throw new ActionException('Debe introducir el paquete.', 1);
				}
			break;
			default:
				throw new ActionException('Comando "'.htmlspecialchars($cCmd).'" no válido o no implementado', 1);
			break;
		}
		putenv('COMPOSER_HOME=' . SKEL_ROOT_DIR);
		$descriptorspec = array(
			//0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		);
		$cmd='php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar '.$cCmd.' '.$pkgs.' '.$opts;
		echo "<h2>Ejecutando: ".$cmd."</h2>";
		$process = proc_open($cmd, $descriptorspec, $pipes);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);


		echo "<h3>STDOUT</h3>";
		echo "<pre>".$stdout."</pre>";
		echo "<h3>STDERR</h3>";
		echo "<pre>".$stderr."</pre>";
		/**/

		if (file_exists(SKEL_ROOT_DIR.'cache')) {
			echo '<h4>Composer ha creado el directorio "'.SKEL_ROOT_DIR.'cache", eliminado directorio</h4>';
			\Filesystem::delTree(SKEL_ROOT_DIR.'cache');
		}

		/*if (is_resource($process)) {
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			// Any error output will be appended to /tmp/error-output.txt

			fwrite($pipes[0], '<?php print_r($_ENV); ?>');
			fclose($pipes[0]);

			echo stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$return_value = proc_close($process);

			echo "command returned $return_value\n";
		}*/
	}

	public function acGenerateComposerAssetPluginComponentsFiles($args) {
		$arrLibsApps=$args['arrLibsApps'];
		\Sintax\Pages\bower::generateAssetsFiles($arrLibsApps,COMPOSER_ASSET_PLUGIN_PATH,'composerAssetPluginComponents.php','appComposerAssetPluginComponents.php');
	}
}
?>
