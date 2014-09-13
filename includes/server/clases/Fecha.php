<?
class Fecha {
	private $dateUnix;

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

	public function GETdate () {return $this->dateUnix;}
	public function SETdate ($dateUnix) {$this->dateUnix=$dateUnix;}

	public function GETano () {return date("Y",$this->dateUnix);}
	public function GETmes () {return date("m",$this->dateUnix);}
	public function GETdia () {return date("d",$this->dateUnix);}
	public function GEThora () {return date("H",$this->dateUnix);}
	public function GETminuto () {return date("i",$this->dateUnix);}
	public function GETsegundo () {return date("s",$this->dateUnix);}

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
		return ($date);
	}

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
		return ($date);
	}

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
		return ($date);
	}


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

	public function toFechaES($date=NULL, $conHora=true) {
		if (is_null($date)) {
			$static = !(isset($this) && get_class($this) == __CLASS__);
			if (!$static) {
				$date=$this->dateUnix;
			}
		}
		if (!is_null($date)) {
			if ($conHora) {
				$date=date("d/m/Y H:i:s",$date);
			} else {
				$date=date("d/m/Y",$date);
			}
		}
		return ($date);
	}

	//http://www.w3.org/TR/NOTE-datetime
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

	public function toAgo($desdeDate=NULL,$hastaDate=NULL,$precision=-1, $separator=', ', $divisors=NULL) {
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