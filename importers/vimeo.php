<?php

/**
* 
*/
class SPVIDEOLITE_IMP_Vimeo implements SPVIDEOLITE_CLASS_IImporter
{
  private static $regexp = '#vimeo\.com\/.*?[\/]?([0-9]+)[\/\?]?#i';
  private static $regexpIdIndex = 1;
  private static $embedTemplate = '<iframe src="http://player.vimeo.com/video/{videoId}" width="560" height="315" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

  public static function getRegExp() {
    return self::$regexp;
  }

  public static function getRegExpIdentifierIndex() {
    return self::$regexpIdIndex;
  }

  public static function embedApplyVideoId($videoId) {
    return str_replace('{videoId}', $videoId, self::$embedTemplate);
  }
  
  public static function getClipIdentifier( $url ) {
    $matches = array();
    if (preg_match(self::$regexp, $url, $matches)) {
      return $matches[self::$regexpIdIndex];
    } else {
      throw new Exception('Unmatched URL of service');
    }
  }

  public static function getClipDetailByUrl( $url ) {
    $id = self::getClipIdentifier( $url );
    return self::getClipDetailByIdentifier($id);
  }

  public static function getClipDetailByIdentifier( $id ) {
    $video = new stdClass;
    # PHP serialized data URL
    $url_data = 'http://vimeo.com/api/v2/video/'.$id.'.php';
    
    # Data
    $data = unserialize(file_get_contents($url_data));

    # Title
    $video->title = $data[0]['title'];

    # Description
    $video->description = $data[0]['description'];

    # Tags
    $video->tags = explode(', ',$data[0]['tags']);

    # Duration
    $video->duration = $data[0]['duration'];

    # Author & author URL
    $video->author = $data[0]['user_name'];
    $video->author_url = $data[0]['user_url'];

    # Publication date
    $video->date_published = new DateTime($data[0]['upload_date']);
    
    # Last update date
    $video->date_updated = null;

    # Thumbnails
    $thumbnail = new stdClass;
    $thumbnail->url = $data[0]['thumbnail_small'];
    list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
    $video->thumbnails[] = $thumbnail;
    $thumbnail = new stdClass;
    $thumbnail->url = $data[0]['thumbnail_medium'];
    list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
    $video->thumbnails[] = $thumbnail;
    $thumbnail = new stdClass;
    $thumbnail->url = $data[0]['thumbnail_large'];
    list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
    $video->thumbnails[] = $thumbnail;

    $video->embedCode = self::embedApplyVideoId($id);

    return $video;
  }
}
