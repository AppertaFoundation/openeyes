$(document).ready(function () {
    $('.js-oct-container').on('click', '.js-listview-expand-btn-assessment', function () {
        let target = $(this).closest('.js-element-eye');
        let expand = $(this).hasClass('expand');

        $(this).toggleClass('collapse expand');
        target.find('#js-listview-' + $(this).data('list') + '-full').toggle(expand);
        target.find('#js-listview-' + $(this).data('list') + '-pro').toggle(!expand);
    });
});