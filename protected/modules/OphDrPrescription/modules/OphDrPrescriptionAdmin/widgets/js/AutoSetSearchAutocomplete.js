$(document).ready(function() {
    $('#MedicationSet_auto_name').autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: '/OphDrPrescription/admin/DrugSet/search',
                dataType: 'json',
                data: { search: {query: request.term, automatic: true} },
                beforeSend: function(){
                    $('.js-spinner-as-icon').show();
                },
                success: function(data) {
                    const resp = $.map( data.items, function( item ) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id
                        };
                    });
                    response(resp);
                },
                complete: function() {
                    $('.js-spinner-as-icon').hide();
                }
            });
        },
        minLength: 3,
        select: function( event, ui ) {
            $('#MedicationSet_id').val(ui.item.id);
        },
    }).data('ui-autocomplete')._renderItem = function (ul, item) {
        ul.addClass("oe-autocomplete");
        return $("<li class='oe-menu-item'></li>")
            .data("item.autocomplete ui-menu-item oe-menu-item", item)
            .append('<a>' + item.label + '</a>')
            .appendTo(ul);
    };
});
