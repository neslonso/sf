<?
namespace Sintax\Core;

class ReturnInfo {
	public static function ensureArray() {
		if (!isset($_SESSION['returnInfo'])) {
			$_SESSION['returnInfo']=$sri=array();
		} else {
			if (!is_array($_SESSION['returnInfo'])) {
				$sri=array();
				$sri[0]=$_SESSION['returnInfo'];
				$_SESSION['returnInfo']=$sri;
			} else {
				$sri=$_SESSION['returnInfo'];
			}
		}
		return $sri;
	}

	public static function add($msg, $title='') {
		$sri=self::ensureArray();
		array_push ($sri,array(
				'msg' => $msg,
				'title' => $title,
		));
		$_SESSION['returnInfo']=$sri;
	}
	public static function existe () {
		$sri=self::ensureArray();
		return (count($sri)>0) ;
	}
	public static function clear () {
		unset($_SESSION['returnInfo']);
	}
	public static function msgsToLis ($class='sriMsg') {
		$result='';
		$sri=self::ensureArray();
		foreach ($sri as $arrInfo) {
			$result.='<li class="'.$class.'">'.$arrInfo['msg'].'</li>';
		}
		return $result;
	}
}
?>