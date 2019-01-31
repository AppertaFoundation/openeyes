function removeSelectedContactLabel() {
    $('#no_contact_label_result').hide();
    $('.selected_contact_label span.name').text('');
    $('#selected_contact_label_wrapper').hide();
    $('#Contact_contact_label_id').val('-1');
}
function addItem(wrapper_id, ui){
    var $wrapper = $('#' + wrapper_id);
    $wrapper.find('.js-name').text(ui.item.label);
    $wrapper.show();
    $wrapper.find('.hidden_id').val(ui.item.id);
}
$(document).ready(function () {
    $('#selected_contact_label_wrapper').on('click', '.remove', function () {
        removeSelectedContactLabel();
    });
});