<?php

if(!isset($_GET['v'])){
	echo("Usage: youtubedownloader.php?v=videoid");
	exit;
}

$videoid = $_GET['v'];


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
echo($filename.'<br>');
echo($dlurl.'<br>');





set_time_limit(0);
$fp = fopen (dirname(__FILE__).'/'.$filename, 'w+');
$ch_ = curl_init($dlurl);
curl_setopt($ch_, CURLOPT_TIMEOUT, 50);
curl_setopt($ch_, CURLOPT_FILE, $fp);
curl_setopt($ch_, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch_);
curl_close($ch_);
fclose($fp);





?>


