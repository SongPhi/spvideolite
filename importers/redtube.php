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
class SPVIDEOLITE_IMP_Redtube implements SPVIDEOLITE_CLASS_IImporter
{
	public static $regexp = '/redtube\.com\/(\?id\=)?(\d*)?/i';
	public static $regexpIdIndex = 2;
	public static $embedTemplate = '<iframe src="//embed.redtube.com/?id={videoId}&bgcolor=000000" frameborder="0" width="560" height="315" scrolling="no" allowfullscreen></iframe>';
	// http://img.l3.cdn.redtubefiles.com/_thumbs/0000852/0852986/0852986_012i.jpg

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
		$json_url = 'http://api.redtube.com/?data=redtube.Videos.getVideoById&output=json&thumbsize=all&video_id='.$id;

		# XML
		$data = json_decode(file_get_contents($json_url),true);

		if (isset($data['code']) && $data['code']==2002) 
			throw new Exception("Video with this id doesn't exist!", 2002);

		if (!isset($data['video'])) 
			throw new Exception("Unknown Exception", 1);
			
		$item = $data['video'];
		# Title
		$video->title = $item['title'];
		
		# Description
		$video->description = '';
		
		# Tags		
		$video->tags = array();
		
		# Duration		
		$video->duration = false;
		
		# Author & author URL		
		$video->author = false;
		$video->author_url = false;
		
		# Publication date		
		$video->date_published = false;
		
		# Last update date
		$video->date_updated = false;

		# Thumbnails
		$thumbnail = new stdClass;
		$thumbnail->url = preg_replace("#(http://|https://)#i", "//", $item['thumb']);
		$thumbnail->width = 432;
		$thumbnail->height = 324;
		$video->thumbnails[] = $thumbnail;
		
		# Player URL
		$video->player_url = $data['url'];

		# Files URL
		$video->files = array();

		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		
		# FLV file URL
		// TODO: Récupération de l'URL du fichier flv
		// self::$video->flv_url = 'http://www.youtube.com/get_video.php?video_id='.self::$id;

		return $video;
	}

	public static function fetchDownloadLink($external_id) {
		$sources = self::findRedtubeVideoLinks(UTIL_HttpResource::getContents("http://www.redtube.com/".$external_id)); 
		return $sources;
	}

	public static function findRedtubeVideoLinks($html) {
	    $sources = array();    
	    preg_match_all('/<source.*?type="(.+?)".*?>/i', $html, $sources);
	    
	    return $sources;
	}

	public static function getDirectSources() {
		$sources = self::findRedtubeVideoLinks(UTIL_HttpResource::getContents("http://www.redtube.com/".$external_id)); 
		return $sources;
	}
}