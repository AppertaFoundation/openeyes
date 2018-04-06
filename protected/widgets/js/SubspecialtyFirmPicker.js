$(document).ready(function(){

    $('select.subspecialty').on('change', function() {

        var subspecialty_id = $('#subspecialty_firm_picker_subspecialty_id').val();

        if(subspecialty_id){
            jQuery.ajax({
                url: baseUrl + "/firm/getFirmsBySubspecialty",
                data: {"subspecialty_id": subspecialty_id},
                dataType: "json",
                beforeSend: function () {
                    $('.loader').show();
                    $('#subspecialty_firm_picker_firm_id').prop('disabled', true).css({'background-color':'lightgray'});
                },
                success: function (data) {
                    var options = [];

                    //remove old options
                    $('#subspecialty_firm_picker_firm_id option:gt(0)').remove();

                    //create js array from obj to sort
                    for (item in data) {
                        options.push([item, data[item]]);
                    }

                    options.sort(function (a, b) {
                        if (a[1] > b[1]) return -1;
                        else if (a[1] < b[1]) return 1;
                        else return 0;
                    });
                    options.reverse();

                    //append new option to the dropdown
                    $.each(options, function (key, value) {
                        $('#subspecialty_firm_picker_firm_id').append($("<option></option>")
                            .attr("value", value[0]).text(value[1]));
                    });

                    $('#subspecialty_firm_picker_firm_id').prop('disabled', false).css({'background-color':'#ffffff'});
                },
                complete: function () {
                    $('.loader').hide();
                }
            });
        } else {
            $('#subspecialty_firm_picker_firm_id').prop('disabled', true).css({'background-color':'lightgray'});
        }
    });

});