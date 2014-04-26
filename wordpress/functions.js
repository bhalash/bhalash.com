function navigationWidth() {
    // The left nav will always be longer than the right.
    $('.nav-right a').css('width', $('.nav-left a').outerWidth() + 'px');    
}

function contentHeight() {
    // Page height - article bottom-margin.
    $('.content-right').css('min-height', $(window).height() + 'px');
}

$(function() {
    // Widget width is dynamic and proportional to the div container size. 
    $('.social a').css('height', $('.social a').width() + 'px'); 
});

// var currentBGPos = 0;
// setInterval(function() {
//     // Scrolling background in search input.
//     currentBGPos--;
//     $('input[class=search-input]').css('background-position', '0 ' + currentBGPos + 'px');
// }, 80);

$(function() {
    contentHeight();
    navigationWidth();
});

$(window).resize(function() {
    contentHeight();
    navigationWidth();
});