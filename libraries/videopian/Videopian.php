<?php

/**
 * Videopian
 * get everything about a video
 * 
 * -------------------------------------------------------------------
 * WTF PUBLIC LICENSE
 * 
 * Copyright (C) 2009 Upian.com
 * 211 rue Saint-Maur 75010 Paris, France
 * 
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 * 
 *			DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 * 
 * 0. You just DO WHAT THE FUCK YOU WANT TO.
 * 
 * -------------------------------------------------------------------
 * 
 * @author 	Denis Hovart <denis@upian.com>
 * @author 	Hans Lemuet	<hans@upian.com>
 * @version	0.1.1
 */

class Videopian {

	# ================================================================================
	# Specify here the API keys for the services you want to use.
	# You'll need to request one for each.

	const IMEEM_API_KEY			= '';
	const IMEEM_API_SECRET		= '';
	const VEOH_API_KEY			= '';
	const FLICKR_API_KEY		= '';
	const SEVENLOAD_API_KEY		= '';
	const VIDDLER_API_KEY		= '';
	const REVVER_LOGIN			= '';
	const REVVER_PASSWORD		= '';
	
	
	# ================================================================================
	# Do not change anything under this line
	
	private static $url;
	private static $service;
	private static $id;
	private static $video;
	
	# ================================================================================
	# Process the URL to extract the service and the video id
	private static function processUrl() {
		
		self::$url = preg_replace('#\#.*$#', '', trim(self::$url));
		
		if (!preg_match('#http://#', self::$url)) self::$url = 'http://' . self::$url;
		
		$services_regexp = array(
			'#blip\.tv.*/file/([0-9]*)#i'					=> 'blip',
			'#dailymotion\.com.*/video/([^_]*)#i'			=> 'dailymotion',
			'#flickr\.com.*/photos/[a-zA-Z0-9]*/([^/]*)#'	=> 'flickr',
			'#video\.google\..{0,5}/.*[\?&]docid=([^&]*)#i'	=> 'googlevideo',
			'#imeem\.com/.*/video/([^/]*)#i'				=> 'imeem',
			'#metacafe\.com/watch/(.[^/]*)#i'				=> 'metacafe',
			'#myspace\.com/.*[\?&]videoid=(.*)#i'			=> 'myspace',
			'#revver\.com/video/([^/]*)#i'					=> 'revver',
			'#sevenload.com/.*/(videos|episodes)/([^-]*)#i'	=> 'sevenload',
			'#veoh\.com/.*/([^?&]*)/?#i'					=> 'veoh',
			'#vimeo\.com\/([0-9]*)[\/\?]?#i'				=> 'vimeo',
			'%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i'			=> 'youtube' # TODO: add the support of http://www.youtube.com/v/SToOccPytl8 style URLs
		);
		
		foreach ($services_regexp as $pattern => $service) {
			if (preg_match($pattern, self::$url, $matches)) {
				self::$service = $service;
				if ($service == 'sevenload') self::$id = $matches[2];
				else self::$id = $matches[1];
			}
		}
	}
	
