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

class SPVIDEOLITE_CLASS_ClipService
{
    private static $classInstance = null;
    protected $originalClassInstance;
    
    public static function getInstance() {
        if (!(self::$classInstance instanceof SPVIDEOLITE_CLASS_ClipService)) {
            self::$classInstance = new self();
            $class = new ReflectionClass('VIDEO_BOL_ClipService');
            
            $property = $class->getProperty('classInstance');
            $property->setAccessible(true);
            $property->setValue(self::$classInstance);
            $property->setAccessible(false);

            $property = $class->getProperty('clipDao');
            $property->setAccessible(true);
            $property->setValue(self::$classInstance->originalClassInstance,SPVIDEOLITE_BOL_ClipDao::getInstance());
            $property->setAccessible(false);
        }
        
        return self::$classInstance;
    }
    
    public function formatClipDimensions($code, $width, $height) {
        if (!strlen($code)) return '';
        
        // keep the imported clip default size ratio

        return $code;
    }

    public function findClipById( $id )
    {
        $clip = $this->originalClassInstance->findClipById($id);
         // check for size lock
        $matches = array();
        if (preg_match_all('/sizelock/is', $clip->code, $matches)) {
            if (!defined('SPVIDEOLITE_SIZELOCK')) define('SPVIDEOLITE_SIZELOCK',1);
        }

        if (preg_match(SPVIDEOLITE_IMP_Youtube::$regexp, $clip->code, $matches)) {
            $youtubeClipId = $matches[SPVIDEOLITE_IMP_Youtube::$regexpIdIndex];
            preg_match_all("/width\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $width = isset($matches[2][0])?$matches[2][0]:"640";
            preg_match_all("/height\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $height = isset($matches[2][0])?$matches[2][0]:"360";
            $clip->code = <<<HTML
    <video id="spvideo_player" src="" class="video-js vjs-default-skin vjs-fill" controls preload="auto" width="{$width}" height="{$height}" poster="{$clip->thumbUrl}" ></video>
HTML;
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/video.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/sources/vjs.youtube.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/video-js.min.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/videojs.endcard.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/videojs.endcard.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/videojs.logobrand.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/videojs.logobrand.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            $overlayLogo = OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'img/spvideo-logo.png';

            OW::getDocument()->addOnloadScript(<<<JSCRIPT
                function getRelatedContent(callback) {
                    var el = document.createElement("p");
                    el.innerHTML = "So Cool You'll HAVE to Click This!"
                    setTimeout(function(){
                        // Needs an array
                        callback([el])
                    }, 0);
                }

                function getNextVid(callback) {
                    var anchor = document.createElement('a');
                    anchor.innerHTML = "Users will be taken to the VideoJS website after 10 seconds!"
                    anchor.href = "http://www.videojs.com/"
                    setTimeout(function(){
                        callback(anchor)
                    }, 0);
                }

                var video = videojs(
                        "#spvideo_player",
                        {
                            "autoplay": false,
                            "techOrder": ["youtube"],
                            "ytFullScreenControls": false,
                            "src": "http://www.youtube.com/watch?v={$youtubeClipId}"                            
                        },
                        function() {                            
                            video.endcard({
                                getRelatedContent: getRelatedContent
//                                , getNextVid: getNextVid
                            });
                            video.logobrand({
                                image: "{$overlayLogo}",
                                destination: "https://owdemo.songphi.com/video/"
                            });
                            this.play();
                        }
                    );
                
JSCRIPT
);
        }

        if (preg_match(SPVIDEOLITE_IMP_DailyMotion::$regexp, $clip->code, $matches)) {
            $dailymotionClipId = $matches[SPVIDEOLITE_IMP_DailyMotion::$regexpIdIndex];
            preg_match_all("/width\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $width = isset($matches[2][0])?$matches[2][0]:"640";
            preg_match_all("/height\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $height = isset($matches[2][0])?$matches[2][0]:"360";
            $clip->code = <<<HTML
    <video id="spvideo_player" src="" class="video-js vjs-default-skin vjs-fill" 
        controls preload="auto" width="{$width}" height="{$height}" 
        data-setup='{ "techOrder": ["dailymotion"], "src": "http://www.dailymotion.com/video/{$dailymotionClipId}" }'
        poster="{$clip->thumbUrl}" >
    </video>

HTML;
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/video.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/sources/dailymotion.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/video-js.min.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
        }

        if (preg_match(SPVIDEOLITE_IMP_Vimeo::$regexp, $clip->code, $matches)) {
            $vimeoClipId = $matches[SPVIDEOLITE_IMP_Vimeo::$regexpIdIndex];
            preg_match_all("/width\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $width = isset($matches[2][0])?$matches[2][0]:"640";
            preg_match_all("/height\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $height = isset($matches[2][0])?$matches[2][0]:"360";
            $clip->code = <<<HTML
    <video id="spvideo_player" src="" class="video-js vjs-default-skin vjs-fill" controls preload="auto" width="{$width}" height="{$height}" data-setup='{ "techOrder": ["vimeo"], "src": "//vimeo.com/{$vimeoClipId}" }'></video>
HTML;
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/video.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/sources/vjs.vimeo.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/video-js.min.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
        }

        if (preg_match(SPVIDEOLITE_IMP_Redtube::$regexp, $clip->code, $matches)) {
            $redtubeClipId = $matches[SPVIDEOLITE_IMP_Redtube::$regexpIdIndex];
            preg_match_all("/width\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $width = isset($matches[2][0])?$matches[2][0]:"640";
            preg_match_all("/height\=('|\")(\d+)('|\")/i", $clip->code, $matches);
            $height = isset($matches[2][0])?$matches[2][0]:"360";

            $sources = SPVIDEOLITE_IMP_Redtube::fetchDownloadLink($redtubeClipId);

            $vidSource = $sources[0][0];

            $clip->code = <<<HTML
    <video id="spvideo_player" class="video-js vjs-default-skin vjs-fill" controls preload="auto"
    width="{$width}" height="{$height}" poster="{$clip->thumbUrl}">
        {$vidSource}
        I'm sorry; your browser doesn't support HTML5 video.
    </video>
HTML;
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/video.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/video-js.min.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/videojs.endcard.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/videojs.endcard.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'js/vendor/videojs/videojs.logobrand.js?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);
            OW::getDocument()->addStylesheet(OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'css/vendor/videojs/videojs.logobrand.css?'.SPVIDEOLITE_BOL_Service::PLUGIN_VER);

            $overlayLogo = OW::getPluginManager()->getPlugin('spvideolite')->getStaticUrl() . 'img/spvideo-logo.png';

            OW::getDocument()->addOnloadScript(<<<JSCRIPT
                function getRelatedContent(callback) {
                    var el = document.createElement("p");
                    el.innerHTML = "So Cool You'll HAVE to Click This!"
                    setTimeout(function(){
                        // Needs an array
                        callback([el])
                    }, 0);
                }

                function getNextVid(callback) {
                    var anchor = document.createElement('a');
                    anchor.innerHTML = "Users will be taken to the VideoJS website after 10 seconds!"
                    anchor.href = "http://www.videojs.com/"
                    setTimeout(function(){
                        callback(anchor)
                    }, 0);
                }

                var video = videojs(
                        "#spvideo_player",
                        {
                            "autoplay": false,
                        },
                        function() {                            
                            
                            this.play();
                        }
                    );
                video.endcard({
                    getRelatedContent: getRelatedContent
//                                , getNextVid: getNextVid
                });
                video.logobrand({
                    image: "{$overlayLogo}",
                    destination: "https://owdemo.songphi.com/video/"
                });
                
JSCRIPT
);
        }

        return $clip;
    }

    
    public function validateClipCode($code, $provider = null) {

        if (SPVIDEOLITE_BOL_Configs::getInstance()->get('tweaks.force_https_compat')) {
            $code = str_replace("http://", "//", $code);
            $code = str_replace("https://", "//", $code);
        }

        // alternative way to validate embed code
        $iframeTag = "/<iframe.+?<\/iframe>/is";
        $embedTag = "/<embed.+?<\/embed>/is";
        $objectTag = "/<object.+?<\/object>/is";
        $videoTag = "/<video.+?<\/video>/is";
        $matches = array();
        if (preg_match_all($iframeTag, $code, $matches)) {
            return $matches[0][0];
        }
        if (preg_match_all($embedTag, $code, $matches)) {
            return $matches[0][0];
        }
        if (preg_match_all($objectTag, $code, $matches)) {
            return $matches[0][0];
        }
        if (preg_match_all($videoTag, $code, $matches)) {
            return $matches[0][0];
        }

        return '';
    }
    
    public function getClipThumbUrl($clipId, $code = null, $thumbUrl = null) {
        // some thumb caching mechanism here
        return preg_replace("#(http://|https://)#i", "//", $this->originalClassInstance->getClipThumbUrl($clipId, $code, $thumbUrl));
    }
    
    public function __call($method, $args) {
        if (!method_exists($this, $method)) return call_user_func_array(array($this->originalClassInstance, $method), $args);
        else return call_user_func_array(array($this, $method), $args);
    }
    
    public function __get($name) {
        $class = new ReflectionClass('VIDEO_BOL_ClipService');
        $property = $class->getProperty($name);
        
        $property->setAccessible(true);
        return $property->getValue($this->originalClassInstance);
    }
    
    private function __construct() {
        $this->originalClassInstance = VIDEO_BOL_ClipService::getInstance();
    }
}
