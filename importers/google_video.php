<?php

/**
* 
*/
class SPVIDEOLITE_IMP_GoogleVideo implements SPVIDEOLITE_CLASS_IImporter
{
  private static $regexp = '#video\.google\..{0,5}/.*[\?&]docid=([^&]*)#i';
  private static $regexpIdIndex = 1;
  private static $embedTemplate = '<iframe width="560" height="315" src="http://www.youtube.com/embed/{videoId}" frameborder="0" allowfullscreen></iframe>';

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
    # XML data URL
    $file_data = 'http://video.google.com/videofeed?docid='.$id;
    $video->xml_url = $file_data;
    
    # XML
    $xml = new SimpleXMLElement(utf8_encode(file_get_contents($file_data)));
    $xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
    
    # Title
    $title_query = $xml->xpath('/rss/channel/item/title');
    $video->title = $title_query ? strval($title_query[0]) : null;
    
    # Description
    $description_query = $xml->xpath('/rss/channel/item/media:group/media:description');
    $video->description = $description_query ? strval(trim($description_query[0])) : null;
    
    # Tags
    $video->tags = null;
    
    # Duration
    $duration_query = $xml->xpath('/rss/channel/item/media:group/media:content/@duration');
    $video->duration = $duration_query ? intval($duration_query[0]) : null;
    
    # Author & author URL
    // TODO: WTF?
    // $author_query = $xml->xpath('/rss/channel/item/author');
    // $video->author = $author_query ? strval($author_query[0]) : false;
    $video->author = null;
    $video->author_url = null;
    
    # Publication date
    $date_published_query = $xml->xpath('/rss/channel/item/pubDate');
    $video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
    
    # Last update date
    $video->date_updated = null;
    
    # Thumbnails
    $thumbnails_query = $xml->xpath('/rss/channel/item/media:group/media:thumbnail');
    $thumbnails_query = $thumbnails_query[0]->attributes();
    $thumbnail = new stdClass;
    $thumbnail->url = strval(preg_replace('#&amp;#', '&', $thumbnails_query['url']));
    $thumbnail->width = intval($thumbnails_query['width']);
    $thumbnail->height = intval($thumbnails_query['height']);
    $video->thumbnails[] = $thumbnail;
    
    # Player URL
    $player_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="application/x-shockwave-flash"]/@url');
    $video->player_url = $player_url_query ? strval($player_url_query[0]) : null;
    
    # AVI file URL
    $avi_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-msvideo"]/@url');
    $video->files['video/x-msvideo'] = $avi_url_query ? preg_replace('#&amp;#', '&', $avi_url_query[0]) : null;
    
    # FLV file URL
    $flv_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-flv"]/@url');
    $video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
    
    # MP4 file URL
    $mp4_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/mp4"]/@url');
    $video->files['video/mp4'] = $mp4_url_query ? preg_replace('#&amp;#', '&', $mp4_url_query[0]) : null;

    return $video;
  }
}