	# ================================================================================
	# Fetch and return the video data
	public static function get($url) {
		
		self::$url = $url;
		
		self::processUrl();
		
		self::$video = new stdClass;
		self::$video->url = self::$url;
		self::$video->site = self::$service;
		
		switch (self::$service) {
			
			# --------------------------------------------------------------------------------
			case 'blip' :

			# XML data URL
			$file_data = "http://blip.tv/file/".self::$id."?skin=rss";
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Title
			$title_query = $xml->xpath('/rss/channel/item/title');
			self::$video->title = $title_query ? strval($title_query[0]) : null;
			
			# Description
			$description_query = $xml->xpath('/rss/channel/item/blip:puredescription');
			self::$video->description = $description_query ? strval(trim($description_query[0])) : null;
			
			# Tags
			$tags_query = $xml->xpath('/rss/channel/item/media:keywords');
			self::$video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : null;
			
			# Duration
			$duration_query = $xml->xpath('/rss/channel/item/blip:runtime');
			self::$video->duration = $duration_query ? intval($duration_query[0]) : null;
			
			# Author & author URL
			$author_query = $xml->xpath('/rss/channel/item/blip:user');
			self::$video->author = $author_query ? strval($author_query[0]) : null;
			$author_safe_query = $xml->xpath('/rss/channel/item/blip:safeusername');
			self::$video->author_url = 'http://'.strval($author_safe_query[0]).'.blip.tv';
			
			# Publication date
			$date_published_query = $xml->xpath('/rss/channel/item/blip:datestamp');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;

			# Thumbnails
			$thumbnails_query = $xml->xpath('/rss/channel/item/blip:smallThumbnail');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			$player_url_query = $xml->xpath('/rss/channel/item/blip:embedUrl');
			self::$video->player_url = $player_url_query ? strval($player_url_query[0]) : null;
			
			# FLV file URL
			$flv_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-flv"]/@url');
			self::$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
			
			# MOV file URL
			$mov_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/quicktime"]/@url');
			self::$video->files['video/quicktime'] = $mov_url_query ? strval($mov_url_query[0]) : null;
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'dailymotion' :
			
			# XML data URL
			$file_data = 'http://www.dailymotion.com/rss/video/'.self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Title
			$title_query = $xml->xpath('/rss/channel/item/title');
			self::$video->title = $title_query ? strval($title_query[0]) : null;
			
			# Description
			$description_query = $xml->xpath('/rss/channel/item/itunes:summary');
			self::$video->description = $description_query ? strval(trim($description_query[0])) : null;
			
			# Tags
			$tags_query = $xml->xpath('/rss/channel/item/itunes:keywords');
			self::$video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : null;
			
			# Duration
			$duration_query = $xml->xpath('/rss/channel/item/media:group/media:content/@duration');
			self::$video->duration = $duration_query ? intval($duration_query[0]) : null;
			
			# Author & author URL
			$author_query = $xml->xpath('/rss/channel/item/dm:author');
			self::$video->author = $author_query ? strval($author_query[0]) : null;
			self::$video->author_url = 'http://www.dailymotion.com/'.self::$video->author;
			
			# Publication date
			$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;
			
			# Thumbnails
			$thumbnail = new stdClass;
			$thumbnail->url = 'http://www.dailymotion.com/thumbnail/320x240/video/'.self::$id;
			$thumbnail->width = 320;
			$thumbnail->height = 240;
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			self::$video->player_url = 'http://www.dailymotion.com/swf/'.self::$id;
			
			# FLV file URL
			$flv_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-flv"]/@url');
			self::$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
			
			# MP4 file URL
			// TODO: Récupération de l'URL du fichier mp4
			//$mp4_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/mp4"]/@url');
			//self::$mp4 = $mp4_query ? $mp4_query[0] : '';
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'flickr':
			
			# API key check
			if (self::FLICKR_API_KEY == '') throw new Exception('You need to request an api key in order to grab video information from Flickr.');
			
			# XML data URL
			$file_data = 'http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=' . self::FLICKR_API_KEY . '&photo_id=' . self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Media type check
			$media_query = $xml->xpath('/rsp/photo/@media');
			if($media_query[0] != 'video') throw new Exception('The media you are trying to get from Flickr is not a video.');
			
			# Title
			$title_query = $xml->xpath('/rsp/photo/title');
			self::$video->title = $title_query ? strval($title_query[0]) : null;
			
			# Description
			$description_query = $xml->xpath('/rsp/photo/description');
			self::$video->description = empty($description_query) ? strval(trim($description_query[0])) : null;
			
			# Tags
			$tags_query = $xml->xpath('/rsp/photo/tags/tag');
			$tags = array();
			foreach ($tags_query as $tag_query) {
				$tag = (array) $tag_query;
				$tags[] = $tag[0];
			}
			self::$video->tags = $tags_query ? $tags : null;
			
			# Duration
			$duration_query = $xml->xpath('/rsp/photo/video/@duration');
			self::$video->duration = empty($duration_query) ? intval($duration_query[0]) : null;
			
			# Author & author URL
			$author_query = $xml->xpath('/rsp/photo/owner/@username');
			self::$video->author = $author_query ? strval($author_query[0]) : null;
			$author_id_query = $xml->xpath('/rsp/photo/owner/@nsid');
			self::$video->author_url = $author_id_query ? 'http://www.flickr.com/photos/'.strval($author_query[0]) : null;
			
			# Publication date
			$date_published_query = $xml->xpath('/rsp/photo/dates/@posted');
			self::$video->date_published = $date_published_query ? new DateTime(date(DATE_RSS, intval($date_published_query[0]))) : null;
			
			# Last update date
			$date_updated_query = $xml->xpath('/rsp/photo/dates/@lastupdate');
			self::$video->date_updated = $date_updated_query ? new DateTime(date(DATE_RSS, intval($date_updated_query[0]))) : null;
			
			# Thumbnails
			$thumbnails_query = $xml->xpath('/rsp/photo');
			$thumbnails_query = $thumbnails_query[0]->attributes();
			$thumbnail = new stdClass;
			$thumbnail->url = 'http://farm'.$thumbnails_query['farm'].'.static.flickr.com/'.$thumbnails_query['server'].'/'.self::$id.'_'.$thumbnails_query['secret'].'_m.jpg';
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnail = new stdClass;
			$thumbnail->url = 'http://farm'.$thumbnails_query['farm'].'.static.flickr.com/'.$thumbnails_query['server'].'/'.self::$id.'_'.$thumbnails_query['secret'].'_t.jpg';
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnail = new stdClass;
			$thumbnail->url = 'http://farm'.$thumbnails_query['farm'].'.static.flickr.com/'.$thumbnails_query['server'].'/'.self::$id.'_'.$thumbnails_query['secret'].'_s.jpg';
			$thumbnail->width = 75;
			$thumbnail->height = 75;
			self::$video->thumbnails[] = $thumbnail;
			
			# XML for files data URL
			$file_sizes_data = 'http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key=' . self::FLICKR_API_KEY . '&photo_id=' . self::$id;
			
			# XML
			$xml_sizes = new SimpleXMLElement(file_get_contents($file_sizes_data));
			
			# Player & files URL
			$files_url_query = $xml_sizes->xpath('/rsp/sizes/size[@media="video"]');
			foreach ($files_url_query as $p) {
				switch (strval($p['label'])) {
					case 'Video Player': self::$video->player_url = $files_url_query ? strval($p['source']) : null; break;
					case 'Site MP4': self::$video->files['video/mp4'] = $files_url_query ? strval($p['source']) : null; break;
				}
			}
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'googlevideo' :
			
			# XML data URL
			$file_data = 'http://video.google.com/videofeed?docid='.self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(utf8_encode(file_get_contents($file_data)));
			$xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
			
			# Title
			$title_query = $xml->xpath('/rss/channel/item/title');
			self::$video->title = $title_query ? strval($title_query[0]) : null;
			
			# Description
			$description_query = $xml->xpath('/rss/channel/item/media:group/media:description');
			self::$video->description = $description_query ? strval(trim($description_query[0])) : null;
			
			# Tags
			self::$video->tags = null;
			
			# Duration
			$duration_query = $xml->xpath('/rss/channel/item/media:group/media:content/@duration');
			self::$video->duration = $duration_query ? intval($duration_query[0]) : null;
			
			# Author & author URL
			// TODO: WTF?
			// $author_query = $xml->xpath('/rss/channel/item/author');
			// self::$video->author = $author_query ? strval($author_query[0]) : false;
			self::$video->author = null;
			self::$video->author_url = null;
			
			# Publication date
			$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;
			
			# Thumbnails
			$thumbnails_query = $xml->xpath('/rss/channel/item/media:group/media:thumbnail');
			$thumbnails_query = $thumbnails_query[0]->attributes();
			$thumbnail = new stdClass;
			$thumbnail->url = strval(preg_replace('#&amp;#', '&', $thumbnails_query['url']));
			$thumbnail->width = intval($thumbnails_query['width']);
			$thumbnail->height = intval($thumbnails_query['height']);
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			$player_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="application/x-shockwave-flash"]/@url');
			self::$video->player_url = $player_url_query ? strval($player_url_query[0]) : null;
			
			# AVI file URL
			$avi_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-msvideo"]/@url');
			self::$video->files['video/x-msvideo'] = $avi_url_query ? preg_replace('#&amp;#', '&', $avi_url_query[0]) : null;
			
			# FLV file URL
			$flv_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/x-flv"]/@url');
			self::$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
			
			# MP4 file URL
			$mp4_url_query = $xml->xpath('/rss/channel/item/media:group/media:content[@type="video/mp4"]/@url');
			self::$video->files['video/mp4'] = $mp4_url_query ? preg_replace('#&amp;#', '&', $mp4_url_query[0]) : null;
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'imeem' :
			
			throw new Exception('Imeem is not yet supported.');
			# Support thread opened here: http://www.imeem.com/groups/zJqqiqve/forums/-uuCzu0F/kx-e6b3U/method_searchbyurl/
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'metacafe' :
			
			# XML data URL
			$file_data = "http://www.metacafe.com/api/item/".self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Title
			$title_query = $xml->xpath('/rss/channel/item/title');
			self::$video->title = $title_query ? strval($title_query[0]) : '';
			
			# Description
			$description_query = $xml->xpath('/rss/channel/item/media:description');
			self::$video->description = $description_query ? strval($description_query[0]) : '';
			
			# Tags
			$tags_query = $xml->xpath('/rss/channel/item/media:keywords');
			self::$video->tags = $tags_query ? explode(',', strval(trim($tags_query[0]))) : null;
			
			# Duration
			self::$video->duration = null;
			
			# Author & author URL
			$author_query = $xml->xpath('/rss/channel/item/author');
			self::$video->author = $author_query ? strval($author_query[0]) : '';
			self::$video->author_url = "http://www.metacafe.com/".self::$video->author;
			
			# Publication date
			$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;
			
			# Thumbnails
			$thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			$player_url_query = $xml->xpath('/rss/channel/item/media:content[@type="application/x-shockwave-flash"]/@url');
			self::$video->player_url = $player_url_query ? strval($player_url_query[0]) : '';
			
			# Files URL
			self::$video->files = array();
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'myspace' :
			
			# XML data URL
			$file_data = "http://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=".self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Title
			$title_query = $xml->xpath('/rss/channel/item/title');
			self::$video->title = $title_query ? strval($title_query[0]) : '';
			
			# Description
			self::$video->description = null;
			
			# Tags
			self::$video->tags = null;
			
			# Duration
			self::$video->duration = null;
			
			# Author & author URL
			self::$video->author = null;
			self::$video->author_url = null;
			
			# Publication date
			$date_published_query = $xml->xpath('/rss/channel/item/pubDate');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;
			
			# Thumbnails
			$thumbnails_query = $xml->xpath('/rss/channel/item/media:thumbnail/@url');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			self::$video->player_url = "http://lads.myspace.com/videos/vplayer.swf?m=" . self::$id;
			
			# FLV file URL
			$flv_url_query = $xml->xpath('/rss/channel/item/media:content[@type="video/x-flv"]/@url');
			self::$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'revver' :
			
			# Account check
			# if (self::REVVER_LOGIN == '' || self::REVVER_PASSWORD == '') throw new Exception('Please specify your Revver account information.');
			
			throw new Exception('Revver is not yet supported.');
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'veoh' :
			
			# API key check
			if (self::VEOH_API_KEY == '') throw new Exception('You need to request an API key in order to grab video information from Veoh.');
			
			# XML data URL
			$file_data = "http://www.veoh.com/rest/v2/execute.xml?method=veoh.video.findByPermalink&permalink=" . self::$id . "&apiKey=" . self::VEOH_API_KEY;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			
			# Title
			$title_query = $xml->xpath('/rsp/videoList/video/@title');
			self::$video->title = $title_query ? strval($title_query[0]) : '';
			
			# Description
			$description_query = $xml->xpath('/rsp/videoList/video/@description');
			self::$video->description = $description_query ? strval($description_query[0]) : '';
			
			# Tags
			$tags_query = $xml->xpath('/rsp/videoList/video/tagList/tag/@tagName');
			foreach($tags_query as $tag) self::$video->tags[] = strval($tag[0]);
			
			# Duration
			$duration_query = $xml->xpath('/rsp/videoList/video/@length');
			$duration_raw = $duration_query ? strval($duration_query[0]) : null;
			preg_match('#(([0-9]{0,2}) hr )?([0-9]{0,2}) min ([0-9]{0,2}) sec#', $duration_raw, $matches);
			$hours = intval($matches[2]);
			$minutes = intval($matches[3]);
			$seconds = intval($matches[4]);
			self::$video->duration = ($hours * 60 * 60) + ($minutes * 60) + $seconds;
			
			# Author & author URL
			$author_query = $xml->xpath('/rsp/videoList/video/@username');
			self::$video->author = $author_query ? strval($author_query[0]) : '';
			self::$video->author_url = "http://www.veoh.com/users/".self::$video->author;
			
			# Publication date
			$date_published_query = $xml->xpath('/rsp/videoList/video/@dateAdded');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : null;
			
			# Last update date
			self::$video->date_updated = null;
			
			# Thumbnails
			$thumbnails_query_medres = $xml->xpath('/rsp/videoList/video/@medResImage');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query_medres[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnails_query_highres = $xml->xpath('/rsp/videoList/video/@highResImage');
			$thumbnail = new stdClass;
			$thumbnail->url = strval($thumbnails_query_highres[0]);
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			
			# Player URL
			self::$video->player_url = "http://www.veoh.com/veohplayer.swf?permalinkId=" . self::$id;

			# FLV file URL
			$flv_url_query = $xml->xpath('/rsp/videoList/video/@previewUrl');
			self::$video->files['video/x-flv'] = $flv_url_query ? strval($flv_url_query[0]) : null;
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'viddler':
			
			# API key check
			# if (self::VIDDLER_API_KEY == '') throw new Exception('You need to request an api key in order to grab video information from Viddler.');
			
			throw new Exception('Viddler is not yet supported.');
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'vimeo' :
			
			# PHP serialized data URL
			$url_data = 'http://vimeo.com/api/clip/'.self::$id.'/php';
			
			# Data
			$data = unserialize(file_get_contents($url_data));

			# Title
			self::$video->title = $data[0]['title'];

			# Description
			self::$video->description = $data[0]['caption'];

			# Tags
			self::$video->tags = explode(', ',$data[0]['tags']);

			# Duration
			self::$video->duration = $data[0]['duration'];

			# Author & author URL
			self::$video->author = $data[0]['user_name'];
			self::$video->author_url = $data[0]['user_url'];

			# Publication date
			self::$video->date_published = new DateTime($data[0]['upload_date']);
			
			# Last update date
			self::$video->date_updated = null;

			# Thumbnails
			$thumbnail = new stdClass;
			$thumbnail->url = $data[0]['thumbnail_small'];
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnail = new stdClass;
			$thumbnail->url = $data[0]['thumbnail_medium'];
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;
			$thumbnail = new stdClass;
			$thumbnail->url = $data[0]['thumbnail_large'];
			list($thumbnail->width, $thumbnail->height) = getimagesize($thumbnail->url);
			self::$video->thumbnails[] = $thumbnail;

			# Player URL
			self::$video->player_url = 'http://vimeo.com/moogaloop.swf?clip_id='.self::$id;
			
			# XML data URL
			$file_data = 'http://www.vimeo.com/moogaloop/load/clip:'.self::$id;
			self::$video->xml_url = 'http://vimeo.com/api/clip/'.self::$id.'/xml';
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data), LIBXML_NOCDATA);

