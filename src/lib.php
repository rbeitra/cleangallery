<?php
require_once('config.php');

function bad($warn) {
    header("HTTP/1.1 404 Not Found");
    echo "$warn";
}

function getRequestInput($key, $default, $pattern = ''){
	$result = $default;
	if(isset($_REQUEST[$key])){
		$result = $_REQUEST[$key];
	}
	if (!get_magic_quotes_gpc()) {
		$result = addslashes($result);
	}
	if($pattern != ''){
		if(!preg_match($pattern, $result)){
			$result = $default;
		}
	}
	return $result;
}

function getDirectoryContents($directory){
    if(substr($directory,-1) == '/'){
        $directory = substr($directory,0,-1);
    }
 
    if(!file_exists($directory) || !is_dir($directory)){
        return FALSE;
 
    } elseif(is_readable($directory)){
    	$directory_tree = array();
        $directory_list = opendir($directory);
        while (FALSE !== ($file = readdir($directory_list))){
            if($file != '.' && $file != '..'){
                $path = $directory.'/'.$file; 
                if(is_readable($path)){
                    $subdirectories = explode('/',$path);
 					$end = end($subdirectories);
 					$mtime = filemtime($path);
                    $sorting = $end;
                    if(is_dir($path)){
                        $directory_tree[$sorting] = array(
                            'path'    => $path,
                            'name'    => $end,
                            'date'    => $mtime,
                            'kind'    => 'directory');
                    } elseif(is_file($path)) {
                    	$exp = explode('.',$end);
                        $extension = end($exp); 
                        $filename = $exp[0]; 
                        $directory_tree[$sorting] = array(
                            'path'      => $path,
                            'name'      => $end,
                            'extension' => $extension,
                            'filename'  => $filename,
                            'size'      => filesize($path),
	                        'date'      => $mtime,
                            'kind'      => 'file');
                    }
                }
            }
        }
        ksort($directory_tree, SORT_STRING);
        closedir($directory_list);  
        return $directory_tree;
    }else{
        return FALSE;    
    }
}

function getGalleries(){
	$contents = getDirectoryContents(GALLERIES_DIR);
	foreach($contents as $k => $v){
		if($v['kind'] == 'directory'){
			$subdirs[] = $v;
		}
	}
	return $subdirs;
}

function getGalleryContents($name){
	$galleries = getGalleries();
	$result = FALSE;
	$contents = FALSE;
	foreach($galleries as $k => $v){
		if($v['name'] == $name){
			$contents = getDirectoryContents(GALLERIES_DIR.$name);
			break;
		}
	}
	if($contents){
		foreach($contents as $k => $v){
			if($v['kind'] == 'file'){
				$extension = strtolower($v['extension']);
				if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'png'){
					$result[] = $v;
				}
			}
		}
	}
	return $result;
}

function getGalleryPhoto($gallery, $photo, $offset){
	$result = FALSE;
	$images = getGalleryContents($gallery);
	if($images){
		//find it
		$index = -1;
		foreach($images as $k => $v){
			if($v['name'] == $photo){
				$index = $k;
				break;
			}
		}
		if($index != -1){
			$index = ($index + $offset + count($images))%count($images);
			$result = $images[$index];
		} 
	}
	return $result;
}

function getImageHeader($extension){
	$extension = strtolower($extension);
	if($extension == 'jpg' || $extension == 'jpeg'){
		$type = "Content-Type: image/jpeg";
	} else if ($extension == 'png'){
		$type = "Content-Type: image/png";
	} else if ($extension == 'gif'){
		$type = "Content-Type: image/gif";
	}
	return $type;
}

function getExtension($path){
	return end(explode('.', $path));
}

function getImage($path){
	$extension = strtolower(getExtension($path));
	if($extension == 'jpg' || $extension == 'jpeg'){
		$image = imagecreatefromjpeg($path);
	} else if ($extension == 'png'){
		$image = imagecreatefrompng($path);
	} else if ($extension == 'gif'){
		$image = imagecreatefromgif($path);
	}
	return $image;
}

?>