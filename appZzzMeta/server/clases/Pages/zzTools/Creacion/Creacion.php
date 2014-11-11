<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;
use Sintax\Core\ReturnInfo;

class Creacion extends Error implements IPage {
	public function __construct(User $objUsr) {
		parent::__construct($objUsr);
	}
	public function pageValida () {
		return $this->objUsr->pagePermitida($this);
		//return parent::pageValida();
	}
	public function accionValida($metodo) {
		//return $this->objUsr->accionPermitida($this,$metodo);
		switch ($metodo) {
			case "acCrearAppSkel": $result=true;break;
			case "acCrearPagina": $result=true;break;
			case "CrearClases": $result=true;break;
			default: $result=false;
		}
		return $result;
	}

	public function title() {
		return parent::title();
	}

	public function favIcon() {
		$favIcon='';
		$favIcon.='<link rel="shortcut icon" type="image/x-icon" href="./binaries/imgs/tools.favicon.ico" />';
		$favIcon.=PHP_EOL;
		$favIcon.='<link rel="icon" type="image/x-icon" href="./binaries/imgs/tools.favicon.ico" />';
		return $favIcon."\n";
	}

	public function head() {
		parent::head();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/head.php");
	}
	public function js() {
		parent::js();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/js.php");
	}
	public function css() {
		parent::css();
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/css.php");
	}
	public function markup() {
		\cDb::conf(_DB_HOST_,_DB_USER_,_DB_PASSWD_,_DB_NAME_);
		$mysqli=\cDb::getInstance();
		$arrStdObjTableInfo=array();
		if ($result = $mysqli->query("show full tables where Table_Type = 'BASE TABLE'")) {
			while ($table = $result->fetch_array()) {
				$stdObjTableInfo=$this->getTableInfo($table[0]);
				array_push($arrStdObjTableInfo,$stdObjTableInfo);
				unset($stdObjTableInfo);
			}
		} else {
			echo $mysqli->error;
		}
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/markup.php");
	}

	public function acCrearAppSkel($args) {
		$file=SKEL_ROOT_DIR.$args['fileApp'];
		$path=SKEL_ROOT_DIR.$args['rutaApp'];
		$relPathFileDirToSkel=\Filesystem::find_relative_path(dirname($file),SKEL_ROOT_DIR).'/';
		if (file_exists($file)) {
			throw new ActionException('El punto de entrada "'.realpath($file).'" ya existe.', 1);
		}
		if (file_exists($path)) {
			throw new ActionException('La carpeta de la app "'.realpath($path).'" ya existe.', 1);
		}

		$mode=0755;
		mkdir($path,$mode,true);
		if (!is_dir($path)) {
			throw new ActionException('No se pudo crear "'.$path.'"', 1);
		}
		$htaccessDest=dirname($file)."/.htaccess";
		if (!file_exists($file)) {
			if (!file_exists(dirname($file))) {
				mkdir(dirname($file),$mode,true);
			}
			copy(APP_IMGS_DIR.'../creacionApp/fileApp.php', $file);
		} else {
			throw new Exception("El punto de entrada '".$file."' ya existe", 1);
		}
		if (!file_exists($htaccessDest)) {
			copy(APP_IMGS_DIR.'../creacionApp/.htaccess', $htaccessDest);
		} else {
			ReturnInfo::add("No se ha realizado la copia de htaccess a '".$htaccessDest."'","htaccess ya existe");
		}

		try {
			\Filesystem::copyDir(APP_IMGS_DIR.'../creacionApp/appSkel/',$path);
		} catch (Exception $e) {
			throw new Exception('Error durante copia de appSkel a "'.$path.'"', 1,$e);
		}
		ReturnInfo::add("
		<ul>
			<li>SKEL_ROOT_DIR: ".SKEL_ROOT_DIR."</li>
			<li>File: ".$file."</li>
			<li>Ruta: ".$path."</li>
			<li>.htaccess: ".$htaccessDest."</li>
			<li>Definición de APP:
<pre>
'".basename($file)."' => array(
	'FILE_APP' => '".basename($file)."',
	'RUTA_APP' => SKEL_ROOT_DIR.'".str_replace(SKEL_ROOT_DIR,'',$path)."/',
	'NOMBRE_APP' => 'Sitio web',
),
</pre>
			</li>
			<li>Definición SKEL_ROOT_DIR (debe ir en ".str_replace($_SERVER['DOCUMENT_ROOT'],'',$file)."):
<pre>
define ('SKEL_ROOT_DIR',realpath(__DIR__.'/'.'<em style='color:red;'>".$relPathFileDirToSkel."</em>').'/');
</pre>
			</li>
			<li>Definición RewriteBase (.htacces): RewriteBase ".str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($file))."</li>
			<li>Cambios en RewriteRule (.htacces):
<pre>
#Si la url tenía idNumerica
RewriteRule ^([^/]*)/(.*)/([0-9]+)/(.*)/ $4 [L] -> RewriteRule ^([^/]*)/(.*)/([0-9]+)/(.*)/ <em style='color:red;'>".$relPathFileDirToSkel."</em>$4 [L]
#Si no tenía id númerica
RewriteRule ^([^/]*)/(.*)/$ $2 [L] -> RewriteRule ^([^/]*)/(.*)/$ <em style='color:red;'>".$relPathFileDirToSkel."</em>$2 [L]
</pre>
			</li>
		</ul>",
		'Skeleto creado con exito');
	}

