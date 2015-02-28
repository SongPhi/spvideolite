<?php

@BOL_LanguageService::getInstance()->addPrefix('spvideolite','Super Video Lite');

@$path = OW::getPluginManager()->getPlugin('spvideolite')->getRootDir() . 'langs.zip';
@BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spvideolite');

@OW::getPluginManager()->addPluginSettingsRouteName('spvideolite', 'spvideolite.admin');

OW::getDbo()->query($sql);

$sql = "SHOW COLUMNS FROM `".OW_DB_PREFIX."video_clip` LIKE 'plugin';";
$cols = OW::getDbo()->queryForList($sql);

if (!count($cols)) {
  $sql = "ALTER TABLE `".OW_DB_PREFIX."video_clip` ADD `plugin` VARCHAR(255) NULL DEFAULT 'video' ; ";
  OW::getDbo()->queryForList($sql);
}


// CREATE TABLE IF NOT EXISTS `ow_spvideo_upl_temp` (
//   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//   `userId` int(10) unsigned DEFAULT NULL,
//   `token` varchar(32) DEFAULT NULL,
//   `title` varchar(254) DEFAULT NULL,
//   `description` text,
//   `modified` int(11) DEFAULT NULL,
//   `filename` text,
//   `filesize` bigint(20) DEFAULT NULL,
//   `thumbUrl` text,
//   `isCompleted` tinyint(1) DEFAULT NULL,
//   PRIMARY KEY (`id`),
//   UNIQUE KEY `token` (`token`)
// ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


// CREATE TABLE IF NOT EXISTS `ow_spvideo_clip_format` (
//   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//   `clipId` int(10) unsigned DEFAULT NULL,
//   `format` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
//   `url` text CHARACTER SET utf8,
//   `size` bigint(20) unsigned DEFAULT NULL,
//   PRIMARY KEY (`id`)
// ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


// CREATE TABLE IF NOT EXISTS `ow_spvideo_clip` (
//   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//   `videoId` int(10) unsigned DEFAULT NULL,
//   `userId` int(10) unsigned DEFAULT NULL,
//   `totalSize` bigint(20) unsigned DEFAULT NULL,
//   `module` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
//   `status` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
//   PRIMARY KEY (`id`)
// ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


// CREATE TABLE `oxwall`.`ox_spvideo_categories` (
// `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
// `alias` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
// `video_count` INT UNSIGNED NULL
// ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;