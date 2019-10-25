$(document).ready(function() {
        let id = 0;
        let maxPages = $(".multipage-stack img").size();

        function scrollToPage(page) {
            let pageStack = $('.multipage-stack');
            pageStack.animate({
                scrollTop: pageStack.scrollTop() + $($('.multipage-stack img')[page - 1]).position().top
            });
        }

        $('#js-scroll-btn-down').click(function() {
            if (id + 1 < maxPages) {
                id++;
                scrollToPage(id);
            }
        });

        $('#js-scroll-btn-up').click(function() {
            if (id > 0) {
                id--;
                scrollToPage(id);
            }
        });

        $('.page-num-btn').click(function() {
            id = $(this).data('page');
            scrollToPage(id);
        });
    });