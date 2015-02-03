<?php

/**
* 
*/
class SPVIDEOLITE_IMP_Blip implements SPVIDEOLITE_CLASS_IImporter
{
  private static $regexp = '#blip\.tv.*/*#i';
  private static $regexpIdIndex = 0;
  private static $embedTemplate = '';

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
    if (preg_match(self::$regexp, $url, $matches) && isset($matches[self::$regexpIdIndex])) {
      return $matches[self::$regexpIdIndex];
    } else {
      throw new Exception('Unmatched URL of service');
    }
  }

  public static function getClipDetailByUrl( $url ) {
    $id = self::getClipIdentifier( $url );
    return self::getClipDetailByIdentifier($url);
  }

  public static function getClipDetailByIdentifier( $id ) {
    $video = new stdClass;
    # XML data URL
    $file_data = $id."?skin=rss";
    $video->xml_url = $file_data;
    
    # XML
    $xml = new SimpleXMLElement(file_get_contents($file_data));
    
    # Title
    $title_query = $xml->xpath('/rss/channel/item/title');
    $video->title = $title_query ? strval($title_query[0]) : null;
    
    # Description
    $description_query = $xml->xpath('/rss/channel/item/blip:puredescription');
    $video->description = $description_query ? strval(trim($description_query[0])) : null;
    
    # Tags
    $tags_query = $xml->xpath('/rss/channel/item/media:keywords');
    $video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : null;
    
    # Duration
    $duration_query = $xml->xpath('/rss/channel/item/blip:runtime');
    $video->duration = $duration_query ? intval($duration_query[0]) : null;
    
    # Author & author URL
    $author_query = $xml->xpath('/rss/channel/item/blip:user');
    $video->author = $author_query ? strval($author_query[0]) : null;
    $author_safe_query = $xml->xpath('/rss/channel/item/blip:safeusername');
    $video->author_url = 'http://'.strval($author_safe_query[0]).'.blip.tv';
    
    # Publication date
    $date_published_query = $xml->xpath('/rss/channel/item/blip:datestamp');
    $video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
    
    # Last update date
    $video->date_updated = null;

    # Thumbnails
    $thumbnails_query = $xml->xpath('/rss/channel/item/blip:picture');
    $thumbnail = new stdClass;
    $thumbnail->url = strval($thumbnails_query[0]);
    list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
    $video->thumbnails[] = $thumbnail;
    $thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
    $thumbnail = new stdClass;
    $thumbnail->url = strval($thumbnails_query[0]);
    list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
    $video->thumbnails[] = $thumbnail;
    
    # Player URL
    $player_url_query = $xml->xpath('/rss/channel/item/blip:embedUrl');
    $video->player_url = $player_url_query ? strval($player_url_query[0]) : null;
    
    # Embed Code
    $mediaplayer_query = $xml->xpath('/rss/channel/item/media:player'); 
    $video->embedCode = $mediaplayer_query ? strval($mediaplayer_query[0]) : null;

    if (!empty($video->embedCode)) $video->embedCode = substr($video->embedCode, 0, strpos($video->embedCode, '<embed'));

    return $video;
  }
}