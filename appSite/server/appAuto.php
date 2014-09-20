<?
define ('ARR_CRON_JOBS', serialize(array(
	'logEachMinute' => array (
		'activado' => false,
		'minuto' => '*', //(0 - 59)
		'hora' => '*', //(0 - 23)
		'diaMes' => '*', //(1 - 31)
		'mes' => '*', //(1 - 12)
		'diaSemana' => '*', //(0 - 7) (Domingo=0 o 7)
		'comando' => 'error_log("logEachMinute Job");', //argumento para eval
	),
	'log5Minutes' => array (
		'activado' => false,
		'minuto' => '0/5', //(0 - 59)
		'hora' => '*', //(0 - 23)
		'diaMes' => '*', //(1 - 31)
		'mes' => '*', //(1 - 12)
		'diaSemana' => '*', //(0 - 7) (Domingo=0 o 7)
		'comando' => 'error_log("log5Minutes Job");', //argumento para eval
	),
	'sitemap' => array (
		'activado' => false,
		'minuto' => '20', //(0 - 59)
		'hora' => '*', //(0 - 23)
		'diaMes' => '*', //(1 - 31)
		'mes' => '*', //(1 - 12)
		'diaSemana' => '*', //(0 - 7) (Domingo=0 o 7)
		'comando' => 'error_log("sitemap job");sitemap();', //argumento para eval
	),
)));
?>