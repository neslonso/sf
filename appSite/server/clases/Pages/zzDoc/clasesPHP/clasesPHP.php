<?
namespace Sintax\Pages;
use Sintax\Core\IPage;
use Sintax\Core\User;

class clasesPHP extends docHome implements IPage {
	public function __construct(User $objUsr=NULL) {
		parent::__construct($objUsr);
	}
	public function pageValida () {
		return $this->objUsr->pagePermitida($this);
		//return parent::pageValida();
	}
	public function accionValida($metodo) {
		return $this->objUsr->accionPermitida($this,$metodo);
	}
	public function title() {
		return parent::title();
	}
	public function metaTags() {
		return parent::metaTags();
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
	public function content() {
		require_once( str_replace("//","/",dirname(__FILE__)."/")."markup/content.php");
	}

	/**
	 * Analiza un fichero fuente PHP y extrae documentación
	 * @param  string $file: ruta del fichero a analizar
	 * @param  string $class: Nombre de la clase/interfaz/namespace de la que se desea extraer información o * para todas las del fichero
	 * @param  boolean $own: default true. indica si se desea restringir la información a la clase indicada o extraer información tambien de las clases ascendientes
 	 * @param  boolean $docComments: default true. indica si se desea extraer los comentarios de cada elemento
	 * @return string Fragmento de HTML con marcado de descripción del código examinado
	 */
	public static function ulClass ($file,$class="*",$own=true,$docComments=true) {
		$result='';
		$broker = new \TokenReflection\Broker(new \TokenReflection\Broker\Backend\Memory());
		$broker->processFile($file);
		$arrClases=array();
		foreach ($broker->getClasses() as $fullClassName => $objReflectionClass) {
			if ($class=="*") {
				$arrClases[]=$objReflectionClass;
			} else {
				if (
					$class==$fullClassName ||
					$class==$objReflectionClass->getShortName()
				) {
					$arrClases[]=$objReflectionClass;
				}
			}
		}
		foreach ($arrClases as $objReflectionClass) {
			if ($objReflectionClass->isInterface()) {$tipo='Interfaz';} else {$tipo='Clase';}
			$result.='<div>';
			$result.="<h4>".$tipo." ".$objReflectionClass->getShortName()."</h4>";
			//Constantes
			$arrConstantes=($own)?$objReflectionClass->getOwnConstantReflections():$objReflectionClass->getConstantReflections();
			if (count($arrConstantes)>0) {
				$result.="<h5>Constantes</h5>";
				$result.='<ul>';
				foreach ($arrConstantes as $objReflectionConstant) {
					$dc='';
					if ($docComments) {
						$objReflectionAnnotation=new \TokenReflection\ReflectionAnnotation($objReflectionConstant,$objReflectionConstant->getDocComment());
						$dc=$objReflectionAnnotation->getAnnotation(' short_description');
					}
					$result.="<li>".$objReflectionConstant->getShortName().": ".$dc."</li>";
				}
				$result.='</ul>';
			}
			//Propiedades
			$arrPropiedades=($own)?$objReflectionClass->getOwnProperties():$objReflectionClass->getProperties();
			if (count($arrPropiedades)>0) {
				$result.="<h5>Propiedades</h5>";
				$result.='<ul>';
				foreach ($arrPropiedades as $objReflectionProperty) {
					$short_description=$arrAnnotations='';
					try {
						if ($docComments) {
							$objReflectionAnnotation=new \TokenReflection\ReflectionAnnotation($objReflectionProperty,$objReflectionProperty->getDocComment());
							$short_description=$objReflectionAnnotation->getAnnotation(' short_description');
							$arrAnnotations=$objReflectionAnnotation->getAnnotations();
							unset($arrAnnotations[' short_description']);
						}
					} catch (Exception $e) {
						error_log('No se pudieron conseguir anotaciones para la propiedad: '.$objReflectionProperty->getPrettyName());
					}
					$liCssClass='';
					if (!$objReflectionProperty->isPublic()) {$liCssClass='noPublic';}
					$result.='<li class="'.$liCssClass.'">';
					$result.="<code>".$objReflectionProperty->name."</code>: ".$short_description;
					//Annotations
					$result.="<ul>";
					if (is_array($arrAnnotations) && count($arrAnnotations)>0) {
						foreach ($arrAnnotations as $anotationType => $arrAnnotationsOfType) {
							if (is_array($arrAnnotationsOfType)) {
								foreach ($arrAnnotationsOfType as $strAnnotation) {
									$result.="<li>";
									$result.='<em>@'.$anotationType.':</em> '.$strAnnotation;
									$result.="</li>";
								}
							} else {
								error_log ('Una de las anotaciones no es un array: '.$anotationType."::".$arrAnnotationsOfType);
							}
						}
					}
					$result.="</ul>";
					$result.="</li>";
				}
				$result.='</ul>';
			}
			//Metodos
			$arrMetodos=($own)?$objReflectionClass->getOwnMethods():$objReflectionClass->getMethods();
			if (count($arrMetodos)>0) {
				$result.="<h5>Metodos</h5>";
				$result.='<ul>';
				foreach ($arrMetodos as $objReflectionMethod) {
					//$short_description=$arrAnnotations=$arrParam=$arrReturn='';
					$short_description=$arrAnnotations='';
					try {
						if ($docComments) {
							$objReflectionAnnotation=new \TokenReflection\ReflectionAnnotation($objReflectionMethod,$objReflectionMethod->getDocComment());
							$short_description=$objReflectionAnnotation->getAnnotation(' short_description');
							$arrAnnotations=$objReflectionAnnotation->getAnnotations();
							unset($arrAnnotations[' short_description']);
						}
						//$arrParam=$objReflectionAnnotation->getAnnotation('param');
						//$arrReturn=$objReflectionAnnotation->getAnnotation('return');
					} catch (Exception $e) {
						error_log('No se pudieron conseguir anotaciones para el metodo: '.$objReflectionMethod->getShortName());
					}
					$liCssClass='';
					if (!$objReflectionMethod->isPublic()) {$liCssClass='noPublic';}
					$result.='<li class="'.$liCssClass.'">';
					$result.='<code>'.$objReflectionMethod->getShortName()."</code>: ".$short_description;
					//Annotations
					$result.='<ul>';
					if (is_array($arrAnnotations) && count($arrAnnotations)>0) {
						foreach ($arrAnnotations as $anotationType => $arrAnnotationsOfType) {
							if (is_array($arrAnnotationsOfType)) {
								foreach ($arrAnnotationsOfType as $strAnnotation) {
									$result.="<li>";
									$result.='<em>@'.$anotationType.':</em> '.$strAnnotation;
									$result.="</li>";
								}
							} else {
								error_log ('Una de las anotaciones no es un array: '.$anotationType."::".$arrAnnotationsOfType);
							}
						}
					}
					/* Annotations tratadas independientemente
					if (is_array($arrParam)) {
						foreach ($arrParam as $paramComment) {
							$result.="<li>";
							$result.='<em>@param:</em> '.$paramComment;
							$result.="</li>";
						}
					}
					if (is_array($arrReturn)) {
						foreach ($arrReturn as $returnComment) {
							$result.="<li>";
							$result.='<em>@return:</em> '.$returnComment;
							$result.="</li>";
						}
					}
					*/

					/*
					//parametros (ya incluido con las anotaciones)
					foreach ($objReflectionMethod->getParameters() as $objReflectionParameter) {
						$result.="<li>";
						$result.=$objReflectionParameter;
						$result.="</li>";
					}
					*/
					$result.='</ul>';
					$result.="</li>";
				}
				$result.='</ul>';
			}
			$result.='</div>';
		}
		return $result;
	}
}
?>
