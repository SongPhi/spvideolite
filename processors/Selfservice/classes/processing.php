<?php

class SPVIDEO_PRO_SELFSERVICE_CLASS_Processing
{
  public static function processTemporaryUpload($token, $videoId, $userId) {

    $dbo = OW::getDbo();
    $userfilesDir = SPVIDEO_DIR_USERFILES.DS.($userId);
    if ( !file_exists($userfilesDir) ) {
        mkdir($userfilesDir);
        chmod($userfilesDir, 0777);
    }

    $temp = $dbo->queryForObject('SELECT * FROM `'.OW_DB_PREFIX.'spvideo_upl_temp` WHERE `token`="'.$token.'"','OW_Entity');
    $storeFilePath = $userfilesDir.DS.$videoId.'_'.$temp->filename;
    rename(SPVIDEO_DIR_PLUGINFILES.DS.$token.DS.$temp->filename, $storeFilePath);
    UTIL_File::removeDir(SPVIDEO_DIR_PLUGINFILES.DS.$token);

    $totalSize = $temp->filesize;
    $spClipId = $dbo->insert('INSERT INTO `'.OW_DB_PREFIX.'spvideo_clip` (videoId,userId,totalSize,`module`,`status`) VALUES ('.($videoId).','.($userId).','.$totalSize.',\'selfservice\',\'ok\')');
    $format = strtolower( substr($temp->filename, strrpos($temp->filename,'.') - strlen($temp->filename) + 1 ) );

    switch ($format) {
      case 'webm':
        $format = 'webmv';
        break;
      case 'mp4':
        $format = 'm4v';
        break;
      
      default:
        $format = $format;
        break;
    }

    $url = 'baseurl:/ow_userfiles/plugins/spvideo/'.($userId).'/'.$videoId.'_'.$temp->filename;
    $size = $temp->filesize;
    $dbo->insert('INSERT INTO `'.OW_DB_PREFIX.'spvideo_clip_format` (clipId,format,url,size) VALUES ('.$spClipId.',\''.$format.'\',\''.addslashes($url).'\','.$size.')');
  }
  
}