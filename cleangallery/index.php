<?php
$time_start = microtime(true);
require_once './lib.php';
$title = SITE_NAME;
?><!DOCTYPE html>
<html lang="en">
<meta charset=utf-8>
<title><?=SITE_NAME?></title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div class="page">
<div class="title"><?=SITE_NAME?></div>
<?php

function notify($message){
	if(NOTIFICATIONS) echo '<div class="notification">'.$message.'</div>';
}

$path = $gallery = getRequestInput('gallery', '', '/^[0-9a-zA-Z_\040\/]+$/');
$gallery = getRequestInput('gallery', '', '/^[0-9a-zA-Z_\040\/]+$/');
$photo = getRequestInput('photo', '');
$galleryurl = './?gallery='.rawurlencode($gallery);
$photourl = GALLERIES_DIR.rawurlencode($gallery).'/'.rawurlencode($photo);

//some startup checks of configuration etc
if(!getDirectoryReadability(GALLERIES_DIR)){
	notify('Warning: The galleries directory is not readable or does not exist.');
}
if(!getDirectoryWritability(THUMBS_DIR)){
	notify('Warning: The thumbs directory is not writable or does not exist. This will severely affect performance!');
}

if($gallery == ''){
	if(LIST_GALLERIES){
		$galleries = getGalleries();
		if($galleries){
			//render gallery list
			foreach($galleries as $k => $v){
				$name = $v['name'];
				$url = './?gallery='.urlencode($name);
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
			$ename = urlencode($name);
			$egallery = urlencode($gallery);
			$filename = $v['filename'];
			$url = './?gallery='.$egallery.'&amp;photo='.$ename;
			$thumburl = "./img.php?gallery=$egallery&amp;photo=$ename&amp;width=$thumbwidth&amp;height=$thumbheight";
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
		$nexturl = './?gallery='.urlencode($gallery).'&amp;photo='.urlencode($nextphoto['name']);
		$prevurl = './?gallery='.urlencode($gallery).'&amp;photo='.urlencode($prevphoto['name']);
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

if(GENERATE_TIME){
	$time_end = microtime(true);
	$total_time = $time_end - $time_start;
	notify("Page generated in $total_time seconds");
}

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