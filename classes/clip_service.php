<?php
class SPVIDEOLITE_CLASS_ClipService
{
    private static $classInstance = null;
    private $originalClassInstance;
    
    public static function getInstance() {
        if (!(self::$classInstance instanceof SPVIDEOLITE_CLASS_ClipService)) {
            self::$classInstance = new self();
            $class = new ReflectionClass('VIDEO_BOL_ClipService');
            $property = $class->getProperty('classInstance');
            
            $property->setAccessible(true);
            $property->setValue(self::$classInstance);
            $property->setAccessible(false);
        }
        
        return self::$classInstance;
    }
    
    public function formatClipDimensions($code, $width, $height) {
        if (!strlen($code)) return '';
        
        // keep the imported clip default size ratio
        // remove %
        // $code = preg_replace("/width=(\"|')?[\d]+(%)?(\"|')?/i", 'width=${1}' . $width . '${3}', $code);
        // $code = preg_replace("/height=(\"|')?[\d]+(%)?(\"|')?/i", 'height=${1}' . $height . '${3}', $code);

        // // adjust width and height
        // $code = preg_replace("/width=(\"|')?[\d]+(px)?(\"|')?/i", 'width=${1}' . $width . '${3}', $code);
        // $code = preg_replace("/height=(\"|')?[\d]+(px)?(\"|')?/i", 'height=${1}' . $height . '${3}', $code);

        // $code = preg_replace("/width:( )?[\d]+(px)?/i", 'width:' . $width . 'px', $code);
        // $code = preg_replace("/height:( )?[\d]+(px)?/i", 'height:' . $height . 'px', $code);

        return $code;
    }
    
    public function validateClipCode($code, $provider = null) {
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
