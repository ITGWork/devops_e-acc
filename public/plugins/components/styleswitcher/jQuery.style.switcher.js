// Theme color settings
var url = $(location).attr("protocol") + "//" + $(location).attr("hostname");

var port = location.port;
if (port != '80' || port != '443') {
    url += ':' + port;
}

var path = window.location.pathname;

if (path.indexOf('/public') != -1) {
    url += path.substring(path.indexOf('/public'), -1) + '/public';
}

function store(name, val) {

    $.get(url + '/admin/users/savetheme/' + val, function(data) {
        //console.log('success');
    });

    if (typeof(Storage) !== "undefined") {
        localStorage.setItem(name, val);
    } else {
        window.alert('Please use a modern browser to properly view this template!');
    }
}
$(document).ready(function() {

    $('body').addClass($('ul.layouts li.active a').data('layout'));

    $("*[data-theme]").click(function(e) {
        e.preventDefault();
        var currentStyle = $(this).attr('data-theme');
        store('theme', currentStyle);
        $('#theme').attr("href", url + "/css/colors/" + currentStyle + ".css");

        $('#themecolors li a').removeClass('working');
        $(this).addClass('working');

    });

});
