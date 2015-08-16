<?
class Fecha {
	/**
	 * Fecha en formato unix (segundos desde 01/01/1970)
	 * @var integer
	 */
	private $dateUnix;
	/**
	 * Crea un objeto fecha a partir de un valor de fecha y una indicación del formato en el que es suministrado el valor
	 * o, si es llamado sin parametros, crea un objeto representando la fecha actual
	 * @param string | integer | NULL $date valor de fecha inicial del objeto
	 * @param string $tipo tipo de valor de fecha suministrado (MySQL | FechaES | FechaEN)
	 */
	public function __construct ($date=NULL,$tipo=NULL) {
		if (is_null($date) && is_null($tipo)) {$date=time();}
		switch ($tipo) {
			case "mysql":
			case "mySQL":
			case "MySQL": $this->dateUnix=$this->fromMysql($date);break;
			case "FechaES": $this->dateUnix=$this->fromFechaES($date);break;
			case "FechaEN": $this->dateUnix=$this->fromFechaEN($date);break;
			default: $this->dateUnix=$date;break;
		}
	}
	/**
	 * Devuelve la fecha representada como un timestamp de Unix
	 * @return integer. timestamp de Unix
	 */
	public function GETdate () {return $this->dateUnix;}
	/**
	 * Establece la fecha representada como un timestamp de Unix
	 * @param integer $dateUnix timestamp de Unix
	 */
	public function SETdate ($dateUnix) {$this->dateUnix=$dateUnix;}
	/**
	 * Devuelve el año con 4 digitos
	 * @return integer Año.
	 */
	public function GETano () {return date("Y",$this->dateUnix);}
	/**
	 * Devuelve el numero de mes con dos digitos
	 * @return integer Mes (01 - 12).
	 */
	public function GETmes () {return date("m",$this->dateUnix);}
	/**
	 * Devuelve el numero de día con dos digitos
	 * @return integer Día (01 - 31).
	 */
	public function GETdia () {return date("d",$this->dateUnix);}
	/**
	 * Devuelve la hora dos digitos, formato 24 horas
	 * @return integer Hora (00 - 23).
	 */
	public function GEThora () {return date("H",$this->dateUnix);}
	/**
	 * Devuelve el minuto con dos digitos
	 * @return integer Hora (00 - 59).
	 */
	public function GETminuto () {return date("i",$this->dateUnix);}
	/**
	 * Devuelve el segundo con dos digitos
	 * @return integer Hora (00 - 59).
	 */
	public function GETsegundo () {return date("s",$this->dateUnix);}
	/**
	 * Crea un objeto fecha a partir de una fecha MySQL (AAAA-MM-DD HH-MM-SS / AAAAMMDDHHMMSS)
	 * @param  string $date fecha en formato MySQL
	 * @return object self Instancia de self
	 */
	public static function fromMysql ($date) {
		if (empty($date)) {$date=NULL;}
		if (!is_null($date)) {
			$contieneSeparadores=(ereg("[^0-9]",$date))?true:false;
			if ($contieneSeparadores) {
				//Recibimos un timestamp de mysql (AAAA-MM-DD HH-MM-SS)
				$anio=substr ($date,0,4);
				$mes=substr ($date,5,2);
				$dia=substr ($date,8,2);
				$hora=substr ($date,11,2);
				$minuto=substr ($date,14,2);
				$segundo=substr ($date,17,2);

			} else {
				//Recibimos un timestamp de mysql (AAAAMMDDHHMMSS)
				$anio=substr ($date,0,4);
				$mes=substr ($date,4,2);
				$dia=substr ($date,6,2);
				$hora=substr ($date,8,2);
				$minuto=substr ($date,10,2);
				$segundo=substr ($date,12,2);
			}
			$date=mktime ($hora, $minuto, $segundo, $mes, $dia, $anio);
		}
		//return ($date);
		return (new self ($date));
	}
	/**
	 * Crea un objeto fecha a partir de una fecha en formato español (DD/MM/YYYY HH:MM:SS), con o sin hora.
	 * Se adminte / o - como separador de fecha y : como separador de hora
	 * @param  string $date fecha en formato español
	 * @return object self Instancia de self
	 */
	public static function fromFechaES ($date) {
		//Recibimos una fecha con formato ES (DD/MM/YYYY HH:MM:SS)
			//Dias, meses, horas, minutos y segundo pueden tener 1 o 2 cifras
			//años pueden tener 2 o 4 cfiras.
			//El separador de fecha es / o - y el de hora :
 		if (empty($date)) {$date=NULL;}
		if (!is_null($date)) {
			//"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"
			//	-> Captura fecha y hora o solo fecha (En caso de solo fecha los indices de arrPreg correspondientes a la Hora viene vacios)
			preg_match_all( "#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#", $date, $arrPreg);
			$anio=$arrPreg[3][0];
			$mes=$arrPreg[2][0];
			$dia=$arrPreg[1][0];
			$hora=(is_numeric($arrPreg[4][0]))?$arrPreg[4][0]:0;
			$minuto=(is_numeric($arrPreg[5][0]))?$arrPreg[5][0]:0;
			$segundo=(is_numeric($arrPreg[6][0]))?$arrPreg[6][0]:0;
			$date=mktime ($hora, $minuto, $segundo, $mes, $dia, $anio);
		}
		//return ($date);
		return (new self ($date));
	}
	/**
	 * Crea un objeto fecha a partir de una fecha en formato inglés (MM/DD/YYYY HH:MM:SS), con o sin hora.
	 * Se adminte / o - como separador de fecha y : como separador de hora
	 * @param  string $date fecha en formato inglés
	 * @return object self Instancia de self
	 */
	public static function fromFechaEN ($date) {
		//Recibimos una fecha con formato EN (MM/DD/YYYY HH:MM:SS)
			//Dias, meses, horas, minutos y segundo pueden tener 1 o 2 cifras
			//años pueden tener 2 o 4 cfiras.
			//El separador de fecha es / o - y el de hora :
 		if (empty($date)) {$date=NULL;}
		if (!is_null($date)) {
			//"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"
			//	-> Captura fecha y hora o solo fecha (En caso de solo fecha los indices de arrPreg correspondientes a la Hora viene vacios)
			preg_match_all( "#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#", $date, $arrPreg);
			$anio=$arrPreg[3][0];
			$mes=$arrPreg[1][0];
			$dia=$arrPreg[2][0];
			$hora=(is_numeric($arrPreg[4][0]))?$arrPreg[4][0]:0;
			$minuto=(is_numeric($arrPreg[5][0]))?$arrPreg[5][0]:0;
			$segundo=(is_numeric($arrPreg[6][0]))?$arrPreg[6][0]:0;
			$date=mktime ($hora, $minuto, $segundo, $mes, $dia, $anio);
		}
		//return ($date);
		return (new self ($date));
	}
	/**
	 * Devuelve la fecha en formato MySQL,
	 * @param  string $date timestamp de unix a convertir. Si no se especifica se utiliza el contenido en la instancia utilizada para invocar el metodo
	 * @param  boolean $conSeparadores Indica si se desea obtenre la fecha con separadores (- para la fecha, : para la hora)
	 * @param  boolean $conHora Indica si se desea incorporar la hora en la fecha devuelta
	 * @return string timestamp de MySQL
	 */
	public function toMysql($date=NULL,$conSeparadores=false,$conHora=true) {
		if (is_null($date)) {
			$static = !(isset($this) && get_class($this) == __CLASS__);
			if (!$static) {
				$date=$this->dateUnix;
			}
		}
		if (!is_null($date)) {
			if ($conHora) {
				if ($conSeparadores) {
					$date=date("Y-m-d H:i:s",$date);
				} else {
					$date=date("YmdHis",$date);
				}
			} else {
				if ($conSeparadores) {
					$date=date("Y-m-d",$date);
				} else {
					$date=date("Ymd",$date);
				}
			}
		}
		return ($date);
	}
	/**
	 * Devuelve la fecha en formato español,
	 * @param  string $date timestamp de unix a convertir. Si no se especifica se utiliza el contenido en la instancia utilizada para invocar el metodo
	 * @param  boolean $conHora Indica si se desea incorporar la hora en la fecha devuelta
	 * @return string fecha en formato español (DD/MM/AAA HH:MM:SS)
	 */
	public function toFechaES($conHora=true) {
		$date=$this->dateUnix;
		if ($conHora) {
			$date=date("d/m/Y H:i:s",$date);
		} else {
			$date=date("d/m/Y",$date);
		}
		return ($date);
	}
	/**
	 * Devuelve la fecha en formato W3C,
	 * @param  string $date timestamp de unix a convertir. Si no se especifica se utiliza el contenido en la instancia utilizada para invocar el metodo
	 * @param  boolean $conHora Indica si se desea incorporar la hora en la fecha devuelta
	 * @return string fecha en formato W3C (http://www.w3.org/TR/NOTE-datetime)
	 */
	public function toW3C($date=NULL, $conHora=true) {
		if (is_null($date)) {
			$static = !(isset($this) && get_class($this) == __CLASS__);
			if (!$static) {
				$date=$this->dateUnix;
			}
		}
		if ($conHora) {
			$date=date("Y-m-d\TH:i:sP",$date);
		} else {
			$date=date("Y-m-d",$date);
		}
		return ($date);
	}
	/**
	 * Devuelve la diferencia entre las fechas
	 * @param  integer  $desdeDate timestamp de unix, fecha inicial.
	 * Si no se especifica se utiliza la fecha contenida en la instancia utilizada para invocar el metodo
	 * @param  integer  $hastaDate timestamp de unix, fecha final, Si no se especifica se utiliza la fecha actual
	 * @param  integer $precision Número de partes a devolver, e.g. 3 devuelve '1 Año, 3 Días, 9 Horas' mientras que 2 devuelve '1 Año, 3 Días'.
	 * El valor -1 hace que se devuelvan todos las partes
	 * @param  string  $separator caracter de separación entre partes, por amisión se utiliza ', '.
	 * @return string Diferencia entre las fechas (Hace/Dentro de 1 Año, 2 Meses, 3 días, 9 Horas, 26 segundos)
	 */
	public function toAgo($desdeDate=NULL,$hastaDate=NULL,$precision=-1, $separator=', ') {
		$divisors=NULL;
		if (is_null($desdeDate)) {
			$desdeDate=$this->dateUnix;
		}
		if (is_null($hastaDate)) {
			$hastaDate=time();
		}
		$segundosDiferencia=abs($hastaDate-$desdeDate);
		$particula=($hastaDate>$desdeDate)?"Hace":"Dentro de";

		// Return the formatted interval
		return $particula." ".self::format_interval($segundosDiferencia, $precision, $separator, $divisors);
	}
	/**
	* Formats any number of seconds into a readable string
	*
	* @param int Seconds to format
	* @param string Seperator to use between divisors
	* @param int Number of divisors to return, ie (3) gives '1 Year, 3 Days, 9 Hours' whereas (2) gives '1 Year, 3 Days'
	* @param array Set of Name => Seconds pairs to use as divisors, ie array('Year' => 31536000)
	* @return string Formatted interval
	*/
	private static function format_interval($seconds, $precision = -1, $separator = ', ', $divisors = NULL) {
		// Default set of divisors to use
		if(!isset($divisors)) {
			$divisors = Array(
				'año'		=> 31536000,
				'mes'		=> 2628000,
				'día'		=> 86400,
				'hora'		=> 3600,
				'minuto'	=> 60,
				'segundo'	=> 1);
			$plurales = Array(
				'año'		=> 'años',
				'mes'		=> 'meses',
				'día'		=> 'días',
				'hora'		=> 'horas',
				'minuto'	=> 'minutos',
				'segundo'	=> 'segundos');
		}

		arsort($divisors);

		// Iterate over each divisor
		foreach($divisors as $name => $divisor) {
			// If there is at least 1 of thie divisor's time period
			if($value = floor($seconds / $divisor)) {
				// Add the formatted value - divisor pair to the output array.
				// Omits the plural for a singular value.
				if($value == 1) {
					$out[] = "$value $name";
				} else {
					//$out[] = "$value {$name}s";
					$out[] = "$value ".$plurales[$name];
				}
				// Stop looping if we've hit the precision limit
				if(--$precision == 0) break;
			}

			// Strip this divisor from the total seconds
			$seconds %= $divisor;
		}

		// FIX
		if (!isset($out)) {
			$out[] = "0" . $name;
		}
		//var_dump($out);
		// Join the value - divisor pairs with $separator between each element
		return implode($separator, $out);
	}

	/*public function __call($name, $arguments) {
		switch ($name) {
			case 'toMysql':
				if(count($arguments) === 2 ){
					return $this->toMysqlDynamic($arguments[0],$arguments[1]);
				} else if(count($arguments) === 3){
					return self::toMysqlStatic($arguments[0], $arguments[1],$arguments[2]);
				} else {
					throw new exception("Function ".$name." no tiene una version de ".count($arguments)." argumentos.");
				}
				break;
			default:
				throw new exception("Function ".$name." does not exists.");
				break;
		}
	}*/
}
?>