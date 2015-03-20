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
