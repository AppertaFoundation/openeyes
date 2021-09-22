
/* Module-specific javascript can be placed here */
var dnaExtractionPrintUrl;
$(document).ready(function() {

  $(this).on('click','#et_cancel',function(e) {
        if (m = window.location.href.match(/\/update\/[0-9]+/)) {
                window.location.href = window.location.href.replace('/update/','/view/');
        } else {
                window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
        }
        e.preventDefault();
    });

  $(this).on('click', '#et_print',function(e) {
       // e.preventDefault();
       // printEvent(null);
        printIFrameUrl(dnaExtractionPrintUrl, null);
        enableButtons();
        e.preventDefault();

    });

  $(this).on('click', '#et_canceldelete',function(e) {
        if (m = window.location.href.match(/\/delete\/([0-9]+)/)) {
                window.location.href = baseUrl+'/patient/parentEvent/'+m[1];
        } else {
                window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
        }
        e.preventDefault();
    });

    $('select.populate_textarea').unbind('change').change(function() {
        if ($(this).val() != '') {
            var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
            var el = $('#'+cLass+'_'+$(this).attr('id'));
            var currentText = el.text();
            var newText = $(this).children('option:selected').text();

            if (currentText.length == 0) {
                    el.text(ucfirst(newText));
            } else {
                    el.text(currentText+', '+newText);
            }
        }
    });
    $(this).on('click', '#save-new-storage-btn',function(f) {
        saveNewStorage();
        $(".close-icon-btn").click();
    });
    $(this).on('click', '#addNewStoragePopup',function(e) {

        $.ajax({
          'type': 'POST',
          'data': {YII_CSRF_TOKEN: YII_CSRF_TOKEN},
          'url': baseUrl+'/OphInDnaextraction/default/GetNewStorageFields',
          'success': function(html) {

            var storageDialog = new OpenEyes.UI.Dialog({
              content: html,
              title: "Add new storage",
              autoOpen: false,
                dialogClass: 'oe-popup',
              onClose: function() { enableButtons(); },
              open: function (event, ui) {
              },
            });

            storageDialog.open();
          },
        });
      });
});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
            // handle event
    }
}

function getAvailableLetterNumberToBox( obj ){
    obj = $(obj);
    
    $.ajax({
        'type': 'POST',
        'url': baseUrl+'/OphInDnaextraction/default/getAvailableLetterNumberToBox',
        'data': {
                box_id: obj.val(),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
        },  
        'dataType': 'json',
        'success': function(response) {
            if (typeof(response.letter) != "undefined"){
                $('#dnaextraction_letter').attr("placeholder", response.letter);
                $('#dnaextraction_number').attr("placeholder", response.number);

                $('#dnaextraction_letter').prop('disabled', false);
                $('#dnaextraction_number').prop('disabled', false);
            } else {        
                $('#dnaextraction_letter').prop('disabled', true);
                $('#dnaextraction_number').prop('disabled', true);
            }
        }
    });
}

function saveNewStorage(){
    data = $('#dnaextraction_addNewStorageForm').serialize() + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN;
    
    var result = false;
    $.ajax({
        'type': 'POST',
        'url': baseUrl+'/OphInDnaextraction/default/saveNewStorage',
        'data': data,
        'dataType': 'json',
        'async': false,
        'success': function(response) {
            if(response.s == '0'){
                new OpenEyes.UI.Dialog.Alert({
                    content: response.msg
                }).open();
            } else {
                
                refreshStorageSelect( response.selected );
                result = true;
            }
        }
    });
    
    return result;
}

function refreshStorageSelect( selectedID ){
    $.ajax({
        'type': 'GET',
        'url': baseUrl+'/OphInDnaextraction/default/refreshStorageSelect',    
        'dataType': 'json',
        'success': function(response) {
            
            var count = Object.keys(response).length;
            var option = '<option value="">- Select -</option>';
            
            for(var i = 0; i < count; i++){
                key = Object.keys(response)[i];
                value = Object.values(response)[i];

                if(key == selectedID){
                    option += '<option value="'+key+'" SELECTED>'+value+'</option>';
                } else {
                    option += '<option value="'+key+'">'+value+'</option>';
                }

            }
            
            $('#Element_OphInDnaextraction_DnaExtraction_storage_id').html(option);
           
        }
    });
}

function setUppercase( obj ){
    obj.value = obj.value.toUpperCase();
}



