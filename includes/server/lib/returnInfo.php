<?
function ensureArrayReturnInfo() {
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
function addReturnInfo($msg, $title='') {
	$sri=ensureArrayReturnInfo();
	array_push ($sri,array(
			'msg' => $msg,
			'title' => $title,
	));
	$_SESSION['returnInfo']=$sri;
}
function existeReturnInfo () {
	$sri=ensureArrayReturnInfo();
	return (count($sri)>0) ;
}
function clearReturnInfo () {
	unset($_SESSION['returnInfo']);
}
function liMsgsReturnInfo ($class='sriMsg') {
	$result='';
	$sri=ensureArrayReturnInfo();
	foreach ($sri as $arrInfo) {
		$result.='<li class="'.$class.'">'.$arrInfo['msg'].'</li>';
	}
	return $result;
}
?>