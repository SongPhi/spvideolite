<?php

try
{
	$sql = "SHOW COLUMNS FROM `".OW_DB_PREFIX."video_clip` LIKE 'plugin';";
    $cols = Updater::getDbo()->queryForList($sql);

    if (!count($cols)) {
    	$sql = "ALTER TABLE `".OW_DB_PREFIX."video_clip` ADD `plugin` VARCHAR(255) NULL DEFAULT 'video' ; ";
    	Updater::getDbo()->queryForList($sql);
    }

}
catch ( Exception $e ){ }

