<?
ob_start();
?>
<?
# error reporting
//ini_set('display_errors',1);
//error_reporting(E_ALL|E_STRICT);

header('Content-Type: text/html; charset=utf-8');
session_start();

$test=(isset($_GET['test']))?$_GET['test']:'';
if ($test=='') {throw new Exception("Clase de test no especificada.", 1);}
$test='Sintax\\Tests\\'.$test;

$objUsr=new Sintax\Core\AnonymousUser();
if (isset($_SESSION['usuario'])) {
	//$objUsr=$_SESSION['usuario'];
	$usrClass=get_class($_SESSION['usuario']);
	if ($usrClass!="__PHP_Incomplete_Class") {
		$objUsr=$_SESSION['usuario'];
	}
}

if (class_exists($test)) {
	define ('PHPUNIT_TESTSUITE',1);//Para que PHPUnit no nos cierre los buffers
	/*//Si tenemos runkit, podemos alterar la funcion blacklist para que no tenga en cuetna la constante anterior
	runkit_method_rename('PHPUnit_Util_Blacklist','isBlacklisted','isBlacklisted_Original');
	runkit_method_add('PHPUnit_Util_Blacklist','isBlacklisted','$file','
		runkit_constant_remove("PHPUNIT_TESTSUITE");
		$result=$this->isBlacklisted_Original($file);
		runkit_constant_add("PHPUNIT_TESTSUITE",1);
		return $result;
	',RUNKIT_ACC_PUBLIC);
	*/
	$suite = new PHPUnit_Framework_TestSuite($test);
	$arguments=array();
	$arguments['coverageHtml']=1; //<- Necesita Xdebug, el php de nx614 no lo tiene.

	$arguments['verbose']=true;
	$arguments['debug']=true;

	//$arguments['testdoxHTMLFile']=TMP_UPLOAD_DIR.'phpunit.testdoxHTMLFile.html';
	//$arguments['testdoxTextFile']=TMP_UPLOAD_DIR.'phpunit.testdoxHTMLFile.txt';
	$buffer='';
	ob_start();
		PHPUnit_TextUI_TestRunner::run($suite,$arguments);
	$buffer=ob_get_clean();

	$lines = explode("\n", $buffer);
	$include = array();
	foreach ($lines as $line) {
		if (strpos($line, 'phar:') !== FALSE) {
			continue;
		}
		$include[] = $line;
	}
	$buffer=implode("\n", $include);
	echo "<h1>Ejecutando PHPUnit para el testCase: <code>".$test."</code></h1>";
	echo "<pre>".$buffer."</pre>";
} else {
	throw new Exception("No se encontró la clase de test: ".$test, 1);
}
?>