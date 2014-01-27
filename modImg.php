<?php
/**
* img
*/
class modImg{
	private $urlOriginal = null;
	private $urlDestino = null;
	private $nombre = null;
	private $ext;
	private $error = false;
	private $procesada = false;
	private static $msgNoImg = "No existe ninguna imagen";
	private $mensaje;

	public function setImagen($urlOriginal = null ){
		if ($urlOriginal != null) {
			$this->urlOriginal = $urlOriginal;
			$formatosValidos = array("jpg","jpeg","png","gif");
			list($txt, $ext) = explode('.', $this->urlOriginal);
			if ( in_array($ext, $formatosValidos) ) {
				$buscar = array(" ", "/", "ñ","ó","í","ç","á","é","í","ú","Á","É","Í","Ú","Ó");
				$this->nombre = strtolower( time().substr(str_replace($buscar, "_", $txt), -1) .".".$ext);
				$this->ext = $ext;
				return $this->nombre;
			} else{
				$this->mensaje =  "¡Formato invalido!";
				$this->error = true; # true = 1
				return $this->mensaje;
			}
		} else {
			$this->mensaje = "¡Intrudusca la url de la imagen!";
			$this->error = true; # true = 1
			return $this->mensaje;
		}
	}

	public function redimensionar( $conf = array() ){
		if ( !empty($conf) && !$this->error ) {
			$ruta_imagen            = $this->urlOriginal;
			$miniatura_ancho_maximo = $conf['size'][0];
			$miniatura_alto_maximo  = $conf['size'][1];

			$info_imagen  = getimagesize($ruta_imagen);
			$imagen_ancho = $info_imagen[0];
			$imagen_alto  = $info_imagen[1];
			$imagen_tipo  = $info_imagen['mime'];


			$proporcion_imagen = $imagen_ancho / $imagen_alto;
			$proporcion_miniatura = $miniatura_ancho_maximo / $miniatura_alto_maximo;

			if ( $proporcion_imagen > $proporcion_miniatura ){
				$miniatura_ancho = $miniatura_alto_maximo * $proporcion_imagen;
				$miniatura_alto = $miniatura_alto_maximo;
			} else if ( $proporcion_imagen < $proporcion_miniatura ){
				$miniatura_ancho = $miniatura_ancho_maximo;
				$miniatura_alto = $miniatura_ancho_maximo / $proporcion_imagen;
			} else {
				$miniatura_ancho = $miniatura_ancho_maximo;
				$miniatura_alto = $miniatura_alto_maximo;
			}

			$x = ( $miniatura_ancho - $miniatura_ancho_maximo ) / 2;
			$y = ( $miniatura_alto - $miniatura_alto_maximo ) / 2;

			switch ( $imagen_tipo ){
				case "image/jpg":
				case "image/jpeg":
				$imagen = imagecreatefromjpeg( $ruta_imagen );
				break;
				case "image/png":
				$imagen = imagecreatefrompng( $ruta_imagen );
				break;
				case "image/gif":
				$imagen = imagecreatefromgif( $ruta_imagen );
				break;
			}

			$lienzo = imagecreatetruecolor( $miniatura_ancho_maximo, $miniatura_alto_maximo );
			$lienzo_temporal = imagecreatetruecolor( $miniatura_ancho, $miniatura_alto );

			imagecopyresampled($lienzo_temporal, $imagen, 0, 0, 0, 0, $miniatura_ancho, $miniatura_alto, $imagen_ancho, $imagen_alto);
			imagecopy($lienzo, $lienzo_temporal, 0,0, $x, $y, $miniatura_ancho_maximo, $miniatura_alto_maximo);
			$rutaRelativa = $conf['pathRelativo'];
			$this->urlDestino = $conf['pathRelativo']. $conf['size'][0] ."x" .$conf['size'][1] . "-" .$this->nombre;
			if (file_exists($rutaRelativa)) {
				if ( imagejpeg($lienzo,$this->urlDestino, $conf['calidad']) ) {
					$this->procesada = true;
				}
				imagedestroy($lienzo);
				imagedestroy($lienzo_temporal);
			} else {
				if ( mkdir($rutaRelativa, 0700) ) {
					if ( imagejpeg($lienzo,$this->urlDestino, $conf['calidad']) ) {
						$this->procesada = true;
						imagedestroy($lienzo);
						imagedestroy($lienzo_temporal);
					}
				} else {
					$this->mensaje = "¡Error al crear la carpeta!";
					$this->error = true; # true = 1
					return $this->mensaje;
				}
			}
		} else {
			$this->mensaje = "¡Intrudusca los parametros correctos!";
				$this->error = true; # true = 1
				return $this->mensaje;
			}
		}

		/**
		 * [getEstado description]
		 * @return boleano 1 si la imagen fue redimensionada correctamente
		 * y 0 si ocurrio algun error
		 */
		public function getEstado()
		{
			return $this->procesada;
		}

		public function getUrl(){
			if ( $this->procesada ) {
				return $this->urlDestino;
			} else {
				return self::$msgNoImg;
			}
		}
	}

	?>