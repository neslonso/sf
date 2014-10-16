<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class composer extends Home implements IPage {
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
		/*
		register_shutdown_function(function () {
			echo "<pre>";
			var_dump($GLOBALS['dbg_stack']);
			echo "</pre>";
		});
		register_tick_function(function () {
			$GLOBALS['dbg_stack'] = debug_backtrace(0);
		});
		declare(ticks=1);
		// Composer\Factory::getHomeDir() method
		// needs COMPOSER_HOME environment variable set
		error_log('COMPOSER_HOME=' . SKEL_ROOT_DIR);
		putenv('COMPOSER_HOME=' . SKEL_ROOT_DIR);

		//$tmpFile = tmpfile();
		$tmpFile = fopen('./composer.output.txt','w+');
		$output  = new StreamOutput($tmpFile);
		// call `composer install` command programmatically
		$input = new ArrayInput(array('command' => 'install'));
		$application = new Application();
		$application->setAutoExit(false); // prevent `$application->run` method from exitting the script
		$application->setCatchExceptions(false);
		error_log('0');
		$application->run($input,$output);
		error_log('************************************************************************************1');

		fseek($tmpFile, 0);
		$output = fread($tmpFile, 1024);
		fclose($tmpFile);
		echo $output;

		//Probar esta salida
		//$output = new BufferedOutput();
		//$input = new ArrayInput($arguments);
		//$code = $command->run($input, $output);
		////RUN
		//if($code == 0) {
		//	$outputText = $output->fetch();
		//	echo $outputText;
		//}
		*/

		/*
		echo "<pre>";
		//echo shell_exec('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar');
		echo shell_exec('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar install --dry-run');
		//echo shell_exec('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar install --dry-run --optimize-autoloader');
		echo "</pre>";
		*/
		putenv('COMPOSER_HOME=' . SKEL_ROOT_DIR);
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
		);
		//$cwd = '/tmp';
		//$env = array('some_option' => 'aeiou');
		//$process = proc_open('php', $descriptorspec, $pipes, $cwd, $env);
		//$process = proc_open('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar', $descriptorspec, $pipes);
		//$process = proc_open('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar install --dry-run', $descriptorspec, $pipes);
		//$process = proc_open('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar install', $descriptorspec, $pipes);
		$process = proc_open('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar install --optimize-autoloader', $descriptorspec, $pipes);
		//$process = proc_open('php '.SKEL_ROOT_DIR.'includes/server/vendor/composer.phar update', $descriptorspec, $pipes);
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		echo "<h3>STDOUT</h3>";
		echo "<pre>".$stdout."</pre>";
		echo "<h3>STDERR</h3>";
		echo "<pre>".$stderr."</pre>";


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
		//require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}
}
?>
