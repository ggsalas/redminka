/*! barley-for-wordpress - v1.7.0 - 2014-10-20 - http://plainmade.com */var wordpress = {};

// Set Local Storage
barley.editor.localSet = function(k, v) {
    if (localStorage) {
        localStorage[k] = v;
    }
};

// Retrieve Local Storage
barley.editor.localGet = function(k) {
    if (localStorage && localStorage[k] !== undefined) {
        return localStorage[k];
    } else if (this[k] !== undefined) {
        return this[k];
    }
    return '';
};

// Simple Truncate Method
barley.editor.truncateText = function(string, len){
  if (string.length > len){ return string.substring(0,len)+'...'; } else { return string; }
}

// Remove Local Storage (Empty)
barley.editor.localRemove = function(k) {
    if (this[k] !== undefined) {
        this[k] = '';
    }
    if (localStorage) {
        localStorage.removeItem(k);
    }
};

barley.editor.save = function(obj) {
    var k    = obj.data('barley');
    var v    = obj.html();
    var wpid = obj.data('barley-wordpressid');

    $barley.post(
        barleyVars.ajaxurl,
        {
            action : 'barley_update_post',
            p: wpid,
            k: barley.urlEncode(k),
            v: barley.urlEncode(v)
        },
        function( res) {
            barley.logMessage('KEY: ' + k);
            barley.logMessage('VALUE: ' + v);
            barley.logMessage('WPID: ' + wpid);
            barley.logMessage(res);
        }
    );
};

// Image/File Upload Hook
barley.editor.upload = function(type, width, height) {
    return barley.editor.wpmedia.upload(type, width, height);
};

// Video Upload Hook
barley.editor.video = function(type, url, width) {
    switch (type) {
        case "open" :
            return barley.editor.youtube.showModal();
        break;
        case "embed" :
            return barley.editor.youtube.urlEmbed(url, width);
        break;
    }
};


// Execute Functions
$barley(document).ready(function(){

    // Fixes sites that don't allow debug_bactrace to scrub sidebar and nav's that use the_title
    $barley('[data-barley-editor]').each(function(){
        if($barley(this).parent('a').length > 0){
            $barley(this).attr('title', "").attr('alt', "").removeAttr('data-barley').removeAttr('data-barley-editor').attr('contenteditable', false);
            $barley(this).parent('a').attr('title', "").attr('alt', "");
        }
    });

    $barley('.barley-delete-post a').on('click', function(e){

        var barleydeletepost = confirm('¿Estás seguro que deseas eliminar esta publicación?');
        if (barleydeletepost == true) {
            return true;
        } else {
            return false;
        }
    });


    // Set up Drafts Modal
    $barley('#wp-admin-bar-barley-view-drafts a').on('click', function(e){
        e.preventDefault();

        // Retrieve Drafts
        $barley.post(
            barleyVars.ajaxurl,
            {
                action : 'barley_get_post_drafts',
            },
            function(res) {

                $barley('#barley-drafts-modal .barley-modal-content-area').html(res);

            }
        );  


        // Show Modal
        return $barley('<a href="#barley-drafts-modal" style="display:none;"></a>').barleyModal({ top : 50, closeButton: ".barley-bar-close-modal" }).trigger('click');
    });


});
/** wordpress-upload.js ****************/
/*
 * WordPress Media Plugin for Barley Editor
 * Copyright 2013 Plain LLC
*/

// Define the Editor and Meta object
barley.editor = barley.editor || {};
barley.editor.wpmedia = {};


// Method called from the editor to launch the media uploader.
// Arguments: `type` which media window to display, `width` & `height` used for replacement
barley.editor.wpmedia.upload = function(type, width, height){

    // Initially set post ID
    wp.media.model.settings.post.id = barley.wp_post_id;

    // Action based on type
    switch(type){
        case "single" :
            this.file_frame_single.open();
            this.file_frame_single.on( 'select', function() {
                attachment = barley.editor.wpmedia.file_frame_single.state().get('selection').first().toJSON();
                wp.media.model.settings.post.id = barley.wp_post_id;
                return barley.editor.wpmedia.determineOutput(attachment);
            });
            this.file_frame_single.on( 'close', function() { return barley.editor.tidyImage(); });
        break;
        case "featured" :
            this.file_frame_featured.open();
            this.file_frame_featured.on( 'select', function() {
                attachment = barley.editor.wpmedia.file_frame_featured.state().get('selection').first().toJSON();
                wp.media.model.settings.post.id = barley.wp_post_id;
                return barley.editor.featured.callback(attachment);
            });
            this.file_frame_featured.on( 'close', function() { return barley.editor.tidyImage(); });      
        break;
        case "replace" :
            this.file_frame_replace.open();
            this.file_frame_replace.on( 'select', function() {
                attachment = barley.editor.wpmedia.file_frame_replace.state().get('selection').first().toJSON();
                if(attachment.type == "image"){
                    window.barley.editor.image_triggered.attr('src', attachment.url);
                    wp.media.model.settings.post.id = barley.wp_post_id;
                    barley.editor.save($barley(window.barley.editor.context));
                    return barley.editor.tidyImage();
                } else {
                    barley.editor.tidyImage();
                    return alert('Disculpas, las imagenes se pueden reemplazar únicamente con otra imagen. Por favor intentá de nuevo.');
                }
                this.off();         
            });
            this.file_frame_replace.on( 'close', function() { return barley.editor.tidyImage(); });
        break;
        case "replace-crop" :
            this.file_frame_replace.open();
            this.file_frame_replace.on( 'select', function() {
                attachment = barley.editor.wpmedia.file_frame_replace.state().get('selection').first().toJSON();
                wp.media.model.settings.post.id = barley.wp_post_id;
                if(attachment.type == "image"){
                    $barley.post(
                        barleyVars.ajaxurl,
                        {
                            action : 'barley_image_resize',
                            i: attachment.url,
                            w: width,
                            h: height,
                            c: true
                        },
                        function(res) {
                            if(res.success){
                                window.barley.editor.image_triggered.attr('src', res.fileURL).attr('width', width);
                                barley.editor.save($barley(window.barley.editor.context));
                                return barley.editor.tidyImage();
                            } else {
                                barley.editor.tidyImage();
                                alert('La imagen no pudo ser reemplazada, Porfavor intentá de nuevo.');
                            }

                        }
                    );                    
                } else {
                    barley.editor.tidyImage();
                    return alert('Disculpas, las imagenes se pueden reemplazar únicamente con otra imagen. Por favor intentá de nuevo.');
                }
                this.off();
            });
            this.file_frame_replace.on( 'close', function() { return barley.editor.tidyImage(); });
        break;
    }

};

