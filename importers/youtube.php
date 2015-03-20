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
class SPVIDEOLITE_IMP_Youtube implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
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
		$file_data = 'http://gdata.youtube.com/feeds/api/videos/'.$id;
		$xml_url = $file_data;
		
		# XML
		$xml = new SimpleXMLElement(file_get_contents($file_data));
		$xml->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
		$xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
		$xml->registerXPathNamespace('yt', 'http://gdata.youtube.com/schemas/2007');
		
		# Title
		$title_query = $xml->xpath('/a:entry/a:title');
		$video->title = $title_query ? strval($title_query[0]) : false;
		
		# Description
		$description_query = $xml->xpath('/a:entry/a:content');
		$video->description = $description_query ? strval(trim($description_query[0])) : false;
		
		# Tags
		$tags_query = $xml->xpath('/a:entry/media:group/media:keywords');
		$video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : false;
		
		# Duration
		$duration_query = $xml->xpath('/a:entry/media:group/yt:duration/@seconds');
		$video->duration = $duration_query ? intval($duration_query[0]) : false;
		
		# Author & author URL
		$author_query = $xml->xpath('/a:entry/a:author/a:name');
		$video->author = $author_query ? strval($author_query[0]) : false;
		$video->author_url = 'http://www.youtube.com/'.$video->author;
		
		# Publication date
		$date_published_query = $xml->xpath('/a:entry/a:published');
		$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : false;
		
		# Last update date
		$date_updated_query = $xml->xpath('/a:entry/a:updated');
		$video->date_updated = $date_updated_query ? new DateTime($date_updated_query[0]) : false;
		
		# Thumbnails
		$thumbnail_query = $xml->xpath('/a:entry/media:group/media:thumbnail');
		foreach ($thumbnail_query as $t) {
			$thumbnail = new stdClass;
			$thumbnail_query = $t->attributes();
			$thumbnail->url = strval($thumbnail_query['url']);
			$thumbnail->width = intval($thumbnail_query['width']);
			$thumbnail->height = intval($thumbnail_query['height']);
			$video->thumbnails[] = $thumbnail;
		}
		
		# Player URL
		$video->player_url = 'http://www.youtube.com/v/'.$id;

		# Files URL
		$video->files = array();

		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		
		# FLV file URL
		// TODO: Récupération de l'URL du fichier flv
		// self::$video->flv_url = 'http://www.youtube.com/get_video.php?video_id='.self::$id;

		return $video;
	}
}