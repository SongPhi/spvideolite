<?php

/**
* 
*/
class SPVIDEO_IMP_DailyMotion implements SPVIDEO_CLASS_IImporter
{
	private static $regexp = '#dailymotion\.com.*/video/([^_]*)#i';
	private static $regexpIdIndex = 1;

	public static function getRegExp() {
		return self::$regexp;
	}

	public static function getRegExpIdentifierIndex() {
		return self::$regexpIdIndex;
	}
	
}