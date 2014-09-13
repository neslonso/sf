<?
/*
Ejempls de regex
//Credit card: All major cards
'^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$'

//Credit card: American Express
'^3[47][0-9]{13}$'

//Credit card: Diners Club
'^3(?:0[0-5]|[68][0-9])[0-9]{11}$'

//Credit card: Discover
'^6011[0-9]{12}$'

//Credit card: MasterCard
'^5[1-5][0-9]{14}$'

//Credit card: Visa
'^4[0-9]{12}(?:[0-9]{3})?$'

//Credit card: remove non-digits
'/[^0-9]+/'
*/
class Cadena {
	/*
	private $cadena;

	public function __construct ($cadena) {
		$this->cadena=$cadena;
	}
	*/

	public static $pronombres=array(
		//Tónicos
		'yo', 'tu', 'usted', 'ustedes', 'el', 'ella', 'ello', 'nosotros', 'nosotras', 'vosotros', 'vosotras', 'ellos', 'ellas',
		'mi', 'conmigo', 'ti', 'contigo', 'si', 'consigo',
		//Atonos y Reflexivos
		'me', 'te', 'se', 'lo', 'la', 'le', 'se',
		'nos', 'os', 'los', 'las', 'les',
		//Posesivos
		'mio', 'tuyo', 'suyo', 'mia', 'tuya', 'suya',
		'mios', 'tuyos', 'suyos', 'mias', 'tuyas', 'suyas',
		//Demostrativos
		'este', 'ese', 'aquel', 'esta', 'esa', 'aquella', 'esto', 'eso', 'aquello',
		'estos', 'esos', 'aquellos', 'estas', 'esas', 'aquellas',
		//Relativos e Interrogativos/Exclamativos
		'que', 'cual', 'cuales', 'donde', 'quien', 'quienes', 'cuyo', 'cuya', 'cuyos', 'cuyas',
		'cuanto', 'cuanta', 'cuantos', 'cuantas',
		//Indefinidos
		'uno', 'una', 'unos', 'unas',
		'alguno', 'alguna', 'algo', 'algunos', 'algunas',
		'ninguno', 'ninguna', 'nada', 'ningunos', 'ningunas',
		'poco', 'poca', 'pocos', 'pocas',
		'escaso', 'escasa', 'escasos', 'escasas',
		'mucho', 'mucha', 'muchos', 'muchas',
		'demasiado', 'demasiada', 'demasiados', 'demasiadas',
		'todo', 'toda', 'todos', 'todas',
		'varios', 'varias',
		'otro', 'otra', 'otros', 'otras',
		'mismo', 'misma', 'mismos', 'mismas',
		'tan', 'tanto', 'tanta', 'tantos', 'tantas',
		'alguien', 'nadie',
		'cualquiera', 'cualesquiera',
		'quienquiera', 'quienesquiera',
		'demas',
	);

	public static $articulos=array(
		'el', 'la', 'los', 'las',
		'un', 'una', 'unos', 'unas',
		'lo', 'al', 'del'
	);

	public static $preposiciones=array(
		'a', 'ante', 'bajo', 'cabe', 'con', 'contra', 'de', 'desde','durante',
		'en', 'entre', 'hacia', 'hasta', 'mediante', 'para', 'por', 'pro',
		'según', 'sin', 'so', 'sobre', 'tras', 'vía'
	);

	//eliminar las locuciones, estos arrays deben contener solo palabras
	public static $conjunciones=array(
		//Copulativas
		'y', 'e', 'ni',
		//Adversativas
		'mas', 'pero', 'sino', 'sino que',
		//Disyuntivas
		'o', 'u',
		//Causales
		'porque', 'pues', 'puesto que',
		//Condicionales
		'si', 'con tal que', 'siempre que',
		//Concesivas
		'aunque', 'si bien', 'asi', 'por lo tanto',
		//Comparativas
		'como', 'tal como',
		//Consecutivas
		'tan', 'tanto que', 'asi que',
		//Temporales
		'cuando', 'antes que',
		//Finales
		'para que', 'a fin de que'
	);

	public static $adverbios=array(
		//Lugar
		'aqui', 'ahi', 'alli', 'cerca', 'lejos', 'arriba', 'abajo', 'alrededor', 'dentro', 'fuera',
		//Tiempo
		'ahora', 'luego', 'después', 'ayer', 'hoy', 'mañana', 'entonces', 'pronto', 'tarde', 'siempre',
		//Modo
		'bien', 'mal', 'así', 'aprisa', 'deprisa', 'despacio',
		//Cantidad
		'mas', 'menos', 'poco', 'mucho', 'bastante', 'muy', 'casi',
		//Afirmación
		'si', 'tambien', 'cierto',
		//Negación
		'no', 'tampoco', 'nunca',
		//Duda
		'quizas', 'tal vez', 'acaso'
	);

