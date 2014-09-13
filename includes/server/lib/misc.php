<?
//NOTA: Considerar que en ciertas ocasiones (por ejemplo cuando tenemos un select y solo necesitamos la id seleccionada,
//	no el texto selected="selected", lo mejor es tratarlo como un cmapo normal
function arrFormFromArrPost ($arrPost, $nonTextFields=NULL, $checkboxCheckedValue=1) {
	//$nonTextFields=array("sexo"=>"select","fnacDia"=>"select","fnacMes"=>"select","fnacAno"=>"select",
	//	"consienteEstadisticas"=>"checkbox","pru"=>"radio");
	$arr=array();
	if (isset($arrPost)) {
		foreach ($arrPost as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $arrKey => $arrValue) {
					if (isset($nonTextFields[$key])) {
						//echo 'nonTextFields['.$key.']=>'.$nonTextFields[$key]."=>".$value."=>".$key.$value."<br>";
						switch ($nonTextFields[$key]) {
							case "select":
								$arr[$key.$arrKey.$arrValue]='selected="selected"';
								$arr[$key][$arrKey][$arrValue]='selected="selected"';
							break;
							case "checkbox":if ($arrValue==$checkboxCheckedValue) {$arr[$key][$arrKey]='checked="checked"';}break;
							case "radio":$arr[$key.$value]='checked="checked"';break;
							case "selectMultiple":
							default:$arr[$key][$arrKey]=$arrValue;
						}
					} else {
						$arr[$key][$arrKey]=$arrValue;
					}
				}
			} else {
				if (isset($nonTextFields[$key])) {
					//echo 'nonTextFields['.$key.']=>'.$nonTextFields[$key]."=>".$value."=>".$key.$value."<br>";
					switch ($nonTextFields[$key]) {
						case "select":
							$arr[$key.$value]='selected="selected"';
							//Cambiamos el paradigma de tratamiento de los select, pero conservamos el anterior $arr[$key.$value] por compatibilidad
							$arr[$key][$value]='selected="selected"';
						break;
						case "checkbox":if ($value==$checkboxCheckedValue) {$arr[$key]='checked="checked"';}break;
						case "radio":$arr[$key.$value]='checked="checked"';break;
						case "selectMultiple":
						default:$arr[$key]=$value;
					}
				} else {
					$arr[$key]=$value;
				}
			}
		}
	}
	//print_r($arr);
	return $arr;
}

function logPageData($titulo="Grupo logPageData") {
	$GLOBALS['firephp']->group($titulo, array('Collapsed' => true, 'Color' => '#FF9933'));
	$GLOBALS['firephp']->group('_SESSION, page y usr', array('Collapsed' => true, 'Color' => '#3399FF'));
	$GLOBALS['firephp']->info($_SESSION,"SESSION");
	//$GLOBALS['firephp->info(htmlspecialchars($_SERVER['HTTP_REFERER']),'_SERVER[HTTP_REFERER]');//hace fallar al validador del w3c
	$GLOBALS['firephp']->info($GLOBALS['page'],'clase de página ($page)');
	$GLOBALS['firephp']->info($GLOBALS['objUsr'],'$objUsr');
	$GLOBALS['firephp']->groupend();
}

