<?php

class SPVIDEOLITE_PRO_ALLMYVIDEOS_CLASS_Processing
{
  public static function processTemporaryUpload($token, $filename) {
  	$baseUrl = str_replace('http://', '', OW::getRouter()->getBaseUrl());
  	$baseUrl = str_replace('https://', '', $baseUrl);
  	$baseUrl = $_POST['protocol'].$baseUrl;

    $url =  $baseUrl.'ow_userfiles/plugins/spvideolite/'.($token).'/'.($filename);

    $srvResp = file_get_contents('http://spvideo.songphi.com/helpers/amv/new.php?t='.$token.'&f='.base64_encode($url));

  }
  
}