			# Files URL
			self::$video->files = array();
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'sevenload' :
			
			# API key check
			# if (self::SEVENLOAD_API_KEY == '') throw new Exception('You need to request an api key in order to grab video information from Sevenload');
			
			throw new Exception('Sevenload is not yet supported.');
			
			break;
			
			
			# --------------------------------------------------------------------------------
			case 'youtube' :
			
			# XML data URL
			$file_data = 'http://gdata.youtube.com/feeds/api/videos/'.self::$id;
			self::$video->xml_url = $file_data;
			
			# XML
			$xml = new SimpleXMLElement(file_get_contents($file_data));
			$xml->registerXPathNamespace('a', 'http://www.w3.org/2005/Atom');
			$xml->registerXPathNamespace('media', 'http://search.yahoo.com/mrss/');
			$xml->registerXPathNamespace('yt', 'http://gdata.youtube.com/schemas/2007');
			
			# Title
			$title_query = $xml->xpath('/a:entry/a:title');
			self::$video->title = $title_query ? strval($title_query[0]) : false;
			
			# Description
			$description_query = $xml->xpath('/a:entry/a:content');
			self::$video->description = $description_query ? strval(trim($description_query[0])) : false;
			
			# Tags
			$tags_query = $xml->xpath('/a:entry/media:group/media:keywords');
			self::$video->tags = $tags_query ? explode(', ',strval(trim($tags_query[0]))) : false;
			
