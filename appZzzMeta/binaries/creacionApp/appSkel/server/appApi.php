<?
define ('ARR_API_SERVICES', serialize(array(
	'LOG' => array (
		'active' => true,
		'keys' => array(
			'neslonso@gmail.com' => '' //Nombre de la key (sin uso por el momento) => valor que tiene que llegar en $_REQUEST['key']
			),
		'comando' => 'LOG()', //argumento para eval
	),
)));
function LOG () {
	error_log('LOG API.');
}
?>
