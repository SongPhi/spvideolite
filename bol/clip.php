<?php

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