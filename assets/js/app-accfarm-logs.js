jQuery(document).ready(function ($) {
    $(window).scroll(function () {
        if ($(document).height() - $(this).height() === $(this).scrollTop()) {
            alert('Scrolled to Bottom');
        }
    });
});