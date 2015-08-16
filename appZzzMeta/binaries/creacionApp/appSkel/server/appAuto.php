<?
define ('ARR_CRON_JOBS', serialize(array(
	'log5Minutes' => array (
		'activado' => false,
		'minuto' => '0/5', //(0 - 59)
		'hora' => '*', //(0 - 23)
		'diaMes' => '*', //(1 - 31)
		'mes' => '*', //(1 - 12)
		'diaSemana' => '*', //(0 - 7) (Domingo=0 o 7)
		'comando' => 'log5Minutes();', //argumento para eval
	),
	'logHour' => array (
		'activado' => true,
		'minuto' => '0',
		'hora' => '*',
		'diaMes' => '*',
		'mes' => '*',
		'diaSemana' => '*',
		'comando' => 'logHour();',
	),
)));
function log5Minutes () {
	error_log('log5Minutes Job');
}
function logHour () {
	error_log('Son las '.date ('H:i:s'));
}
?>