// Used to determine how to insert the media into the page. 
// Checks for the type of media.
barley.editor.wpmedia.determineOutput = function(atachment){

    var type = attachment.type, value;

    switch(type){

        case "image" :
            return barley.editor.insert('insertImage', attachment.url, false);
        break;
        case "application" :
            value = '<a href="'+attachment.url+'">'+attachment.title+'</a>';
            return barley.editor.insert('insertHTML', value, false);
        break;
        case "video" :
            value = '<a href="'+attachment.url+'">'+attachment.title+'</a>';
            return barley.editor.insert('insertHTML', value, false);
        break;
        case "audio" :
            value = { 'src': attachment.url };
            $barley.post(
                barleyVars.ajaxurl,
                {
                    action : 'barley_process_audio',
                    audio: value,
                },
                function(res) {
                    if(res.success){
                        return barley.editor.insert('insertHTML', res.html, false);
                    } else {
                        return alert(res.message);
                    }
                }
            );
        break;
    }


};

// On page load, initate all of 3 of the media uploader types.
barley.editor.wpmedia.init = function(){

    this.file_frame_single = wp.media.frames.file_frame = wp.media({ title: "Seleccioná un archivo para insertar en tu publicación.", frame: 'select', button: { text: 'Agregar', }, multiple: false });

    this.file_frame_featured = wp.media.frames.file_frame = wp.media({ title: "Seleccioná la imagen destacada de tu publicación.", frame: 'select', button: { text: 'Fijar como Destacada', }, library : { type : 'image'}, multiple: false });

    this.file_frame_replace = wp.media.frames.file_frame = wp.media({ title: "Seleccioná una imagen para reemplazar la imagen seleccionada.", frame: 'select', button: { text: 'Reemplazar', }, library : { type : 'image'}, multiple: false });

};

barley.editor.wpmedia.init();
/** youtube.js ****************/
/*
 * YouTube Plugin for Barley Editor
 * Copyright 2013 Plain LLC
*/


barley.editor = barley.editor || {};
barley.editor.youtube = {};

// Set your API KEY
// Since this API key will be public
// be sure to set your referers access in your Google API Console
barley.editor.youtube.api_key = 'AIzaSyAwsXlCZvf0l936qA5e11ixOwV65GrWYUU'; // Barley Test Key


// Set default data
barley.editor.youtube.endpoint = 'https://www.googleapis.com/youtube/v3';
barley.editor.youtube.properties = ['channel_id', 'username'];

barley.editor.youtube.showModal = function() {
    var userid = barley.editor.localGet('barley_editor_youtube_username');
    if(userid != '' || userid !== undefined){
        barley.editor.youtube.setChannel(userid);
    }
    return $barley('<a href="#barley-video-modal" style="display:none;"></a>').barleyModal({ top : 50, closeButton: ".barley-bar-close-modal" }).trigger('click');
}

barley.editor.youtube.createProperties = function() {
    var prop = '';
    for (var i = 0; i < this.properties.length; i++) {
        prop = this.properties[i];
        this[prop] = '';
    }
};

barley.editor.youtube.getChannel = function(username, callback) {
    this.username = username;
    this.send('/channels', 'part=id&forUsername=' + encodeURIComponent(username), callback);
};

barley.editor.youtube.disconnect = function(){
    barley.editor.localRemove('barley_editor_youtube_username');
    barley.editor.localRemove('barley_editor_youtube_channel_id');
    var newHTML = '<div class="alert-message error"></div><div class="fieldset action"><form id="barley-yt-user-action-form"><label for="barley-yt-user">YouTube Channel Name:</label><input type="text" name="barley-yt-user" class="barley-modal-text-input" value="" id="barley-yt-user"><button type="submit" class="barley-icon-ok" value="" id="barley-yt-user-action"></button></form></div><div class="barley-yt-all-videos"><div class="barley-yt-video-results-list-empty"><p>Please provide your YouTube channel name above. Your YouTube channel name is part of your YouTube URL. E.g. http://youtube.com/user/PlainLLC : Channel name is "PlainLLC".</p><p>Please note that only your public videos will be available in this video viewer. If you have any private or unlisted videos they will not show here and you\'ll need to visit YouTube to find their embed code.</p><p>We\'ll only need to ask for your YouTube channel name once and you\'ll be able to remove it at any time.</p></div></div>';
    $barley('#barley-videos-yours').html(newHTML);
    $barley('.barley-modal-user-account').empty();
};

barley.editor.youtube.search = function(obj, callback) {
    var channel_id = obj.channel_id || '';
    var query = obj.query || '';
    var page_token = obj.page_token || '';

    // Figure max results
    var max = obj.max || 10;
    max = parseInt(max, 10);
    max = (!isNaN(max) && max > 0 && max < 51) ? max : 10;

    // Figure the sort order
    var orders = ['date', 'rating', 'relevance', 'title', 'videoCount', 'viewCount'];
    var order = obj.order || 'date';
    var order_found = false;
    for (var i = 0; i < orders.length; i++) {
        order_found = (orders[i] == order) ? true : order_found;
    }
    order = (order_found === false) ? 'date' : order;

    // Create query
    var q = 'part=snippet&type=video&videoEmbeddable=true&maxResults=' + max.toString();
    q += (channel_id != '') ? '&channelId=' + channel_id : '';
    q += (query != '') ? '&q=' + encodeURIComponent(query) : '';
    q += (page_token != '') ? '&pageToken=' + page_token : '';

    this.send('/search', q, callback);
};

