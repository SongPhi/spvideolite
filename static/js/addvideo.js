/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * @author Kairat Bakitow <kainisoft@gmail.com>
 * @package ow_plugins.spvideolite
 * @since 1.6.1
 */
(function( window, $ ) {'use strict';

    $.event.props.push('dataTransfer');
    
    var _vars = $.extend({}, (window.ajaxAddVideoParams || {}), {
        isHTML5: window.hasOwnProperty('FormData'),
        urls: [],
        UPLOAD_THREAD_COUNT: 3
    }),
    _elements = {},
    _methods = {
        createSlot: function()
        {
            var slotPrototype = _elements.slotPrototype.clone(true);
            var id = 'slot-' + (++_elements.slotCounter);
            
            slotPrototype.attr('id', id).appendTo(_elements.slotArea);
            _elements.slotData[id] = slotPrototype;
            
            return id;
        },
        destroySlot: function( slotId, id )
        {
            if ( !_methods.isSlotExist(slotId) )
            {
                return;
            }
            
            _methods.afterAddTask();
            
            _elements.slotData[slotId].animate({opacity: '0'}, 300, function()
            {
                /*if ( id != null )
                {
                    $.ajax(
                    {
                        url: _vars.deleteAction,
                        data: {id: id},
                        cache: false,
                        type: 'POST'
                    });
                }*/
                
                _elements.slotData[slotId].remove();
                _elements.descEditors[slotId].setValue('');
                _elements.descEditors[slotId].clearHistory();
                
                delete _elements.slotData[slotId];
                delete _elements.descEditors[slotId];
                delete _elements.descCache[slotId];
                delete _elements.relations[slotId];
                delete _elements.titleCache[slotId];
                delete _elements.codeCache[slotId];
                delete _elements.thumbnailCache[slotId];
                delete _elements.tagsCache[slotId];
                /*
                if(id == null)
                {
                    id = slotId.split("-")[1];
                }
                var rotateId = 'data[' + id + '][rotate]';
                var descId = 'data[' + id + '][desc]';
                var codeId = 'data[' + id + '][code]';
                var clipURLId = 'data[' + id + '][thumbnail]';
                var titleId = 'data[' + id + '][title]';
                var tagsId = 'data[' + id + '][tags]';
                
                owForms['ajax-upload'].deleteElement(rotateId);
                owForms['ajax-upload'].deleteElement(descId);
                owForms['ajax-upload'].deleteElement(codeId);
                owForms['ajax-upload'].deleteElement(titleId);
                owForms['ajax-upload'].deleteElement(clipURLId);
                owForms['ajax-upload'].deleteElement(tagsId);
                */
            });
        },
        updateSlot: function( slotId, clipURL, id1, descStr, title, code, tags)
        {
            var id = slotId.split("-")[1];
            if ( !slotId || !clipURL || !id || !_methods.isSlotExist(slotId) )
            {
                return;
            }
            
            _methods.afterAddTask();
            
            var slot = _elements.slotData[slotId];
            
            var rotateId = 'data[' + id + '][rotate]';
            slot.find('[name="rotate"]').attr({id: rotateId, name: rotateId, value:0});
            
            var descId = 'data[' + id + '][desc]';
            var descSlot = slot.find('[name="desc"]');
            descSlot.attr({id: descId, name: descId});
            
            var codeId = 'data[' + id + '][code]';
            slot.find('[name="code"]').attr({id: codeId, name: codeId, value: code});
            
            var clipURLId = 'data[' + id + '][thumbnail]';
            slot.find('[name="thumbnail"]').attr({id: clipURLId, name: clipURLId, value: clipURL});
            
            var titleId = 'data[' + id + '][title]';
            var titleSlot = slot.find('[name="title"]');
            titleSlot.attr({id: titleId, name: titleId, value: title});
            titleSlot.append(tags);

            var tagsId = 'data[' + id + '][tags]';
            var tagsSlot = slot.find('[name="tags"]');
            tagsSlot.attr({id: tagsId, name: tagsId});
            tagsSlot.append(title);
            
            _elements.titleCache[slotId] = title;
            _elements.codeCache[slotId] = code;
            _elements.descCache[slotId] = descStr;
            _elements.relations[slotId] = id;
            _elements.thumbnailCache[slotId] = clipURL;
            _elements.tagsCache[slotId] = tags;
            _methods.handleFocus(slotId);
            

            
            owForms['ajax-upload'].addElement(new OwFormElement(rotateId, rotateId));
            owForms['ajax-upload'].addElement(new OwFormElement(descId, descId));
            owForms['ajax-upload'].addElement(new OwFormElement(codeId, codeId));
            owForms['ajax-upload'].addElement(new OwFormElement(titleId, titleId));
            owForms['ajax-upload'].addElement(new OwFormElement(clipURLId, clipURLId));
            owForms['ajax-upload'].addElement(new OwFormElement(tagsId, tagsId));
            
            slot.find('.ow_video_preview_x').on('click', function()
            {
                _methods.destroySlot(slotId, id);
            });
            slot.find('.ow_video_preview_rotate').on('click', function()
            {
                var clip = slot.find('.ow_video_preview_image'), _rotate;
                var rotate = (_rotate = clip.data('rotate')) === undefined ? 90 : _rotate;
                
                clip.rotate(rotate);
                slot.find('[name="' + rotateId + '"]').val(rotate);
                clip.data('rotate', rotate += 90);
            });
            
            var img = new Image();
            
            img.onload = function()
            {
                slot.find('.ow_video_preview_image')
                    .hide(0, function()
                    {
                        this.style.backgroundImage = 'url(' + img.src + ')';
                        $(this).removeClass('ow_video_preview_loading').fadeIn(300);
                        
                        OW.trigger('photo.onRenderUploadSlot', [_elements.descEditors[slotId]], slot);
                    });
            };
            img.src = clipURL;
            

        },
        handleBlur: function(slotId)
        {
            if ( !_methods.isSlotExist(slotId) )
            {
                return;
            }
            var limit = 5000;
            var maxLines = 20;
            var slot = _elements.slotData[slotId];
            var editor = _elements.descEditors[slotId];
            var lineCount = editor.lineCount();
            var value = editor.getValue().trim(), lineCount;
            if ( value.length === 0 || value === OW.getLanguageText('spvideolite', 'describe_video') )
            {
                $(editor.display.wrapper).addClass('invitation');
                if ( _elements.descCache.hasOwnProperty(slotId) )
                {
                    var value = _elements.descCache[slotId].trim(), lineCount;
                    if ( value.length > limit )
                    {
                        editor.setValue(value.substring(0, limit) + '...');
                    }else
                    {
                        editor.setValue(value);
                    }
                }else
                {
                    //editor.setValue(OW.getLanguageText('spvideolite', 'describe_video'));
                }
            }
            else if ( (lineCount = editor.lineCount()) > maxLines )
            {
                editor.setLine(2, editor.getLine(2).substring(0, 20) + '...');

                for ( var i = maxLines; i < lineCount; i++ )
                {
                    editor.removeLine(maxLines);
                }
            }
            else
            {
                
                /*
                switch ( lineCount )
                {
                    case 1: limit = 70; break;
                    case 2: limit = 50; break;
                    case 3: limit = 20; break;
                }*/

                if ( value.length > limit )
                {
                    editor.setValue(value.substring(0, limit) + '...');
                }
            }
            
            editor.setSize('100%', 58 + 'px');
            
            _elements.descCache[slotId] = value;
            slot.find('.ow_video_preview_image').removeClass('ow_video_preview_image_active');
            
            if ( _elements.slotArea.find('.ow_video_preview_image_active').length === 0 )
            {
                _elements.slotArea.removeClass('ow_video_preview_image_filtered');
            }
        },
        handleFocus: function (slotId)
        {
            if ( !_methods.isSlotExist(slotId) )
            {
                return;
            }
            var maxLines = 20;
            var slot = _elements.slotData[slotId];
            var editor = _elements.descEditors[slotId];
            var lineCount = editor.lineCount();
            var value = editor.getValue().trim(), lineCount;
            $(editor.display.wrapper).removeClass('invitation');
            var value = editor.getValue().trim();
            if ( value.length === 0 || value === OW.getLanguageText('spvideolite', 'describe_video') )
            {
                if ( _elements.descCache.hasOwnProperty(slotId) )
                {
                    editor.setValue(_elements.descCache[slotId]);
                }
            }
            else if ( lineCount > maxLines )
            {
                editor.setLine(2, editor.getLine(2).substring(0, 20) + '...');

                for ( var i = maxLines; i < lineCount; i++ )
                {
                    editor.removeLine(maxLines);
                }
            }
            else
            {
                var value = editor.getValue().trim();
            
                if ( value === OW.getLanguageText('spvideolite', 'describe_video') )
                {
                    editor.setValue('');
                }
            }
            
            var height = editor.doc.height;
            
            switch ( true )
            {
                case height <= 42:
                    editor.setSize('100%', 58 + 'px');
                    break;
                case height > 42 && height < 108:
                    editor.setSize('100%', height + 14 + 'px');
                    editor.scrollTo(0, height + 14);
                    break;
                default:
                    editor.setSize('100%', '108px');
                    editor.scrollTo(0, 108);
                    break;
            }
            
            setTimeout(function()
            {
                editor.setCursor(editor.lineCount(), 0);
            }, 1);
             
            //_elements.slotArea.addClass('ow_video_preview_image_filtered');
            slot.find('.ow_video_preview_image').addClass('ow_video_preview_image_active');
            
            //change
            var height = editor.doc.height;
                
            switch ( true )
            {
                case height <= 42:
                    editor.setSize('100%', 58 + 'px');
                    break;
                case height > 42 && height < 108:
                    editor.setSize('100%', height + 14 + 'px');
                    break;
                default:
                    editor.setSize('100%', '108px');
                    break;
            }
            editor.setSize('100%', 58 + 'px');
        },
        initHashtagEditor: function( slotId )
        {
            if ( !_methods.isSlotExist(slotId) )
            {
                return;
            }
            var limit = 5000;
            var maxLines = 20;
            var slot = _elements.slotData[slotId];
            var editor = _elements.descEditors[slotId] = CodeMirror.fromTextArea(slot.find('textarea')[0], {mode: "text/hashtag", lineWrapping: true, extraKeys: {Tab: false}});
            var lineCount = editor.lineCount();
            var value = editor.getValue().trim(), lineCount;
            //editor.setValue(OW.getLanguageText('spvideolite', 'describe_video'));
            editor.on('blur', function( editor )
            {
                var lineCount = editor.lineCount();
                var value = editor.getValue().trim(), lineCount;
                
                if ( value.length === 0 || value === OW.getLanguageText('spvideolite', 'describe_video') )
                {
                    $(editor.display.wrapper).addClass('invitation');
                    if ( _elements.descCache.hasOwnProperty(slotId) )
                    {
                        var value = _elements.descCache[slotId].trim(), lineCount;
                        if ( value.length > limit )
                        {
                            editor.setValue(value.substring(0, limit) + '...');
                        }else
                        {
                            editor.setValue(value);
                        }
                    }else
                    {
                        //editor.setValue(OW.getLanguageText('spvideolite', 'describe_video'));
                    }
                }
                else if (  lineCount > maxLines )
                {
                    editor.setLine(2, editor.getLine(2).substring(0, 20) + '...');

                    for ( var i = maxLines; i < lineCount; i++ )
                    {
                        editor.removeLine(maxLines);
                    }
                }
                else
                {
                    /*
                    
                    switch ( lineCount )
                    {
                        case 1: limit = 70; break;
                        case 2: limit = 50; break;
                        case 3: limit = 20; break;
                    }
                    */
                    if ( value.length > limit )
                    {
                        editor.setValue(value.substring(0, limit) + '...');
                    }
                }
                
                editor.setSize('100%', 58 + 'px');
                
                _elements.descCache[slotId] = value;
                slot.find('.ow_video_preview_image').removeClass('ow_video_preview_image_active');
                
                if ( _elements.slotArea.find('.ow_video_preview_image_active').length === 0 )
                {
                    _elements.slotArea.removeClass('ow_video_preview_image_filtered');
                }
                
                //_methods.handleBlur(slotId);
            });
            editor.on('focus', function( editor )
            {
                $(editor.display.wrapper).removeClass('invitation');
                var value = editor.getValue().trim();
                var lineCount = editor.lineCount();
                if ( value.length === 0 || value === OW.getLanguageText('spvideolite', 'describe_video') )
                {
                    if ( _elements.descCache.hasOwnProperty(slotId) )
                    {
                        editor.setValue(_elements.descCache[slotId]);
                    }
                }
                else if ( lineCount > maxLines )
                {
                    editor.setLine(2, editor.getLine(2).substring(0, 20) + '...');

                    for ( var i = maxLines; i < lineCount; i++ )
                    {
                        editor.removeLine(maxLines);
                    }
                }
                else
                {
                    var value = editor.getValue().trim();
                
                    if ( value === OW.getLanguageText('spvideolite', 'describe_video') )
                    {
                        editor.setValue('');
                    }
                }
                
                var height = editor.doc.height;
                
                switch ( true )
                {
                    case height <= 42:
                        editor.setSize('100%', 58 + 'px');
                        break;
                    case height > 42 && height < 108:
                        editor.setSize('100%', height + 14 + 'px');
                        editor.scrollTo(0, height + 14);
                        break;
                    default:
                        editor.setSize('100%', '108px');
                        editor.scrollTo(0, 108);
                        break;
                }
                
                setTimeout(function()
                {
                    editor.setCursor(editor.lineCount(), 0);
                }, 1);
                 
                _elements.slotArea.addClass('ow_video_preview_image_filtered');
                slot.find('.ow_video_preview_image').addClass('ow_video_preview_image_active');
                
                //_methods.handleFocus(slotId);
            });
            editor.on('change', function( editor )
            {
                var height = editor.doc.height;
                
                switch ( true )
                {
                    case height <= 42:
                        editor.setSize('100%', 58 + 'px');
                        break;
                    case height > 42 && height < 108:
                        editor.setSize('100%', height + 14 + 'px');
                        break;
                    default:
                        editor.setSize('100%', '108px');
                        break;
                }
            });
            editor.setSize('100%', 58 + 'px');
            
        },
        isSlotExist: function( slotId )
        {
            return slotId && _elements.slotData.hasOwnProperty(slotId);
        },
        requestSuccess: function( jsonStr, slotId )
        {
            if ( !jsonStr || !slotId )
            {
                return false;
            }
            
            var data;
                            
            try
            {
                data = JSON.parse(jsonStr);
            }
            catch( e )
            {
                OW.error(e);
                _methods.destroySlot(slotId);

                return false;
            }

            if ( data && data.status )
            {
                switch ( data.status )
                {
                    case 'success':
                        _methods.updateSlot(slotId, data.fileUrl, data.id, data.description, data.title, data.code, data.tags);
                        break;
                    case 'error':
                    default:
                        _methods.destroySlot(slotId);

                        OW.error(data.msg);
                        break;
                }
            }
            else
            {
                _methods.destroySlot(slotId);
                OW.error(OW.getLanguageText('spvideolite', 'not_all_videos_added'));
            }
        },
        setIsRuning: function()
        {
            _vars.isRuning = true;
            OW.inProgressNode($(':submit', owForms['ajax-upload'].form));
        },
        pushURL: function( url )
        {
            if ( !url  || !(_vars.isHTML5) )
            {
                return;
            }
            
            _vars.urls.push(url);
            
            if ( !_vars.isRuning )
            {
                _methods.setIsRuning();
                _methods.runAsyncAddVideo(_vars.UPLOAD_THREAD_COUNT);
            }
            /*SPVideo.loadingElement = $('#checkClip_loading2');
            SPVideo.detailElement = $('#checkClip_resp_place_holder2');
            SPVideo.checkClip( url );
            $('#spvideo_btn_check').addClass('ow_inprogress');*/
        },
        runAsyncAddVideo: function( count )
        {
            count = isNaN(+count) ? 1 : count;
            
            for ( var i = 0; i < count; i++ )
            {
                var url = _vars.urls.shift();
                
                if ( url != null )
                {
                    _methods.addVideo(url);
                }
            }
        },
        addVideo: function( url )
        {
            var slotId;
            
            if ( _vars.isHTML5 )
            {
                var formData = new FormData();

                formData.append('clipUrl', url);

                $.ajax(
                {
                    isVideoAdd: true,
                    url: _vars.actionUrl,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    timeout: 60000,
                    beforeSend: function( jqXHR, settings )
                    {
                        slotId = _methods.createSlot();
                        _methods.initHashtagEditor(slotId);
                    },
                    success: function( response, textStatus, jqXHR )
                    {
                        _methods.requestSuccess(response, slotId);
                    },
                    error: function( jqXHR, textStatus, errorThrown )
                    {
                        OW.error(textStatus + ': ' + url);
                        _methods.destroySlot(slotId);

                        throw textStatus;
                    },
                    complete: function( jqXHR, textStatus )
                    {
                        if ( textStatus === 'success' && jqXHR.responseText.length === 0 )
                        {
                            _methods.destroySlot(slotId);
                        }
                    }
                });
            }
            else
            {
                slotId = _methods.createSlot();

                _elements.dropArea.off('click').on('click', function(){alert(OW.getLanguageText('spvideolite', 'please_wait'))});
                _elements.uploadForm.submit();
                _elements.iframeForm.off().load(function()
                {
                    _elements.dropArea.off('click').on('click', function()
                    {
                        
                    });
                    
                    _methods.requestSuccess($(this).contents().find('body').html(), slotId);
                });
            }
        },
		afterAddTask: function()
        {
            if ( _vars.urls.length !== 0 )
            {
                setTimeout(function()
                {
                    _methods.runAsyncAddVideo();
                }, 10);
            }
            else
            {
                _vars.isRuning = false;
                OW.activateNode($(':submit', owForms['ajax-upload'].form));
            }
        },
        getFormData: function()
        {
            var data = [];
            var result = [];
            var i = 0;
            $.each(_elements.relations, function( index )
            {
                var desc, title, code, thumbnail, tags;
                var id = index.split("-")[1];
                
                data[i] = [];
                
                //Thumbnail
                thumbnail = _elements.thumbnailCache[index].trim();
                var descId = 'data[' + id + '][desc]';
                // desc = document.getElementById(descId).value;
                desc = _elements.descEditors[index].getValue().trim();
                if ( desc.length === 0 )
                {
                    if ( _elements.descCache.hasOwnProperty(index) )
                    {
                        desc = _elements.descCache[index].trim();
                    }
                    else
                    {
                        data[i]['desc'] = '';
                    }
                }
                else
                {
                    data[i]['desc'] = desc;
                }
                
                //Title
                var titleId = 'data[' + id + '][title]';
                title = document.querySelector('[name="'+titleId+'"]').value;
                
                if ( title.length === 0 )
                {
                    title = _elements.titleCache[index].trim();
                    if ( title.length === 0 )
                    {
                        data[i]['title'] = '';
                    }
                }
                else
                {
                    data[i]['title'] = title.trim();
                }
                
                //Tags
                var tagsId = 'data[' + id + '][tags]';
                tags = document.querySelector('[name="'+tagsId+'"]').value;
                
                if ( tags.length === 0 )
                {
                    tags = _elements.tagsCache[index].trim();
                    if ( tags.length === 0 )
                    {
                        data[i]['tags'] = '';
                    }
                }
                else
                {
                    data[i]['tags'] = tags.trim();
                }
                
                //Code
                code = _elements.codeCache[index].trim();
                if ( code.length === 0 )
                {
                    data[i]['code'] = '';
                }
                else
                {
                    data[i]['code'] = code;
                }
                data[i]['rotate'] = 0;
                data[i]['thumbnail'] = thumbnail;
                result.push(data[i]);
                i++;
            });
            return result;
        },
        submitVideo: function( )
        {
            if ( _vars.isHTML5 )
            {
                var data = [];
                var data1 = [];
                var jsonStr;
                var formData = new FormData();
                
                data = _methods.getFormData();

                for(var i=0; i < data.length; i++)
                {
                    for(var key in data[i])
                    {
                        formData.append('data['+i+']['+key+']',data[i][key]);
                    }
                }
                
                $.ajax(
                {
                    isVideoAdd: true,
                    url: _vars.submitUrl,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    timeout: 60000,
                    beforeSend: function( jqXHR, settings )
                    {
                        //alert("beforesend");
                    },
                    success: function( response, textStatus, jqXHR )
                    {
                        var result = JSON.parse(response);
                        //alert("success");
                        if ( result.url )
                        {
                            window.location.href = result.url;
                        }
                    },
                    error: function( jqXHR, textStatus, errorThrown )
                    {
                        alert(errorThrown);
                        throw textStatus;
                    },
                    complete: function( jqXHR, textStatus )
                    {
                        if ( textStatus === 'success' && jqXHR.responseText.length === 0 )
                        {
                            alert("complete");;
                        }
                    }
                });
            }
            else
            {
                alert("Error");
            }
        }
    };
    
    var _a = $('<a>', {class: 'ow_hidden ow_content a'}).appendTo(document.body);
    OW.addCss('.cm-hashtag{cursor:pointer;color:' + _a.css('color') + '}');
    _a.remove();
    
    window.ajaxVideoAdder = Object.defineProperties({}, {
        init: { value: function()
        {
            $.extend(_elements, {
                dropArea: $('#drop-area').off(),
                dropAreaLabel: $('#drop-area-label').off(),

                slotArea: $('#slot-area').off(),
                slotPrototype: $('#slot-prototype').removeAttr('id').off(),
                slotData: {},
                slotCounter: 0,

                descEditors: {},
                descCache: {},
                relations: {},
                titleCache: {},
                tagsCache: {},
                codeCache: {},
                thumbnailCache: {},
                uploadForm: $('#upload-form').off(),
                iframeForm: $('#iframe_upload').off(),
            });
            
            if ( !_vars.isHTML5 )
            {
                _elements.dropAreaLabel.html(OW.getLanguageText('spvideolite', 'dnd_not_support'));
            }
            $('#spvideo_btn_add').click(function() {
                _methods.submitVideo( );
                $('#spvideo_btn_add').addClass('ow_inprogress');
            });
            _elements.dropArea.add(_elements.dropAreaLabel).on(
                (function()
                {
                    var eventMap = {
                        click: function()
                        {
                        }
                    };

                    if ( _vars.isHTML5 )
                    {
                        eventMap.drop = function( event )
                        {
                            event.preventDefault();
                            _methods.pushURL(event.dataTransfer.getData('text'));
                            _elements.dropArea.css('border', 'none');
                            _elements.dropAreaLabel.html(OW.getLanguageText('spvideolite', 'dnd_support'));

                            return false;
                        };
                        eventMap.dragenter = function(event)
                        {
                            event.preventDefault();
                            _elements.dropArea.css('border', '1px dashed #E8E8E8');
                            _elements.dropAreaLabel.html(OW.getLanguageText('spvideolite', 'drop_here'));
                        };
                        eventMap.dragleave = function(event)
                        {
                            event.preventDefault();
                            _elements.dropArea.css('border', 'none');
                            _elements.dropAreaLabel.html(OW.getLanguageText('spvideolite', 'dnd_support'));
                        };
                    }

                    return eventMap;
                })()
            );
            owForms['ajax-upload'].bind('submit', function( data )
            {
                var invitation = OW.getLanguageText('spvideolite', 'describe_video');
                data = _methods.getFormData();
            });
            $.ajaxPrefilter(function(options, origOPtions, jqXHR)
            {
                if ( _vars.isRuning && options.isVideoAdd !== true )
                {
                    jqXHR.abort();

                    typeof origOPtions.success == 'function' && (origOPtions.success.call(options, {}));
                    typeof origOPtions.complete == 'function' && (origOPtions.complete.call(options, {}));

                }
            });
        }},
        isHasData: {value: function()
        {
            return Object.keys(_elements.slotData).length !== 0 ||
                owForms['ajax-upload'].elements['description'].getValue().trim().length !== 0;
        }}
    });
})( window, window.jQuery );
