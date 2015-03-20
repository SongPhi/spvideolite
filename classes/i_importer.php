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
interface SPVIDEOLITE_CLASS_IImporter
{
	public static function getRegExp();
	public static function getRegExpIdentifierIndex();
	public static function embedApplyVideoId($videoId);
	public static function getClipIdentifier( $url );
	public static function getClipDetailByUrl( $url );
	public static function getClipDetailByIdentifier( $id );
}