<?php

class SPVIDEO_CLASS_ClipService {
    private static $classInstance = null;
    private $originalClassInstance;

    public static function getInstance() {
        if ( !( self::$classInstance instanceof SPVIDEO_CLASS_ClipService ) ) {
            self::$classInstance = new self();
            $class = new ReflectionClass( 'VIDEO_BOL_ClipService' );
            $property = $class->getProperty( 'classInstance' );

            $property->setAccessible( true );
            $property->setValue( self::$classInstance );
            $property->setAccessible( false );
        }

        return self::$classInstance;
    }

    public function formatClipDimensions( $code, $width, $height )
    {
        if ( !strlen($code) )
            return '';

        //adjust width and height
        // $code = preg_replace("/width=(\"|')?[\d]+(px)?(\"|')?/i", 'width=${1}' . $width . '${3}', $code);
        // $code = preg_replace("/height=(\"|')?[\d]+(px)?(\"|')?/i", 'height=${1}' . $height . '${3}', $code);

        // $code = preg_replace("/width:( )?[\d]+(px)?/i", 'width:' . $width . 'px', $code);
        // $code = preg_replace("/height:( )?[\d]+(px)?/i", 'height:' . $height . 'px', $code);

        return $code;
    }

    public function findClipsList( $type, $page, $limit )
    {
        if ( $type == 'toprated' )
        {
            $first = ( $page - 1 ) * $limit;
            $topRatedList = BOL_RateService::getInstance()->findMostRatedEntityList('video_rates', $first, $limit);

            $clipArr = $this->clipDao->findByIdList(array_keys($topRatedList));

            $clips = array();

            foreach ( $clipArr as $key => $clip )
            {
                $clipArrItem = (array) $clip;
                $clips[$key] = $clipArrItem;
                $clips[$key]['score'] = $topRatedList[$clipArrItem['id']]['avgScore'];
                $clips[$key]['rates'] = $topRatedList[$clipArrItem['id']]['ratesCount'];
            }

            usort($clips, array('VIDEO_BOL_ClipService', 'sortArrayItemByDesc'));
        }
        else
        {
            $clips = $this->clipDao->getClipsList($type, $page, $limit);
        }

        $list = array();
        if ( is_array($clips) )
        {
            foreach ( $clips as $key => $clip )
            {
                $clip = (array) $clip;
                $list[$key] = $clip;
                $list[$key]['thumb'] = $this->getClipThumbUrl($clip['id'], $clip['code'], $clip['thumbUrl']);
            }
        }

        return $list;
    }

    public function updateClip( VIDEO_BOL_Clip $clip , $notify = true)
    {
        $this->clipDao->save($clip);
        
        $this->cleanListCache();

        if ($notify) {
            $event = new OW_Event(self::EVENT_AFTER_EDIT, array('clipId' => $clip->id));
            OW::getEventManager()->trigger($event);

            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'video',
                'entityType' => 'video_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            OW::getEventManager()->trigger($event);    
        }        

        return $clip->id;
    }

    public function findUserClipsList( $userId, $page, $itemsNum, $exclude = null ) {
        $clips = $this->clipDao->getUserClipsList($userId, $page, $itemsNum, $exclude);

        if ( is_array($clips) ) {
            $list = array();
            foreach ( $clips as $key => $clip ) {
                $clip = (array) $clip;
                $list[$key] = $clip;
                $list[$key]['thumb'] = $this->getClipThumbUrl($clip['id'], $clip['code'], $clip['thumbUrl']);
            }

            return $list;
        }

        return null;
    }

    public function findTaggedClipsList( $tag, $page, $limit ) {
        $first = ($page - 1 ) * $limit;

        $clipIdList = BOL_TagService::getInstance()->findEntityListByTag('video', $tag, $first, $limit);

        $clips = $this->clipDao->findByIdList($clipIdList);

        $list = array();
        if ( is_array($clips) ) {
            foreach ( $clips as $key => $clip ) {
                $clip = (array) $clip;
                if ($clip['status']!='approved') continue; // skip clips that werent approved yet
                $list[$key] = $clip;
                $list[$key]['thumb'] = $this->getClipThumbUrl($clip['id'], $clip['code'], $clip['thumbUrl']);
            }
        }

        return $list;
    }

    public function getClipThumbUrl( $clipId, $code = null, $thumbUrl = null ) {
        return $this->originalClassInstance->getClipThumbUrl( $clipId, $code, $thumbUrl );
    }

    public function __call( $method, $args ) {
        if ( !method_exists( $this, $method ) )
            return call_user_func_array( array( $this->originalClassInstance, $method ), $args );
        else
            return call_user_func_array( array( $this, $method ), $args );
    }

    public function __get( $name ) {
        $class = new ReflectionClass( 'VIDEO_BOL_ClipService' );
        $property = $class->getProperty( $name );

        $property->setAccessible( true );
        return $property->getValue( $this->originalClassInstance );
    }

    private function __construct() {
        $this->originalClassInstance = VIDEO_BOL_ClipService::getInstance();
    }

}
