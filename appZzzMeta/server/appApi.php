<?
define ('ARR_API_SERVICES', serialize(array(
	'LOG' => array (
		'active' => true,
		'keys' => array(
			'neslonso@gmail.com' => '' //Nombre de la key (sin uso por el momento) => valor que tiene que llegar en $_REQUEST['key']
			),
		'comando' => 'error_log("LOG API.");', //argumento para eval
	),
	'TPVV_SERMEPA' => array (
		'active' => false,
		'comando' => 'TPVV_SERMEPA();', //argumento para eval
	),
	'TPVV_CECA' => array (
		'active' => false,
		'comando' => 'TPVV_CECA();',
	),
	'MO_SMS_LLEIDA' => array (
		'active' => false,
		'comando' => 'MO_SMS_LLEIDA();',
	),
	'M_SEG' => array (
		'active' => false,
		'comando' => 'M_SEG();',
	),
)));


function TPVV_SERMEPA () {
	//mail (DEBUG_EMAIL,SITE_NAME.". SERMEPA.PHP: ".$_SERVER['REMOTE_ADDR'],print_r($GLOBALS,true));
	if (isset($_POST['Ds_Order'])) {
		//Actualizamos TPVV_SERMEPA
		$objTPVV_SERMEPA=new TPVV_SERMEPA();
		$objTPVV_SERMEPA->cargarArray($_POST);
		$objTPVV_SERMEPA->grabar();

		//Actualizamos el Pedido
		$idPedido=(explode(".",$_POST['Ds_Order']));$idPedido=(int)$idPedido[0];
		if (Pedido::existeId($idPedido)) {
			$objPed=new Pedido($idPedido);
			$newEstado=($_POST['Ds_Response']=="0000")?6:7;
			/*$objPed->SETidPedidoEstado($newEstado);
			$objPed->grabar();*/
			$objPed->cambiarEstado($newEstado);
		}
	}
}

