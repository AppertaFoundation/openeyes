$(document).ready(function() {
    let id = 0;
    let maxPages = $(".multipage-stack img").size();

    function scrollToPage(page) {
        let pageStack = $('.multipage-stack');
        pageStack.animate({
            scrollTop: pageStack.scrollTop() + $($('.multipage-stack img')[page]).position().top
        });
    }

    $('#js-scroll-btn-down').click(function() {
        // Do not scroll down if already at the last page.
        if (id + 1 < maxPages) {
            id++;
            scrollToPage(id);
        }
    });

    $('#js-scroll-btn-up').click(function() {
        // Do not scroll up if already at the first page.
        if (id > 0) {
            id--;
            scrollToPage(id);
        }
    });

    $('.page-num-btn').click(function() {
        // Jump to the selected page.
        id = $(this).data('page');
        scrollToPage(id);
    });
});