	public static $verbosAuxiliares=array(

	);

	public static $palabrasTematicas=array(
		'artículo', 'ley', 'proyecto', 'ámbito', 'aplicación',
		'gobierno', 'españa',
		//Numeros romanos
		'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X',
		//Simbolos
		'º', 'ª', '-', '—'
	);

	public function palabrasGramaticales() {
		return array_merge(Cadena::$pronombres,Cadena::$articulos,Cadena::$preposiciones,Cadena::$conjunciones,Cadena::$verbosAuxiliares);
	}

	public function densidad($texto,$maxPalabras=10) {
		$result=$texto;
		/*
		$utf8=false;
		if (mb_detect_encoding($texto, 'UTF-8', true)) {
			$utf8=true;
			$texto=utf8_decode($texto);
		}
		*/

		// I
		// str_word_count($str,1) - returns an array containing all the words found inside the string
		$words = str_word_count(mb_strtolower($texto),1,'áéíóúüñü');

		$numWords = count($words);

		// array_count_values() returns an array using the values of the input array as keys and their frequency in input as values.
		$word_count = (array_count_values($words));
		arsort($word_count);
		$result=array();
		$i=0;
		$radioContexto=5;
		foreach ($word_count as $key=>$val) {
			//if ($utf8) {$key=utf8_encode($key);	}
			$density=($val/$numWords)*100;
			$result[$i]->palabra=$key;
			$result[$i]->veces=$val;
			$result[$i]->densidad=$density;
			$i++;
			if ($i>=$maxPalabras) {break;}
		}
		return $result;
	}

	public static function eliminaPalabras ($texto, $arrPalabras) {
		/*
		$utf8=false;
		//si el texto viene en UTF8 lo pasamos a latin-1 para que funcione bien la regex.
		//teoricamente la regex debería funcionar sobre unicode añadienle el modificador u,
		//pero si lo pongo el script tarda más de 30 seg y el server lo corta
		if (mb_detect_encoding($texto, 'UTF-8', true)) {
			$utf8=true;
			$texto=utf8_decode($texto);
		}
		*/

		/*
		// I
		//Con esta técnica, buscamos palabras en el texto y
		//las reemplazamos con el resultado del callback.
		//Todo lo que no se considere palabra quedará igual,
		$callback='eliminaPalabrasCallback';
		//$regex="!\w+!u";
		$regex="!\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*!u";
		$result=preg_replace_callback($regex, $callback, $texto);
		//if ($utf8) {$result=utf8_encode($result);}
		// I
		*/

		/*
		// II
		//Con esta técnica extraemos las palabras del texto
		//y creamos uno nuevo con las palabras que queramos.
		//Nada que no se considera palabra estará en el nuevo texto.
		$regex="!\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*!u";
		preg_match_all($regex, $texto, $matches);
		$words=$matches[0];
		$words=array_map('mb_strtolower',$matches[0]);
		*/

		// III
		// Con las dos técnicas anteriores el script peta
		// pq tarda más de 30 seg en un texto de 600K (93000 Palabras).
		// usando str_word_count, no se como lo hace, pero
		// lo hace bien y en menos de 30 seg.
		// str_word_count($str,1) - returns an array containing all the words found inside the string
		$words = str_word_count(mb_strtolower($texto),1,'áéíóúüñü');

		$result='';
		$arrPalabras=array_map('mb_strtolower',$arrPalabras);

		foreach ($words as $word) {
			if (mb_strlen($word)>3) {
				$result.= (in_array($word,$arrPalabras))?"":$word;
				$result.= ' ';
			}
		}
		// II
		return $result;
	}


	/*No UTF-8 Aware*/
	static function resumeTexto($texto,$len) {
		$texto=strip_tags ($texto);
		return (strlen ($texto)>$len)?substr($texto,0,$len-3)."...":$texto;
	}


