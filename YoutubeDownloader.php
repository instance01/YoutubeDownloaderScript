<?php
echo("The source code of this script can be found <a href='https://github.com/instance01/YoutubeDownloaderScript/blob/master/YoutubeDownloader.php'> here </a>.<br><br>");
if(!isset($_GET['v'])){
	echo("Usage: youtubedownloader.php?v=videoid<br>Videoid would be the red part: https://www.youtube.com/watch?v=<font color='red'>U8B8RkcF0Wc</font><br>Example: http://www.instancedev.com/youtubedl/YoutubeDownloader.php?v=U8B8RkcF0Wc");
	exit;
}
echo("Don't close this website until the download is done!<br><br>");

$videoid = $_GET['v'];

function getDownloadLink($videoid){
	$invalid_filechars = array('/', '?', '*', '\\', '\n', '\r', '\t', '\0', '\f', '<', '>', '|', '\"', ':', '`');

	$ch = curl_init("http://www.youtube.com/get_video_info?video_id=$videoid&fmt=18");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	parse_str($data, $params);
	curl_close($ch);

	$urlmap = urldecode($params['url_encoded_fmt_stream_map']);
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
	
	$filename = str_replace($invalid_filechars, '_', $params['title']).'.mp4';

	if(isset($_GET['debug'])){
		echo($filename.'<br><br>');
		echo($dlurl.'<br><br>');
	}
	
	//return $dlurl;

	$headers = get_headers($dlurl, 1);
	$type = $headers["Content-Type"];
	if(isset($_GET['debug'])){
		echo($type."<br>");
	}
	if(strrpos($type, "video") !== FALSE){
		echo('<a href="'.$dlurl.'">Rightclick -> Download Target Under ..</a><br><br>');
		forceDownload($dlurl, $filename);
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
    header('Content-Type: application/download');
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.strlen($r));
    header('Connection: close');
    echo $r;
}


/*set_time_limit(0);
$fp = fopen (dirname(__FILE__).'/'.$filename, 'w+');
$ch_ = curl_init($dlurl);
curl_setopt($ch_, CURLOPT_TIMEOUT, 50);
curl_setopt($ch_, CURLOPT_FILE, $fp);
curl_setopt($ch_, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch_);
curl_close($ch_);
fclose($fp);

echo('<a href="'.$filename.'">Download</a>');*/
$cont = TRUE;
while($cont){
	$cont = getDownloadLink($videoid);
}


?>


