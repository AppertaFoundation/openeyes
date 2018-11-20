$(document).ready(function(){
    $(this).on('change', '.js-subspecialty-dropdown', function (e) {
        let subspecialty_id = $(this).val();
        let $firm = $('.js-firm-dropdown');

        $('.js-firm-dropdown option').remove();
        if (subspecialty_id === '') {
            $firm.append($('<option>').text('All Contexts'));
            $firm.append($('<option>').value(''));
            $firm.attr('disabled', true);
        } else {
            $.ajax({
                'type': 'GET',
                'url': baseUrl + '/Firm/getFirmsBySubspecialty?subspecialty_id=' + subspecialty_id,
                dataType: "json",
                'success': function (data) {
                    $firm.attr('disabled', false);
                    $firm.append($('<option>', {
                        text: 'All Contexts',
                        value: '',
                    }));
                    for (var id in data) {
                        if (data.hasOwnProperty(id)) {
                            $firm.append($('<option>', {
                                value: id,
                                text: data[id]
                            }));
                        }
                    }
                    $firm.addClass('js-firm-dropdown cols-full');
                }
            });
        }
    });
});