			# Duration
			$duration_query = $xml->xpath('/a:entry/media:group/yt:duration/@seconds');
			self::$video->duration = $duration_query ? intval($duration_query[0]) : false;
			
			# Author & author URL
			$author_query = $xml->xpath('/a:entry/a:author/a:name');
			self::$video->author = $author_query ? strval($author_query[0]) : false;
			self::$video->author_url = 'http://www.youtube.com/'.self::$video->author;
			
			# Publication date
			$date_published_query = $xml->xpath('/a:entry/a:published');
			self::$video->date_published = $date_published_query ? new DateTime($date_published_query[0]) : false;
			
			# Last update date
			$date_updated_query = $xml->xpath('/a:entry/a:updated');
			self::$video->date_updated = $date_updated_query ? new DateTime($date_updated_query[0]) : false;
			
			# Thumbnails
			$thumbnail_query = $xml->xpath('/a:entry/media:group/media:thumbnail');
			foreach ($thumbnail_query as $t) {
				$thumbnail = new stdClass;
				$thumbnail_query = $t->attributes();
				$thumbnail->url = strval($thumbnail_query['url']);
				$thumbnail->width = intval($thumbnail_query['width']);
				$thumbnail->height = intval($thumbnail_query['height']);
				self::$video->thumbnails[] = $thumbnail;
			}
			