barley.editor.youtube.send = function(path, query, callback) {
    query += '&key=' + barley.editor.youtube.api_key;
    $barley.ajax({
        'url': barley.editor.youtube.endpoint + path,
        'data': query,
        'dataType': 'jsonp',
        'error': function(xhr, status, error) {
            barley.logMessage('There were errors getting results for: ' + barley.editor.youtube.endpoint + path);
            barley.logMessage(status + ': ' + error);

            if ($barley.isFunction(callback)) {

                barley.trackError({
                    'error': error,
                    'status': status,
                    'request': JSON.stringify(xhr)
                });

                callback({
                    'error': error,
                    'status': status,
                    'request': xhr
                });
            }
        },
        'success': function(res) {
            if ($barley.isFunction(callback)) {
                callback(res);
            }
        }
    });
};

barley.editor.youtube.urlEmbed = function (url, width){
    var regExp, parseUrl, height, width = (width) ? width : 500;
    if(url.indexOf("youtu") > -1){
        height = width * .56;
        $barley('#barley-video-modal a.barley-bar-close-modal').trigger('click');
        barley.editor.youtube.error('hide');
        return url.replace(/(?:http:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/g, '<iframe width="'+width+'" height="'+height+'" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>');
    } else if(url.indexOf("vimeo") > -1){
        regExp = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
        parseUrl = regExp.exec(url);
        if(parseUrl){
            videoId = parseUrl[5];
            height = width * .56;
            $barley('#barley-video-modal a.barley-bar-close-modal').trigger('click');
            barley.editor.youtube.error('hide');
            return '<iframe src="http://player.vimeo.com/video/'+videoId+'?title=0&amp;byline=0&amp;portrait=0&amp;badge=0" width="'+width+'" height="'+height+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';            
        } else {
            return barley.editor.youtube.error('ERROR: this service is currently not supported.', 'barley-video-tab-2');
       }
    } else {
        return barley.editor.youtube.error('ERROR: this service is currently not supported.', 'barley-video-tab-2');
    }
}

barley.editor.youtube.error = function(message, location){
    if(message == "hide"){ return $barley('#barley-video-modal .alert-message').hide(); } // Just Hide Quickly
    $barley('#'+location+' .alert-message.error').text(message).fadeIn(); // Then Show
    $barley('#barley-video-modal .barley-icon-spinner').hide(); // Hide Spinner

}

barley.editor.youtube.setChannel = function (userid) {
    // Example of getting channel id
    barley.logMessage('gettting channel ID for YouTube...');
    this.getChannel(userid, function(obj, username) {
        if(obj.pageInfo.totalResults < 1 && userid != ''){
            barley.editor.youtube.error('ERROR: No se encuentra el usuario.', 'barley-video-tab-1');
            barley.editor.localRemove('barley_editor_youtube_username');
            barley.editor.localRemove('barley_editor_youtube_channel_id');
        }
        if (obj.items[0] !== undefined) {
            // Sets to object property & localStorage if LS is available
            var channelID = obj.items[0].id;
            barley.editor.localSet('barley_editor_youtube_username', userid);
            barley.editor.localSet('barley_editor_youtube_channel_id', channelID);

            // Show example
            barley.logMessage('Channel ID: ' + channelID + ' for ' + userid);

            // Show Results
            $barley('.barley-modal-user-account').empty().html('<i class="barley-icon-user user-icon"></i><a title="Click aquí para borrar tu cuenta" href="#">' + userid + '</a><i class="barley-icon-remove settings-icon"></i>');

            videoList = '<div class="fieldset action"><form id="barley-yt-your-search-action-form"><label for="barley-yt-your-search" class="title-label">SEARCH YOUR VIDEOS:</label><input type="text" name="'+ channelID +'" class="barley-modal-text-input" value="" id="barley-yt-your-search"><button type="submit" class="barley-icon-search" value="" id="barley-yt-your-search-action"></button></form></div><div class="barley-yt-all-videos"><div class="barley-content-grid" id="barley-yt-video-results-list-yours"><i class="barley-icon-spinner barley-bar-animate-spin"></i></div></</div>';

            $barley('#barley-videos-yours').empty().html(videoList);

            // Populate Videos.
            barley.editor.youtube.search({
                'channel_id': channelID,
                'max': 12
            }, function(obj) {
                if (obj.items[0] !== undefined) {
                    var output = '';
                    for (var x = 0; x < obj.items.length; x++) {
                        output += barley.editor.youtube.videoFigure(obj.items[x]);
                    }
                    output += barley.editor.youtube.setPagination(obj, channelID);
                    $barley('#barley-yt-video-results-list-yours').empty().html(output);
                }
                barley.logMessage(obj);
            });

        } else {
            barley.logMessage('Could not find channel id for ' + barley.editor.youtube.username + '.');
            barley.editor.localRemove('barley_editor_youtube_username');
            barley.editor.localRemove('barley_editor_youtube_channel_id');
        }
    });
};

barley.editor.youtube.showResults = function(type, term, channelID, pagination){
    var outputDiv, outputTitle;
    if(term === undefined){ term = ''; }
    if(type == 'account'){
        this.query = { 'query': term, 'channel_id': channelID, 'max': 12, 'page_token': pagination };
        outputDiv = $barley('#barley-yt-video-results-list-yours');
        outputTitle = $barley('.barley-yt-your-results-title');
    } else if(type == 'all') {
        this.query = { 'query': term, 'max': 12, 'order': 'relevance', 'page_token': pagination };
        outputDiv = $barley('#barley-yt-video-results-list');
        outputTitle = $barley('.barley-yt-results-title');
    }
    barley.editor.youtube.search(this.query, function(obj) {
        $barley('.barley-modal-content').animate({ scrollTop: 0}, 'slow');
        if (obj.items[0] !== undefined) {
            var output = '';
            for (var x = 0; x < obj.items.length; x++) {
                output += barley.editor.youtube.videoFigure(obj.items[x]);
            }
            output += barley.editor.youtube.setPagination(obj, channelID, term);
            outputDiv.empty().html(output);
            outputTitle.text('Search: '+term);
        }
        barley.logMessage(obj);
    });

};

barley.editor.youtube.videoFigure = function(video){
    date = new Date(video.snippet.publishedAt);
    today = new Date();
    if (date.toDateString() == today.toDateString()){
        date = "Today";
    } else {
        date = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear().toString().substr(2, 2);
    }
    title = barley.editor.truncateText(video.snippet.title, 22);
    channel = barley.editor.truncateText(video.snippet.channelTitle, 11);
    return '<figure class="barley-content-item media" id="'+video.id.videoId+'"><img class="thumb-preview" src="'+video.snippet.thumbnails.medium.url+'"><span class="barley-media-title">'+title+'</span><span class="barley-media-caption">by '+channel+'</span><span class="barley-media-date">'+date+'</span><div class="action-overlay"><a href="http://youtube.com/watch?v='+video.id.videoId+'" rel="insertFromVideoModalURL" class="item-action-button barley-yt-video-embed"><i class="barley-icon-ok"></i>Embed</a><a href="http://youtube.com/watch?v='+video.id.videoId+'" class="item-action-button barley-yt-video-preview" target="_blank"><i class="barley-icon-share-alt"></i>Preview</a></div></figure>';
};

barley.editor.youtube.setPagination = function(res, channelID, searchTerm){
    if(res.nextPageToken !== undefined && res.prevPageToken !== undefined){
        return '<div class="barley-modal-content-paging" data-byt-channel="'+channelID+'" data-byt-term="'+searchTerm+'"><a class="prev" href="#" rel="'+res.prevPageToken+'"><i class="barley-icon-angle-left"></i> Prev</a><a class="next" href="#" rel="'+res.nextPageToken+'">Next <i class="barley-icon-angle-right"></i></a></div>';
    } else if(res.nextPageToken !== undefined) {
        return '<div class="barley-modal-content-paging" data-byt-channel="'+channelID+'" data-byt-term="'+searchTerm+'"><a class="next" href="#" rel="'+res.nextPageToken+'">Next <i class="barley-icon-angle-right"></i></a></div>';
    } else if(res.prevPageToken !== undefined) {
        return '<div class="barley-modal-content-paging" data-byt-channel="'+channelID+'" data-byt-term="'+searchTerm+'"><a class="prev" href="#" rel="'+res.prevPageToken+'"><i class="barley-icon-angle-left"></i> Prev</a></div>';
    }
};

barley.editor.youtube.init = function() {
    // Example of getting username/channel from local storage
    // and setting to object properties
    this.createProperties();
    this.username = barley.editor.localGet('barley_editor_youtube_username');
    this.channel_id = barley.editor.localGet('barley_editor_youtube_channel_id');

    barley.logMessage('On Load - YT Username: ' + this.username);
    barley.logMessage('On Load - YT Channel: ' + this.channel_id);

    //  YouTube Modal Functions.
    $barley('#barley-video-modal .barley-modal-content-wrap').not(':first').hide();
    $barley('.barley-modal-nav ul a').on('click', function(e){
        e.preventDefault(); e.stopPropagation();
        var tab = $barley(this).attr("href");
        // Change Active State
        $barley('.barley-modal-nav ul li').removeClass('active');
        $barley(this).parents('li').addClass('active');
        // Close all tabs
        $barley('#barley-video-modal .barley-modal-content-wrap').hide();
        $barley(tab).show();
        barley.editor.youtube.error('hide');
    });

    // Barley Modal Video Specific Tab
    $barley('#barley-video-tab-1 .barley-modal-content-area').not(':first').hide();
    $barley('#barley-video-modal .barley-modal-content-nav a').on('click', function(e){
        e.preventDefault(); e.stopPropagation();
        var tab = $barley(this).attr("href");
        // Close all tabs
        $barley('#barley-video-tab-1 .barley-modal-content-area').hide();
        $barley('#barley-video-modal .barley-modal-content-nav a').parent().removeClass('active');
        // Activate Correct One
        $barley(this).parent().addClass('active');
        $barley(tab).show();
    });

    // Barley Video Auth
    var videoSpinner = '<i class="barley-icon-spinner barley-bar-animate-spin"></i>';
    $barley(document).on('submit', '#barley-yt-user-action-form', function(e){
        e.preventDefault();
        $barley('.barley-yt-video-results-list-empty').prepend(videoSpinner);
        var yt_user = $barley('#barley-yt-user').val();
        setTimeout(function() { barley.editor.youtube.setChannel(yt_user) }, 800);
    });

    $barley('#barley-yt-video-search-action-form').on('submit', function(e){
        e.preventDefault();
        $barley('#barley-yt-video-results-list').empty().html(videoSpinner);
        var searchTerm = $barley('#barley-yt-video-search').val();
        barley.editor.youtube.showResults('all', searchTerm);
    });
    $barley(document).on('submit', '#barley-yt-your-search-action-form', function(e){
        e.preventDefault();
        $barley('#barley-yt-video-results-list-yours').empty().html(videoSpinner);
        var searchTerm = $barley('#barley-yt-your-search').val();
        var channel_id = $barley('#barley-yt-your-search').attr('name');
        barley.editor.youtube.showResults('account', searchTerm, channel_id);
    });

    // Video Pagination
    $barley(document).on('click', '#barley-video-modal .barley-modal-content-paging a', function(e){
        e.preventDefault();
        var context = $barley(this).parent().parent().attr("id");
        var channelID = $barley(this).parent().data('byt-channel');
        var pagination = $barley(this).attr('rel');

        if(context == "barley-yt-video-results-list"){
            var type = "all";
            var scrollID = "barley-videos-all";
            var searchTerm = $barley(this).parent().data('byt-term');
        } else if(context == "barley-yt-video-results-list-yours") {
            var type = "account";
            var scrollID = "barley-videos-yours";
            var searchTerm = '';
        }
        barley.editor.youtube.showResults(type, searchTerm, channelID, pagination);
    });

    // User Removing Account
    $barley(document).on('click', '#barley-video-modal .barley-modal-user-account a', function(e){
        e.preventDefault();
        $barley('#barley-yt-video-results-list-yours').empty().html(videoSpinner);
        setTimeout(function() { barley.editor.youtube.disconnect(); }, 800);
    });

    // Toggle Advanced Settigns
    $barley('.barley-modal-advanced-heading').on('click', function(e){
        $barley(this).find('i').toggleClass("barley-icon-chevron-down").toggleClass("barley-icon-chevron-up");
        $barley(this).next('.barley-modal-advanced').toggle();
    });

    $barley(document).on('click', '.barley-yt-video-embed', function(e){
        e.preventDefault();
        barley.editor.execute($barley(this));
    });


};

barley.editor.youtube.init();
/** meta.js ****************/
/*
 * CMS Meta Plugin for Barley Editor
 * Copyright 2013 Plain LLC
*/


// Define the Editor and Meta object
barley.editor = barley.editor || {};
barley.editor.meta = {};

// Method for retrieving the current categories assigned to the current post.
// This is run on pageload. Retrieves the categories and builds a response to be inserted into the meta editor.
barley.editor.meta.getCategories = function(){

    var built_response = '', built_response_a = '', built_response_b = '';
    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_get_categories',
            id: barley.wp_post_id
        },
        function(res) {
            // Upon successfull return of categories, loop through each and prepare the correct HTML
            $barley.each(res, function() {
                if(this.current_cat){
                    built_response_a += '<li data-barley-catid="'+this.cat_ID+'"><input type="checkbox" checked id="'+this.category_nicename+'"><label for="'+this.category_nicename+'">'+this.cat_name+'</label></li>';
                } else {
                    built_response_b += '<li data-barley-catid="'+this.cat_ID+'"><input type="checkbox" id="'+this.category_nicename+'"><label for="'+this.category_nicename+'">'+this.cat_name+'</label></li>';
                }
            });
            return $barley('#barley_editor_catList .barley_editor_meta_content').html(built_response_a).append(built_response_b);
        }
    );
};

