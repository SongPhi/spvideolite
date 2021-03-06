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
class SPVIDEOLITE_IMP_Facebook implements SPVIDEOLITE_CLASS_IImporter
{
	private static $regexp = '#facebook\.com/(.*?/)?(videos?/)?(video.php\??v=)?(\d+).*#i';
	private static $regexpIdIndex = 4;
	private static $embedTemplate = '<iframe width="{width}" height="{height}" src="{iframeSrc}" frameborder="0" allowfullscreen></iframe>';

	public static function getRegExp() {
		return self::$regexp;
	}

	public static function getRegExpIdentifierIndex() {
		return self::$regexpIdIndex;
	}

	public static function embedApplyVideoId($code) {
		return '';
	}

	public static function getVideoEmbedCode($code,$videoId) {
		$matches = array();
		preg_match_all("/width=(\"|')?([\d]+)(px)?(\"|')?/i", $code, $matches);
		$width = $matches[2][0];
		preg_match_all("/height=(\"|')?([\d]+)(px)?(\"|')?/i", $code, $matches);
		$height = $matches[2][0];
		
		$output = str_replace('{iframeSrc}', OW::getRouter()->urlForRoute('spvideolite.videojs.fbembed',array('videoId'=>$videoId)), self::$embedTemplate);
		$output = str_replace('{width}', $width, $output);
		$output = str_replace('{height}', $height, $output);

		return $output;
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
		$file_data = 'http://graph.facebook.com/'.$id;
		$json_url = $file_data;

		# HTML
		$data = json_decode(file_get_contents($file_data),true);
		
		# Title
		$video->title = $data["description"];
		
		# Description
		$video->description = false;
		
		# Tags
		$categories = explode('/',$data['from']['category']);
		$video->tags = is_array($categories) && count($categories)>0? $categories : false;
		
		# Duration
		$video->duration = false;
		
		# Author & author URL
		$author = $data['from'];
		$video->author = $author['name'] ? $author['name'] : false;
		$video->author_url = 'http://www.facebook.com/'.$author['id'];
		
		# Publication date
		$created_time = $data['created_time'];
		$video->date_published = $created_time ? new DateTime($created_time) : false;
		
		# Last update date
		$date_updated = $data["updated_time"];
		$video->date_updated = $date_updated ? new DateTime($date_updated) : false;
		
		# Thumbnails
		$thumb = new stdClass();
		$thumb->url = $data["picture"];
		$video->thumbnails[] = $thumb;
		
		# Player URL
		$video->player_url = false;

		# Files URL
		$video->files = array( $data['source'] );

		# Embed Code
		$video->embedCode = array_pop($data['format']);
		// $video->embedCode = $video->embedCode['embed_html'];
		$video->embedCode = self::getVideoEmbedCode($video->embedCode['embed_html'],$id);
		
		# FLV file URL
		// TODO: Récupération de l'URL du fichier flv
		// self::$video->flv_url = 'http://www.youtube.com/get_video.php?video_id='.self::$id;

		return $video;
	}
}