function dataTablesGenericServerSide($objCliente=NULL) {
	$sOrder = "";
	if ( isset( $_REQUEST['iSortCol_0'] ) ) {
		for ( $i=0 ; $i<intval( $_REQUEST['iSortingCols'] ) ; $i++ ) {
			if ( $_REQUEST[ 'bSortable_'.intval($_REQUEST['iSortCol_'.$i]) ] == "true" ) {
				$sOrder .= "`"
					.$GLOBALS['db']->real_escape_string($_REQUEST['mDataProp_'.intval( $_REQUEST['iSortCol_'.$i])])
					."` ".$GLOBALS['db']->real_escape_string($_REQUEST['sSortDir_'.$i]).", ";
			}
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
	}
	$GLOBALS['firephp']->info ($sOrder);

	//TODO: Mejora: Realizar el filtro mediante indices FULLTEXT
	/*
	* Filtering
	* NOTE this does not match the built-in DataTables filtering which does it
	* word by word on any field. It's possible to do here, but concerned about efficiency
	* on very large tables, and MySQL's regex functionality is very limited
	*/
	$sWhere = "";
	if (isset($_REQUEST['sSearch']) && $_REQUEST['sSearch']!="") {
		$sWhere = "(";
		for ($i=0; $i<$_REQUEST['iColumns'];$i++) {
			if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" ) {
				$sWhere .= "`"
					.$GLOBALS['db']->real_escape_string($_REQUEST['mDataProp_'.$i])
					."` LIKE '%".$GLOBALS['db']->real_escape_string($_REQUEST['sSearch'])."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	// Individual column filtering
	/* TODO: Mejora: Implementar filtro individual por columnas
	for ($i=0;$i<$_REQUEST['iColumns'];$i++) {
		if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && $_REQUEST['sSearch_'.$i] != '' ) {
			if ( $sWhere == "" ) {
				$sWhere = "";
			} else {
				$sWhere .= " AND ";
			}
			$sWhere .= "`".$GLOBALS['db']->real_escape_string($_REQUEST['mDataProp_'.$i])."` LIKE '%".$GLOBALS['db']->real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
		}
	}
	*/
	$GLOBALS['firephp']->info ($sWhere);

	$sLimit="";
	if ($_REQUEST['iDisplayLength']!=-1) {
		$sLimit=intval($_REQUEST['iDisplayStart']).",".intval($_REQUEST['iDisplayLength']);
	}
	$GLOBALS['firephp']->info ($sLimit);


	//La clase pasada debe contener el dataMetodo, que debe aceptar 3 parametros, sWhere, sOrder y sLimit  (busqueda, orden y paginacion)
	//El valor especial 'thisUsr' siginfica que la clase es el objeto usuario de la session y la llamada no es estatica
	if (!(isset($_REQUEST['clase']) && $_REQUEST['clase']=='thisUsr')) {
		if (isset($_REQUEST['clase'])) {
			if (class_exists($_REQUEST['clase'])) {$clase=$_REQUEST['clase'];}
				else {throw new Exception('Clase '.$_REQUEST['clase'].' no existe');}
		} else {throw new Exception('Parametro clase requerido');}
		if (isset($_REQUEST['metodo'])) {
			if (method_exists($clase,$_REQUEST['metodo'])) {$metodo=$_REQUEST['metodo'];}
				else {throw new Exception('Metodo '.$_REQUEST['metodo'].' no existe');}
		} else {throw new Exception('Parametro metodo requerido');}

		$arrStdObjs=$clase::$metodo();
		$total=count($arrStdObjs);
		$arrStdObjs=$clase::$metodo($sWhere);
		$totalDisplay=count($arrStdObjs);
		$arrStdObjs=$clase::$metodo($sWhere,$sOrder,$sLimit);
		$arrDataTables=$arrStdObjs;
	} else {
		//$clase=new Cliente($idCliente);
		$clase=$objCliente;
		if (isset($_REQUEST['metodo'])) {
			if (method_exists($clase,$_REQUEST['metodo'])) {$metodo=$_REQUEST['metodo'];}
				else {throw new Exception('Metodo '.$_REQUEST['metodo'].' no existe');}
		} else {throw new Exception('Parametro metodo requerido');}
		$arrStdObjs=$clase->$metodo();
		$total=count($arrStdObjs);
		$arrStdObjs=$clase->$metodo($sWhere);
		$totalDisplay=count($arrStdObjs);
		$arrStdObjs=$clase->$metodo($sWhere,$sOrder,$sLimit);
		$arrDataTables=$arrStdObjs;
	}

	for ($i=0; $i<count($arrDataTables); $i++) {
		$arrDataTables[$i]->DT_RowId=$arrDataTables[$i]->id;
	}

	$objDT->sEcho=$_REQUEST['sEcho'];
	$objDT->iTotalRecords=$total;
	$objDT->iTotalDisplayRecords=$totalDisplay;
	$objDT->data=$arrDataTables;
	return $objDT;
}

function sitemap($file="./sitemap.xml") {
	//define( "ENT_XML1",        16    );//Porque no conoce esto el PHP?????!!!
	$sl="\n";
	$sg="\t";
	$fp=fopen ($file,"w");

	$where="visible=1";$order="id asc";$limit="";$tipo="arrIds";
	$arrIdsProd=Producto::AllToArray($where,$order,$limit,$tipo);

	$where="visible=1";$order="id asc";$limit="";$tipo="arrIds";
	$arrIdsCat=Categoria::AllToArray($where,$order,$limit,$tipo);

	$contents='<?xml version="1.0" encoding="UTF-8" ?>'.$sl;
	$contents.='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$sl;
	foreach ($arrIdsCat as $idCat) {
		$objCat=new Categoria($idCat);
		$contents.=$sg.'<url>'.$sl;

		$contents.=$sg.$sg.'<loc>';
		$contents.=str_replace("&", "&amp;", $objCat->url(false));
		$contents.='</loc>'.$sl;

		$contents.=$sg.$sg.'<lastmod>';
		$contents.=Fecha::toW3C(Fecha::fromMysql($objCat->GETupdate()));
		$contents.='</lastmod>'.$sl;

		$contents.=$sg.$sg.'<changefreq>';
		$contents.="weekly";
		$contents.='</changefreq>'.$sl;

		$contents.=$sg.$sg.'<priority>';
		$contents.='0.5';//TODO: mejora: establecer este valor en base a las valoraciones de los usuarios
		$contents.='</priority>'.$sl;

		$contents.=$sg.'</url>'.$sl;
	}
	foreach ($arrIdsProd as $idProd) {
		$objProd=new Producto($idProd);
		$contents.=$sg.'<url>'.$sl;

		$contents.=$sg.$sg.'<loc>';
		$contents.=str_replace("&", "&amp;", $objProd->url(false));
		$contents.='</loc>'.$sl;

		$contents.=$sg.$sg.'<lastmod>';
		$contents.=Fecha::toW3C(Fecha::fromMysql($objProd->GETupdate()));
		$contents.='</lastmod>'.$sl;

		$contents.=$sg.$sg.'<changefreq>';
		$contents.="weekly";
		$contents.='</changefreq>'.$sl;

		$contents.=$sg.$sg.'<priority>';
		$contents.='0.5';//TODO: mejora: establecer este valor en base a las valoraciones de los usuarios
		$contents.='</priority>'.$sl;

		$contents.=$sg.'</url>'.$sl;
	}
	$contents.='</urlset>'.$sl;
	fwrite ($fp,$contents);
	fclose ($fp);
	chmod ($file,0666);
	//comprimirlo
	$gzfile = $file.".gz";
	// Open the gz file (w9 is the highest compression)
	$fp = gzopen ($gzfile, 'w9');
	// Compress the file
	gzwrite ($fp, file_get_contents($file));
	// Close the gz file and we are done
	gzclose($fp);
	chmod ($gzfile,0666);
	unlink($file);
}
/* Obsoleta, borrable reemplazada por filter_input_array
function sanitize($arr) {
	//Saenamos los parametros de la request ($_GET, $_POST y $_COOKIE), deben ser todos de tipo (string)
	//$arr es un array con la siguiente forma: array( 'nombreParametro' => $datosTratamiento, ...)
	//$datosTratamiento es un array con la siguientes formas:
		//1.- text
		//2.- numeric
	//devuelve un array con la siguiente forma: array( 'nombreParametro' => array('newValue'=>'', 'msg'=>'...', 'usable','True|False'), ...)
	$arrResult=array();
	foreach ($_REQUEST as $key => $arrValues) {
		//$_REQUEST[$key]=sanitize($value);
		$arrResult[$key]=array();
		if (isset($arr[$key])) {
			if (!is_array($arrValues)) {
				$arrValues=array($arrValues);
			}
			if (isset($arr[$key]['type'])) {
				foreach ($arrValues as $index => $value) {
					$arrResult[$key][$index]=array();
					$arrResult[$key][$index]['origValue']=$value;
					$arrResult[$key][$index]['newValue']=NULL;
					$arrResult[$key][$index]['usable']=NULL;
					$arrResult[$key][$index]['transformed']=NULL;
					$arrResult[$key][$index]['msg']='';
					switch ($arr[$key]['type']) {
						case 'text':
							$arrResult[$key][$index]['usable']=true;
							if (!isset($arr[$key]['subtype'])) {
								$arr[$key]['subtype']='plain';
							}
							$arrResult[$key][$index]['usable']=true;
							$minLenght=(isset($arr[$key]['minLenght']))?$arr[$key]['minLenght']:NULL;
							$maxLenght=(isset($arr[$key]['maxLenght']))?$arr[$key]['maxLenght']:NULL;
							if (!is_null($minLenght)) {if (strlen($value)<$minLenght) {$arrResult[$key][$index]['usable']=false;}}
							if (!is_null($maxLenght)) {if (strlen($value)>$maxLenght) {$arrResult[$key][$index]['usable']=false;}}
							switch ($arr[$key]['subtype']) {
								case 'plano':
								case 'plain':
									//TODO: Mejora: implementar allowedChars
									$arrResult[$key][$index]['newValue']=htmlentities(trim($value),ENT_QUOTES,"UTF-8");
									//$arrResult[$key][$index]['newValue']=trim($value);
								break;
								case 'html':
									$allowedTags=(isset($arr[$key]['allowedTags']))?$arr[$key]['allowedTags']:"";
									$arrResult[$key][$index]['newValue']=strip_tags($value,$allowedTags);
								break;
							}
							if (!$arrResult[$key][$index]['usable']) {
								$arrResult[$key][$index]['msg']=$arr[$key]['msg'];
							}
						break;
						case 'numeric':
							if (is_numeric($value)) {
								if (!isset($arr[$key]['subtype'])) {
									$arr[$key]['subtype']='int';
								}
								$min=(isset($arr[$key]['min']))?$arr[$key]['min']:NULL;
								$max=(isset($arr[$key]['max']))?$arr[$key]['max']:NULL;
								if (!is_null($min)) {if ($value<$min) {$arrResult[$key][$index]['usable']=false;}}
								if (!is_null($max)) {if ($value>$max) {$arrResult[$key][$index]['usable']=false;}}
								switch ($arr[$key]['subtype']) {
									case 'int':
										//TODO: Mejora: implementar maxLenght (diferente de max y min)
										if(ctype_digit($value)) {
											$arrResult[$key][$index]['newValue']=$value;
											$arrResult[$key][$index]['usable']=true;
										} else {
											$arrResult[$key][$index]['newValue']=(int)$value;
											$arrResult[$key][$index]['usable']=false;
											$arrResult[$key][$index]['transformed']=true;
										}
									break;
									case 'float':
										$arrResult[$key][$index]['newValue']=$value;
										$arrResult[$key][$index]['usable']=true;
									break;
								}
							} else {
								$arrResult[$key][$index]['usable']=false;
							}
							if (!$arrResult[$key][$index]['usable']) {
								$arrResult[$key][$index]['msg']=$arr[$key]['msg'];
							}
						break;
						case 'list':
							$arrResult[$key][$index]['usable']=false;
							if (!isset($arr[$key]['allowedValues'])) {
								$arr[$key]['allowedValues']=array();
							}
							if (in_array($value, $arr[$key]['allowedValues'])) {
								$arrResult[$key][$index]['newValue']=$value;
								$arrResult[$key][$index]['usable']=true;
							}
							if (!$arrResult[$key][$index]['usable']) {
								$arrResult[$key][$index]['msg']=$arr[$key]['msg'];
							}
						break;
						case 'date':
						break;
						case 'time':
						break;
						case 'datetime':
						break;
						case 'email':
						break;
						case 'tlf':
						break;
						//case '':break;
					}
				}
			} else {
				throw new Exception('Sanitize sin type para '.$arr[$key]);
			}
		} else {//no tenemos clave en el array de entrada para el dato, así que no se requiere comprobacion alguna
			$arrResult[$key][0]['newValue']=$arrValues;
			$arrResult[$key][0]['msg']="";
			$arrResult[$key][0]['usable']=true;
		}
	}
	return $arrResult;
}
*/
?>