	/*No UTF-8 Aware*/
	/*
	static function resumeHtml ($html, $len=15) {
		error_log($html);
		error_log(html_entity_decode($html));
		$html=html_entity_decode($html);
		$result=$html;
		$lenText=strlen(strip_tags($html));
		if ($lenText>$len) {
			$lenResumen=$lenText-$len;
			//Recorremos la cadena del final al principio y quitamos solo caracteres que no pertenezcan a etiquetas HTML
			$i=0;//Contamos el nuemro de letras que hemos resumido
			$arrResumen=array();
			$resumible=true;
			foreach (array_reverse(str_split($html)) as $letra) {
				switch ($letra) {
					case ">":
						$resumible=false;
						break;
					case "<":
						$resumible=true;
						break;
				}
				if ((!$resumible) || ($letra=="<")) {
					array_push($arrResumen,$letra);
				} else {
					if ($lenText-$i>$len) {
						$i++;
					} else {
						array_push($arrResumen,$letra);
					}
					//echo "$i .- ";
				}
			}
			$result="";
			$arrResumen=array_reverse($arrResumen);
			foreach ($arrResumen as $letra) {
				$result.=$letra;
			}
			$result=trim($result)."...";
		}
		return $result;
	}
	/**/

	/*UTF-8 Aware*/
	//TODO: Mejora: Para que sea mas rápida se puede intentar con regexp, ver Ejemplo 2 de preg_match_all en http://es.php.net/manual/es/function.preg-match-all.php
	public static function resumeHtml ($html, $len=15) {
		$html=html_entity_decode($html,ENT_QUOTES,"UTF-8");
		$result=$html;
		$lenTotal=mb_strlen($html);
		$lenText=mb_strlen(strip_tags($html));
		if ($lenText>$len) {
			$lenResumen=$lenText-$len;
			//Recorremos la cadena del final al principio y quitamos solo caracteres que no pertenezcan a etiquetas HTML
			$i=0;//Contamos el numero de letras que hemos resumido
			$arrResumen=array();
			$resumible=true;
			$aux=0;
			while(abs($aux)<$lenTotal) {
				$aux-=1;
				$letra=mb_substr($html,$aux,1);
				switch ($letra) {
					case ">":
						$resumible=false;
						break;
					case "<":
						$resumible=true;
						break;
				}
				if ((!$resumible) || ($letra=="<")) {
					array_push($arrResumen,$letra);
				} else {
					if ($lenText-$i>$len) {
						$i++;
					} else {
						array_push($arrResumen,$letra);
					}
					//echo "$i .- ";
				}
			}
			$result="";
			$arrResumen=array_reverse($arrResumen);
			foreach ($arrResumen as $letra) {
				$result.=$letra;
			}
			$result=trim($result)."...";
		}
		//return $result;
		$result=preg_replace('/(<br>)+$/', '', $result);
		$result=preg_replace('/(<br \/>)+$/', '', $result);
		return Cadena::stripEmptyTags($result);
	}
	/**/

	public static function toOneCodeLine ($str,$nl2br=true) {
		//error_log ($str);
		if ($nl2br) {
			$str=nl2br($str);
		}
		$str = str_replace("\r\n","",$str);
		$str = str_replace("\r","",$str);
		$str = str_replace("\n","",$str);
		return $str;
	}
	public static function toOneLine ($str) {
		return self::toOneCodeLine($str);
	}

	public static function toEsNumber($str) {
		if (is_numeric($str)) {
			$str=number_format($str,2,',',".");
		}
		return $str;
	}

	public static function pwParseCorreo($var, $urlBase=".", $noBRs=false) {
		/*Esta funcion parsea un texto sustituyendo determinados tokens
		por codigo HTML, los enlaces se hacen con URL absoluta pq el texto
		sale por email*/

			//$res = isset($var) ? nl2br(htmlSpecialChars(stripslashes(strip_tags($var)))) : "";
			//$res = isset($var) ? nl2br(htmlSpecialChars(stripslashes($var))) : "";
			$res=$var;
			$res=stripslashes($res);
			if (!$noBRs) {$res = nl2br($res);}
			$res = ereg_replace("<br /><br />", "<br />",$res);//Para eliminar un posible exceso de <br />s
			$res = ereg_replace("  ", " &nbsp;",$res);//Para respetar el esceso de espacios que tenga el texto
			//$res = ereg_replace("\t", " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ",$res);
			$res = ereg_replace("(([A-Za-z0-9.\-])*)@([a-zA-Z0-9\.]*)([a-zA-Z0-9])", "<a class=enlace href=\"mailto:\\0\">\\0</a>",$res);
			$res = ereg_replace("\(#\)", "<br />&bull;",$res);
			$res= ereg_replace('negrita\(([^)]*))', '<b>\\1</b>',$res);
			$res= ereg_replace("cursiva\(([^)]*))", "<i>\\1</i>",$res);
			$res= ereg_replace("subr\(([^)]*))", "<u>\\1</u>",$res);
			$res = ereg_replace("enlaceI\(([^,]*),([^)]*),([^)]*))", "<a class=enlace href=\"".$urlBase."/index.php?clase=\\2&id=\\3\">\\1</a>",$res);
			$res = ereg_replace("enlaceE\(([^,]*),([^)]*))", "<a class=enlace target=\"_blank\" href=\"http://\\2\">\\1</a>",$res);
			//$res = ereg_replace("www.(([A-Za-z0-9.\-])*)([a-zA-Z0-9])", "<a class=enlace target=_new href=\"\\0\">\\0</a>",$res);
			/**/
			return $res;
	}

