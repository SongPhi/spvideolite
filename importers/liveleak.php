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

// jwplayer\(".+?"\)\.setup\((\{.*?\})\);
// generate_embed_code_generator_html\('(.+?)'\)
// <span class="section_title" style="vertical-align:top; padding-right:10px">Guy Kisses Dude's Girlfriend Right In Front Of His Face &nbsp;<img src="http://edge.liveleak.com/80281E/u/u/ll2/hd_video_icon.jpg"></span>

class SPVIDEOLITE_IMP_Liveleak implements SPVIDEOLITE_CLASS_IImporter
{
	// http://www.liveleak.com/view?i=7c1_1446145186
	public static $regexp = "/liveleak\\.com\\/view\\?i\\=([a-z0-9_]+)?.*$/is";
	public static $regexpIdIndex = 1;
	public static $embedTemplate = '<iframe width="640" height="360" src="http://www.liveleak.com/ll_embed?f={embedId}" frameborder="0" allowfullscreen></iframe>';

	public static function getRegExp() {
		return self::$regexp;
	}

	public static function getRegExpIdentifierIndex() {
		return self::$regexpIdIndex;
	}

	public static function embedApplyVideoId($embedId) {
		return str_replace('{embedId}', $embedId, self::$embedTemplate);
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
		$html_url = 'http://mobile.liveleak.com/view?i='.$id.'&ajax=1';
		# XML
		$htmlData = file_get_contents($html_url);

		$matches = array();
		if (preg_match("/liveleak\\.com\\/player/i",$htmlData)) {
			preg_match_all("/generate_embed_code_generator_html\\('(.+?)'\\)/is", $htmlData, $matches);
			$embedId = $matches[1][0];

			$matches = array();
			preg_match("/<span\\s*?class=\"section_title\".*?>(.+?)<\\/span>.*?<div\\s*?id=\"body_text\".*?>(.*?)<\\/div>/is", $htmlData, $matches);
			$title = strip_tags($matches[1]);
			$description = strip_tags($matches[2]);

			$matches = array();
			preg_match_all("/jwplayer\\(\".+?\"\\)\\.setup\\((\\{.*?\\})\\)/is", $htmlData, $matches);
			$data = $matches[1][0];

			$matches = array();
			preg_match_all('/image\:\s*?"(.*?)"/i', $data, $matches);
			$image = $matches[1][0];

			// var_dump($matches);

			// if (isset($data['code']) && $data['code']==2002) 
			// 	throw new Exception("Video with this id doesn't exist!", 2002);

			// if (!isset($data['video'])) 
			// 	throw new Exception("Unknown Exception", 1);
				
			$item = $data['video'];
			# Title
			$video->title = $title;
			
			# Description
			$video->description = $description;
			
			# Tags		
			$video->tags = array();
			
			# Duration		
			$video->duration = false;
			
			# Author & author URL		
			$video->author = false;
			$video->author_url = false;
			
			# Publication date		
			$video->date_published = date();
			
			# Last update date
			$video->date_updated = false;

			# Thumbnails
			$thumbnail = new stdClass;
			$thumbnail->url = $image;
			$thumbnail->width = 640;
			$thumbnail->height = 360;
			$video->thumbnails[] = $thumbnail;
			
			# Player URL
			$video->player_url = false;

			# Files URL
			$video->files = false; //isset($data['sources'])?$data['sources']:array($data['file']);

			# Embed Code
			$video->embedCode = self::embedApplyVideoId($embedId);
			
			# FLV file URL
			// TODO: Récupération de l'URL du fichier flv
			// self::$video->flv_url = 'http://www.youtube.com/get_video.php?video_id='.self::$id;

			return $video;
		} else if (preg_match_all("/<div\\s*?id=\"body_text\".*?>.*?<\\/div>\\s+<iframe.*?src=\"(.*?)\".*?>.*?<\\/iframe>/is", $htmlData, $matches)) {
			return SPVIDEOLITE_CLASS_ImportService::getInstance()->checkClip($matches[1][0]);
		}

		throw new Exception("Error Processing Request", 1);
		
	}
}