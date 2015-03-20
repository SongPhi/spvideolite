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

