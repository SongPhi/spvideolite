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

class SPVIDEOLITE_BOL_Clip extends OW_Entity
{
    /**
     * Clip owner
     *
     * @var int
     */
    public $userId;
    /**
     * Embeddable code
     *
     * @var string
     */
    public $code;
    /**
     * Clip title
     *
     * @var string
     */
    public $title;
    /**
     * Clip description
     *
     * @var string
     */
    public $description;
    /**
     * Date and time clip was added
     *
     * @var int
     */
    public $addDatetime;
    /**
     * Embed code provider like 'youtube', 'metacafe' etc.
     *
     * @var string
     */
    public $provider;
    /**
     * Clip approval status ('approval' | 'approved' | 'blocked')
     *
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $privacy;
    /**
     * @var string
     */
    public $thumbUrl;
    /**
     * @var int
     */
    public $thumbCheckStamp;
    /**
     * Returns user id
     *
     * @return int
     */

    public $plugin;
    /**
     * Returns user id
     *
     * @return int
     */

    public function getUserId()
    {
        return $this->userId;
    }
}