	public static function random_color($minLuminosity=0,$maxLuminosity=255){
		mt_srand((double)microtime()*1000000);
		do {
			$r=mt_rand(0, 255);
			$g=mt_rand(0, 255);
			$b=mt_rand(0, 255);

			$luminosity=sqrt(($r*$r*0.299) + ($g*$g*0.587) + ($b*$b*0.114));
			//luminosity no es la componente V de HSV ni la componente L de HSL,
			//ver HSP Color Model — Alternative to HSV (HSB) and HSL (http://alienryderflex.com/hsp.html)
			//luminosity es un valor entre 0 y 255 que representa el gris correspondiente al color dado
		} while (($luminosity<$minLuminosity) || ($luminosity>$maxLuminosity));

		$r=sprintf("%02X",$r);
		$g=sprintf("%02X",$g);
		$b=sprintf("%02X",$b);

		return "#".$r.$g.$b;
	}

	public static function charNewGetVar ($URL) {
		return (strstr ($URL,'?')==false)?"?":"&";
	}

	public static function toUrlString ($str) {
		/*$arrPalabras=array(".",":",",",";","%","/","\\","(",")","+","º","ª",
						"2ª",
						"a","ante","bajo","cabe","con","contra","de","desde","en","entre","hacia","por","para","segun","sin","so","sobre","tras",
						"en","la","al",
						"10","15","20","25","30","40","50","60","70","80","90","100","125","150","200","250","300","400","500","600","700","750","800","900",
						"10ml","15ml","20ml","25ml","30ml","40ml","50ml","60ml","70ml","80ml","90ml","100ml","125ml","150ml","200ml","250ml","300ml","400ml","500ml","600ml","700ml","750ml","800ml","900ml",
						"10gr","15gr","20gr","25gr","30gr","40gr","50gr","60gr","70gr","80gr","90gr","100gr","125gr","150gr","200gr","250gr","300gr","400gr","500gr","600gr","700gr","750gr","800gr","900gr",
						"un","ml","gr","cm","mg",
						"un.","ml.","gr.","cm.","mg.",
						"spf","fps","caps","comp","comprimido","comprimidos","sobres","frascos",
						"ahora","compra","compras","tiene","tienen","regalo","regalos","duplo","ahorro","descuento","oferta","gratis",
						"lote","pack","unidad");
		$str=self::eliminaPalabras($str, $arrPalabras);*/
		$str=str_replace(array('á','é','í','ó','ú','ñ','Á','É','Í','Ó','Ú','Ñ','ü','Ü'),
						 array('a','e','i','o','u','n','A','E','I','O','U','N','u','U'),$str);
		$str=str_replace(array('%'),
						 array(''),$str);
		$str=trim($str);
		$str=str_replace(" ","-",$str);
		$str=str_replace("\r\n","",$str);
		$str=str_replace("\r","",$str);
		$str=str_replace("\n","",$str);
		return $str;
	}

	public static function stripEmptyTags($str) {
		//$html = "abc<p></p><p>dd</p><b>non-empty</b>";
		//$pattern = "/<p[^>]*><\\/p[^>]*>/"; //elimina solo tags p
		$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/"; //use this pattern to remove any empty tag

		return preg_replace($pattern, '', $str);
		// output
		//abc<p>dd</p><b>non-empty</b>
	}