// Method for retrieving the current tags assigned to the current post.
// This is run on pageload. Retrieves the tags and builds a response to be inserted into the meta editor. 
barley.editor.meta.getTags = function(){

    var built_response = '';
    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_get_tags',
            id: barley.wp_post_id
        },
        function(res) {
            // Upon successfull return of tags, loop through each and prepare the correct HTML
            if(typeof res.success == 'undefined'){
                $barley.each(res, function() {
                    built_response += '<li data-barley-tagid="'+this.slug+'"><a href="#" class="barley_editor_tag_remove"><i class="barley-icon-remove"></i></a><span>'+this.name+'</span></li>';
                });
                return $barley('#barley_editor_tagList .barley_editor_meta_content').html(built_response);    
            } else {
                return $barley('#barley_editor_tagList .barley_editor_meta_content').html("");
            }
        }
    );

};

// Method to update the categories
// Arguments: `cat` is the category passed in and `remove` is a boolean whether we are adding or removing that cat
barley.editor.meta.updateCategories = function(cat, remove){

    var removecat = (remove) ? remove : false;

    // Process the request
    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_update_categories',
            id: barley.wp_post_id,
            category: cat,
            remove: removecat
        },
        function(res) {
            if (res.categories_in === '1') {
                $barley('#barley_editor_catList').find("[data-barley-catid='1']").find('input').prop('checked', true);
            }
        }
    );

};

