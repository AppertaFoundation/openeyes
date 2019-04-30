var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function TableController(options) {
        this.options = $.extend(true, {}, TableController._defaultOptions, options);

        this.initialiseTriggers();
    }

    TableController._defaultOptions = {
        'selector': '.js-table-controller',
        'wrapper' : 'body'
    };

    TableController.prototype.initialiseTriggers = function() {
        $(this.options.wrapper).on('click', this.options.selector + 'tbody tr', function(e) {
            e.preventDefault();
            window.location.href = $(this).data('url');
        });

        $(this.options.wrapper).on('click', this.options.selector + ' tfoot .pagination a', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let link = $(this).attr('href');
            let $table = $(this).closest('table');
            let tableId = $table.attr('id');
            $.ajax({
                'url': link,
                'type': 'GET',
                'beforeSend': function() {
                    //<div class="spinner-loader"><i class="spinner"></i></div>
                    let $loader = $('<div>', {"class": "spinner-loader"}).append($('<i>', {"class": "spinner"}));
                    $table.find('tfoot tr td:first-child').append($loader);
                },
                'success': function (data) {
                    let html = $.parseHTML(data);
                    let new_table = $(html).find("#" + tableId).html();
                    $table.html(new_table);
                },
                'complete': function() {
                    $table.find('tfoot div.spinner-loader').remove();
                }
            });
        });
    };

    exports.TableController = TableController;

}(OpenEyes.UI));

$(document).ready(function(){
    new OpenEyes.UI.TableController();
});
