<?php

class SPVIDEOLITE_PRO_ALLMYVIDEOS_CLASS_Processing
{
  public static function processTemporaryUpload($token, $filename) {
  	$baseUrl = str_replace('http://', '', OW::getRouter()->getBaseUrl());
  	$baseUrl = str_replace('https://', '', $baseUrl);
  	$baseUrl = $_POST['protocol'].$baseUrl;

    $url =  $baseUrl.'ow_userfiles/plugins/spvideolite/allmyvideos/'.($token).'/'.($filename);
    $srvUrl = 'http://spvideo.songphi.com/helpers/amv/new.php?t='.$token.'&f='.base64_encode($url);
	$ch = curl_init();
 
	curl_setopt($ch, CURLOPT_URL, $srvUrl);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	 
	curl_exec($ch);
	curl_close($ch);
  }
  
}