	public function acCrearPagina() {
		$ruta=SKEL_ROOT_DIR.$_POST['ruta'];
		$page=$_POST['page'];
		$extends=$_POST['extends'];
		$markupFunc=$_POST['markupFunc'];
		$markupFile=$_POST['markupFile'];
		$class=$_POST['class'];
		$arrExcluidos=array();
		$arrValidators=array();
		$pageType="";
		if ($class!="") {
			if (isset($_REQUEST[$class."_excluir"])) {
				$arrExcluidos=$_REQUEST[$class."_excluir"];
			}
			if (isset($_REQUEST[$class."_validators"])) {
				$arrValidators=$_REQUEST[$class."_validators"];
			}
			$pageType=$_REQUEST["pageType"];
		}

		if ($class!="") {
			$this->CrearClase($class);
		}

		//Inicio carpetas de la page
		if (!file_exists($ruta.$page)) {
			mkdir($ruta.$page, 0755, true);
			mkdir($ruta.$page.'/markup', 0755, true);
		}
		//Fin carpetas de la page
		$sl="\n";
		$sg="\t";
		//Inicio css.php
		$file=$ruta.$page."/markup/css.php";
		$fp=fopen ($file,"w");
		fwrite ($fp,'<?if (false) {?><style><?}?>'.$sl);
		fwrite ($fp,'<?="\n/*".get_class()."*/\n"?>'.$sl);
		if ($class!="") {
			$stdObjTableInfo=$this->getTableInfo($class,$arrExcluidos);
			if (BOOTSTRAP!=false) {
				switch ($pageType) {
					case 'CRUD':
						$this->cssCrud($fp,$class);
					break;
					case 'DBdataTable':
						$this->cssDBdataTable($fp,$class);
					break;
				}
			}
		}
		fclose ($fp);
		chmod ($file,0777);
		//Fincss.php
		//Inicio head.php
		$file=$ruta.$page."/markup/head.php";
		$fp=fopen ($file,"w");
		fwrite ($fp,'<?="\n<!-- ".get_class()." -->\n"?>'.$sl);
		fwrite ($fp,'<?="\n<!-- /".get_class()." -->\n"?>'.$sl);
		fclose ($fp);
		chmod ($file,0777);
		//Fin head.php
		//Inicio js.php
		$file=$ruta.$page."/markup/js.php";
		$fp=fopen ($file,"w");
		fwrite ($fp,'<?if (false) {?><script><?}?>'.$sl);
		fwrite ($fp,'<?="\n/*".get_class()."*/\n"?>'.$sl);
		if ($class!="") {
			if (BOOTSTRAP!=false) {
				switch ($pageType) {
					case 'CRUD':
						$this->jsCrud($fp,$page,$class,$stdObjTableInfo,$arrValidators);
					break;
					case 'DBdataTable':
						$this->jsDBdataTable($fp,$page,$class,$stdObjTableInfo,$arrValidators);
					break;
				}
			}
		}
		fclose ($fp);
		chmod ($file,0777);
		//Fin js.php
		//Inicio $markupFunc.php (Fichero de markup)
		$file=$ruta.$page."/markup/".$markupFunc.".php";
		$fp=fopen ($file,"w");
		fwrite ($fp,'<?="\n<!-- ".get_class()." -->\n"?>'.$sl);
		if ($markupFile!="" && file_exists($markupFile)) {
			fwrite($fp, file_get_contents($markupFile));
		}
		if ($class!="") {
			switch ($pageType) {
				case 'CRUD':
					$this->markupCrud($fp,$page,$class,$stdObjTableInfo,$arrValidators);
				break;
				case 'DBdataTable':
					$this->markupDBdataTable($fp,$page,$class,$stdObjTableInfo);
				break;
			}
		}
		fwrite ($fp,'<?="\n<!-- /".get_class()." -->\n"?>'.$sl);
		fclose ($fp);
		chmod ($file,0777);
		//Fin $markupFunc.php (Fichero de markup)
		//Inicio fichero de la clase
		$file=$ruta.$page."/".$page.".php";
		$fp=fopen ($file,"w");
		fwrite ($fp,"<?".$sl);
		fwrite ($fp,'namespace Sintax\Pages;'.$sl);
		fwrite ($fp,'use Sintax\Core\IPage;'.$sl);
		fwrite ($fp,'use Sintax\Core\User;'.$sl);
		fwrite ($fp,'use Sintax\Core\ReturnInfo;'.$sl);

		fwrite ($fp,"class ".$page." extends ".$extends." implements IPage {".$sl);
			//Inicio __construct
		fwrite ($fp,$sg.'public function __construct(User $objUsr) {'.$sl);
		fwrite ($fp,$sg.$sg.'parent::__construct($objUsr);'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin __construct
			//Inicio pageValida
		fwrite ($fp,$sg.'public function pageValida () {'.$sl);
		fwrite ($fp,$sg.$sg.'return $this->objUsr->pagePermitida($this);'.$sl);
		//fwrite ($fp,$sg.$sg.'return parent::pageValida();'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin pageValida
			//Inicio accionValida
		fwrite ($fp,$sg.'public function accionValida($metodo) {'.$sl);
		fwrite ($fp,$sg.$sg.'return $this->objUsr->accionPermitida($this,$metodo);'.$sl);
		/*
		fwrite ($fp,$sg.$sg.'switch ($metodo) {'.$sl);
		if ($class!="") {
			switch ($pageType) {
				case 'CRUD':
					fwrite ($fp,$sg.$sg.$sg.'case "'.'acGrabar'.'": $result=true;break;'.$sl);
					fwrite ($fp,$sg.$sg.$sg.'case "'.'acBorrar'.'": $result=true;break;'.$sl);
				break;
				case 'DBdataTable':
					fwrite ($fp,$sg.$sg.$sg.'case "'.'acDataTables'.'": $result=true;break;'.$sl);
				break;
			}
		}
		fwrite ($fp,$sg.$sg.$sg.'default: $result=false;'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.'return $result;'.$sl);
		*/
		fwrite ($fp,$sg.'}'.$sl);
			//Fin accionValida
			//Inicio title
		fwrite ($fp,$sg.'public function title() {'.$sl);
		fwrite ($fp,$sg.$sg.'return parent::title();'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin title
			//Inicio metaTags
		fwrite ($fp,$sg.'public function metaTags() {'.$sl);
		fwrite ($fp,$sg.$sg.'return parent::metaTags();'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin metaTags
			//Inicio head
		fwrite ($fp,$sg.'public function head() {'.$sl);
		fwrite ($fp,$sg.$sg.'parent::head();'.$sl);
		fwrite ($fp,$sg.$sg.'require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/head.php");'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin head
			//Inicio js
		fwrite ($fp,$sg.'public function js() {'.$sl);
		fwrite ($fp,$sg.$sg.'parent::js();'.$sl);
		fwrite ($fp,$sg.$sg.'require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/js.php");'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin js
			//Inicio css
		fwrite ($fp,$sg.'public function css() {'.$sl);
		fwrite ($fp,$sg.$sg.'parent::css();'.$sl);
		fwrite ($fp,$sg.$sg.'require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/css.php");'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin css
			//Inicio markupFunc
		fwrite ($fp,$sg.'public function '.$markupFunc.'() {'.$sl);
		if ($class!="") {
			switch ($pageType) {
				case 'CRUD':
					$this->markupFuncCrud($fp,$class,$stdObjTableInfo);
				break;
				case 'DBdataTable':
				break;
			}
		}
		fwrite ($fp,$sg.$sg.'require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/'.$markupFunc.'.php");'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin markupFunc
		if ($class!="") {
			switch ($pageType) {
				case 'CRUD':
					$this->actionFuncsCrud($fp,$class,$stdObjTableInfo);
				break;
				case 'DBdataTable':
					$this->actionFuncsDBdataTable($fp,$class,$stdObjTableInfo);
				break;
			}
		}
		//Fin fichero de la clase
		fwrite ($fp,"}".$sl);
		//Llave de cierre de la clase
		fwrite ($fp,"?>".$sl);
		fclose ($fp);
		chmod ($file,0777);
	}

	public function CrearClases() {
		unset ($_REQUEST["APP"]);
		unset ($_REQUEST["acClase"]);
		unset ($_REQUEST["acMetodo"]);
		unset ($_REQUEST["acTipo"]);
		unset ($_REQUEST["acReturnURI"]);
		foreach ($_REQUEST as $tableName => $uno) {
			$this->CrearClase($tableName);
		}
	}

/* CRUD creation functions ****************************************************/
	private function cssCrud($fp,$class) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,'#frm'.ucfirst($class).' .input-group+.form-control-feedback {'.$sl);
		fwrite ($fp,$sg.'top: 25px !important;'.$sl);
		fwrite ($fp,'}'.$sl);
	}

	private function jsCrud($fp,$page,$class,$stdObjTableInfo,$arrValidators) {
				$sl="\n";
				$sg="\t";

				fwrite ($fp,'$(document).ready(function() {'.$sl);
				fwrite ($fp,$sg.'$(".input-group.date").datepicker({'.$sl);
				fwrite ($fp,$sg.$sg.'format: "dd/mm/yyyy",'.$sl);
				fwrite ($fp,$sg.$sg.'weekStart: 1,'.$sl);
				fwrite ($fp,$sg.$sg.'language: "es"'.$sl);
				fwrite ($fp,$sg.'});'.$sl);
				fwrite ($fp,$sg.'$(".input-group.date").on("changeDate", function(ev){'.$sl);
				fwrite ($fp,$sg.$sg.'$(this).datepicker("hide");'.$sl);
				fwrite ($fp,$sg.'});'.$sl);
				fwrite ($fp,$sl);

				fwrite ($fp,$sg.'$("#btnAcBorrar").click(function() {'.$sl);
				fwrite ($fp,$sg.$sg.'Post("MODULE","actions","acClase","'.$page.'","acMetodo","acBorrar","acTipo","stdAssoc",
					"id",$(this).data("id"));'.$sl);
				fwrite ($fp,$sg.'});'.$sl);

				fwrite ($fp,$sg.'$("#frm'.ucfirst($class).'").bootstrapValidator({'.$sl);
				fwrite ($fp,$sg.$sg.'// http://bootstrapvalidator.com/settings/'.$sl);
				fwrite ($fp,$sg.$sg.'//container: CSS selector | tooltip | popover'.$sl);
				fwrite ($fp,$sg.$sg.'//excluded: [":disabled", ":hidden", ":not(:visible)"],'.$sl);
				fwrite ($fp,$sg.$sg.'// To use feedback icons, ensure that you use Bootstrap v3.1.0 or later'.$sl);
				fwrite ($fp,$sg.$sg.'feedbackIcons: {'.$sl);
				fwrite ($fp,$sg.$sg.$sg.'valid: "glyphicon glyphicon-ok",'.$sl);
				fwrite ($fp,$sg.$sg.$sg.'invalid: "glyphicon glyphicon-remove",'.$sl);
				fwrite ($fp,$sg.$sg.$sg.'validating: "glyphicon glyphicon-refresh"'.$sl);
				fwrite ($fp,$sg.$sg.'},'.$sl);
				fwrite ($fp,$sg.$sg.'//group: ".form-group",'.$sl);
				fwrite ($fp,$sg.$sg.'//live: "enabled",'.$sl);
				fwrite ($fp,$sg.$sg.'//submitButtons: \'button[type="submit"]\','.$sl);
				fwrite ($fp,$sg.$sg.'//threshold: null,'.$sl);
				fwrite ($fp,$sg.$sg.'//trigger: null,'.$sl);
				fwrite ($fp,$sg.$sg.'fields: {'.$sl);
				$code='';
				$code.=$sg.$sg.$sg.'//Posibles parametros dentro de cada field: container, enabled, excluded, feedbackIcons, group, message, selector, threshold, trigger'.$sl;
				foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
					$codeIdentical="";
					if ($stdObjFieldInfo->key!="PRI") {
						if (isset($arrValidators[$field]) && $arrValidators[$field][0]!='') {
							$code.=$sg.$sg.$sg.$field.': {'.$sl;
							$code.=$sg.$sg.$sg.$sg.'message: "'.$field.' no válido",'.$sl;
							$code.=$sg.$sg.$sg.$sg.'validators: {'.$sl;
							foreach ($arrValidators[$field] as $validatorName) {
								switch ($validatorName) {
									case "base64":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "between":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','inclusive'=>'true','min'=>'0','max'=>'3');break;
									case "callback":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','callback'=>'function (value,validator) {return {valid:false,message:"msg desde el callback"};}');break;
									case "choice":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','min'=>'0','max'=>'3');break;
									case "creditCard":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "cusip":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "cvv":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','creditCardField'=>'"name de input"');break;
									case "date":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','format'=>'"DD/MM/YYYY"','separator'=>'"/"');break;
									case "different":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','field'=>'"name de input"');break;
									case "digits":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "ean":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "emailAddress":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "file":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','extension'=>'"jpeg,png"','type'=>'"image/jpeg,image/png"','maxSize'=>'2048*1024');break;
									case "greaterThan":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','inclusive'=>'true','value'=>'33');break;
									case "grid":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "hex":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "hexColor":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "iban":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','country'=>'"ES"');break;
									case "id":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','country'=>'"ES"');break;
									case "identical":
										$arrValidatorParams=array('message'=>'"'.$field.' y '.$field.'Identical deben ser iguales."','field'=>'"'.$field.'Identical"');
										$arrValidatorParams2=array('message'=>'"'.$field.' y '.$field.'Identical deben ser iguales."','field'=>'"'.$field.'"');break;
									break;
									case "imei":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "integer":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "ip":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','ipv4'=>'true','ipv6'=>'false');break;
									case "isbn":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "isin":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "ismn":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "issn":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "lessThan":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','inclusive'=>'true','value'=>'33');break;
									case "mac":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "notEmpty":$arrValidatorParams=array('message'=>'"debe rellenar '.$field.'"');break;
									case "numeric":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','separator'=>'","');break;
									case "phone":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','country'=>'"ES"');break;
									case "regexp":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','regexp'=>'/^[a-z\s]+$/i');break;
									case "remote":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','data'=>'Object','name'=>'"The name of field which need to validate"','type'=>'"POST"','url'=>'function(validator) {return "the URL";}');break;
									case "rtn":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "sedol":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "siren":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "siret":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "step":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','baseValue'=>'3.3','step'=>'7');break;
									case "stringCase":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','case'=>'lower');break;
									case "stringLength":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "uri":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','allowLocal'=>'true');break;
									case "uuid":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','version'=>'"all"');break;
									case "vat":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','country'=>'"ES"');break;
									case "vin":$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
									case "zipCode":$arrValidatorParams=array('message'=>'"'.$field.' no válido"','country'=>'"US"');break;
									//default:$arrValidatorParams=array('message'=>'"'.$field.' no válido"');break;
								}
								$code.=$sg.$sg.$sg.$sg.$sg.'//ver http://bootstrapvalidator.com/validators/'.$validatorName.$sl;
								$code.=$sg.$sg.$sg.$sg.$sg.''.$validatorName.': {'.$sl;
								foreach ($arrValidatorParams as $nombre => $value) {
									$code.=$sg.$sg.$sg.$sg.$sg.$sg.''.$nombre.': '.$value.','.$sl;
								}
								$code=substr($code, 0,-2);
								$code.=$sl;
								$code.=$sg.$sg.$sg.$sg.$sg.'},'.$sl;
								/**/
								if ($validatorName=="identical") {
									$codeIdentical.=$sg.$sg.$sg.$field.'Identical: {'.$sl;
									$codeIdentical.=$sg.$sg.$sg.$sg.'message: "'.$field.' no válido",'.$sl;
									$codeIdentical.=$sg.$sg.$sg.$sg.'validators: {'.$sl;

									$codeIdentical.=$sg.$sg.$sg.$sg.$sg.''.$validatorName.': {'.$sl;
									foreach ($arrValidatorParams2 as $nombre => $value) {
										$codeIdentical.=$sg.$sg.$sg.$sg.$sg.$sg.''.$nombre.': '.$value.','.$sl;
									}
									$codeIdentical=substr($codeIdentical, 0,-2);
									$codeIdentical.=$sl;
									$codeIdentical.=$sg.$sg.$sg.$sg.$sg.'},'.$sl;

									$codeIdentical=substr($codeIdentical, 0,-2);
									$codeIdentical.=$sl;
									$codeIdentical.=$sg.$sg.$sg.$sg.'}'.$sl;
									$codeIdentical.=$sg.$sg.$sg.'},'.$sl;
								}
								/**/
							}
							$code=substr($code, 0,-2);
							$code.=$sl;
							$code.=$sg.$sg.$sg.$sg.'}'.$sl;
							$code.=$sg.$sg.$sg.'},'.$sl;
						}
					}
					$code.=$codeIdentical;
					$codeIdentical="";
				}
				$code=substr($code, 0,-2);
				$code.=$sl;
				fwrite ($fp,$code.$sl);
				fwrite ($fp,$sg.$sg.'}'.$sl);//Cierre fields
				fwrite ($fp,$sg.'});'.$sl);//Cierre bootstrapValidator
				fwrite ($fp,'});'.$sl);
	}

	private function markupCrud($fp,$page,$class,$stdObjTableInfo,$arrValidators) {
			$sl="\n";
			$sg="\t";
			fwrite ($fp,'<form action="<?=BASE_URL.FILE_APP?>" method="post" enctype="multipart/form-data" id="frm'.ucfirst($class).'" style="width:50%; margin:auto;">'.$sl);
			fwrite ($fp,$sg.'<input name="MODULE" id="MODULE" type="hidden" value="actions"/>'.$sl);
			fwrite ($fp,$sg.'<input name="acClase" id="acClase" type="hidden" value="'.$page.'"/>'.$sl);
			fwrite ($fp,$sg.'<input name="acMetodo" id="acMetodo" type="hidden" value="'.'acGrabar'.'"/>'.$sl);
			fwrite ($fp,$sg.'<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>'.$sl);
			fwrite ($fp,$sg.'<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>"/>'.$sl);
			fwrite ($fp,$sg.'<fieldset>'.$sl);
			fwrite ($fp,$sg.$sg.'<legend>Campos de '.ucfirst($class).'</legend>'.$sl);

			$code="";
			foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
				$type=$stdObjFieldInfo->type;
				$tag=$stdObjFieldInfo->tag;
				$tagType=$stdObjFieldInfo->tagType;
				if (isset($stdObjFieldInfo->arrSelectValues)) {
					$arrSelectValues=$stdObjFieldInfo->arrSelectValues;
				}
				$addIdentical=false;
				if (isset($arrValidators[$field])) {
					if (in_array("identical", $arrValidators[$field])) {
						$addIdentical=true;
					}
				}
				$inputClass='';
				$divField='<div>';
				$divFieldCierre='</div>';
				$btnClass='';
				if (BOOTSTRAP!=false) {
					$inputClass='form-control';
					$divField='<div class="form-group">';
					$divFieldCierre='</div>';
					$btnClass="btn btn-primary";
				}
				switch ($tag) {
					case "input":
						//color, date, datetime, datetime-local, month, range, search, tel, time, url, week
						switch ($tagType) {
							case "color":
							case "email":
							case "month":
							case "number":
							case "range":
							case "search":
							case "tel":
							case "time":
							case "url":
							case "week":
							case "text":
								$code.=$sg.$sg.$divField.$sl;
								$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).':</label>'.$sl;
								$code.=$sg.$sg.$sg.'<input type="'.$tagType.'" name="'.$field.'" id="'.$field.'" value="<?=(isset($'.$field.'))?$'.$field.':"";?>" class="'.$inputClass.'" />'.$sl;
								$code.=$sg.$sg.$divFieldCierre.$sl;
								if ($addIdentical) {
									$code.=$sg.$sg.$divField.$sl;
									$code.=$sg.$sg.$sg.'<label for="'.$field.'Identical" accesskey="">'.ucfirst($field).'Identical:</label>'.$sl;
									$code.=$sg.$sg.$sg.'<input type="'.$tagType.'" name="'.$field.'Identical" id="'.$field.'Identical" value="<?=(isset($'.$field.'Identical))?$'.$field.'Identical:"";?>" class="'.$inputClass.'" />'.$sl;
									$code.=$sg.$sg.$divFieldCierre.$sl;
								}
							break;
							case "hidden":
								$code.=$sg.$sg.$sg.'<input type="'.$tagType.'" name="'.$field.'" id="'.$field.'" value="<?=(isset($'.$field.'))?$'.$field.':"";?>" class="'.$inputClass.'" />'.$sl;
							break;
							case "checkbox":
								$code.=$sg.$sg.$divField.$sl;
								$code.=$sg.$sg.$sg.'<input name="'.$field.'" id="'.$field.'.Dummy" type="hidden" value="0" />'.$sl;
								$code.=$sg.$sg.$sg.'<input name="'.$field.'" id="'.$field.'" type="checkbox" value="1" <?=(isset($'.$field.'))?$'.$field.':"";?>  class="" />'.$sl;
								$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).'</label>'.$sl;
								//$code.=$sg.$sg.$sg.''.$sl;
								$code.=$sg.$sg.$divFieldCierre.$sl;
							break;
							case "date":
							case "datetime":
							case "datetime-local":
								$code.=$sg.$sg.$divField.$sl;
								if (BOOTSTRAP!=false) {
									$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).':</label>'.$sl;
									$code.=$sg.$sg.$sg.'<div class="input-group date">'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<span class="input-group-addon">'.$sl;
									$code.=$sg.$sg.$sg.$sg.$sg.'<i class="glyphicon glyphicon-calendar"></i>'.$sl;
									$code.=$sg.$sg.$sg.$sg.'</span>'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<input type="text" name="'.$field.'" id="'.$field.'" value="<?=(isset($'.$field.'))?$'.$field.':"";?>" class="'.$inputClass.'" placeholder="'.$field.'" />'.$sl;
									$code.=$sg.$sg.$sg.'</div>'.$sl;
								} else {
									$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).':</label>'.$sl;
									//$code.=$sg.$sg.$sg.''.$sl;
									$code.=$sg.$sg.$sg.'<select name="'.$field.'Dia" id="'.$field.'Dia">'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="-1">Día:</option>'.$sl;
									$code.=$sg.$sg.$sg.'<? for ($i=1;$i<32;$i++) {?>'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$field.'Dia[$i]))?$'.$field.'Dia[$i]:"";?>><?=$i?></option>'.$sl;
									$code.=$sg.$sg.$sg.'<? }?>'.$sl;
									$code.=$sg.$sg.$sg.'</select>'.$sl;
									$code.=$sg.$sg.$sg.'<select id="'.$field.'Mes" name="'.$field.'Mes">'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="-1">Mes:</option>'.$sl;
									$code.=$sg.$sg.$sg.'<? for ($i=1;$i<13;$i++) {?>'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$field.'Mes[$i]))?$'.$field.'Mes[$i]:"";?>><?=$i?></option>'.$sl;
									$code.=$sg.$sg.$sg.'<? }?>'.$sl;
									$code.=$sg.$sg.$sg.'</select>'.$sl;
									$code.=$sg.$sg.$sg.'<select name="'.$field.'Ano" id="'.$field.'Ano">'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="-1">Año:</option>'.$sl;
									$code.=$sg.$sg.$sg.'<? for ($i=date("Y");$i>1900;$i--) {?>'.$sl;
									$code.=$sg.$sg.$sg.$sg.'<option value="<?=$i?>" <?=(isset($'.$field.'Ano[$i]))?$'.$field.'Ano[$i]:"";?>><?=$i?></option>'.$sl;
									$code.=$sg.$sg.$sg.'<? }?>'.$sl;
									$code.=$sg.$sg.$sg.'</select>'.$sl;
									//$code.=$sg.$sg.$sg.''.$sl;
								}
								$code.=$sg.$sg.$divFieldCierre.$sl;
							break;
						}
					break;
					case "select":
						$code.=$sg.$sg.$divField.$sl;
						$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).':</label>'.$sl;
						switch ($tagType) {
							case 'enum':
								//$arrSelectValues trae un elto por option del select y se usa para value y contenido del cada option
								$code.=$sg.$sg.$sg.'<select name="'.$field.'" id="'.$field.'"  class="'.$inputClass.'">'.$sl;
								foreach ($arrSelectValues as $key => $value) {
									$value=trim($value, "'");
									$code.=$sg.$sg.$sg.$sg.'<option value="'.$value.'" <?=(isset($'.$field.'["'.$value.'"]'.'))?$'.$field.'["'.$value.'"]'.':"";?>>'.$value.'</option>'.$sl;
								}
								$code.=$sg.$sg.$sg.'</select>'.$sl;
							break;
							case 'dbSelect':
								//$arrSelectValues trae la sql a ejecutar para obtener los options (debe devuelve dos columnas, value y content)
								$code.=$sg.$sg.$sg.'<select name="'.$field.'" id="'.$field.'"  class="'.$inputClass.'">'.$sl;
								$code.="<?".$sl;
								$code.='$rsl=cDb::gI()->query ("'.$arrSelectValues[0].'");'.$sl;
								$code.='while ($arrRow=$rsl->fetch_array()) {'.$sl;
								$code.=$sg.'$value=$arrRow["value"];$content=$arrRow["content"];'.$sl;
								$code.="?>".$sl;
								$code.=$sg.$sg.$sg.$sg.'<option value="<?=$value?>" <?=(isset($'.$field.'[$value]'.'))?$'.$field.'[$value]'.':"";?>><?=$content?></option>'.$sl;
								$code.="<?".$sl;
								$code.='}'.$sl;
								$code.="?>".$sl;
								$code.=$sg.$sg.$sg.'</select>'.$sl;
							break;
						}
						$code.=$sg.$sg.$divFieldCierre.$sl;
					break;
					case "textarea":
						$code.=$sg.$sg.$divField.$sl;
						$code.=$sg.$sg.$sg.'<label for="'.$field.'" accesskey="">'.ucfirst($field).':</label>'.$sl;
						$code.=$sg.$sg.$sg.'<textarea name="'.$field.'" id="'.$field.'" value="<?=(isset($'.$field.'))?$'.$field.':"";?>" class="'.$inputClass.'" rows="3" /><?=(isset($'.$field.'))?$'.$field.':"";?></textarea>'.$sl;
						$code.=$sg.$sg.$divFieldCierre.$sl;
					break;
				}
			}
			fwrite ($fp,$code);
			fwrite ($fp,$sg.$sg.'<input id="btnSubmit" type="submit" class="'.$btnClass.'" value="Grabar" />'.$sl);
			fwrite ($fp,$sg.$sg.'<input id="btnAcBorrar" type="button" class="'.$btnClass.'" value="Borrar" data-id="<?=$id?>" />'.$sl);
			fwrite ($fp,$sg.'</fieldset>'.$sl);
			fwrite ($fp,'</form>'.$sl);
	}

	private function markupFuncCrud($fp,$class,$stdObjTableInfo) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,$sg.$sg.'$id=(isset($_GET["id"]) && '.ucfirst($class).'::existeId($_GET["id"]))?$_GET["id"]:0;'.$sl);
		fwrite ($fp,$sg.$sg.'$obj'.ucfirst($class).'=new '.ucfirst($class).'($id);'.$sl);
		fwrite ($fp,$sg.$sg.'foreach ($obj'.ucfirst($class).'->toArray() as $key => $value) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$func="GET".$key;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$$key=$obj'.ucfirst($class).'->$func();'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
			switch ($stdObjFieldInfo->tag) {
				case "input":
					switch ($stdObjFieldInfo->tagType) {
						case "checkbox":
							fwrite ($fp,$sg.$sg.'$'.$field.'=($'.$field.')?\'checked="checked"\':"";'.$sl);
						break;
						case "date":
						case "datetime":
							$conHora=($stdObjFieldInfo->tagType=="date")?"false":"true";
							/* Compatibilidad con fecha de 3 selects
							fwrite ($fp,$sg.$sg.'$'.$field.'=new Fecha($'.$field.',"MySQL");'.$sl);
							fwrite ($fp,$sg.$sg.'$'.$field.'Dia=array((int)$'.$field.'->GETdia() => \'selected="selected"\');'.$sl);
							fwrite ($fp,$sg.$sg.'$'.$field.'Mes=array((int)$'.$field.'->GETmes() => \'selected="selected"\');'.$sl);
							fwrite ($fp,$sg.$sg.'$'.$field.'Ano=array((int)$'.$field.'->GETano() => \'selected="selected"\');'.$sl);
							*/
							fwrite ($fp,$sg.$sg.'$'.$field.'=fecha::toFechaES(fecha::fromMysql($'.$field.'),'.$conHora.');'.$sl);
						break;
					}
				break;
				case "select":
					fwrite ($fp,$sg.$sg.'$'.$field.'=array($'.$field.' => "selected");'.$sl);
				break;
				case "textarea":
				break;
			}
		}
	}

	private function actionFuncsCrud($fp,$class,$stdObjTableInfo) {
		$sl="\n";
		$sg="\t";
			//Inicio acGrabar
		fwrite ($fp,$sg.'public function acGrabar() {'.$sl);
		fwrite ($fp,$sg.$sg.'try {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$args=array ('.$sl);
		$code='';
		foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
			//fwrite ($fp,$sg.$sg.$sg.'$'.$field.'=$_REQUEST["'.$field.'"];'.$sl);
			$filterDefinitionCode=var_export($stdObjFieldInfo->filterDefinition,true);
			echo "<pre>".$filterDefinitionCode;
			$filterDefinitionCode=preg_replace("~'filter' ?=> ?[\"\'](.+)[\"\']~","'filter' => $1",$filterDefinitionCode);//filter trae una cadena, pero queremos descomillarla para que sea codigo
			$filterDefinitionCode=preg_replace("~'flags' ?=> ?[\"\'](.+)[\"\']~","'flags' => $1",$filterDefinitionCode);//flags trae una cadena, pero queremos descomillarla para que sea codigo
			$filterDefinitionCode=str_replace("  ",$sg,$filterDefinitionCode);//Resangramos, espacios a $sg
			$filterDefinitionCode=str_replace("\n",$sl.$sg.$sg.$sg.$sg,$filterDefinitionCode);//Resangramos, sangrado base
			$filterDefinitionCode=preg_replace("~\([".$sl."|".$sg."]+\)~","()",$filterDefinitionCode);//Parentesis vacios sin sangrado ni saltos
			$filterDefinitionCode=preg_replace("~ [".$sl."|".$sg."]+array+~"," array",$filterDefinitionCode);//arrays sin sangrado ni saltos antes de su definicion
			$code.=$sg.$sg.$sg.$sg.'"'.$field.'" => '.$filterDefinitionCode.','.$sl;
		}
		$code=substr($code, 0,-2).$sl;
		fwrite($fp, $code);
		fwrite ($fp,$sg.$sg.$sg.');'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$arrInputData=filter_input_array(INPUT_POST,$args);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$badData=false;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$ulProblems="<ul>";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'if (is_array($arrInputData)) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'foreach ($arrInputData as $key => $value) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'	if ($value===false) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'		$badData=true;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'		$ulProblems.="<li>".$key." no es válido.</li>";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'	}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'	$badData=true;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'//echo "<pre style=\"float:right;\">";var_dump($arrInputData);echo "</pre>";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'//echo "<pre>";var_dump($_POST);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'if ($badData) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'	//echo $ulProblems;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'	$sriTitle="Operación no completada.";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'	$sriMsg="Se encontraron los siguientes problemas con los datos suministrados:".$ulProblems;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'	ReturnInfo::add($sriMsg,$sriTitle);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'$id=(isset($arrInputData["id"]) && '.ucfirst($class).'::existeId($arrInputData["id"]))?$arrInputData["id"]:NULL;'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'$obj'.ucfirst($class).'=new '.ucfirst($class).'($id);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'$obj'.ucfirst($class).'->cargarArray($arrInputData);'.$sl);
		foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
			switch ($stdObjFieldInfo->tag) {
				case "input":
					switch ($stdObjFieldInfo->tagType) {
						case "checkbox":
						break;
						case "date":
						case "datetime":
							/* Compatibilidad con fecha de 3 selects'.$sl);
							fwrite ($fp,$sg.$sg.$sg.'$arrInputData["'.$field.'"]=new Fecha($arrInputData["'.$field.'Ano"]."/".$arrInputData["'.$field.'Mes"]."/".$arrInputData["'.$field.'Dia"],"FechaES");'.$sl);
							*/
							fwrite ($fp,$sg.$sg.$sg.$sg.'$arrInputData["'.$field.'"]=new Fecha($arrInputData["'.$field.'"],"FechaES");'.$sl);
							fwrite ($fp,$sg.$sg.$sg.$sg.'$obj'.ucfirst($class).'->SET'.$field.'($arrInputData["'.$field.'"]->toMysql());'.$sl);
						break;
					}
				break;
				case "select":
				break;
				case "textarea":
				break;
			}
		}
		fwrite ($fp,$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'$result=$obj'.ucfirst($class).'->grabar();'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'if ($result) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriTitle="Operación completada.";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriMsg="Datos actualizados correctamente.";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'ReturnInfo::add($sriMsg,$sriTitle);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'	throw new ActionException("Error durante la grabación en BD. '.ucfirst($class).' no grabado.");'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
		//fwrite ($fp,$sg.$sg.$sg.'return $result;'.$sl);
		fwrite ($fp,$sg.$sg.'} catch (Exception $e) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'throw new ActionException("Error durante la actualización de datos. '.ucfirst($class).' no grabado.",0,$e);'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin acGrabar
			//Inicio acBorrar
		fwrite ($fp,$sg.'public function acBorrar() {'.$sl);
		fwrite ($fp,$sg.$sg.'if (isset($_REQUEST["id"])) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$id=$_REQUEST["id"];'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'if ('.ucfirst($class).'::existeId($id)) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'$obj'.ucfirst($class).'=new '.ucfirst($class).'($id);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'if ($obj'.ucfirst($class).'->borrar()) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriTitle="Operacion completada";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriMsg="'.ucfirst($class).' borrado con exito";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriTitle="Operacion no completada";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.$sg.'$sriMsg="Error al borrar '.ucfirst($class).'";'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'ReturnInfo::add($sriMsg,$sriTitle);'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'throw new ActionException("'.ucfirst($class).' ".$id." no encontrado");'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.$sg.'} else {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'throw new ActionException("Parametros no validos");'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
			//Fin acBorrar
	}
/* FIN CRUD creation functions ************************************************/
/* DBdataTable creation functions *********************************************/
	private function cssDBdataTable($fp,$class) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,'#'.$class.'Table th {text-transform:capitalize;}'.$sl);
		fwrite ($fp,'#'.$class.'Table .checkboxCol .glyphicon-ok {color: green;}'.$sl);
		fwrite ($fp,'#'.$class.'Table .checkboxCol .glyphicon-remove {color: red;}'.$sl);
		fwrite ($fp,'#'.$class.'Table td.checkboxCol {text-align:center}'.$sl);
		fwrite ($fp,'#'.$class.'Table .noData {opacity:0.5;}'.$sl);
	}

	private function jsDBdataTable($fp,$page,$class,$stdObjTableInfo,$arrValidators) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,'$(document).ready(function() {'.$sl);
		fwrite ($fp,$sg.'var $'.$class.'Table=$("#'.$class.'Table");'.$sl);
		fwrite ($fp,$sg.'$'.$class.'Table.DBdataTable({'.$sl);
		fwrite ($fp,$sg.$sg.'"sDom":"<\'H\'lfr>t<\'F\'ip>",'.$sl);
		fwrite ($fp,$sg.$sg.'"aoColumns": ['.$sl);
		$code='';
		foreach ($stdObjTableInfo->arrStdObjColumnInfo as $field => $stdObjFieldInfo) {
			$bVisible=($stdObjFieldInfo->tagType!='hidden')?'true':'false';
			$bSearchable=$bVisible;
			$sWidth=round(95/count($stdObjTableInfo->arrStdObjColumnInfo)).'%';
			$code.=$sg.$sg.$sg.'{"sTitle": "'.$field.'","mData":"'.$field.'", "sClass":"'.$field.' '.$stdObjFieldInfo->tagType.'Col", "bVisible": '.$bVisible.', "bSearchable": '.$bSearchable.', "sWidth":"'.$sWidth.'", "bSortable": true';
			switch ($stdObjFieldInfo->tagType) {
				case 'text':
					$code.=','.$sl;
					$code.=$sg.$sg.$sg.$sg.'"mRender":function (data,type,full) {'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'if (data!=null && data!="") return data;'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'else return \'<span class="noData">[Sin datos]</span>\';'.$sl;
					$code.=$sg.$sg.$sg.'}';
				break;
				case 'checkbox':
					$code.=','.$sl;
					$code.=$sg.$sg.$sg.$sg.'"mRender":function (data,type,full) {'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'if (data==1) return \'<span class="glyphicon glyphicon-ok"></span>\';'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'else return \'<span class="glyphicon glyphicon-remove"></span>\';'.$sl;
					$code.=$sg.$sg.$sg.'}';
				break;
				case 'datetime':
				case 'date':
					$conHora=($stdObjFieldInfo->tagType=='datetime')?'true':'false';
					$code.=','.$sl;
					$code.=$sg.$sg.$sg.$sg.'"mRender":function (data,type,full) {'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'if (data!=null && data!="" && data!="0000-00-00" && data!="0000-00-00 00:00:00") return Date.fromMysql(data).toStringES('.$conHora.');'.$sl;
					$code.=$sg.$sg.$sg.$sg.$sg.'else return \'<span class="noData">[Sin datos]</span>\';'.$sl;
					$code.=$sg.$sg.$sg.'}';
				break;
			}
			$code.='},'.$sl;
		}
		$code.=$sg.$sg.$sg.'{ "sTitle": "Seleccionar", "mData":"id", "sClass":"seleccionar", "sWidth":"5%", "bSortable": false,'.$sl;
		$code.=$sg.$sg.$sg.$sg.'"mRender":function (data,type,full) {'.$sl;
		$code.=$sg.$sg.$sg.$sg.$sg.'var disabled="";'.$sl;
		$code.=$sg.$sg.$sg.$sg.$sg.'if (full.borrable==0) {'.$sl;
		$code.=$sg.$sg.$sg.$sg.$sg.$sg.'var disabled=\'disabled="disabled"\';'.$sl;
		$code.=$sg.$sg.$sg.$sg.$sg.'}'.$sl;
		$code.=$sg.$sg.$sg.$sg.$sg.'return \'<input class="chkSeleccionar" type="checkbox" name="selected[\'+full.id+\']" value="1" \'+disabled+\' />\''.$sl;
		$code.=$sg.$sg.$sg.'}}'.$sl;
		$code=substr($code,0,-1);
		fwrite ($fp,$code.$sl);
		fwrite ($fp,$sg.$sg.'],'.$sl);
		fwrite ($fp,$sg.$sg.'"aaSorting":[[4,"desc"]],'.$sl);
		fwrite ($fp,$sg.$sg.'"sAjaxSource": "'.FILE_APP.'",'.$sl);
		fwrite ($fp,$sg.$sg.'"fnServerParams": function ( aoData ) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"MODULE", "value":"actions"});'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"acClase","value":"'.$page.'"});'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"acMetodo","value":"acDataTables"});'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"acTipo","value":"ajaxAssoc"});'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"clase","value":"'.ucfirst($class).'"});'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'aoData.push({"name":"metodo","value":"ls"});'.$sl);
		fwrite ($fp,$sg.$sg.'},'.$sl);
		fwrite ($fp,$sg.$sg.'"fnDrawCallback": function( oSettings ) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'$(".chkSeleccionar",$'.$class.'Table).click(function (e) {'.$sl);
		fwrite ($fp,$sg.$sg.$sg.$sg.'e.stopPropagation();'.$sl);
		fwrite ($fp,$sg.$sg.$sg.'});'.$sl);
		fwrite ($fp,$sg.$sg.'}'.$sl);
		fwrite ($fp,$sg.'});'.$sl);
		fwrite ($fp,$sg.'$'.$class.'Table.bind("rowClick", function (evt, idRow) {'.$sl);
		fwrite ($fp,$sg.$sg.'//window.location="<?=FILE_APP?>?page=crud'.ucfirst($class).'&id="+idRow;'.$sl);
		fwrite ($fp,$sg.'});'.$sl);
		fwrite ($fp,'});'.$sl);
	}
	private function markupDBdataTable($fp,$page,$class,$stdObjTableInfo) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,'<form action="<?=FILE_APP?>" method="post" enctype="multipart/form-data" id="frm'.ucfirst($class).'">'.$sl);
		fwrite ($fp,$sg.'<input name="MODULE" id="MODULE" type="hidden" value="actions"/>'.$sl);
		fwrite ($fp,$sg.'<input name="acClase" id="acClase" type="hidden" value="'.$page.'"/>'.$sl);
		fwrite ($fp,$sg.'<input name="acMetodo" id="acMetodo" type="hidden" value="'.'acGrabar'.'"/>'.$sl);
		fwrite ($fp,$sg.'<input name="acTipo" id="acTipo" type="hidden" value="stdAssoc"/>'.$sl);
		fwrite ($fp,$sg.'<input name="acReturnURI" id="acReturnURI" type="hidden" value="<?=$_SERVER["REQUEST_URI"]?>"/>'.$sl);
		fwrite ($fp,$sg.'<fieldset>'.$sl);
		fwrite ($fp,$sg.$sg.'<legend>Listado de '.ucfirst($class).'</legend>'.$sl);
		fwrite ($fp,$sg.$sg.'<table id="'.$class.'Table" class="stdDataTable"></table>'.$sl);
		fwrite ($fp,$sg.'</fieldset>'.$sl);
		fwrite ($fp,'</form>'.$sl);
	}
	private function actionFuncsDBdataTable($fp,$class,$stdObjTableInfo) {
		$sl="\n";
		$sg="\t";
		fwrite ($fp,$sg.'public function acDataTables() {'.$sl);
		fwrite ($fp,$sg.$sg.'return dataTablesGenericServerSide($this->objUsr);'.$sl);
		fwrite ($fp,$sg.'}'.$sl);
	}
	private function markupFuncDBdataTable($fp,$class,$stdObjTableInfo) {
		$sl="\n";
		$sg="\t";
	}
