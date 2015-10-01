SPVIDEO LITE (OXWALL PLUGIN)
====

## ABOUT

This plugin powers up basic Oxwall video plugin with useful tweaks. This lite version is a stripped down version of SPVIDEO Full which support even more features (upload, user video manager dashboard, categories, etc.).

## FEATURES

* Import video from link (no more copy and paste embeded code). See list of supported sites below.
* Show more/less video description (Youtube idea).
* Resize video player larger and restore (Youtube idea).
* Correct default video player size to fit the container.
* Fix long video title that make the listing ugly.
* Drag and drop link import (thanks to Pustak Sadan contribute this feature)
* YouTube default thumbnail quality configuration.

## FEATURE REQUEST & BUG REPORT

Visit our Issues Tracking page [https://code.songphi.org/projects/spvideolite/issues]. Registering an account is required for creating issues. 

## INSTALLATION

```Bash
# optional if you have not installed "bower" and "grunt-cli" already
npm install -g bower grunt-cli

cd path/to/ow_plugins
git clone git@github.com:SongPhi/spvideolite.git
cd spvideolite
npm install
bower install
grunt
```

## SUPPORTED SITES
* Allmyvideos.net
* BlipTV
* DailyMotion
* Facebook
* GoogleVideo
* MetaCafe
* Thevideo.me
* Youtube
* Vk
* Vidzi.tv
* Vimeo

## CHANGELOGS

### v2.0.2

* Bugfix: removed code that causing htmlarea error on adding video form.
* Bugfix: corrected regexp that cause problems from controllers/admin

### v2.0.1

* Added YouTube default thumbnail quality configuration.


## CONTRIBUTORS

* Thao Le ( developer )
* Pustak Sadan ( developer )
