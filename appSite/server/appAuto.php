<?
define ('ARR_CRON_JOBS', serialize(array(
	'nombreJob' => array (
		'activado' => true,
		'minuto' => '', //(0 - 59)
		'hora' => '', //(0 - 23)
		'diaMes' => '', //(1 - 31)
		'mes' => '', //(1 - 12)
		'diaSemana' => '', //(0 - 7) (Domingo=0 o 7)
		'comando' => '',
	),
));
?>