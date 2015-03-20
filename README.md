SPVIDEO LITE (OXWALL PLUGIN)
====

##ABOUT

This plugin powers up basic Oxwall video plugin with useful tweaks. This lite version is a stripped down version of SPVIDEO Full which support even more features (upload, user video manager dashboard, categories, etc.).


##FEATURES

* Import video from link (no more copy and paste embeded code). Currently supports video sites: BlipTV, DailyMotion, GoogleVideo, MetaCafe, Vimeo, Youtube.
* Show more/less video description (Youtube idea).
* Resize video player larger and restore (Youtube idea).
* Correct default video player size to fit the container.
* Fix long video title that make the listing ugly.


##FEATURE REQUEST & BUG REPORT

Visit our Issues Tracking page [https://code.songphi.org/projects/spvideolite/issues]. Registering an account is required for creating issues. 

##INSTALLATION

```Bash
# optional if you have not installed "bower" and "grunt-cli" already
npm install -g bower grunt-cli

cd path/to/ow_plugins
git clone git@github.com:SongPhi/spvideolite.git
cd spvideolite
npm install --dev
bower install
grunt
```
