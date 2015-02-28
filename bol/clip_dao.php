<?php

class SPVIDEOLITE_BOL_ClipDao extends VIDEO_BOL_ClipDao {
	/**
     * Class instance
     *
     * @var VIDEO_BOL_ClipDao
     */
    private static $classInstance;
    
    const CACHE_TAG_VIDEO_LIST = 'video.list';

    /**
     * Class constructor
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns class instance
     *
     * @return VIDEO_BOL_ClipDao
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     * @return string
     */
    public function getDtoClassName()
    {
        return 'SPVIDEOLITE_BOL_Clip';
    }

    /**
     * Get clips list (featured|latest|toprated)
     *
     * @param string $listtype
     * @param int $page
     * @param int $limit
     * @return array of VIDEO_BOL_Clip
     */
    public function getClipsList( $listtype, $page, $limit, $plugin = 'video' )
    {
        $first = ($page - 1 ) * $limit;

        $cacheLifeTime = $first == 0 ? 24 * 3600 : null;
        $cacheTags = $first == 0 ? array(self::CACHE_TAG_VIDEO_LIST) : null;
        
        switch ( $listtype )
        {
            case 'featured':
                $clipFeaturedDao = VIDEO_BOL_ClipFeaturedDao::getInstance();

                $query = "
                    SELECT `c`.*
                    FROM `" . $this->getTableName() . "` AS `c`
                    LEFT JOIN `" . $clipFeaturedDao->getTableName() . "` AS `f` ON (`f`.`clipId`=`c`.`id`)
                    WHERE `c`.`status` = 'approved' AND `c`.`privacy` = 'everybody' AND `f`.`id` IS NOT NULL
                    	AND `c`.`plugin`= '".$plugin."'
                    ORDER BY `c`.`addDatetime` DESC
                    LIMIT :first, :limit";

                $qParams = array('first' => $first, 'limit' => $limit);

                return $this->dbo->queryForObjectList($query, 'VIDEO_BOL_Clip', $qParams, $cacheLifeTime, $cacheTags);

            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');
                $example->andFieldEqual('plugin', $plugin);
                $example->setOrder('`addDatetime` DESC');
                $example->setLimitClause($first, $limit);

                return $this->findListByExample($example, $cacheLifeTime, $cacheTags);
        }

        return null;
    }

    /**
     * Get user video clips list
     *
     * @param int $userId
     * @param $page
     * @param int $itemsNum
     * @param int $exclude
     * @return array of VIDEO_BOL_Clip
     */
    public function getUserClipsList( $userId, $page, $itemsNum, $exclude, $plugin = 'video' )
    {
        $first = ($page - 1 ) * $itemsNum;

        $example = new OW_Example();

        $example->andFieldEqual('status', 'approved');
        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('plugin', $plugin);

        if ( $exclude )
        {
            $example->andFieldNotEqual('id', $exclude);
        }

        $example->setOrder('`addDatetime` DESC');
        $example->setLimitClause($first, $itemsNum);

        return $this->findListByExample($example);
    }

    public function getUncachedThumbsClipsList( $limit, $plugin = 'video' )
    {
        $example = new OW_Example();
        $example->andFieldIsNull('thumbUrl');
        $example->andFieldEqual('plugin', $plugin);
        $example->andFieldNotEqual('provider', 'undefined');
        $example->setOrder('`thumbCheckStamp` ASC');
        $example->setLimitClause(0, $limit);

        return $this->findListByExample($example);
    }

    /**
     * Counts clips
     *
     * @param string $listtype
     * @return int
     */
    public function countClips( $listtype, $plugin = 'video' )
    {
        switch ( $listtype )
        {
            case 'featured':
                $featuredDao = VIDEO_BOL_ClipFeaturedDao::getInstance();

                $query = "
                    SELECT COUNT(`c`.`id`)       
                    FROM `" . $this->getTableName() . "` AS `c`
                    LEFT JOIN `" . $featuredDao->getTableName() . "` AS `f` ON ( `c`.`id` = `f`.`clipId` )
                    WHERE `c`.`status` = 'approved' AND `c`.`privacy` = 'everybody' AND `f`.`id` IS NOT NULL
                    AND `c`.`plugin` = '".$plugin."'
                ";

                return $this->dbo->queryForColumn($query);

                break;

            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');
                $example->andFieldEqual('plugin', $plugin);

                return $this->countByExample($example);

                break;
        }

        return null;
    }

    /**
     * Counts clips added by a user
     *
     * @param int $userId
     * @return int
     */
    public function countUserClips( $userId, $plugin = 'video' )
    {
        $example = new OW_Example();

        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('plugin', $plugin);
        $example->andFieldEqual('status', 'approved');

        return $this->countByExample($example);
    }
    
    public function findByUserId( $userId, $plugin = 'video' )
    {
        $example = new OW_Example();

        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('plugin', $plugin);

        return $this->findIdListByExample($example);
    }
    
    public function updatePrivacyByUserId( $userId, $privacy, $plugin = 'video' )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `privacy` = :privacy 
            WHERE `userId` = :userId AND `plugin` = '".$plugin."'";
        
        $this->dbo->query($sql, array('privacy' => $privacy, 'userId' => $userId));
    }
}
