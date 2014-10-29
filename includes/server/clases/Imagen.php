<?
class Imagen {
	/**
	 * La imagen es redimensionanda para producir el tamaño solicitado.
	 * Si no se especifica una de las dimensiones la imagen se reescala proporcionalmente
	 */
	const OUTPUT_MODE_SCALE=	0x1;		//Redimensionamos la imagen mediante la funcion resize, si falta el width o el height se calcula proporcionalmente, si estan los dos produce una imagen de width x height, aunque cambie el aspect ratio
	/**
	 * La imagen es redimensionada para que queda en el hueco dado por los parametros ancho y alto,
	 * esto produce una imagen de ancho*X o de X*alto, dependiendo de la relaccion alto/ancho entre
	 * la imagen y el tamaño deseado
	 */
	const OUTPUT_MODE_FIT=		0x10;		//Redimensionamos la imagen para que queda en el hueco dado por los parametros width y height, esto produce una imagen de width*X o de X*height, dependiendo de la relaccion entre los aspect ratios de la foto y el tamaño deseado
	/**
	 * La imagen es redimensionada para que llene el hueco dado por los parametros ancho y alto sin
	 * perder su relación alto/ancho, esto produce una imagen del tamaño deseado, rellenada con partes
	 * transparentes para mantener la relación alto/ancho original
	 */
	const OUTPUT_MODE_FILL=		0x100;		//Encajamos y rellenamos con partes transparentes la imagen para que llene el hueco dado por los parametros width y height, esto produce una imagen de width x height
	/**
	 * Si la relación ancho/alto de la imagen original es mayor que 2, la imagen es rotada 45 grados antes de ser reescalada.
	 */
	const OUTPUT_MODE_ROTATE_H=	0x1000;
	/**
	 * Si la relación ancho/alto de la imagen original es menor que 0.5, la imagen es rotada -45 grados antes de ser reescalada.
	 */
	const OUTPUT_MODE_ROTATE_V=	0x10000;
	/**
	 * La imagen se reescala para producir una imagen de ancho*alto manteniendo las proporciones y recortando la parte sobrante
	 */
	const OUTPUT_MODE_CROP=		0x100000;	//Redimensionamos la imagen para que llene el hueco dado por los parametros width y height, esto produce una imagen de width x height
	/**
	 * Se superpone la imagen un patron de 2*2 pixeles con 3 transparentes y 1 negro
	 */
	const OUTPUT_MODE_PUNTEADO=	0x1000000;
	/**
	 * FLip horizontal
	 */
	const OUTPUT_MODE_FLIP_H=	0x10000000;
	/**
	 * Flip Vertical
	 */
	const OUTPUT_MODE_FLIP_V=	0x100000000;
	/**
	 * Identificador de recurso de imagende la imagen en tratamiento
	 * @var resource
	 */
	private $imgData;//image
	/**
	 * Ancho de la imagen
	 * @var integer
	 */
	private $width;
	/**
	 * Alto de la imagen
	 * @var [type]
	 */
	private $height;
	/**
	 * unas de las constantes IMAGETYPE_XXX (http://www.php.net/manual/es/image.constants.php)
	 * @var integer
	 */
	private $imgType;//una de las constantes IMAGETYPE_XXX (Ihttp://www.php.net/manual/es/image.constants.php)
	/**
	 * Contiene información sobre las marcas de agua a aplicar a la imagen.
	 * Estructura:
	 *  file: string. ruta de la imagen a usar como marca de agua,
	 *  mWidth: float. multiplicador de anchura de la marca de agua respecto al tamaño de la imagen. Default 0.5.
	 *  mHeight: float. multiplicador de altura de la marca de agua respecto al tamaño de la imagen. Default 0.5.
	 *  position: string. Dos palabras que indican alineación horizontal (left | right | center) y vertical (top | bottom | center)
	 * @var array
	 */
	private $marcasAgua=array();//array


	/*public function __construct () {}*/

