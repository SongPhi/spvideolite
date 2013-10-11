<?php

@BOL_LanguageService::getInstance()->addPrefix('spvideo','Super Video');

$path = OW::getPluginManager()->getPlugin('spvideo')->getRootDir() . 'langs.zip';
@BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spvideo');

@OW::getPluginManager()->addPluginSettingsRouteName('spvideo', 'spvideo.admin');

// CREATE TABLE IF NOT EXISTS `ow_spvideo_upl_temp` (
//   `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;