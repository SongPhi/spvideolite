<?php

ini_set('display_errors', 1);
error_reporting(0);

$config = new \PHPVideoToolkit\Config(array(
  'temp_directory' => '/tmp',
  'ffmpeg' => '/usr/bin/ffmpeg',
  'ffprobe' => '/usr/bin/ffprobe',
  'yamdi' => '/usr/bin/yamdi',
  'qtfaststart' => '/usr/bin/qt-faststart',
));
$ffmpeg = new \PHPVideoToolkit\FfmpegParser($config);
$is_available = $ffmpeg->isAvailable(); // returns boolean
$ffmpeg_version = $ffmpeg->getVersion(); // outputs something like - array('version'=>1.0, 'build'=>null)

// $video  = new \PHPVideoToolkit\Video('/home/oxwall/sites/www.oxwall.dev/public_html/ow_userfiles/plugins/spvideo/1/26_xvideos.com_54bf9379c7b365d1501968f1eedbb6d5.flv', $config);
// unlink('/home/oxwall/sites/www.oxwall.dev/public_html/ow_userfiles/plugins/spvideo/thumb.jpg');
// $output = $video->extractFrame(new \PHPVideoToolkit\Timecode(100))
//                 ->save('/home/oxwall/sites/www.oxwall.dev/public_html/ow_userfiles/plugins/spvideo/thumb.jpg');

// header('Content-type: image/jpg');
// echo file_get_contents('/home/oxwall/sites/www.oxwall.dev/public_html/ow_userfiles/plugins/spvideo/thumb.jpg');

$parser = new \PHPVideoToolkit\MediaParser($config);
$data = $parser->getFileInformation('/home/oxwall/sites/www.oxwall.dev/public_html/ow_userfiles/plugins/spvideo/1/26_xvideos.com_54bf9379c7b365d1501968f1eedbb6d5.flv');
echo '<pre>'.print_r($data, true).'</pre>';

die();
