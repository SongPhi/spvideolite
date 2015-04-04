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


class SPVIDEOLITE_IMP_Allmyvideos implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '#allmyvideos\.net/([a-z0-9]+)#i';
	private static $regexpIdIndex = 1;
	private static $embedTemplate = '<iframe src="http://allmyvideos.net/embed-{videoId}.html" frameborder="0" marginwidth="0" marginheight="0" scrolling="NO" allowfullscreen="true" width="600" height="332"></iframe>';

	public static function getRegExp() {
		return self::$regexp;
	}

	public static function getRegExpIdentifierIndex() {
		return self::$regexpIdIndex;
	}

	public static function embedApplyVideoId($videoId) {
		$code = str_replace('{videoId}', $videoId, self::$embedTemplate);
		return $code;
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
		$video = new stdClass();
		# HTML data URLs
		$file_data = 'http://allmyvideos.net/'.$id;
		$file_data2 = 'http://allmyvideos.net/embed-'.$id.'.html';
		$video->html_url = $file_data;

		# HTML
		$html = file_get_contents($file_data);
		$html2 = file_get_contents($file_data2);

		$matches = array();

		$titleRegex = '#<title>Watch (.*?)</title>#i';
		preg_match_all($titleRegex, $html, $matches);

		# Title
		$video->title = $matches[1][0] ? $matches[1][0] : null;
		
		$jsonRegex = '/"playlist".*?\]\s+/is';
		$json = null;

		if (preg_match_all($jsonRegex, $html2, $matches)) {
			$json = '{' . $matches[0][0] . '}';
			$json = json_decode($json, true);
		}

		if ($json == null)
			throw new Exception('Failed to fetch video detail');

		$json = $json['playlist'][0];

		# Description
		$video->description = $json['description'] ? strval(trim($json['description'])) : null;
		
		# Tags
		$video->tags = null;
		
		# Duration
		$video->duration = $json['duration'];
		
		# Author & author URL
		$video->author_url = null;
		
		# Publication date
		$video->date_published = null;
		
		# Last update date
		$video->date_updated = null;
		
		# Thumbnails
		$thumbnail = new stdClass;
		$thumbnail->url = $json['image'];
		$thumbnail->width = 320;
		$thumbnail->height = 240;
		$video->thumbnails[] = $thumbnail;
		
		# Player URL
		$video->player_url = null;
		
		# FLV file URL
		$video->files = array();
		
		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		return $video;
	}
}

