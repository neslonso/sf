<?
namespace Sintax\Tests;
use Sintax\Core\ReturnInfo;

class testTest extends \PHPUnit_Framework_TestCase {
	protected $backupGlobals = FALSE;//PHPUnit hace copia de seguridad de las variables globales y las restaura al acabar cada test, esto es para que no lo haga

	public function setUp() {parent::setUp();}
	public function tearDown() {parent::tearDown();}

	/**
	 * @covers ::ReturnInfo::clear
	 */
	public function testClearReturnInfo () {
		ReturnInfo::clear();
		$this->assertTrue(!isset($_SESSION['returnInfo']));
	}

	/**
	 * @covers ::ReturnInfo::add
	 */
	public function testAddReturnInfo () {
		ReturnInfo::add('mensaje de prueba','Titulo de prueba');
		$this->assertArrayHasKey('returnInfo',$_SESSION);
		$this->assertArrayHasKey('title',$_SESSION['returnInfo'][0]);
		$this->assertArrayHasKey('msg',$_SESSION['returnInfo'][0]);
		return $_SESSION['returnInfo'];
	}

	/**
	 * @covers ::ReturnInfo::msgsToLis
	 * @depends testAddReturnInfo
	 */
	public function testLiMsgsReturnInfo ($returnInfo) {
		$result=ReturnInfo::msgsToLis();
		$this->assertRegExp('/.*mensaje de prueba.*/',$result,'No se encontró la cadena añadida en "testAddReturnInfo"');
	}

	/**
	 * @covers Sintax\Pages\crudPrueba::acGrabar
	 * @dataProvider crudPruebaAcGrabarDataProvider
	 */
	public function NOtestAcGrabar ($id) {
		$this->objCrudPrueba=new \Sintax\Pages\crudPrueba($_SESSION['usuario']);
		$_POST['id']=$id;
		$this->assertTrue($this->objCrudPrueba->acGrabar());
	}
	public function crudPruebaAcGrabarDataProvider () {
		return array (
			array ("a"),
			array ("b"),
			array (999999),
		);
	}
}
?>