<?php
require_once './lib.php';
error_reporting(0);//this may give an error if it fails. we could suppress it and we will return the generated image anyway!
		
$gallery = getRequestInput('gallery', '', '/^[0-9a-zA-Z_\040\/]+$/');
$photo = getRequestInput('photo', '');
$width = getRequestInput('width', 0, '/^[0-9]+$/');
$height = getRequestInput('height', 0, '/^[0-9]+$/');
$width = min($width, 1024);
$height = min($height, 1024);

$type = getImageHeader(getExtension($photo));

$imgname = $gallery . ':' . $photo . ':' . $width . ':' . $height;
$path = THUMBS_DIR.md5($imgname);


//if(!file_exists(THUMBS_DIR)){
//	mkdir(THUMBS_DIR);
//}

$thumb_exists = file_exists($path);
$create_thumb = !$thumb_exists;
$thumb_generated = FALSE;
$thumb_image = 0;

if($create_thumb){
	//try to create the thumb!
	$imageinfo = getGalleryPhoto($gallery, $photo, 0);

	if($width == 0 && $height == 0){
		//just use the original!
		$path = $imageinfo['path'];
		$thumb_exists = TRUE;
	} else {
		$image = getImage($imageinfo['path']);
		$iwidth = imagesx($image);//input
		$iheight = imagesy($image);
		$scale = 1;
		$swidth = $iwidth;//source rectangle
		$sheight = $iheight;
		$owidth = $width;//output
		$oheight = $height;
		//resize it and store it
		if($width == 0){
			//clamp to the height
			$scale = $height/$iheight;
			$owidth = $scale*$iwidth;
		} else if($height == 0){
			//clamp to the width
			$scale = $width/$iwidth;
			$oheight = $scale*$iheight;
		} else {
			//crop
			$iaspect = $iwidth/$iheight;
			$oaspect = $width/$height;
			if($oaspect > $iaspect){
				//output is wider
				$scale = $width/$iwidth;
				$sheight = $height/$scale;
			} else {
				//output is taller
				$scale = $height/$iheight;
				$swidth = $width/$scale;
			}
		}
		
		$thumb_image = imagecreatetruecolor($owidth, $oheight);
		
		imagecopyresampled($thumb_image, $image, 0, 0, ($iwidth-$swidth)*0.5, ($iheight-$sheight)*0.5, $owidth, $oheight, $swidth, $sheight);
		$thumb_generated = TRUE;
		//$errlevel = ini_get('error_reporting');
		//error_reporting(0);//this may give an error if it fails. we could suppress it and we will return the generated image anyway!
		imagejpeg($thumb_image, $path, 90);
		//error_reporting($errlevel);
		$thumb_exists = file_exists($path);
	}
}


$mtime = filemtime($path);
if($thumb_exists && isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $mtime)){
	//The image has been cached. Don't send it.
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mtime).' GMT', true, 304);
} else {	
	if($thumb_exists || $thumb_generated){
		//We have a thumb in memory or on disk. Prefer memory to avoid more io.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mtime).' GMT', true, 200);
		header($type);
		if($thumb_generated){
			//get the size first!
			ob_start();
			imagejpeg($thumb_image, NULL, 90);
			$thumb_str = ob_get_contents();
			$thumb_size = ob_get_length();
			ob_end_clean();
			header('Content-Length: '.$thumb_size);
			echo $thumb_str;
		} else {
			header('Content-Length: '.filesize($path));
			readfile($path);
		}
	}
}
if($thumb_generated){
	imagedestroy($thumb_image);
}
?>