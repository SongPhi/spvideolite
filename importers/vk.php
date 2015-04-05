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


class SPVIDEOLITE_IMP_Vk implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '#vk\.com.*/video(\-?\d+_\d+)#i';
	private static $regexpIdIndex = 1;
	private static $embedTemplate = '<iframe src="//vk.com/video_ext.php?oid={ownerId}&id={videoId}&hash={hash}&hd=1" width="640" height="360"  frameborder="0"></iframe>';

	public static function getRegExp() {
		return self::$regexp;
	}

	public static function getRegExpIdentifierIndex() {
		return self::$regexpIdIndex;
	}

	public static function embedApplyVideoId($videoId) {
		$ids = explode('_', $videoId);
		$oid = $ids[0];
		$id = $ids[1];
		$hash = $ids[2];
		$code = str_replace('{videoId}', $id, self::$embedTemplate);
		$code = str_replace('{ownerId}', $oid, $code);
		$code = str_replace('{hash}', $hash, $code);
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
		$video = new stdClass;
		# XML data URL
		$file_data = 'http://vk.com/video'.$id;
		$video->html_url = $file_data;

		# XML
		$html = file_get_contents($file_data);

		$matches = array();

		$jsonRegex = '#var\svars\s=\s(.*?)\\\\n#i';
		preg_match_all($jsonRegex, $html, $matches);
		$json = $matches[1][0];
		$json = stripcslashes($json);
		if (substr($json, -1) == ';')
			$json = substr($json, 0, -1);
		$json = json_decode($json, true);

		# Title
		$video->title = $json['md_title'] ? strval($json['md_title']) : null;
		
		# Description
		$description_query = '#<div class="video_row_info_description">(.*?)<\/div>#i';
		$description = array();
		preg_match_all($description_query, $html, $description);
		$description = isset($description[1][0])?$description[1][0]:false;
		$video->description = $description ? strval(trim($description)) : null;
		
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
		$thumbnail = new stdClass;
		$thumbnail->url = $json['jpg'];
		$thumbnail->width = 320;
		$thumbnail->height = 240;
		$video->thumbnails[] = $thumbnail;
		
		# Player URL
		$video->player_url = null;
		
		# FLV file URL
		$video->files = array();
		
		# Embed Code
		$video->embedCode = self::embedApplyVideoId($id . '_' . $json['hash2']);
		return $video;
	}
}

