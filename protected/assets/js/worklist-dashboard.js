$(document).ready(function() {
    $('table.worklist').on('click', 'tr.clickable', function(e) {
        e.preventDefault();
        window.location.href = $(this).data('url');
    });
});