// Method to update tags.
// Arguments: `tag` is the tag to add to post. If empty, it will simply just re-initiate the current tags
barley.editor.meta.updateTags = function(tag){
    var tagList, len;
    if(tag !== undefined){
        tagList = tag + ", ";
    } else {
        tagList = "";
    }
    len = $barley('#barley_editor_tagList .barley_editor_meta_content li').length;
    $barley('#barley_editor_tagList .barley_editor_meta_content li').each(function(i){
        tagList += $barley(this).find('span').text();
        if (i != len - 1) { tagList += ", "; }
    });

    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_update_tags',
            id: barley.wp_post_id,
            tags: tagList
        },
        function(res) {
            if(!res){ alert(res.message); }
        }
    );
};

// Pageload Initiation 
// Needs better commenting
barley.editor.meta.init = function() {

    var config = {
        over: function(event){
            clearTimeout(window.tagHoverOut);
            $barley('#barley_editor_edit_meta').fadeTo( 400, 1 );
        },
        out: function(){
            if(!$barley('#barley_editor_meta').is(':visible')){
                window.tagHoverOut = setTimeout(function(){ $barley('#barley_editor_edit_meta').fadeTo( 400, 0 ); }, 2000);
            }
        },
        timeout: 200
    };
    $barley('#barley_the_content').hoverIntent(config);
    $barley('#barley_editor_edit_meta').hoverIntent(function(){
        clearTimeout(window.tagHoverOut);
    }, function(){});
    $barley('html').not('#barley_show_meta_cxt').on('click', function(e){

        if ($barley('#barley_editor_meta label, #barley_editor_meta a, #barley_editor_meta input, #barley_editor_meta i, #barley_the_content > *, #barley_editor_insert_after, #barley_the_content').is(e.target)){} else {
            $barley('#barley_editor_edit_meta').fadeTo( 400, 0 );
        }
        
    });
    $barley('#barley_show_meta_cxt').on('click', function(e){
        clearTimeout(window.tagHoverOut);
        barley.editor.meta.getCategories();
        barley.editor.meta.getTags();
        var position = { top: e.pageY, left: e.pageX };
        barley.editor.showToolbar(position, 'meta');
        return false;
    });
    $barley('input.barley_editor_meta_inpt').on('click', function(e){
        var meta_cont = $barley(this).next();
        if(meta_cont.is(':visible')){
            //$barley(this).next().slideUp('slow');
        } else {
            $barley('.barley_editor_meta_content').each(function(){
                if($barley(this).is(':visible')){
                    $barley(this).slideUp();
                }
            });
            if(meta_cont.is(':empty')){} else {
               meta_cont.slideDown();
            }
        }
        return false;
    });
    $barley('.barley_editor_meta_single .barley_editor_meta_inpt').on('input', function() { $barley(this).prev().fadeIn('slow'); });
    $barley('.barley_editor_meta_single .barley_editor_meta_inpt').on('blur', function() { if(this.value == '') { $barley(this).prev().hide(); } });
    $barley('a.barley_editor_meta_action').on('click', function(e){
        var which, value, nice_name, parent, html_add = "";     
        which = $barley(this).parent().attr('id');
        parent = $barley('#'+which);
        value = $barley(this).next().val();
        nice_name = value.toLowerCase().replace(/ /g, '-');
        if(which == "barley_editor_catList"){
            html_add = '<li><input checked="checked" type="checkbox" id="'+nice_name+'"><label for="'+nice_name+'">'+value+'</label></li>';
            barley.editor.meta.updateCategories(value);
        } else if(which == "barley_editor_tagList"){
            if (value.indexOf(',') > -1) { 
                var segments = value.split(',');
                $barley.each(segments, function(i, segment) {
                    value = $barley.trim(segment);
                    if (value !== "") {
                        html_add += '<li><a href="#" class="barley_editor_tag_remove"><i class="barley-icon-remove"></i></a><span>'+value+'</span></li>';
                    }
                });
            } else {
                value = $barley.trim(value);
                if (value !== "") {
                    html_add = '<li><a href="#" class="barley_editor_tag_remove"><i class="barley-icon-remove"></i></a><span>'+value+'</span></li>';
                }
            }

            if($barley('#barley_editor_tagList .barley_editor_meta_content').is(':visible')){} else {
                $barley('#barley_editor_tagList .barley_editor_meta_content').slideDown();
            }

            barley.editor.meta.updateTags(value);
        }
        parent.find('.barley_editor_meta_content').prepend(html_add);
        parent.find('.barley_editor_meta_inpt').val('');
        parent.find('.barley_editor_meta_action').fadeOut();
        return false;
    });
    $barley(document).on('click', 'a.barley_editor_tag_remove', function(e){
        e.preventDefault();
        $barley(this).parent().remove();
        if($barley('#barley_editor_tagList .barley_editor_meta_content').is(':empty')){
            $barley('#barley_editor_tagList .barley_editor_meta_content').slideUp();
        }
        barley.editor.meta.updateTags();
        return false;
    });
    $barley(".barley_editor_meta_inpt").keyup(function (e) {
        if (e.keyCode == 13) { $barley(this).prev().trigger('click'); }
    });
    $barley(document).on('click', '#barley_editor_catList input[type="checkbox"]', function(e){
        var ckbx = $barley(this), value;
        value = ckbx.next().text();
        if(ckbx.is(':checked')){
            barley.editor.meta.updateCategories(value);
        } else {
            barley.editor.meta.updateCategories(value, true);
        }
    });

};