function TPVV_CECA() {
	/* POST OK
	[MerchantID] => 085491272
	[AcquirerBIN] => 0000554027
	[TerminalID] => 00000003
	[Num_operacion] => 0013.01s5
	[Importe] => 000000053953
	[TipoMoneda] => 978
	[Exponente] => 2
	[Referencia] => 120010761912110103522606007000
	[Firma] => 8b8fa3f7d3fdfcbe7279190809f03f8e299dae57
	[Codigo_pedido] => 0013.01s5
	[Codigo_cliente] =>
	[Codigo_comercio] =>
	[Num_aut] => 101000
	[BIN] => 554050
	[Cambio_moneda] => 1.000000
	[Idioma] => 1
	[Pais] => 724
	[Tipo_tarjeta] => C
	[Descripcion] =>
	*/

	//mail (DEBUG_EMAIL,SITE_NAME.". CECA.PHP: ".$_SERVER['REMOTE_ADDR'],print_r($GLOBALS,true));
	if (isset($_POST['Num_operacion'])) {
		$objTPVV_CECA=new TPVV_CECA();
		$objTPVV_CECA->cargarArray($_POST);
		$objTPVV_CECA->grabar();

		//Actualizamos el Pedido
		$idPedido=(explode(".",$_POST['Num_operacion']));$idPedido=(int)$idPedido[0];
		if (Pedido::existeId($idPedido)) {
			$objPed=new Pedido($idPedido);
			//El maldito TPV de la Ceca solo llama a la notificacion en pedidos correctos
			//$newEstado=($_POST['']=="0000")?6:7;
			$newEstado=6;
			/*$objPed->SETidPedidoEstado($newEstado);
			$objPed->grabar();*/
			$objPed->cambiarEstado($newEstado);
		}
	}
}
function MO_SMS_LLEIDA() {
	mail (DEBUG_EMAIL,SITE_NAME.". API.PHP MO_SMS_LLEIDA: ".$_SERVER['REMOTE_ADDR'],
		var_export($_POST,true)
		."\n\n--\n\n".
		print_r($GLOBALS,true));

	$objSMS=new SMS();
	$objSMS->SETdestino($_POST['destino']);
	$objSMS->SETfecha($_POST['fecha']);
	$objSMS->SETidmo($_POST['idmo']);
	$objSMS->SETorigen($_POST['origen']);
	$objSMS->SETtexto(utf8_encode($_POST['texto']));
	$objSMS->grabar();

	//grabamos un aviso
	$objAviso=new AdministradorAviso();
	$objAviso->SETimagen(NULL);
	$objAviso->SETtexto($_POST['texto']);
	$objAviso->SETtitulo('Nuevo SMS recibido de '.$_POST['origen']);
	$objAviso->SETurl(BASE_DIR."admin.php"."?page=lsSMS");
	$objAviso->SETvisto(0);
	//$objAviso->SETidAdministrador();
	$objAviso->grabarParaTodos();

	//comprobamos si el SMS viene del movil de un cliente y si figura el número de algun pedido de ese cliente
	//que este pendiente de confirmar.
	if (substr($_POST['origen'],0,3)==" 34") {
		$arrIdsCli=Cliente::AlltoArray("movil='".substr($_POST['origen'],3)."'","","","arrIds");
		foreach ($arrIdsCli as $idCli) {
			if (Cliente::existeId($idCli)) {
				$objCli=new Cliente($idCli);
				//TODO: imprescindible: estamos usando el id de estado para pendiente de confirmar contrareembolso,
				//esto tendria que cambiar y haber un campo que representase que esta situación
				$arrIdsPed=$objCli->arrPeds("idPedidoEstado='".(9)."'","","","arrIds");
				foreach ($arrIdsPed as $idPed) {
					if (Pedido::existeId($idPed)) {
						$objPed=new Pedido($idPed);
						if (strstr($_POST['texto'],$objPed->GETnumero())) {
							$objPed->cambiarEstado(10);
						}
					}
				}
			}
		}
	}
}

function M_SEG() {
	$file='./binaries/imgs/lib/spacer.gif';
	$objImg=Imagen::fromFile($file);
	ob_clean();//limpiamos el buffer antes de mandar la imagen, no queremos nada más que la imagen

	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 60*60*24*364));

	$ancho=NULL;
	$alto=NULL;
	$modo=Imagen::OUTPUT_MODE_SCALE;
	$formato="gif";
	$objImg->output($ancho,$alto,$modo,$formato);

	$email=$_GET['email'];
	$token=$_GET['token'];
	$idEnvio=$_GET['id'];
	if ($token==md5('M_SEG'.$email)) {//el token coincide con el email/
		//Actualizamos la ultimaActividad del cliente O el campo ultimoCorreo del emailPublico
		//Y el campo seguimiento del resultado del envío
		//error_log("api token=email: ".$email);
		$objCli=new Cliente();
		if ($objCli->cargarPorEmail($email)) {
			$objCli->SETultimaActividad(date('YmdHis'));
			$objCli->grabar();
		}
		$objEmailPublico=new EmailPublico();
		if ($objEmailPublico->cargarPorEmail($email)) {
			$objEmailPublico->SETultimoCorreo(date('YmdHis'));
			$objEmailPublico->grabar();
		}
		if ($idEnvio!=0) {
			if (EnvioProgramado::existeId($idEnvio)) {
				$objEnvProg=new EnvioProgramado($idEnvio);
				$objEnvProgRsl=new EnvioProgramadoResultados();
				if ($objEnvProgRsl->cargarPorIdEnvioMasEmail ($idEnvio,$email)) {
					$objEnvProgRsl->SETseguimiento(date('YmdHis'));
					$objEnvProgRsl->grabar();
				}
			} else {
				throw new ApiException("idEnvio (".$idEnvio.") no encontrado");
			}
		}
	}
}
?>