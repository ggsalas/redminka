/*
Barley Object
Files to include
*/
var barley      = barley || {}, barleyVars = {};
barley.loaded   = {};
barley.host     = window.location.hostname;
barley.timers   = {};
barley.debug    = false;
barley.special_chars     = {
    '\\+': '&#43;'
};
barley.bar = barley.bar || {};
barley.bar.settings = barley.bar.settings || {};
barley.bar.settings.lock_edits = false;
barley.wp_post_id = jQuery("[data-barley]").data('barley-wordpressid');

// Define console and console.log if not defined
var console     = console || {};
console.log     = console.log || function(){};

barley.replaceSpecial = function(str)
{
    if (str !== undefined && str !== null) {
        var regex = null;
        for (var i in barley.special_chars) {
            regex = new RegExp(i, 'gi');
            str   = str.replace(regex, barley.special_chars[i]);
        }
    }
    return str;
};

barley.trackError = function()
{

};

barley.urlEncode = function(str)
{
    str = barley.replaceSpecial(str);
    return encodeURIComponent(str);
};

barley.logMessage = function(msg){
    if (barley.debug == true) {
        console.log(msg);
    }
}