	/**
	 * Devuelve los datos de la imagen en crudo.
	 * @param  integer $width ancho de la imagen de salida
	 * @param  integer $height alto de la imagen de salida
	 * @param  integer $outputMode combinaciones válidas de las constantes OUTPUT_MODE_*
	 * @param  string $format formato de salida de la imagen (gif | jpeg | jpg | png | wbmp)
	 * @return string Datos de la imagen
	 */
	public function toString ($width=NULL, $height=NULL, $outputMode=self::OUTPUT_MODE_SCALE, $format="png") {
		ob_start();
		$this->output($width,$height,$outputMode,$format,true);
		$result=ob_get_contents();
		ob_end_clean();
		return $result;
	}
	/**
	 * Vuelca en lka salida estandar los datos de la imagen
	 * Devuelve los datos de la imagen en crudo.
	 * @param  integer $width ancho de la imagen de salida
	 * @param  integer $height alto de la imagen de salida
	 * @param  integer $outputMode combinaciones válidas de las constantes OUTPUT_MODE_*
	 * @param  string $format formato de salida de la imagen (gif | jpeg | jpg | png | wbmp)
	 * @param  boolean $withoutHeader Si es true solo se vuelcan los datos de la imagen,
	 *  si es false se añade una cabecera Content-Type acorde al parametro formato
	 */
	public function output ($width=NULL, $height=NULL, $outputMode=self::OUTPUT_MODE_SCALE, $format="png",$withoutHeader=false) {
		$this->ensureAlpha();
		$puntear=false;
		if($outputMode & self::OUTPUT_MODE_ROTATE_H) {
			if ($this->width()/$this->height()>2) {
				$this->rotate(45);
			}
		}
		if ($outputMode & self::OUTPUT_MODE_ROTATE_V) {
			if ($this->width()/$this->height()<0.5) {
				$this->rotate(-45);
			}
		}

		if ($outputMode & self::OUTPUT_MODE_PUNTEADO) {
			$puntear=true;
		}

		if ($outputMode & self::OUTPUT_MODE_FLIP_H) {
			$this->flip(1);
		}

		if ($outputMode & self::OUTPUT_MODE_FLIP_V) {
			$this->flip(2);
		}

		if (!is_null($width) || !is_null($height)) {
			$outputMode=$outputMode & (~self::OUTPUT_MODE_ROTATE_H);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_ROTATE_V);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_PUNTEADO);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_FLIP_H);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_FLIP_V);
			switch ($outputMode) {
				case self::OUTPUT_MODE_SCALE:
					$this->resize($width,$height);
				break;
				case self::OUTPUT_MODE_FIT:
					$this->fit($width,$height);
				break;
				case self::OUTPUT_MODE_FILL:
					$this->fill($width,$height);
				break;
				case self::OUTPUT_MODE_CROP:
					$this->crop($width,$height);
				break;
				default:
					$this->resize($width,$height);
			}
		}

		if ($puntear) {
			$this->fillPattern(self::pattern4x1());
		}

		if (count($this->marcasAgua)>0) {
			foreach ($this->marcasAgua as $arrParams) {
				if (!is_null($arrParams['file']) && is_readable($arrParams['file'])) {
					$this->superponer($arrParams['file'],$arrParams['mWidth'],$arrParams['mHeight'],$arrParams['position']);
				}
			}
		}

		$outputData=$this->imgData;

		if (!$withoutHeader) {
			switch ($format) {
				case "gif":header ("Content-Type: image/gif");break;
				case "jpeg":
				case "jpg":header ("Content-Type: image/jpeg");break;
				case "png":header ("Content-Type: image/png");break;
				case "wbmp":header ("Content-Type: image/wbmp");break;
			}
		}

		switch ($format) {
			case "gif":imagegif($outputData);break;
			case "jpeg":
			case "jpg":imagejpeg($outputData);break;
			case "png":
				imagealphablending($this->imgData, false);
				imagesavealpha($this->imgData, true);
				imagepng($outputData);
				//imagepng($outputData,NULL,9,PNG_ALL_FILTERS);
			break;
			case "wbmp":imagewbmp($outputData);break;
		}
	}
	/**
	 * Devuelve la anchura en pixeles de la imagen tratado
	 */
	function width() {
		return imagesx($this->imgData);
	}
	/**
	 * Devuelve la altura en pixeles de la imagen tratado
	 */
	function height() {
		return imagesy($this->imgData);
	}
	/**
	 * Rota la imagen
	 * @param  float $angle Ángulo de rotación. Número de grados en el sentido contrario de las agujas de reloj que la imagen va a rotar.
	 */
	function rotate($angle) {
		$im    = imagecreatetruecolor($this->width(),$this->height()); // New image
		$bg    = imagecolorallocatealpha($im, 255,255,255, 127); // Transparent Background
		imagefill($im, 0, 0 , $bg); // Fill with transparent background

 		imagecopy($im, $this->imgData,0,0,0,0,$this->width(),$this->height());

		$im = imagerotate($im,$angle,$bg); // Rotate 45 degrees and allocated the transparent colour as the one to make transparent (obviously)
		imagesavealpha($im,true); // Finally, make sure image is produced with alpha transparency

		$this->imgData = $im;
	}
	/**
	 * Reescala la imagen
	 * @param  integer $width nueva anchura de la imagen
	 * @param  integer $height nueva altura de la imagen
	 */
	function resize ($width=NULL,$height=NULL) {
		if (
			(is_null($width) && is_null($height))
		) {
			throw new InvalidArgumentException('Parametros no válidos Imagen::resize');
		} elseif (is_null($height)) {
			$ratio = $width / $this->width();
			$height = $this->height() * $ratio;
		} elseif (is_null($width)) {
			$ratio = $height / $this->height();
			$width = $this->width() * $ratio;
		}
		$new_image = imagecreatetruecolor($width, $height);
		$bg    = imagecolorallocatealpha($new_image, 255,255,255, 127); // Transparent Background
		imagefill($new_image, 0, 0 , $bg); // Fill with transparent background
		imagecopyresampled($new_image, $this->imgData, 0, 0, 0, 0, $width, $height, $this->width(), $this->height());
		$this->imgData = $new_image;
	}
	/**
	 * Reescala la imagen para que quepa en un hueco de $width*$height respetando proporciones, la imagen tendrá el tamaño máximo posible para que queda en el hueco
	 * @param  integer  $width   anchura del hueco en el que debe caber la imagen
	 * @param  integer  $height  altura del hueco en el que debe caber la imagen
	 * @param  boolean $reverse Si es false la funcion hace coincidir la dimensión más grande de la imagen con la más pequeña del tamaño de destino
	 *  Si es true hace coindicir la dimensión más pequeña de la imagen con la mas grande del tamaño de destino (util para la funcion crop).
	 */
	function fit ($width,$height,$reverse=false) {
		$imgRatio = $this->height()/$this->width();
		$holderRatio=$height/$width;

		if (!$reverse) {
			if($imgRatio>$holderRatio) {
				$this->resize(NULL,$height);
			} else {
				$this->resize($width,NULL);
			}
		} else {
			if($imgRatio>$holderRatio) {
				$this->resize($width,NULL);
			} else {
				$this->resize(NULL,$height);
			}
		}
	}
	/**
	 * Fit la imagen en $width*$height y rellena lo que sobre con transparente para generar una imagen de exactamente el mismo tamaño que el hueco
	 * @param  integer  $width   anchura del hueco que debe rellenar la imagen
	 * @param  integer  $height  altura del hueco que debe rellenar la imagen
	 */
	function fill($width,$height) {
		$this->fit($width,$height);
		//Fill
		$dst_x=($width-$this->width())/2;
		$dst_y=($height-$this->height())/2;

		$new_image = imagecreatetruecolor($width, $height);
		$col=imagecolorallocatealpha($new_image,0,0,0,127);
		imagefill($new_image, 0, 0, $col);

		imagecopy($new_image, $this->imgData, $dst_x, $dst_y, 0, 0, $this->width(), $this->height());

		$this->imgData = $new_image;
	}
	/**
	 * Reescala la imagen (mediante fit reverse) para que llene un hueco de $width*$height respetando proporciones y corta lo que sobre
	 * @param  integer $width anchura del hueco que debe rellenar la imagen
	 * @param  integer $height altura del hueco que debe rellenar la imagen
	 * @param  string $position Dos palabras que indican alineación horizontal (left | right | center) y vertical (top | bottom | center)
	 */
	function crop ($width,$height,$position="center center") {
		$this->fit($width,$height,true);

		$arrPos=explode(' ', $position);
		switch (count($arrPos)) {
			case '1':
				$hPos=$arrPos[0];$vPos=$arrPos[0];break;
			case '2':
				$hPos=$arrPos[0];$vPos=$arrPos[1];break;
			default:
				$hPos="center";$vPos="center";
		}
		switch ($hPos) {
			case 'left':$src_x=0;break;
			case 'right':$src_x=$this->width()-$width;break;
			default://center
				$src_x=$this->width()/2-$width/2;
		}
		switch ($vPos) {
			case 'top':$src_y=0;break;
			case 'bottom':$src_y=$this->height()-$height;break;
			default://center
				$src_y=$this->height()/2-$height/2;
		}

		$new_image = imagecreatetruecolor($width, $height);
		$col=imagecolorallocatealpha($new_image,0,0,0,127);
		imagefill($new_image, 0, 0, $col);

		imagecopy($new_image, $this->imgData, 0, 0, $src_x, $src_y, $width, $height);

		$this->imgData = $new_image;
	}
	/**
	 * Superpone una imagen sobre la imagen tratada
	 * @param  string $file ruta de la imagen a superponer
	 * @param  float  $mWidth multiplicador de anchura de la imagen a superponer respecto al tamaño de la imagen. Default 0.5.
	 * @param  float  $mHeight multiplicador de altura de la imagen a superponer respecto al tamaño de la imagen. Default 0.5.
	 * @param  string $position Dos palabras que indican alineación horizontal (left | right | center) y vertical (top | bottom | center)
	 */
	public function superponer ($file,$mWidth=0.5,$mHeight=0.5,$position="center bottom") {
		$objImgMarca=Imagen::fromFile($file);
		$objImgMarca->fill ($this->width()*$mWidth,$this->height()*$mHeight);

		$arrPos=explode(' ', $position);
		switch (count($arrPos)) {
			case '1':
				$hPos=$arrPos[0];$vPos=$arrPos[0];break;
			case '2':
				$hPos=$arrPos[0];$vPos=$arrPos[1];break;
			default:
				$hPos="center";$vPos="center";
		}
		switch ($hPos) {
			case 'left':$xDest=0;break;
			case 'right':$xDest=$this->width()-$objImgMarca->width();break;
			default://center
				$xDest=$this->width()/2-$objImgMarca->width()/2;
		}
		switch ($vPos) {
			case 'top':$yDest=0;break;
			case 'bottom':$yDest=$this->height()-$objImgMarca->height();break;
			default://center
				$yDest=$this->height()/2-$objImgMarca->height()/2;
		}

		imagecopy($this->imgData, $objImgMarca->imgData, $xDest, $yDest,
			0, 0, $objImgMarca->width(), $objImgMarca->height());
	}
	/**
	 * Añade una marca de agua al array de amrcas de agua
	 * @param  string $file ruta de la imagen a superponer
	 * @param  float  $mWidth multiplicador de anchura de la imagen a superponer respecto al tamaño de la imagen. Default 0.5.
	 * @param  float  $mHeight multiplicador de altura de la imagen a superponer respecto al tamaño de la imagen. Default 0.5.
	 * @param  string $position Dos palabras que indican alineación horizontal (left | right | center) y vertical (top | bottom | center)
	 */
	public function marcaAgua ($file,$mWidth=0.3,$mHeight=0.3,$position="center bottom") {
		$arr=array(
			'file' => $file,
			'mWidth' => $mWidth,
			'mHeight' => $mHeight,
			'position' => $position
		);
		array_push($this->marcasAgua, $arr);
	}
	/**
	 * Rellena la imagen con un patrón
	 * @param  resource $tile Identificador de recurso de imagen de la imagen patrón
	 */
	public function fillPattern($tile) {
		if (!imagesettile($this->imgData, $tile)) {
			throw new Exception("imagesettile error", 1);
		}

		imagefilledrectangle ($this->imgData, 0, 0, $this->width(), $this->height(), IMG_COLOR_TILED);
		//$this->imgData=$tile;
	}
	/**
	 * Voltea la imagen
	 * @param  integer $mode sentido del volteo. 1:vertical, 2:Horizontal, 3:Ambos
	 */
	function flip ($mode) {
		$imgsrc=$this->imgData;
		$width=imagesx ($imgsrc);
		$height=imagesy ($imgsrc);
		$src_x=0;
		$src_y=0;
		$src_width=$width;
		$src_height=$height;

		switch ($mode) {
			case '1': //vertical
				$src_y=$height -1;
				$src_height=-$height;
			break;
			case '2': //horizontal
				$src_x=$width -1;
				$src_width=-$width;
			break;
			case '3': //both
				$src_x=$width -1;
				$src_y=$height -1;
				$src_width=-$width;
				$src_height=-$height;
			break;
		}
		$imgdest=imagecreatetruecolor ($width,$height);
		if ( imagecopyresampled ( $imgdest, $imgsrc, 0, 0, $src_x, $src_y , $width, $height, $src_width, $src_height ) ) {
			$this->imgData=$imgdest;
		}
	}
	/**
	 * Copia la imagen original a otra con canal alpha
	 */
	private function ensureAlpha() {
		$new_image = imagecreatetruecolor($this->width(), $this->height());
		$col=imagecolorallocatealpha($new_image,0,0,0,127);
		imagefill($new_image, 0, 0, $col);
		imagecopy($new_image, $this->imgData, 0, 0, 0, 0, $this->width(), $this->height());
		$this->imgData=$new_image;
	}

	/**
	 * Modifca el canal alpha de cada pixel en el porcentaje dado por $opacity
	 * @param  integer $opacity porcentaje de opacidad (0 - 100)
	 */
	public function filter_opacity( $opacity=100 ) {
		$img=$this->imgData;
		$opacity /= 100;

		//get image width and height
		$w = imagesx( $img );
		$h = imagesy( $img );

		//turn alpha blending off
		imagealphablending( $img, false );

		//find the most opaque pixel in the image (the one with the smallest alpha value)
		$minalpha = 127;
		for( $x = 0; $x < $w; $x++ ) {
			for( $y = 0; $y < $h; $y++ ) {
				$alpha = ( imagecolorat( $img, $x, $y ) >> 24 ) & 0xFF;
				if( $alpha < $minalpha ) { $minalpha = $alpha; }
			}
		}
		//loop through image pixels and modify alpha for each
		for( $x = 0; $x < $w; $x++ ) {
			for( $y = 0; $y < $h; $y++ ) {
				//get current alpha value (represents the TANSPARENCY!)
				$colorxy = imagecolorat( $img, $x, $y );
				$alpha = ( $colorxy >> 24 ) & 0xFF;
				//calculate new alpha
				if( $minalpha !== 127 ) { $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha ); }
				else { $alpha += 127 * $opacity; }
				//get the color index with new alpha
				$alphacolorxy = imagecolorallocatealpha( $img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
				//set pixel with the new color + opacity
				if( !imagesetpixel( $img, $x, $y, $alphacolorxy ) ) { return false; }
			}
		}
		$this->imgData=$img;
	}

