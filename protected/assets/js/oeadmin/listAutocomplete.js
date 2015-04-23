// we need to refresh the list when select values change
$(document).ready(
    function () {
        $('.filterfieldselect').change(
            function () {
                $(this).closest('form').submit();
            }
        );
    }
);

function deleteItem(itemId, deleteURL) {
    if (itemId === undefined) {
        return false;
    } else {

        $.ajax({
                url: deleteURL + "?itemId=" + itemId,
                error: function () {
                    console.log("ERROR, something went wrong!");
                },
                success: function () {
                    // we can dynamicaly rebuild the list here, but I think we don't need to develop more code for that :)
                    window.location.reload();
                }
            }
        );
    }
}

function addItem(itemId, saveURL) {
    if (itemId === undefined) {
        return false;
    } else {
        // itemId: autocomplete value
        // .filterfieldselect: all filter fields
        var saveParams = $('.ui-autocomplete-input').attr('name') + "=" + itemId;
        $('.filterfieldselect').each(function () {
            // name: search[filterid][field_name][value]
            fieldNameData = $(this).attr('name').split('[');
            fieldName = fieldNameData[2].replace(']', '');
            saveParams += "&" + fieldName + "=" + $(this).val();
        });

        $.ajax({
                url: saveURL + "?" + saveParams,
                error: function () {
                    console.log("ERROR, something went wrong!");
                },
                success: function () {
                    // we can dynamicaly rebuild the list here, but I think we don't need to develop more code for that :)
                    window.location.reload();
                }
            }
        );
    }

}