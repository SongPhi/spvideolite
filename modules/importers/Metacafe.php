<?php

/**
* 
*/
class SPVIDEO_IMP_Metacafe implements SPVIDEO_CLASS_IImporter
{
	private static $regexp = '#metacafe\.com/watch/(.[^/]*)#i';
	private static $regexpIdIndex = 1;
	private static $embedTemplate = '<iframe src="http://www.metacafe.com/embed/{videoId}/" width="540" height="304" allowFullScreen frameborder=0></iframe>';

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
		$file_data = "http://www.metacafe.com/api/item/".$id;
		$video->xml_url = $file_data;
		
		# XML
		$xml = new SimpleXMLElement(file_get_contents($file_data));
		
		# Title
		$title_query = $xml->xpath('/rss/channel/item/title');
		$video->title = $title_query ? strval($title_query[0]) : '';
		
		# Description
		$description_query = $xml->xpath('/rss/channel/item/media:description');
		$video->description = $description_query ? strval($description_query[0]) : '';
		
		# Tags
		$tags_query = $xml->xpath('/rss/channel/item/media:keywords');
		$video->tags = $tags_query ? explode(',', strval(trim($tags_query[0]))) : null;
		
		# Duration
		$video->duration = null;
		
		# Author & author URL
		$author_query = $xml->xpath('/rss/channel/item/author');
		$video->author = $author_query ? strval($author_query[0]) : '';
		$video->author_url = "http://www.metacafe.com/".$video->author;
		
		# Publication date
		$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
		$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
		
		# Last update date
		$video->date_updated = null;
		
		# Thumbnails
		$thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
		$thumbnail = new stdClass;
		$thumbnail->url = strval($thumbnails_query[0]);
		list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
		$video->thumbnails[] = $thumbnail;
		
		# Player URL
		$player_url_query = $xml->xpath('/rss/channel/item/media:content[@type="application/x-shockwave-flash"]/@url');
		$video->player_url = $player_url_query ? strval($player_url_query[0]) : '';
		
		# Files URL
		$video->files = array();

		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		return $video;
	}
}