var SPVideoClass = function(_baseUrl, _loadingEl, _detailElement) {
    this.baseUrl = _baseUrl;
    this.loadingElement = _loadingEl;
    this.detailElement = _detailElement;
    this.checkClipUrl = this.baseUrl + '/ajax_get_clip';
    this.checkClipFromElement = function(inputHandle) {
        var url = jQuery(inputHandle).val();
        this.checkClip(url);
    };
    this.checkClip = function(url) {
        var reqUrl = this.checkClipUrl;
        // show loadingel, hide detailel
        this.detailElement.css('display', 'none');
        this.loadingElement.css('display', 'block');
        jQuery.post(reqUrl, {
            "clipUrl": url
        }, jQuery.proxy(this.handleCheckClipResponse, this), "json").fail(jQuery.proxy(this.handleCheckClipError, this));
    };
    this.handleCheckClipResponse = function(data) {
        // hide loadingel, show detailel
        this.detailElement.css('display', 'block');
        this.loadingElement.css('display', 'none');
        // display response
        this.detailElement.html('');
        if (!data.error) {
            this.detailElement.html(Base64.decode(data.formHtml));
            var tagsField = $('form[name=SPVideoAddForm]').find('input[name=tags]');
            eval(Base64.decode(data.script));
        } else {
            OW.warning(data.errMsg);
        }
        $('#spvideo_btn_check').removeClass('ow_inprogress');
    };
    this.handleCheckClipError = function(data) {
        //TODO: code here
    };
    this.addEnlargeButton = function() {
        $('<div id="enlarged-remaining" class="ow_superwide ow_left" style="display:none"></div>').insertAfter($('.ow_video_player').parent());
        $('#btn-resize-player').click(function() {
            var parent = $('.ow_video_player').parent();
            var player = $('.ow_video_player');
            var iframe = $('.ow_video_player iframe');
            var remains = $('#enlarged-remaining');
            var origHeight = iframe.height();
            if (player.attr('data-origheight')) {
                origHeight = parseInt(player.attr('data-origheight'));
            } else {
                player.attr('data-origheight', origHeight);
            }
            if (parent.hasClass('ow_left')) {
                $('.ow_right').animate({
                    "margin-top": player.height() + "px"
                }, 500, function() {
                    $('.ow_right').css('margin-top', '');
                    parent.removeClass();
                    parent.addClass('ow_stdmargin');
                    var newHeight = iframe.height() * (player.width() / iframe.width());
                    if (newHeight > 600) {
                        iframe.height(600);
                    } else {
                        iframe.height(newHeight);
                    }
                    iframe.width(player.width());
                    $('#btn-resize-player a').html(OW.getLanguageText('spvideolite', 'btn_smaller'));
                    remains.show();
                    $('.ow_video_description').appendTo(remains);
                    $('#video-show-more').appendTo(remains);
                    $('#video-show-less').appendTo(remains);
                    $('div[id^=comments-video]').appendTo(remains);
                });
            } else {
                $('.ow_right').css('margin-top', player.height() + 'px');
                parent.removeClass();
                parent.addClass('ow_superwide');
                parent.addClass('ow_left');
                iframe.width(player.width());
                iframe.height(origHeight);
                $('#btn-resize-player a').html(OW.getLanguageText('spvideolite', 'btn_larger'));
                $('.ow_video_description').appendTo(parent);
                $('#video-show-more').appendTo(parent);
                $('#video-show-less').appendTo(parent);
                $('div[id^=comments-video]').appendTo(parent);
                remains.hide();
                $('.ow_right').animate({
                    "margin-top": "0px"
                }, 500);
            }
        });
    };
    this.correctPlayerSize = function() {
        var parent = $('.ow_video_player').parent();
        var player = $('.ow_video_player');
        var iframe = $('.ow_video_player iframe');
        var iframeWidth = parseInt($(iframe).attr("width"));        
        var remains = $('#enlarged-remaining');
        var newHeight = Math.round(iframe.height() * (player.width() / iframeWidth));
        iframe.css("height",newHeight+"px",'important');
        iframe.css("width",player.width()+"px",'important');
    };
    this.fixLongTitles = function() {
        $('.ow_video_item_title').each(function(index, e) {
            var $e = $(e);
            var title = $e.html().trim();
            if ($e.height() > 42) {
                $e.css({
                    'max-height': '40px',
                    'overflow': 'hidden'
                });
                $e.parent().attr('onmouseover', "$(this).find('.ow_video_item_title').css({'max-height':'','overflow':''})");
                $e.parent().attr('onmouseout', "$(this).find('.ow_video_item_title').css({'max-height':'40px','overflow':'hidden'})");
            }
        });
    };
    this.showLessDescription = function() {
        if ($('.ow_video_description').height() > 60) {
            $('.ow_video_description').attr('origheight', $('.ow_video_description').height());
            $('.ow_video_description').css({
                'max-height': '60px',
                'overflow': 'hidden'
            });
            $('<div id="video-show-less" class="ow_small ow_txtcenter" style="margin-top:5px;margin-bottom:10px;border-top:1px dashed #aaa;display:none"><span id="spvideo-desc-show-less" class="ow_lbutton" style="margin-top:-8px">'+OW.getLanguageText('spvideolite', 'btn_show_less')+'</span></div>').insertAfter($('.ow_video_description'));
            $('<div id="video-show-more" class="ow_small ow_txtcenter" style="margin-top:5px;margin-bottom:10px;border-top:1px dashed #aaa"><span id="spvideo-desc-show-more" class="ow_lbutton" style="margin-top:-8px">'+OW.getLanguageText('spvideolite', 'btn_show_more')+'</span></div>').insertAfter($('.ow_video_description'));
            $('#spvideo-desc-show-more').click(function() {
                $('.ow_video_description').animate({
                    'max-height': $('.ow_video_description').attr('origheight') + 'px'
                }, 300, function() {
                    $('#spvideo-desc-show-more').parent().hide();
                    $('#spvideo-desc-show-less').parent().show();
                });
            });
            $('#spvideo-desc-show-less').click(function() {
                $('.ow_video_description').css({
                    'overflow': 'hidden'
                });
                $('.ow_video_description').animate({
                    'max-height': '60px'
                }, 300, function() {
                    $('#spvideo-desc-show-less').parent().hide();
                    $('#spvideo-desc-show-more').parent().show();
                });
            });
        }
    };
    this.addCategoriesList = function(url) {
        $('<li class="_categories"><a href="' + url + '"><span class="ow_ic_folder">Categories</span></a></li>').insertBefore($('.ow_content_menu li').last());
        $('<div id="categories-list" style="display:none;position:absolute;"><ul><li>Teen</li><li>Amateur</li></ul></div>').appendTo($('body'));
        $('.ow_content_menu ._categories').mouseenter(function() {
            if ($('#categories-list').css('display') != 'none') {
                clearTimeout($('.ow_content_menu ._categories').data('timeoutId'));
                return false;
            }
            $('.ow_content_menu ._categories').addClass('active');
            $('#categories-list').css('top', ($('.ow_content_menu ._categories').offset().top + $('.ow_content_menu ._categories').height()) + 'px');
            $('#categories-list').css('left', $('.ow_content_menu ._categories').offset().left + 'px');
            $('#categories-list').slideDown(200).show();
        }).mouseleave(function() {
            var timeoutId = setTimeout(function() {
                $('.ow_content_menu ._categories').removeClass('active');
                $('#categories-list').slideUp(200, function() {
                    $('#categories-list').hide();
                });
            }, 400);
            $('#categories-list').data('timeoutId', timeoutId);
        });
        $('#categories-list').mouseenter(function() {
            clearTimeout($('#categories-list').data('timeoutId'));
        }).mouseleave(function() {
            var timeoutId = setTimeout(function() {
                $('.ow_content_menu ._categories').removeClass('active');
                $('#categories-list').slideUp(200, function() {
                    $('#categories-list').hide();
                });
            }, 400);
            $('.ow_content_menu ._categories').data('timeoutId', timeoutId);
        });
    };
}
var SPVideo = new SPVideoClass();