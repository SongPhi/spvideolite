<?php

try
{
    $sql = "SELECT * FROM `".OW_DB_PREFIX."video_clip` WHERE provider IN ('youtube','bliptv','vimeo','metacafe','dailymotion') AND (thumbUrl LIKE 'http://%' OR thumbUrl LIKE 'https://%')";

	$clips = Updater::getDbo()->queryForObjectList($sql,'OW_Entity');

	foreach ($clips as $clip) {
	    $clip->thumbUrl = preg_replace("#(http://|https://)#i", "//", $clip->thumbUrl);
	    Updater::getDbo()->updateObject(OW_DB_PREFIX."video_clip", $clip);
	}
}
catch ( Exception $e ){ }

