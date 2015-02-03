<?php

/**
* 
*/
interface SPVIDEOLITE_CLASS_IImporter
{
	public static function getRegExp();
	public static function getRegExpIdentifierIndex();
	public static function embedApplyVideoId($videoId);
	public static function getClipIdentifier( $url );
	public static function getClipDetailByUrl( $url );
	public static function getClipDetailByIdentifier( $id );
}