	public static function highlight($termsToHighlight,$str,$cssClass="ui-state-higlight") {
		if (is_array($termsToHighlight)) {
			$arrTerms=$termsToHighlight;
		} else {
			$arrTerms=preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/',$termsToHighlight, null, PREG_SPLIT_NO_EMPTY);
		}
		foreach ($arrTerms as $term) {
			//$str=str_ireplace($term, '<span class="'.$cssClass.'">'.$term.'</span>', $str);
			$term=preg_quote($term);
			$term=preg_replace('/[aàáâãåäæ]/iu', '[aàáâãåäæ]', $term);
			$term=preg_replace('/[eèéêë]/iu', '[eèéêë]', $term);
			$term=preg_replace('/[iìíîï]/iu', '[iìíîï]', $term);
			$term=preg_replace('/[oòóôõöø]/iu', '[oòóôõöø]', $term);
			$term=preg_replace('/[uùúûü]/iu', '[uùúûü]', $term);

			$regex='/'.$term.'(?![^<]*>)'.'/iu';//(?![^<]*>) es una expresion look ahead negativa
			//$GLOBALS['firephp']->info($regex,"Cadena::highlight");
			$str=preg_replace($regex, '<span class="'.$cssClass.'">$0</span>', $str);
		}
		return $str;
	}

	public static function toLocalNumber($number,$minDecimales=2,$maxDecimales=NULL,$locale="es_ES.utf8") {
		//tuve que yum install php-intl para que php tenga la calse  NumberFormatter
		if (is_null($maxDecimales)) {$maxDecimales=$minDecimales;}
		$numberFormatter = new NumberFormatter($locale, \NumberFormatter::DECIMAL);
		$numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $minDecimales);
		$numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $maxDecimales); // by default some locales got max 2 fraction digits, that is probably not what you want
		return $numberFormatter->format($number);
	}

	public static function generatePassword ($length=8) {
		// start with a blank password
		$password = "";
		// define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyz";
		$i = 0;
		// add random characters to $password until $length is reached
		while ($i < $length) {
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}


	/*
	function utf8_substr($str,$from,$len) {
		# utf8 substr
		# www.yeap.lv
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
			'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
			'$1',$str);
	}
	*/
}

/*
function eliminaPalabrasCallback($matches) {
	$listaNegra=array_merge(Cadena::palabrasGramaticales(),Cadena::$verboAuxiliar,Cadena::$palabrasTematicas);
	$listaNegra=array_map('strtolower',$listaNegra);
	//$replace = (strlen($matches[0])<3)?"":$matches[0];//No eliminamos los matches de menos de 3 caracteres
	$replace = (in_array(strtolower($matches[0]),$listaNegra))?"":$matches[0];
	if (strstr($matches[0],'bito')) {
		error_log ("MATCH: -".strtolower($matches[0])."- :: _".strtolower('ª')."_");
		if ($replace=="") {error_log ("SUPRIMIDA");}
	}
	return $replace;
}
*/

/*
* Explications du masque pour preg_match_all.
*
* La fonction str_word_count standard considère qu'un mot est
* une séquence de caractères qui contient tous les caractères
* alphabétiques, et qui peut contenir, mais pas commencer
* par "'" et "-".
*
* Avec Unicode et UTF-8, une lettre peut être un caractères
* ASCII non accentué tel que "e" ou "E", mais aussi un "é" ou
* un "É", lequel peut se représenter sous la forme de deux
* caractères : d'abord le "E" non accentué, puis l'accent tout
* seul. Une lettre "E" ou "É" fait partie de la classe « L »,
* un accent de la classe « Mn ».
*
* Par ailleurs, "-" n'est plus le seul trait d'union possible.
* Plutôt que de les lister individuellement, j'ai choisi de
* tester les caractères de la classe « Pd ». Un inconvénient
* est que cela inclut aussi le tiret cadratin et d'autres,
* mais cet inconvénient existait déjà avec str_word_count et
* le tiret ascii, et en outre il ne concerne pas le français
* (contrairement à l'anglais, il y a toujours des espaces
* autour de ces tirets).
*
* Enfin, "'" n'est pas non plus la seule apostrophe possible.
* Mais contrairement aux tirets je teste juste l'apostrophe
* typographique U+2019 à part au lieu de tester la classe « Pf »
* car cette dernière contient trop de signes de ponctuation
* à exclure de la définition d'un mot.
*
* Un mot commence donc par une lettre \p{L}, éventuellement
* accentuée (suivie par un nombre quelconque de \p{Mn}), et
* ensuite on peut rencontrer un nombre quelconques d'autres
* lettres (\p{L} et \p{Mn}), de tirets (\p{Pd}) ou d'apostrophes
* (' et \x{2019}). Tout ceci, bien sûr, dans un masque compatible
* avec UTF-8 (/u à la fin).
*
* Pour les références, voir :
* http://fr2.php.net/manual/fr/regexp.reference.php #regexp.reference.unicode
* http://fr2.php.net/manual/fr/reference.pcre.pattern.modifiers.php
*/
define("WORD_COUNT_MASK", "/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u");
?>