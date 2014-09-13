<?
ob_start();
?>
<?
header('Content-Type: text/html; charset=utf-8');

$uniqueId=uniqid("auto.");
//error_log ('----------------------');
//error_log ('/********************/');
//error_log ('LLAMADA A AUTO.PHP: '.$uniqueId);

mb_internal_encoding("UTF-8");
//El UTF-8 para la conexion a la db se establece en el constructor de mysqliDB

date_default_timezone_set('Europe/Madrid');

//No utilizo setlocale, en cuestion de numeros, hace que los floats tengan la , por separador decimal al
//convertirlos a cadena, lo que da problemas al construir sentencias SQL o mandar JSON a JS
//setlocale(LC_ALL,'es_ES');
?>
<?
try {
	$db=new mysqliDB(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
	session_start();

	if (isset($_GET['procesaSiguienteEnvioProgramado'])) {
		procesaSiguienteEnvioProgramado();
	}

	if (isset($_GET['sitemap'])) {
		sitemap();
	}
	//Producto::feedTwenga();
?>
<?
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage()." en fichero ".$e->getFile()." en linea ".$e->getLine();
	mail (DEBUG_EMAIL,SITE_NAME.". ADMIN.AUTO.PHP",
		$infoExc."\n\n--\n\n".$e->getTraceAsString()."\n\n--\n\n".print_r($GLOBALS,true));
}

function procesaSiguienteEnvioProgramado() {
	//Revisamos la tabla envioProgramado, a ver que encontramos en espera
	$idSiguiente=EnvioProgramado::idSiguiente();
	if (envioProgramado::existeId($idSiguiente)) {
		set_time_limit(60*60*12);//12 horas
		$objEnvProg=new EnvioProgramado($idSiguiente);
		//Lo marcamos como procesando
		$objEnvProg->SETestado('PROCESANDO');
		$objEnvProg->grabar();
		//apañamos los datos del correo
		if (Correo::existeId($objEnvProg->GETidCorreo())) {
			$objCorreo=new Correo($objEnvProg->GETidCorreo());
			//apañamos los datos del segmento
			if (ClienteSegmento::existeId($objEnvProg->GETidClienteSegmento())) {
				$objCliSeg=new ClienteSegmento($objEnvProg->GETidClienteSegmento());
				$arrDestinatarios=$objCliSeg->resolve();

				$i=0;
				//realizamos un bucle LENTO por el segmento
				foreach ($arrDestinatarios as $idDestinatario) {
					sleep(1);

					if ($objCliSeg->GETid()!=ID_SEGMENTO_EMAIL_PUBLICO) {
						$objCliDest=new Cliente($idDestinatario);
						$emailTo=$objCliDest->GETemail();
						$emailName=html_entity_decode($objCliDest->GETnombre().' '.$objCliDest->GETapellidos(),ENT_QUOTES,"UTF-8");
						$MsgHTML=$objCorreo->plantillaCorreoPubli($objCliDest->GETid(),NULL,$objEnvProg->GETid());
					} else {
						$objEmailPublico=new EmailPublico($idDestinatario);
						$emailTo=$objEmailPublico->GETemail();
						$emailName=html_entity_decode($objEmailPublico->GETnombre(),ENT_QUOTES,"UTF-8");
						$MsgHTML=$objCorreo->plantillaCorreoPubli(NULL,$objEmailPublico->GETid(),$objEnvProg->GETid());
					}

					//$emailTo="rotsen@osnola.es";
					//$emailTo="noexisteseguro@parqueweb.com";
					//Solo enviamos si aún no hemos lanzado un correo para este envio y este email
					$objEnvProgRsl=new EnvioProgramadoResultados();
					if (!$objEnvProgRsl->cargarPorIdEnvioMasEmail ($objEnvProg->GETid(),$emailTo)) {
						$mail=new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
						$mail->IsSendmail();
						try {
							$mail->CharSet='UTF-8';
							$mail->AddAddress($emailTo, $emailName);
							$mail->SetFrom(FROM_EMAIL, SITE_NAME);
							$mail->Subject=$objCorreo->GETsubject();
							$mail->AltBody='Para visualizar este mensaje es necesario un lector de correo compatible con HTML'; // optional - MsgHTML will create an alternate automatically
							$mail->MsgHTML($MsgHTML);
							$resultado=$mail->Send();

							$objEnvProgRsl=new EnvioProgramadoResultados();
							$objEnvProgRsl->SETdestino($emailTo);
							$objEnvProgRsl->SETresultado( ($resultado)?'ACEPTADO PARA ENTREGA':'NO ACEPTADO' );
							$objEnvProgRsl->SETseguimiento(NULL);
							$objEnvProgRsl->SETidEnvioProgramado($objEnvProg->GETid());
							$objEnvProgRsl->grabar();

						} catch (phpmailerException $e) {
							$objEnvProgRsl=new EnvioProgramadoResultados();
							$objEnvProgRsl->SETdestino($emailTo);
							$objEnvProgRsl->SETresultado(get_class($e).": ".$e->errorMessage());
							$objEnvProgRsl->SETseguimiento(NULL);
							$objEnvProgRsl->SETidEnvioProgramado($objEnvProg->GETid());
							$objEnvProgRsl->grabar();

							//throw new Exception($e->errorMessage());//No lanzamos una nueva excepcion, continuamos
						} catch (Exception $e) {
							$objEnvProgRsl=new EnvioProgramadoResultados();
							$objEnvProgRsl->SETdestino($emailTo);
							$objEnvProgRsl->SETresultado(get_class($e).": ".$e->errorMessage());
							$objEnvProgRsl->SETseguimiento(NULL);
							$objEnvProgRsl->SETidEnvioProgramado($objEnvProg->GETid());
							$objEnvProgRsl->grabar();

							//throw new Exception($e->getMessage());//No lanzamos una nueva excepcion, continuamos
						}
						$objEnvProg->SETenviados($objEnvProg->GETenviados()+1);
						$objEnvProg->grabar();
					}
					//$i++;
					//if ($i>=25) {break;}
				}
			} else {
				throw new Exception("No se encontro el segmento ".$objEnvProg->GETidClienteSegmento().
					" referenciado en el envío ".$objEnvProg->GETid());
			}
		} else {
			throw new Exception("No se encontro el correo ".$objEnvProg->GETidCorreo().
				" referenciado en el envío ".$objEnvProg->GETid());
		}

		//Marcamos el envío como terminado
		$objEnvProg->SETestado('TERMINADO');
		$objEnvProg->grabar();
	}
}
?>