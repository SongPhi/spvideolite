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
	private static $embedTemplate = '<iframe width="560" height="315" src="//www.youtube.com/embed/{videoId}" frameborder="0" allowfullscreen></iframe>';

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
		//spvideo config
		$configs = SPVIDEOLITE_BOL_Configs::getInstance();

		$video = new stdClass;
		# XML data URL
		$json_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.$id;

		$json_url .= '&key=' . $configs->get('importers.youtube.apikey');
		
		# XML
		$data = json_decode(file_get_contents($json_url),true);

		if ($data['pageInfo']['totalResults'] < 1) 
			throw new Exception("Error Processing Request", 1);
			
		$item = $data['items'][0]['snippet'];
		# Title
		$video->title = $item['title'];
		
		# Description
		$video->description = $item['description'];
		
		# Tags		
		$video->tags = array();
		
		# Duration		
		$video->duration = false;
		
		# Author & author URL		
		$video->author = false;
		$video->author_url = false;
		
		# Publication date		
		$video->date_published = new DateTime($item['publishedAt']);
		
		# Last update date
		$video->date_updated = false;

		$requested_thumb_size = $configs->get('tweaks.youtube_thumb_size');
		
		# Thumbnails
		$thumbnail = new stdClass;
		$thumbnail->url = $item['thumbnails'][$requested_thumb_size]['url'];
		$thumbnail->width = $item['thumbnails'][$requested_thumb_size]['width'];
		$thumbnail->height = $item['thumbnails'][$requested_thumb_size]['height'];
		$video->thumbnails[] = $thumbnail;
		
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