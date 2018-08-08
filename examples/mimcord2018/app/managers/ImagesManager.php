<?php

class ImagesManager {


	public function hydrate_one($id){
			$image =  new Timber\Image($id);
			return $image;
	}

	public function add_admin_actions(){

		//	add_action('edit_attachment', array('ImagesManager','remove_empty_meta'));

	}


	public function fit_image($src, $w, $h = 0) {
			// Instantiate TimberImage from $src so we have access to dimensions
			$img = new TimberImage($src);

			// If the image is smaller on both width and height, return original
			if ($img->width() <= $w && $img->height() <= $h) {
				return $src;
			}

			// Compute aspect ratio of target box
			$aspect = $w / $h;

			// Call proportional resize on width or height, depending on how the image's
			// aspect ratio compares to the target box aspect ratio
			if ($img->aspect() > $aspect) {
				return Timber\ImageHelper::resize($src, $w);
			} else {
				return Timber\ImageHelper::resize($src, 0, $h);
			}
		}



	public static function base64($path){
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}


	public static function blur64($path,$width,$height){

		$blur = intval($width/50);
		if($blur < 1){
			$blur = 1;
		} else if($blur > 20){
			$blur = 20;
		}


		$svg = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%0A%20%20%20%20%20xmlns%3Axlink%3D%22http%3A//www.w3.org/1999/xlink%22%0A%20%20%20%20%20width%3D%22".$width."%22%20height%3D%22".$height."%22%0A%20%20%20%20%20viewBox%3D%220%200%20".$width."%20".$height."%22%3E%0A%20%20%3Cfilter%20id%3D%22blur%22%20filterUnits%3D%22userSpaceOnUse%22%20color-interpolation-filters%3D%22sRGB%22%3E%0A%20%20%20%20%3CfeGaussianBlur%20stdDeviation%3D%22".$blur."%20".$blur."%22%20edgeMode%3D%22duplicate%22%20/%3E%0A%20%20%20%20%3CfeComponentTransfer%3E%0A%20%20%20%20%20%20%3CfeFuncA%20type%3D%22discrete%22%20tableValues%3D%221%201%22%20/%3E%0A%20%20%20%20%3C/feComponentTransfer%3E%0A%20%20%3C/filter%3E%0A%20%20%3Cimage%20filter%3D%22url%28%23blur%29%22%0A%20%20%20%20%20%20%20%20%20xlink%3Ahref%3D%22".self::base64($path)."%22%0A%20%20%20%20%20%20%20%20%20x%3D%220%22%20y%3D%220%22%0A%20%20%20%20%20%20%20%20%20height%3D%22100%25%22%20width%3D%22100%25%22/%3E%0A%3C/svg%3E";

		return $svg;

	}

	public static function background64($path,$with,$height){

		$background = "background-image: url(".self::blur64($path,$with,$height)."); background-size:cover; background-position:center center;";

		return $background;
	}

	public static function get_featured($post,$crop='thumbnail'){

		$img = wp_get_attachment_image_src($post->_thumbnail_id,$crop);

		$imgObject = array();
		$imgObject['width'] = $img[1];
		$imgObject['height'] = $img[2];
		$imgObject['src'] = $img[0];

		return $imgObject;

	}

	public static function get_svg($id_url){

			if(!$id_url) return false;

			if(is_integer($id_url)){
					$file = wp_get_attachment_image_src($id_url,false);
			} else {
					$file = $id_url;
			}

			if(!$file) return false;

		 	$content = '<div class="svg-inline">'.file_get_contents($file).'</div>';
			return $content;
	}


	public static function is_image($src_file_name){
		$supported_image = array(
			'gif',
			'jpg',
			'jpeg',
			'png'
		);
		
		$ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION)); // Using strtolower to overcome case sensitive
		if (in_array($ext, $supported_image)) {
			return true;
		} else {
			return false;
		}
	}



}
