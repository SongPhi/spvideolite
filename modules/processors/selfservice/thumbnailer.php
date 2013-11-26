<?php

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
