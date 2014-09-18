<?
ob_start();
?>
<?
$uniqueId=uniqid("auto.");
//error_log ('----------------------');
//error_log ('/********************/');
error_log ('LLAMADA A AUTO.PHP: '.$uniqueId);
?>
<?
try {
	$shellCmd='crontab -l | grep '.BASE_URL.FILE_APP.'?MODULE=auto';
	//echo $shellCmd;
	$jobSearch = shell_exec($shellCmd);
	if ($jobSearch=='') {
		$job='*/5 * * * * curl '.BASE_URL.FILE_APP.'?MODULE=auto &>/dev/null'.PHP_EOL;
		$output = shell_exec('crontab -l');
		$tmpFile=TMP_UPLOAD_DIR.'crontab.txt';
		file_put_contents($tmpFile, $output.PHP_EOL.$job.PHP_EOL);
		echo exec('crontab '.$tmpFile);
		//unlink ($tmpFile);
	} else {
		echo "cronjob found";
		echo "<pre>".$jobSearch."</pre>";
	}

	foreach (unserialize(ARR_CRON_JOBS) as $nombreJob => $arrDatosJob) {
		if ($arrDatosJob['activado']) {

		}
	}

	if (isset($_GET['sitemap'])) {
		sitemap();
	}
?>
<?
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	mail (DEBUG_EMAIL,SITE_NAME.". AUTO.PHP",
		$infoExc."\n\n--\n\n".$e->getTraceAsString()."\n\n--\n\n".print_r($GLOBALS,true));
}
?>