/* Estaticas ******************************************************************/
	/**
	 * Crea una instancia de la clase a partir de un fichero
	 * @param  string $path ruta del fichero
	 * @return Imagen Instancia de la clase Imagen
	 */
	public static function fromFile ($path) {
		$objImg=new Imagen();

		$objImg->imgData=NULL;

		if (file_exists($path)) {
			$image_info = getimagesize($path);
			$objImg->width=$image_info[0];
			$objImg->height=$image_info[1];
			$objImg->imgType = $image_info[2];

			switch ($objImg->imgType) {
				case IMAGETYPE_GIF:$objImg->imgData=imagecreatefromgif($path);break;
				case IMAGETYPE_JPEG:$objImg->imgData=imagecreatefromjpeg($path);break;
				case IMAGETYPE_PNG:
				case IMAGETYPE_JPEG2000:$objImg->imgData=imagecreatefrompng($path);break;
				case IMAGETYPE_WBMP :$objImg->imgData=imagecreatefromwbmp($path);break;
			}
			if (!$objImg->imgData) {
				throw new InvalidArgumentException('Error al cargar el fichero  "'.$path.'". Tipo utilizado: '.$objImg->imgType);
			}
		} else {
			throw new InvalidArgumentException('No se encontró el fichero "'.$path.'"');
		}

		return $objImg;
	}
	/**
	 * Crea una instancia de la clase a partir de los datos en crudo de la imagen
	 * @param  string $data datos de la imagen
	 * @return Imagen Instancia de la clase Imagen
	 */
	public static function fromString ($data) {
		$objImg=new Imagen();
		$objImg->imgData=imagecreatefromstring($data);
		return $objImg;
	}
	/**
	 * Crea un patrón de 2*2 pixeles, con la esquina superior izquierda negra y el resto transparente
	 * @return resource identificador de recurso de imagen
	 */
	public static function pattern4x1 () {
		$tile = imagecreatetruecolor(2, 2);
		imagealphablending($tile, true);
		imagefill($tile, 0, 0, IMG_COLOR_TRANSPARENT);


		imagesetpixel($tile, 0,0, imagecolorallocatealpha($tile, 0, 0, 0, 0));
		/*
		$blanco = imagecolorallocatealpha($tile, 255, 255, 255, 0);
		imagesetpixel($tile, 0,1, $blanco);
		imagesetpixel($tile, 1,0, $blanco);
		imagesetpixel($tile, 1,1, $blanco);
		*/
		return $tile;
	}

}

/*
function load($filename) {

      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {

         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {

         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {

         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image,$filename);
      }
      if( $permissions != null) {

         chmod($filename,$permissions);
      }
*/

/* Constructor obsoleto
*
	public function __construct ($imgPath=NULL) {
		$this->imgPath=$imgPath;
		$this->imgData=NULL;
		if (!is_null($imgPath)) {
			if (file_exists($this->imgPath)) {
				$partesRuta=pathinfo($this->imgPath);
				switch (strtolower($partesRuta['extension'])) {
					case "gif":$this->imgData=imagecreatefromgif($this->imgPath);break;
					case "jpeg":
					case "jpg":$this->imgData=imagecreatefromjpeg($this->imgPath);break;
					case "png":
						$this->imgData=imagecreatefrompng($this->imgPath);
					break;
					case "wbmp":$this->imgData=imagecreatefromwbmp($this->imgPath);break;
				}
				if (!$this->imgData) {
					throw new InvalidArgumentException('Error al cargar el fichero  "'.$this->imgPath.'". Tipo utilizado: '.strtolower($partesRuta['extension']));
				}
			} else {
				throw new InvalidArgumentException('No se encontró el fichero "'.$this->imgPath.'"');
			}
		}
	}
/**/
?>