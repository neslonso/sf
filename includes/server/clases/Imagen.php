<?
class Imagen {
	const OUTPUT_MODE_SCALE=	0x1;		//Redimensionamos la imagen mediante la funcion resize, si falta el width o el height se calcula proporcionalmente, si estan los dos produce una imagen de width x height, aunque cambie el aspect ratio
	const OUTPUT_MODE_FIT=		0x10;		//Redimensionamos la imagen para que queda en el hueco dado por los parametros width y height, esto produce una imagen de width*X o de X*height, dependiendo de la relaccion entre los aspect ratios de la foto y el tamaño deseado
	const OUTPUT_MODE_FILL=		0x100;		//Encajamos y rellenamos con partes transparentes la imagen para que llene el hueco dado por los parametros width y height, esto produce una imagen de width x height
	const OUTPUT_MODE_ROTATE_H=	0x1000;
	const OUTPUT_MODE_ROTATE_V=	0x10000;
	const OUTPUT_MODE_CROP=		0x100000;	//Redimensionamos la imagen para que llene el hueco dado por los parametros width y height, esto produce una imagen de width x height
	const OUTPUT_MODE_PUNTEADO=	0x1000000;

	//private $imgPath;//Si la imagen se crea desde un fichero contiene su path
	private $imgData;//image resource
	private $width;
	private $height;
	private $imgType;//unos de las constantes IMAGETYPE_XXX (Ihttp://www.php.net/manual/es/image.constants.php)

	private $marcasAgua=array();//array


	public function __construct () {
	}

	public function toString ($width=NULL, $height=NULL, $outputMode=self::OUTPUT_MODE_SCALE, $format="png") {
		ob_start();
		$this->output($width,$height,$outputMode,$format,true);
		$result=ob_get_contents();
		ob_end_clean();
		return $result;
	}

	public function output ($width=NULL, $height=NULL, $outputMode=self::OUTPUT_MODE_SCALE, $format="png",$withoutHeader=false) {
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

		if (!is_null($width) || !is_null($height)) {
			$outputMode=$outputMode & (~self::OUTPUT_MODE_ROTATE_H);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_ROTATE_V);
			$outputMode=$outputMode & (~self::OUTPUT_MODE_PUNTEADO);
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

	function width() {
		return imagesx($this->imgData);
	}

	function height() {
		return imagesy($this->imgData);
	}

	function rotate($angle) {
		$im    = imagecreatetruecolor($this->width(),$this->height()); // New image
		$bg    = imagecolorallocatealpha($im, 255,255,255, 127); // Transparent Background
		imagefill($im, 0, 0 , $bg); // Fill with transparent background

 		imagecopy($im, $this->imgData,0,0,0,0,$this->width(),$this->height());

		$im = imagerotate($im,$angle,$bg); // Rotate 45 degrees and allocated the transparent colour as the one to make transparent (obviously)
		imagesavealpha($im,true); // Finally, make sure image is produced with alpha transparency

		$this->imgData = $im;
	}

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

	//Resize la imagen para que quepa en un hueco de $width*$height respetando proporciones
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

	//Fit la imagen en $width*$height y rellena lo que sobre con trasnparente para generar una imagen de $width*$height
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

	//Resize la imagen (mediante fit reverse) para que llene un hueco de $width*$height respetando proporciones y corta lo que sobre
	function crop ($width,$height) {
		$this->fit($width,$height,true);
		//Crop
		$src_x=($this->width()-$width)/2;
		$src_y=($this->height()-$height)/2;

		$new_image = imagecreatetruecolor($width, $height);
		$col=imagecolorallocatealpha($new_image,0,0,0,127);
		imagefill($new_image, 0, 0, $col);

		imagecopy($new_image, $this->imgData, 0, 0, $src_x, $src_y, $width, $height);

		$this->imgData = $new_image;
	}

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

	public function marcaAgua ($file,$mWidth=0.3,$mHeight=0.3,$position="center bottom") {
		$arr=array(
			'file' => $file,
			'mWidth' => $mWidth,
			'mHeight' => $mHeight,
			'position' => $position
		);
		array_push($this->marcasAgua, $arr);
	}

	public function fillPattern($tile) {
		if (!imagesettile($this->imgData, $tile)) {
			throw new Exception("imagesettile error", 1);
		}

		imagefilledrectangle ($this->imgData, 0, 0, $this->width(), $this->height(), IMG_COLOR_TILED);
		//$this->imgData=$tile;
	}

/* Estaticas ******************************************************************/
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

	public static function fromString ($data) {
		$objImg=new Imagen();
		$objImg->imgData=imagecreatefromstring($data);
		return $objImg;
	}

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