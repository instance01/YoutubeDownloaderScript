<?php
if(!isset($_GET['v'])){
	echo("The source code of this script can be found <a href='https://github.com/instance01/YoutubeDownloaderScript/blob/master/YoutubeDownloader.php'> here </a>.<br><br>");
	echo("Usage: youtubedownloader.php?v=videoid<br><b>Videoid</b> would be the red part: https://www.youtube.com/watch?v=<font color='red'>U8B8RkcF0Wc</font><br>Example: <a href='http://www.instancedev.com/youtubedl/YoutubeDownloader.php?v=U8B8RkcF0W'>http://www.instancedev.com/youtubedl/YoutubeDownloader.php?v=U8B8RkcF0Wc</a><br><br>");
	echo("Arguments:<br><b> &mp4=true </b> | Download in mp4 format only <br><b> &debug=true </b> | Print out debug info <br>Example: <a href='http://www.instancedev.com/youtubedl/YoutubeDownloader.php?v=U8B8RkcF0W&mp4=true'>http://www.instancedev.com/youtubedl/YoutubeDownloader.php?v=U8B8RkcF0Wc&mp4=true</a>");
	exit;
}

$videoid = $_GET['v'];

function getDownloadLink($videoid){
	$invalid_filechars = array('/', '?', '*', '\\', '\n', '\r', '\t', '\0', '\f', '<', '>', '|', '\"', ':', '`');

	$ch = curl_init("http://www.youtube.com/get_video_info?video_id=$videoid&fmt=18");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	parse_str($data, $params);
	curl_close($ch);

	$urlmap = urldecode(urldecode($params['url_encoded_fmt_stream_map']));
	/*$index = strpos($urlmap, 'url=') + 4;
	$dlurl = substr($urlmap, $index, strpos($urlmap, ';', $index) - $index);*/

	$dlurl = "";
	$arr = explode('url=', $urlmap);
	$skipped = false;
	foreach ($arr as $val){
		if(!$skipped){
			$skipped = true;
		}else{
			if(strpos($val, 'mp4') !== false){
				$index = 0;
				$dlurl = substr($val, $index, strpos($val, ';', $index) - $index);
				break;
			}
		}
	}


	$headers = get_headers($dlurl, 1);
	$type = $headers["Content-Type"];
	if(isset($_GET['debug'])){
		echo($type."<br>");
	}

	$valid_type = "video";
	if(isset($_GET['mp4'])){
		$valid_type = "video/mp4";
	}

	if(strrpos($type, $valid_type) !== FALSE){
		$filename = str_replace($invalid_filechars, '_', $params['title']).'.'.$filename .= substr($type, strpos($type, "/") + 1);;
		if(isset($_GET['debug'])){
			echo($filename.'<br><br>');
			echo($dlurl.'<br><br>');
			echo('<a href="'.$dlurl.'">Rightclick -> Download Target Under ..</a><br><br>');
		} else {
			forceDownload($dlurl, $filename);
		}
		return false;
	} else { 
		return true;
	}
}

function forceDownload($url, $name) {
	set_time_limit(0);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$r = curl_exec($ch);
	curl_close($ch);
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
	header('Cache-Control: private', false);
	header('Content-Type: application/force-download');
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.strlen($r));
	header('Connection: close');
	echo $r;
}

$cont = TRUE;
while($cont){
	$cont = getDownloadLink($videoid);
	//sleep(1);
}

?>

