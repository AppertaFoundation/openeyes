$(document).ready(function(){
    $('#OphInDnaextraction_DnaExtraction_Storage_letter').keyup(function(){
        this.value = this.value.toUpperCase();
    });
});
function getExtractionStorageLetterNumber( obj ){
    obj = $(obj);
    $.ajax({
        'type': 'POST',
        'url': baseUrl+'/OphInDnaextraction/dnaExtractionStorageAdmin/getNextLetterNumberRow',
        'data': {
                box_id: obj.val(),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
        },  
        'dataType': 'json',
        'success': function(response) {
            if (typeof(response.letter) != "undefined"){
                $('#OphInDnaextraction_DnaExtraction_Storage_letter').val(response.letter);
                $('#OphInDnaextraction_DnaExtraction_Storage_number').val(response.number);
                
                $('#OphInDnaextraction_DnaExtraction_Storage_letter').prop('disabled', false);
                $('#OphInDnaextraction_DnaExtraction_Storage_number').prop('disabled', false);
            } else {        
                $('#OphInDnaextraction_DnaExtraction_Storage_letter').prop('disabled', true);
                $('#OphInDnaextraction_DnaExtraction_Storage_number').prop('disabled', true);
            }
        }
    });

}