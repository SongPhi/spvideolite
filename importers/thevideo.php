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


class SPVIDEOLITE_IMP_Thevideo implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '#thevideo\.me/([a-z0-9]+)#i';
	private static $regexpIdIndex = 1;
	private static $embedTemplate = '<iframe src="http://www.thevideo.me/embed-{videoId}.html" frameborder="0" marginwidth="0" marginheight="0" scrolling="NO" allowfullscreen="true" width="560" height="315"></iframe>';

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
		$file_data = 'http://www.thevideo.me/'.$id;
		$file_data2 = 'http://www.thevideo.me/embed-'.$id.'.html';
		$video->html_url = $file_data;

		# HTML
		$html = file_get_contents($file_data);
		$html2 = file_get_contents($file_data2);

		$matches = array();

		$titleRegex = '#<title>Watch (.*?)</title>#i';
		preg_match_all($titleRegex, $html, $matches);

		# Title
		$video->title = $matches[1][0] ? $matches[1][0] : null;
		
		# Description
		$video->description = '';
		
		# Tags
		$video->tags = null;
		
		# Duration
		$video->duration = null;
		
		# Author & author URL
		$video->author_url = null;
		
		# Publication date
		$video->date_published = null;
		
		# Last update date
		$video->date_updated = null;
		
		# Thumbnails
		$thumbnailQuery = '/<img.+?\ src="(.+?)'.$id.'\.jpg".*?>/is';
		$matches = array();
		preg_match_all($thumbnailQuery, $html2, $matches);

		$video->thumbnails = array();
		if (count($matches)>=2) {
			# Download the thumbnail
			$thumbnail = new stdClass();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $matches[1][0].$id.'.jpg');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_REFERER, $file_data2);
			$jpeg = curl_exec($ch);

			$thumbPath = SPVIDEOLITE_DIR_USERFILES . DS . 'thevideo' . DS . 'thumbs';
			$thumbFile = $thumbPath . DS . $id . '.jpg';

			@mkdir($thumbPath,0777,true);

			file_put_contents($thumbFile, $jpeg);

			$thumbnail->url = OW::getPluginManager()->getPlugin('spvideolite')->getUserFilesUrl().'thevideo/thumbs/'. $id . '.jpg';

			$video->thumbnails[] = $thumbnail;
		}
		
		# Player URL
		$video->player_url = null;
		
		# FLV file URL
		$video->files = array();
		
		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id);
		return $video;
	}
}