			# Player URL
			self::$video->player_url = 'http://www.youtube.com/v/'.self::$id;

			# Files URL
			self::$video->files = array();
			
			# FLV file URL
			// TODO: Récupération de l'URL du fichier flv
			// self::$video->flv_url = 'http://www.youtube.com/get_video.php?video_id='.self::$id;
			
			break;
			
			# --------------------------------------------------------------------------------
			default :
			
			throw new Exception('Unable to get the video data. Please make sure the service you’re trying to use is supported by Videopian.');
			
			break;
			
		}
		
		return self::$video;
	}

	# ================================================================================
	# Build the query for Imeem API
	# Documentation : http://www.imeem.com/developers/documentation/ws/wsoverview/wsappauth
	private function buildImeemQuery($method, $param) {
		
		$base_url = 'http://www.api.imeem.com/api/xml/';
		
		switch($method) {
			case 'mediaGetInfo':
			$args = array(
				'apiKey' => self::IMEEM_API_KEY,
				'mediaIds' => $param,
				'version' => '1.0'
			);
			break;
			
			case 'mediaSearch':
			$args = array(
				'apiKey' => self::IMEEM_API_KEY,
				'mediaType' => 'video',
				'query' => $param,
				'version' => '1.0'
			);
			break;
		}
		
		$httpquery = http_build_query($args);
		
		$sig = md5($method . preg_replace('#&amp;#', '', $httpquery) . self::IMEEM_API_SECRET);
		$query = $base_url . $method . '?' . $httpquery . '&amp;sig=' . $sig;
		
		return $query;
	}
}
?>