//$.ajaxSetup({cache: false});

$(document).bind('mobileinit', function(){
    $.mobile.selectmenu.prototype.options.nativeMenu = false;
});

$('[data-role="page"]').live('pageshow',function() {
    var url = location.hash;
    if (url.length > 0) {
        url = url.substr(1);
    } else {
        url = location.href;
    }
    // update the language form action URL
    $('#langForm').attr('action', url);
    
    // update the "Go to Standard View" href
    var match = url.match(/([&?]?ui=[^&]+)/);
    if (match) {
        var replace = ((match[1].indexOf('?') != -1) ? '?' : '&') + 'ui=standard';
        url = url.replace(match[1], replace);
    } else {
        url += ((url.indexOf('?') == -1) ? '?' : '&') + 'ui=standard';
    }
    url = url.replace('&ui-state=dialog', '');
    $('a.standard-view').each(function() {
        $(this).attr('href', url); 
    });
});

// mostly lifted from http://docs.jquery.com/Frequently_Asked_Questions#How_do_I_select_an_element_by_an_ID_that_has_characters_used_in_CSS_notation.3F
function jqEscape(myid) {
    return String(myid).replace(/(:|\.)/g,'\\$1');
}