/* FIN DBdataTable creation functions *****************************************/

	private function CrearClase($class) {
		$ruta=RUTA_APP."server/clases/Logic/";
		$stdObjTableInfo=$this->getTableInfo($class);
		if (!file_exists($ruta.ucfirst($stdObjTableInfo->tableName).".php")) {
			$objCreadora=new \Creadora (
				$ruta,
				ucfirst($stdObjTableInfo->tableName),
				$stdObjTableInfo->arrAttrs,
				$stdObjTableInfo->tableName,
				$stdObjTableInfo->arrFksFrom,
				$stdObjTableInfo->arrFksTo);
			//$objCreadora=new Creadora (ucfirst($tableName), $arrAttrs, $tableName);
			//$objCreadora->creadoraJS (ucfirst($tableName), $arrAttrs, $tableName);
			//$objCreadora->frmSimple($arrAttrs,ucfirst($tableName));
			$title='Clase '.$class.' creada';
			$msg='<h2 title="'.print_r ($stdObjTableInfo->arrAttrs,true).'">Creando clase '.ucfirst($stdObjTableInfo->tableName).' (tooltip)</h2>';
			$msg.="<h3>Atributos</h3><pre>".print_r ($stdObjTableInfo->arrAttrs,true)."</pre>";
			$msg.="<h3>FKs</h3><pre>".print_r ($stdObjTableInfo->arrFksTo,true)."</pre>";
			$msg.="<hr /><hr /><hr />";
			ReturnInfo::add($msg,$title);
		} else {
			$title='Clase '.$class.' NO re-creada';
			$msg='La clase ya existe.';
			ReturnInfo::add($msg,$title);
		}
	}

	private function getTableInfo($DBtable,$arrExcluidos=array()) {
				$arrTypes2Tags=array(
					"varchar" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"text",
						"filterDefinition" =>array ("filter" => "FILTER_SANITIZE_FULL_SPECIAL_CHARS","flags" => "","options" => array())
					),
					"text" => array (
						"property" =>"type",
						"tag" =>"textarea",
						"tagType" =>"textarea",
						"filterDefinition" =>array ("filter" => "FILTER_SANITIZE_FULL_SPECIAL_CHARS","flags" => "","options" => array())
					),
					"int" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"text",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_INT","flags" => "",
							"options" => array("default" => 1,"min_range" =>1,"max_range" =>10))
					),
					"tinyint(1)" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"checkbox",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_INT","flags" => "",
							"options" => array("min_range" =>0,"max_range" =>1))
					),
					"enum" => array (
						"property" =>"type",
						"tag" =>"select",
						"tagType" =>"enum",
						"filterDefinition" =>array ()//Este sera regex y se crea en el momento de tener los valores de cada enum
					),
					"date" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"date",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_REGEXP","flags" => "",
							"options" => array("regexp" =>"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"))
					),
					"datetime" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"datetime",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_REGEXP","flags" => "",
							"options" => array("regexp" =>"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"))
					),
					"timestamp" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"datetime",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_REGEXP","flags" => "",
							"options" => array("regexp" =>"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"))
					),
					"float" => array (
						"property" =>"type",
						"tag" =>"input",
						"tagType" =>"text",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_FLOAT","flags" => "FILTER_FLAG_ALLOW_THOUSAND",
							"options" => array("decimal" => ","))
					),
					"email" => array (
						"property" =>"field",
						"tag" =>"input",
						"tagType" =>"email",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_EMAIL","flags" => "","options" => array())
					),
					"PRI" => array (
						"property" =>"key",
						"tag" =>"input",
						"tagType" =>"hidden",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_INT", "flags" => "",
							"options" => array("default" => NULL,"min_range" =>1,"max_range" => PHP_INT_MAX))
					),
					"MUL" => array (
						"property" =>"key",
						"tag" =>"select",
						"tagType" =>"dbSelect",
						"filterDefinition" =>array ("filter" => "FILTER_VALIDATE_INT", "flags" => "",
							"options" => array("default" => NULL,"min_range" =>1,"max_range" => PHP_INT_MAX))
					),
				);

				$mysqli=\cDb::getInstance();
				$stdObjTableInfo=new \stdClass();
				$stdObjTableInfo->tableName=$DBtable;

				$rslCreate = $mysqli->query("SHOW CREATE TABLE ".$stdObjTableInfo->tableName);

				$stdObjTableInfo->rslColumns = $mysqli->query("SHOW COLUMNS FROM ".$stdObjTableInfo->tableName);
				$stdObjTableInfo->rslIdx = $mysqli->query("show index from ".$stdObjTableInfo->tableName);
				$stdObjTableInfo->rslFksFrom = $mysqli->query("
					SELECT * FROM information_schema.KEY_COLUMN_USAGE
					WHERE
					TABLE_NAME = '".$stdObjTableInfo->tableName."' AND
					REFERENCED_TABLE_NAME IS NOT NULL
					AND TABLE_SCHEMA = '"._DB_NAME_."';
				");
				$stdObjTableInfo->rslFksTo = $mysqli->query("
					SELECT * FROM information_schema.KEY_COLUMN_USAGE
					WHERE REFERENCED_TABLE_NAME = '".$stdObjTableInfo->tableName."'
					AND TABLE_SCHEMA = '"._DB_NAME_."';
				");

				$stdObjTableInfo->arrCreateInfo = $rslCreate->fetch_array(MYSQLI_ASSOC);

				/**/

				$stdObjTableInfo->arrFksFrom=array();
				while ($fkInfo = $stdObjTableInfo->rslFksFrom->fetch_array(MYSQLI_ASSOC)) {
					$stdObjFkInfo=new \stdClass();
					//$stdObjFkInfo->TABLE_NAME=$fkInfo['REFERENCED_TABLE_NAME'];
					//$stdObjFkInfo->COLUMN_NAME=$fkInfo['COLUMN_NAME'];

					$stdObjFkInfo->TABLE_NAME=$fkInfo['TABLE_NAME'];
					$stdObjFkInfo->COLUMN_NAME=$fkInfo['COLUMN_NAME'];
					$stdObjFkInfo->REFERENCED_TABLE_NAME=$fkInfo['REFERENCED_TABLE_NAME'];
					$stdObjFkInfo->REFERENCED_COLUMN_NAME=$fkInfo['REFERENCED_COLUMN_NAME'];
					array_push($stdObjTableInfo->arrFksFrom,$stdObjFkInfo);
					unset($stdObjFkInfo);
				}
				$stdObjTableInfo->rslFksFrom->data_seek(0);

				$stdObjTableInfo->arrFksTo=array();
				while ($fkInfo = $stdObjTableInfo->rslFksTo->fetch_array(MYSQLI_ASSOC)) {
					$stdObjFkInfo=new \stdClass();
					//$stdObjFkInfo->TABLE_NAME=$fkInfo['TABLE_NAME'];
					//$stdObjFkInfo->COLUMN_NAME=$fkInfo['COLUMN_NAME'];
					$stdObjFkInfo->TABLE_NAME=$fkInfo['TABLE_NAME'];
					$stdObjFkInfo->COLUMN_NAME=$fkInfo['COLUMN_NAME'];
					$stdObjFkInfo->REFERENCED_TABLE_NAME=$fkInfo['REFERENCED_TABLE_NAME'];
					$stdObjFkInfo->REFERENCED_COLUMN_NAME=$fkInfo['REFERENCED_COLUMN_NAME'];
					array_push($stdObjTableInfo->arrFksTo,$stdObjFkInfo);
					unset($stdObjFkInfo);
				}
				$stdObjTableInfo->rslFksTo->data_seek(0);

				/**/

				$stdObjTableInfo->arrStdObjColumnInfo=array();
				$stdObjTableInfo->arrAttrs=array();
				while ($columnInfo = $stdObjTableInfo->rslColumns->fetch_array(MYSQLI_ASSOC)) {
					if (in_array($columnInfo['Field'], $arrExcluidos)) {continue;}
					$stdObjColumnInfo=new \stdClass();
					$stdObjColumnInfo->field=$columnInfo['Field'];
					$stdObjColumnInfo->type=$columnInfo['Type'];
					$stdObjColumnInfo->null=($columnInfo['Null']=='NO')?false:true;
					$stdObjColumnInfo->key=$columnInfo['Key'];
					$stdObjColumnInfo->default=$columnInfo['Default'];
					$stdObjColumnInfo->extra=$columnInfo['Extra'];

					$tag='input';
					$tagType="text";
					$filterDefinition=array();
					foreach ($arrTypes2Tags as $strSearchFor => $options) {
						$property=$options["property"];
						if(strpos($stdObjColumnInfo->$property,$strSearchFor)!==false) {
							$tag=$options["tag"];
							$tagType=$options["tagType"];
							$filterDefinition=$options["filterDefinition"];
						}
						if ($tag=="select") {
							$arrSelectValues=array();
							switch ($strSearchFor) {
							case "enum":
								preg_match('/enum\((.*)\)$/', $stdObjColumnInfo->type, $matches);
								$arrSelectValues = explode(',', $matches[1]);
								$strRegEx="";
								foreach ($arrSelectValues as $value) {
									$value=trim($value, "'");
									$strRegEx.=$value."|";
								}
								$strRegEx=substr($strRegEx, 0,-1);
								$filterDefinition=array (
									"filter" => "FILTER_VALIDATE_REGEXP",
									"flags" => "",
									"options" => array("regexp" =>"~".$strRegEx."~"));
							break;
							//Select con consulta a BD
							case "MUL":
								foreach ($stdObjTableInfo->arrFksFrom as $stdObjFkInfo) {
									if ($stdObjFkInfo->COLUMN_NAME==$stdObjColumnInfo->field) {
										$campoSelect='';
										$rslFk=$mysqli->query('SHOW COLUMNS FROM '.$stdObjFkInfo->REFERENCED_TABLE_NAME);
										while ($rtColumnInfo = $rslFk->fetch_array(MYSQLI_ASSOC)) {
											$fieldName=strtolower($rtColumnInfo['Field']);
											switch ($fieldName) {
												case 'nombre':
												case 'descripcion':
													$campoSelect=$fieldName;
												break 2;
											}
										}
										if ($campoSelect=="") {$campoSelect=$stdObjFkInfo->REFERENCED_COLUMN_NAME;}
										$sql='SELECT '.$stdObjFkInfo->REFERENCED_COLUMN_NAME.' as value, '.
												$campoSelect.' as content '.
											'FROM '.$stdObjFkInfo->REFERENCED_TABLE_NAME.
											' ORDER BY '.$stdObjFkInfo->REFERENCED_COLUMN_NAME;
										$arrSelectValues[]=$sql;
									}
								}
							break;
							}
						}
					}
					$stdObjColumnInfo->tag=$tag;
					$stdObjColumnInfo->tagType=$tagType;
					$stdObjColumnInfo->filterDefinition=$filterDefinition;
					if (isset($arrSelectValues)) {
						$stdObjColumnInfo->arrSelectValues=$arrSelectValues;
						unset($arrSelectValues);
					}

					//array_push($stdObjTableInfo->arrStdObjColumnInfo,$stdObjColumnInfo);
					$stdObjTableInfo->arrStdObjColumnInfo[$stdObjColumnInfo->field]=$stdObjColumnInfo;

					$stdObjTableInfo->arrAttrs[$stdObjColumnInfo->field]=$stdObjColumnInfo->type;

					unset($stdObjColumnInfo);
				}
				$stdObjTableInfo->rslColumns->data_seek(0);

				return $stdObjTableInfo;
	}
}
?>