$barley(document).ready(function(){
    if(barley.editor.activate_meta_editor){
        if ($barley('#barley_editor_edit_meta').length > 0) {
            barley.editor.meta.init();
        }
    }
});
/** meta.js ****************/
/*
 * CMS Meta Plugin for Barley Editor
 * Copyright 2013 Plain LLC
*/


// Define the Editor and Meta object
barley.editor = barley.editor || {};
barley.editor.metapage = {};

// Script to update page attributes
barley.editor.metapage.update = function() {

    var check_parent = $barley('#barley_page_parent').val(),
        check_order = $barley('#barley_page_order').val(),
        check_template = $barley('#barley_page_template').val();

    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_update_post_attributes',
            id: barley.wp_post_id,
            parent: check_parent,
            post_order: check_order,
            page_template: check_template
        },
        function(res) {
            console.log(res);
        }
    );

};

// Pageload Initiation 
// Needs better commenting
barley.editor.metapage.init = function() {

    var config = {
        over: function(event){
            clearTimeout(window.tagHoverOut);
            $barley('#barley_editor_edit_meta_page').fadeTo( 400, 1 );
        },
        out: function(){
            if(!$barley('#barley_editor_meta').is(':visible')){
                window.tagHoverOut = setTimeout(function(){ $barley('#barley_editor_edit_meta_page').fadeTo( 400, 0 ); }, 2000);
            }
        },
        timeout: 200
    };
    $barley('#barley_the_content').hoverIntent(config);
    $barley('#barley_editor_edit_meta_page').hoverIntent(function(){
        clearTimeout(window.tagHoverOut);
    }, function(){});
    $barley('html').not('#barley_show_meta_cxt_page').on('click', function(e){

        if ($barley('#barley_editor_meta label, #barley_editor_meta a, #barley_editor_meta input, #barley_editor_meta select #barley_editor_meta i, #barley_the_content > *, #barley_editor_insert_after, #barley_the_content, .barley_page_attributes').is(e.target)){} else {
            $barley('#barley_editor_edit_meta_page').fadeTo( 400, 0 );
        }
        
    });


    $barley('#barley_show_meta_cxt_page').on('click', function(e){
        clearTimeout(window.tagHoverOut);
        var position = { top: e.pageY, left: e.pageX };
        barley.editor.showToolbar(position, 'meta');
        return false;
    });

    var page_attrs = $barley('#barley_page_attributes_wrap').html();

    $barley('.barley_editor_meta_box').empty().html(page_attrs);


    $barley('#barley_page_parent').on('change', function (e) { barley.editor.metapage.update(); });
    $barley('#barley_page_template').on('change', function (e) { barley.editor.metapage.update(); });
    $barley('#barley_page_order').on('change', function (e) { barley.editor.metapage.update(); });

};

