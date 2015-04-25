<?php

$plugin = OW::getPluginManager()->getPlugin('spvideolite');
try
{
    $sql = "SHOW COLUMNS FROM `".OW_DB_PREFIX."video_clip` LIKE 'plugin';";
    $cols = Updater::getDbo()->queryForList($sql);

    if (!count($cols)) {
        $sql = "ALTER TABLE `".OW_DB_PREFIX."video_clip` ADD `plugin` VARCHAR(255) NULL DEFAULT 'video' ; ";
        Updater::getDbo()->update($sql);
    }

}
catch ( Exception $e ){ }

// import new language keys
try {
	Updater::getLanguageService()->importPrefixFromZip($plugin->getRootDir().'langs.zip', 'spvideolite');
} catch (Exception $e) {
	
}

// refresh static cache
$staticDir = OW_DIR_STATIC_PLUGIN . $plugin->getModuleName() . DS;
$pluginStaticDir = OW_DIR_PLUGIN . $plugin->getModuleName() . DS . 'static' . DS;

if ( file_exists($staticDir) ) {
    UTIL_File::removeDir($staticDir);
}

mkdir($staticDir);
chmod($staticDir, 0777);
UTIL_File::copyDir($pluginStaticDir, $staticDir );

