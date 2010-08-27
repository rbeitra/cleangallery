<?php
$time_start = microtime(true);
require_once './lib.php';
$title = SITE_NAME;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?=SITE_NAME?></title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div class="page">
<div class="title"><?=SITE_NAME?></div>
<?php

$path = $gallery = getRequestInput('gallery', '', '/^[0-9a-zA-Z_\040\/]+$/');
$gallery = getRequestInput('gallery', '', '/^[0-9a-zA-Z_\040\/]+$/');
$photo = getRequestInput('photo', '');
$galleryurl = './?gallery='.$gallery;
$photourl = GALLERIES_DIR.$gallery.'/'.$photo;

if($gallery == ''){
	if(LIST_GALLERIES){
		$galleries = getGalleries();
		if($galleries){
			//render gallery list
			foreach($galleries as $k => $v){
				$name = $v['name'];
				$url = './?gallery='.$name;
				echo <<<EOD
	<div class="gallery">
		<div class="galleryname"><a href="$url">$name</a></div>
	</div>
EOD;
			}
		}
	}
} else if($photo == ''){
	$images = getGalleryContents($gallery);
	if($images){
		$thumbwidth = THUMB_WIDTH;
		$thumbheight = THUMB_HEIGHT;
		if(LIST_GALLERIES){
			$top = '<div class="toolbar"><a href="./">[Top]</a></div>';
		} else {
			$top = '';
		}
			echo <<<EOD
<div class="gallery">
	<div class="galleryname">$gallery</div>
	$top
EOD;
		//render photo list
		foreach($images as $k => $v){
			$name = $v['name'];
			$filename = $v['filename'];
			$url = './?gallery='.$gallery.'&photo='.$name;
			$thumburl = "./img.php?gallery=$gallery&photo=$name&width=$thumbwidth&height=$thumbheight";
			echo <<<EOD
<div class="thumbnail"><a href="$url"><img class="image" src="$thumburl" width="$thumbwidth" height="$thumbheight"></a></div>
	
EOD;
		}
	echo $top; 
	echo "</div>";
	}
} else {
	$nextphoto = getGalleryPhoto($gallery, $photo, 1);
	$prevphoto = getGalleryPhoto($gallery, $photo, -1);
	$photo = getGalleryPhoto($gallery, $photo, 0);
	if($photo){
		//render photo
		$path = $photo['path'];
		$filename = $photo['filename'];
		$nexturl = './?gallery='.$gallery.'&photo='.$nextphoto['name'];
		$prevurl = './?gallery='.$gallery.'&photo='.$prevphoto['name'];
		if(LIST_GALLERIES){
			$top = '<a href="./">[Top]</a> ';
		} else {
			$top = '';
		}
		$tools = <<<EOD
	<div class="toolbar">$top <a href="$galleryurl">[Index]</a> <a href="$prevurl">[Prev]</a> <a href="$nexturl">[Next]</a> <a href="$photourl">[Original]</a></div>
EOD;
		echo <<<EOD
<div class="gallery">
	<div class="galleryname">$gallery</div>
	<div class="photodetails">$filename</div>
	$tools
	<div class="photo"><a href="$nexturl"><img class="image" src="$photourl" width="100%"></a></div>
	$tools
</div>

EOD;
	}
}
$time_end = microtime(true);
$total_time = $time_end - $time_start;
//echo '<div class="toolbar">page generated in ' . $total_time . ' seconds</div>';
echo '</div>';
if(GA_ID){
	$gaid = GA_ID;
	echo <<<EOD
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
_gat._getTracker("$gaid")._trackPageview();
} catch(err) {}</script>
EOD;
}
?>
</body>
</html>