$barley(document).ready(function(){
    if(barley.editor.activate_meta_editor){
        if ($barley('#barley_editor_edit_meta_page').length > 0) {
            barley.editor.metapage.init();
        }
    }
});
/** link-search-suggest.js ****************/
/*
 * WordPress Link Search & Suggest for Barley Editor
 * Copyright 2013 Plain LLC
*/

// Define the Editor and Meta object
barley.editor = barley.editor || {};
barley.editor.linksuggest = {};


// Set some local vars
barley.editor.linksuggest.suggest_icon = '<i class="barley-icon-file barley-activate-suggest"></i>';

// Grabs a list of links
// Params: 
//      type => Custom Post Type
//      title => Title (Post Type Name)
barley.editor.linksuggest.getLinkList = function (type, title) {

    $barley('#barley-search-spin').show();

    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_link_list',
            content_type: type,
            content_title: title
        },
        function(res) {

            $barley('ul.barley-links-categories').after(res);

            setTimeout( function () {
                // Animation to slide panel
                $barley('.barley-link-block').hide(1, function () {
                    $barley('#barley_'+type+'_list').show(1, function () {
                        $barley('.barley-links-visible').animate({ left: "-100%" }, 600, function() {});
                    });
                });
                barley.editor.linksuggest.bindscroll($barley('.barley-link-block'));
                $barley('#barley-search-spin').hide();
            }, 300);

        }
    );

};

// Grabs a list of links with offset
// Params: 
//      type => Custom Post Type
//      offset => The number to offset the results
barley.editor.linksuggest.getLinkListOffset = function (type, offset, obj) {

    var new_offset = parseInt(offset) + 20;

    $barley('#barley-search-spin').show();

    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_link_list_offset',
            content_type: type,
            number_offset: offset
        },
        function(res) {

            $barley(obj).attr('data-barley-link-offset', new_offset);
            $barley(obj).append(res); // append the results.

            barley.editor.linksuggest.bindscroll($barley(obj));

            $barley('#barley-search-spin').hide();

        }
    );

};

// Method to help bind/unbind scroll ... keeps things clean.
barley.editor.linksuggest.bindscroll = function (obj) {
    obj.unbind('scroll');
    obj.on('scroll', function () {
        if($barley(this).scrollTop() + $barley(this).innerHeight()>=$barley(this)[0].scrollHeight) {
            var type, offset;
            type = $barley(this).attr('id');
            offset = $barley(this).attr('data-barley-link-offset');
            barley.editor.linksuggest.getLinkListOffset(type, offset, $barley(this));
        }
    });
};

barley.editor.linksuggest.searchLink = function (query) {

    $barley('#barley-search-spin').show();

    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_link_search',
            search_query: query
        },
        function(res) {

            $barley('#barley-link-search').fadeOut(400, function () {
                $barley(this).empty().html(res).delay(400).fadeIn();
            });

            $barley('#barley-search-spin').hide();

        }
    );

};


barley.editor.linksuggest.loadPages = function (elem) {

    var link_list, link_wrapper, bodyDump = $barley('#barley-post-types'), editorDump = $barley('#barley_editor_controls');

    //link_list = bodyDump.find('.barley-links-wrapper').clone();
    link_list = bodyDump.html();
    link_wrapper = bodyDump.find('.barley-links-visible');

    elem.removeClass('barley-icon-file').addClass('barley-icon-caret-up');

    elem.next('.barley-links-wrapper').slideUp(400, function () {
        $barley(this).remove(); 
        elem.next('#barley-search-spin').remove();
    });
    elem.after(link_list);

    setTimeout( function () {
        editorDump.find('.barley-links-visible').hide().css('left', 0);

        editorDump.find('.barley-links-wrapper').slideDown(600, function () {
            editorDump.find('.barley-links-visible').fadeIn();
        });

    }, 300);

};

// Function to reset the entire link suggestion
barley.editor.linksuggest.resetSuggest = function () {
    $barley('#barley-search-spin').hide();
    $barley('.barley-links-wrapper').slideUp(400, function(){
        $barley('.barley-links-visible').css('left', 0);
        $barley('i.barley-icon-caret-up').removeClass('barley-icon-caret-up').addClass('barley-icon-file');      
    });
};

