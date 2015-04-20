<?php

try {
    BOL_LanguageService::getInstance()->addPrefix('spvideolite','Super Video Lite');
} catch (Exception $e) {
    
}

@$path = OW::getPluginManager()->getPlugin('spvideolite')->getRootDir() . 'langs.zip';
@BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spvideolite');

@OW::getPluginManager()->addPluginSettingsRouteName('spvideolite', 'spvideolite.admin');

$sql = "SHOW COLUMNS FROM `".OW_DB_PREFIX."video_clip` LIKE 'plugin';";
$cols = OW::getDbo()->queryForList($sql);

if (!count($cols)) {
  $sql = "ALTER TABLE `".OW_DB_PREFIX."video_clip` ADD `plugin` VARCHAR(255) NULL DEFAULT 'video' ; ";
  OW::getDbo()->update($sql);
}
