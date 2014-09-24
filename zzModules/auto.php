<?
ob_start();
?>
<?
$uniqueId=uniqid("auto.");
//error_log ('----------------------');
//error_log ('/********************/');
//error_log ('LLAMADA A AUTO.PHP: '.$uniqueId);
?>
<?
try {
	$shellCmd='crontab -l | grep '.BASE_URL.FILE_APP.'?MODULE=auto';
	//echo $shellCmd;
	$jobSearch = shell_exec($shellCmd);
	if ($jobSearch=='') {
		$job='* * * * * curl '.BASE_URL.FILE_APP.'?MODULE=auto &>/dev/null'.PHP_EOL;
		$output = shell_exec('crontab -l');
		$tmpFile=TMP_UPLOAD_DIR.'crontab.txt';
		file_put_contents($tmpFile, $output.PHP_EOL.$job.PHP_EOL);
		echo exec('crontab '.$tmpFile);
		//unlink ($tmpFile);
		echo "No cronjob found for APP ".FILE_APP;
		echo "\n<br />\n";
		echo "Added job: ".$job;
	}

	//TODO: Mejora: Quiza habría que comprobar de algun mod si la llamada procede del cron (quiza mediante useragent curl),
	//para evitar que se repitan tareas si se llama a auto.
	if (defined('ARR_CRON_JOBS')) {
		foreach (unserialize(ARR_CRON_JOBS) as $nombreJob => $arrDatosJob) {
			if ($arrDatosJob['activado']) {
				$cronExpr=$arrDatosJob['minuto'].' '.
					$arrDatosJob['hora'].' '.
					$arrDatosJob['diaMes'].' '.
					$arrDatosJob['mes'].' '.
					$arrDatosJob['diaSemana'];
				$cron=Cron\CronExpression::factory($cronExpr);
				if ($cron->isDue()) {
					eval($arrDatosJob['comando']);
				}
			}
		}
	}
?>
<?
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	mail (DEBUG_EMAIL,SITE_NAME.". AUTO.PHP",
		$infoExc."\n\n--\n\n".$e->getTraceAsString()."\n\n--\n\n".print_r($GLOBALS,true));
}
?>