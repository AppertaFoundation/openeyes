$(document).ready(function() {
    let id = 0;

    function scrollToPage(page) {
        let pageStack = $('.multipage-stack');
        pageStack.animate({
            scrollTop: pageStack.scrollTop() + $($('.multipage-stack img')[page]).position().top
        });
    }

    function scroll(change) {
        let pageStack = $('.multipage-stack');
        let newPos = pageStack.scrollTop() + change;
        pageStack.animate({
            scrollTop: newPos+'px'
        }, 200, 'swing');
    }

    $('#js-scroll-btn-down').click(function() {
        // Do not scroll down if already at the last page.
        scroll(200);
    });

    $('#js-scroll-btn-up').click(function() {
        // Do not scroll up if already at the first page.
        scroll(-200);
    });

    $('.page-num-btn').click(function() {
        // Jump to the selected page.
        id = $(this).data('page');
        scrollToPage(id);
    });
});