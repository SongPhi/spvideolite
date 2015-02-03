<?php

OW::getRouter()->addRoute(
  new OW_Route(
    'spvideolite.embed',
    'spvideo/embed/:videoId',
    'SPVIDEOLITE_CTRL_Spvideo',
    'embed'
  )
);