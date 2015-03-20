<?php
/**
 * Copyright 2015 SongPhi
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

 /**
* 
*/
class SPVIDEOLITE_IMP_DailyMotion implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '#dailymotion\.com.*/video/([^_]*)#i';
	private static $regexpIdIndex = 1;
	private static $embedTemplate = '<iframe frameborder="0" width="560" height="315" src="http://www.dailymotion.com/embed/video/{videoId}"></iframe>';

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
		$file_data = 'http://www.dailymotion.com/rss/video/'.$id;
		$video->xml_url = $file_data;

		# XML
		$xml = new SimpleXMLElement(file_get_contents($file_data));
		
		# Title
		$title_query = $xml->xpath('/rss/channel/item/title');
		$video->title = $title_query ? strval($title_query[0]) : null;
		
		# Description
		$description_query = $xml->xpath('/rss/channel/item/itunes:summary');
		$video->description = $description_query ? strval(trim($description_query[0])) : null;
		
		# Tags
		$tags_query = $xml->xpath('/rss/channel/item/itunes:keywords');
		$video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : null;
		
		# Duration
		$duration_query = $xml->xpath('/rss/channel/item/media:group/media:content/@duration');
		$video->duration = $duration_query ? intval($duration_query[0]) : null;
		
		# Author & author URL
		$author_query = $xml->xpath('/rss/channel/item/dm:author');
		$video->author = $author_query ? strval($author_query[0]) : null;
		$video->author_url = 'http://www.dailymotion.com/'.$video->author;
		
		# Publication date
		$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
		$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
		
		# Last update date
		$video->date_updated = null;
		
		# Thumbnails
		$thumbnail = new stdClass;
		$thumbnail->url = 'http://www.dailymotion.com/thumbnail/320x240/video/'.$id;
		$thumbnail->width = 320;
		$thumbnail->height = 240;
		$video->thumbnails[] = $thumbnail;
		
		# Player URL
		$video->player_url = 'http://www.dailymotion.com/swf/'.$id;
		
		# FLV file URL
		$flv_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-flv"]/@url');
		$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
		
		# MP4 file URL
		// TODO: Récupération de l'URL du fichier mp4
		//$mp4_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/mp4"]/@url');
		//self::$mp4 = $mp4_query ? $mp4_query[0] : '';

		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		return $video;
	}
}