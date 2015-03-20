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

 /**
* 
*/
class SPVIDEOLITE_CLASS_ImportService
{
	const IMPORTER_CLASS_PREFIX = 'SPVIDEOLITE_IMP_';

	private static $classInstance = null;

	private $importerModulePath;
	private $importers;

    public static function getInstance() {
        if ( !( self::$classInstance instanceof SPVIDEOLITE_CLASS_ImportService ) ) {
            self::$classInstance = new self();
        }
        return self::$classInstance;
    }

    public function checkClip($url) {
        foreach ($this->importers as $name => $importer) {
            $matches = array();
    		if (preg_match($importer['regexp'], $url, $matches)) {
    			$video = $this->getImporterInstance($name)->getClipDetailByUrl($url);
    			return $video;
			}
    	}
        throw new Exception("Unsupported video site", 1);
        
    }

    protected function __construct() {
    	$this->importerModulePath = OW::getPluginManager()->getPlugin( 'spvideolite' )->getRootDir() . 'importers' . DS;
    	$this->loadModules();
    }

    private function loadModules() {
    	$this->importers = array();

    	$files = scandir($this->importerModulePath);
        foreach ($files as $file) {
            if ($this->strEndWith($file,'.php')) {
                $className = OW::getAutoloader()->filenameToClass($file,'SPVIDEOLITE_IMP');
                $shortname = strtolower(substr($className,16));
                $importer = array(
                	'className' => $className,
                	'regexp' => $className::getRegExp(),
                	'regexpIdIndex' => $className::getRegExpIdentifierIndex(),
                	'instance' => null,
            	);
                $this->importers[$shortname] = $importer;
            }
        }
    }

    public function getImporterInstance($name) {
    	if (!isset($this->importers[$name])) 
    		throw new Exception("Error Processing Request", 1);
    	
    	if (is_null($this->importers[$name]['instance']))
    		$this->importers[$name]['instance'] = new $this->importers[$name]['className']();

    	return $this->importers[$name]['instance'];
    }

    private function strEndWith($haystack, $needle) {
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }
	    return (substr($haystack, -$length) === $needle);
	}
}