barley.editor.linksuggest.init = function () {


    $barley.post(
        barleyVars.ajaxurl,{
            action : 'barley_post_types'
        },
        function(res) {

            $barley('body').append(res);

            // Initial Defination
            var editorDump = $barley('#barley_editor_controls'), link_input, search_panel, lilen, searchVal;

            // Set Initiation to true so we know this was previously run.
            this.inited = true;

            // Grab the editor link input
            link_input = $barley('input.barley_editor_link');

            // Place Icon and add Click Event
            link_input.each( function () {
                $barley(this).after(function () {
                    return $barley(barley.editor.linksuggest.suggest_icon).on('click', function () {
                        if ($barley('#barley_editor_controls').find('.barley-links-wrapper').is(':visible')) {
                            $barley('#barley_editor_controls').find('.barley-links-wrapper').slideUp();
                            $barley(this).removeClass('barley-icon-caret-up').addClass('barley-icon-file');
                        } else {
                            barley.editor.linksuggest.loadPages($barley(this));
                        }
                    });
                });
            });

            // Set up Watch for Animation of Nav/Sub-nav
            $barley(document).on('click', '.barley-links-wrapper li[data-barley-linkid]', function () {
                var panel = $barley(this).data('barley-linkid'), title = $barley(this).text();
                barley.editor.linksuggest.getLinkList(panel, title);
            });

            // Watches for LINK click... fills in the value and resets the suggestion area
            $barley(document).on('click', '.barley-links-wrapper a', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var url = $barley(this).attr('href');
                link_input.val(url);
                link_input.focus();
                return barley.editor.linksuggest.resetSuggest();
            });

            // Watches for a click of a sub panel header to reset the panel slider
            $barley(document).on('click', '.barley-link-block-header', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $barley(this).parent().parent().clearQueue();
                return $barley(this).parent().parent().animate({ left: "0" }, 600);
            });

            // Watch Search Input
            search_panel = "<div id='barley-link-search'>Hit ENTER to Search...</div>";
            $barley(document).on('keydown', '#barley-search-links', function (e) {
                lilen = editorDump.find('.barley-links-categories li').length;
                $barley('#barley-link-search-reset').fadeIn();
                if (lilen > 1) {
                    editorDump.find('.barley-links-categories li').not(':first').fadeOut().remove();
                    editorDump.find('.barley-links-categories li').after(search_panel);
                }
                if (e.keyCode == 13) {
                    searchVal = $barley(this).val();
                    barley.editor.linksuggest.searchLink(searchVal);
                }
            });

            // Search Reset
            $barley(document).on('click', '#barley-link-search-reset', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $barley(this).hide();
                barley.editor.linksuggest.loadPages($barley('.barley-activate-suggest'));
            });

        }
    );

};

barley.editor.linksuggest.init();
/** featured.js ****************/
/*
 * CMS Feature Image Plugin for Barley Editor
 * Copyright 2013 Plain LLC
 */

// Define the Editor and Featured object
barley.editor = barley.editor || {};
barley.editor.featured = {};

// Sends a request to check for featured image and get it if possible
barley.editor.featured.getFeaturedImage = function() {

    $barley.post(
        barleyVars.ajaxurl,
        {
            action : 'barley_get_featured_image',
            id: barley.wp_post_id
        },
        function(res) {

            if(res.message != ""){
                barley.editor.featured.setLocal(res.message);
            }

        }
    );

};

// A callback method set up to be used inside of the upload.js file
// Arguments: `file` is the file used for updating the featured image
barley.editor.featured.callback = function(file) {

    var fimage, position, orig;

    // Show Toolbar
    orig = $barley('#barley_show_meta_cxt').offset();
    position = { top: (orig.pageY + 15), left: (orig.pageX + 10) }
    setTimeout(function(){ $barley('#barley_editor_edit_meta').fadeTo( 100, 1 ); }, 50);
    clearTimeout(window.tagHoverOut);
    barley.editor.showToolbar(position, 'meta');

    // Prepare and Insert Image
    fimage = '<img src="'+file.url+'" width="201" style="width:201px !important" />';

    this.setLocal(fimage);

    this.updateFeaturedImage(file.id);

};

// Updates the local featured image (display only)
barley.editor.featured.setLocal = function(imgElem){

    // Remove Old Image
    $barley('#barley_editor_featured .barley-editor-featured-image').find('img').remove();

    // Add New Image
    $barley('#barley_editor_featured .barley-editor-featured-image').show().prepend(imgElem).hover(function(e) {
        $barley('.barley_featured_overlay').fadeIn();
        e.stopPropagation();
    }, function(e){
        $barley('.barley_featured_overlay').fadeOut();
        e.stopPropagation();
    });

    // Set up function to remove
    $barley('#barley_editor_featured .barley_editor_meta_inpt').attr('placeholder', 'Quitar Imagen Destacada');

    // Helper Styles
    $barley('#barley_editor_featured').css('padding-bottom', 5);

    // Set up swap
    $barley(document).on('click', '#barley_editor_featured .barley-editor-featured-image', function(e) {
        e.preventDefault();
        barley.editor.hideToolbar();
        barley.editor.wpmedia.upload('featured');
        return false;
    });

};

// Does the actual work to update the local image in the database and attach it to the post
barley.editor.featured.updateFeaturedImage = function(fileid) {

    $barley.post(
        barleyVars.ajaxurl,
        {
            action : 'barley_update_post_meta',
            id: barley.wp_post_id,
            key: '_thumbnail_id',
            value: fileid
        },
        function(res) {

            if(!res.success){
                alert('Disculpas, la imagen destacada no pudo ser actualizada, por favor intentá nuevamente.');
            }

        }
    );

};

// Plug into Meta
barley.editor.featured.init = function() {

    this.html = '<div class="barley_editor_meta_single" id="barley_editor_featured"><i class="barley-icon-picture"></i><input class="barley_editor_meta_inpt" type="text" placeholder="Agregar Imagen Destacada" disabled/><div class="barley-editor-featured-image"><div class="barley_featured_overlay"><i class="barley-icon-exchange"></i></div></div></div>';

    this.location = $barley('#barley_editor_meta .barley_editor_meta_box');

    this.location.append(this.html);

    // Hide other meta areas
    $barley(document).on('click', '#barley_editor_featured', function(e) {

        e.preventDefault();
        $barley('input.barley_editor_meta_inpt').not('#barley_editor_featured input.barley_editor_meta_inpt').next().slideUp('slow');

        if($barley(this).find('.barley-editor-featured-image img').length > 0) {
            $barley(this).find('.barley-editor-featured-image').find('img').remove();
            $barley(this).find('.barley_editor_meta_inpt').attr('placeholder', 'Set Featured Image');
            $barley(this).css('padding-bottom', 0);
            return barley.editor.featured.updateFeaturedImage();
        } else {
            barley.editor.hideToolbar();
            barley.editor.wpmedia.upload('featured');
        }

    });

    $barley('#barley_editor_featured .barley-editor-featured-image').show();
    
    this.getFeaturedImage();

};

$barley(document).ready(function(){
    if ($barley('#barley_editor_edit_meta').length > 0) {
        barley.editor.featured.init();
    }
});
