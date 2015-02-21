<?php

$plugin = OW::getPluginManager()->getPlugin('spvideolite');

$staticDir = OW_DIR_STATIC_PLUGIN . $plugin->getModuleName() . DS;
$staticJsDir = $staticDir  . 'js' . DS;

if ( !file_exists($staticDir) )
{
    mkdir($staticDir);
    chmod($staticDir, 0777);
}

if ( !file_exists($staticJsDir) )
{
    mkdir($staticJsDir);
    chmod($staticJsDir, 0777);
}

@copy($plugin->getStaticJsDir() . 'spvideo.js', $staticJsDir . 'spvideo.js');
