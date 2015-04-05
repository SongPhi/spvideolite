<?php

class SPVIDEOLITE_PRO_ALLMYVIDEOS_CLASS_Processing
{
  public static function processTemporaryUpload($token, $filename) {
  	$baseUrl = str_replace('http://', '', OW::getRouter()->getBaseUrl());
  	$baseUrl = str_replace('https://', '', $baseUrl);
  	$baseUrl = $_POST['protocol'].$baseUrl;

    $url =  $baseUrl.'ow_userfiles/plugins/spvideolite/allmyvideos/'.($token).'/'.($filename);
    $srvUrl = 'http://spvideo.songphi.com/helpers/amv/new.php?t='.$token.'&f='.base64_encode($url);
    $parts = parse_url($srvUrl);

    $fp = fsockopen($parts['host'], $port, $errno, $errstr, 30);

    if ($fp) fclose($fp);
  }
  
}