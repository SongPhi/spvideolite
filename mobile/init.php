<?php

OW::getRouter()->addRoute(
  new OW_Route(
    'spvideo.embed',
    'spvideo/embed/:videoId',
    'SPVIDEO_CTRL_Spvideo',
    'embed'
  )
);