
function checkUploadMode(){
    if($("input[name='upload_mode']:checked").val() == 'single'){
        $('#single_document_uploader').show();
        $('#double_document_uploader').hide();
        clearUploadStatus()
    }else if($("input[name='upload_mode']:checked").val() == 'double'){
        $('#single_document_uploader').hide();
        $('#double_document_uploader').show();
        clearUploadStatus();
    }
}

function clearUploadStatus(){
    $('#showUploadStatus').width(0);
    $('#showUploadStatus').text('');
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.files;
    $(ev.target).closest(".upload-box").find("input[type=file]").prop("files", ev.dataTransfer.files);
}

function documentUpload(field){
    var formData;
    formData = new FormData($('#document-create')[0]);

	$.ajax({
            url: '/OphCoDocument/Default/fileUpload',
            type: 'POST',
            xhr: function() { 
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress', showIMGProgress, false);
                }
                return myXhr;
            },
            enctype: 'multipart/form-data',
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){	

            },
            success:function(response){
                if(response.s === 0){
                    clearInputFile( response.index );
                    clearUploadStatus();
                    new OpenEyes.UI.Dialog.Alert({
                        content: response.msg
                    }).open();
                } else{
                    clearUploadStatus();
                    $.each(response, function(index, value){
                        filedata = field.val().split('.');
                        extension = filedata[filedata.length-1];
                        if($('#Element_OphCoDocument_Document_'+index).length) {
                            $('#Element_OphCoDocument_Document_' + index).val(value);
                        }else{
                            $('#showUploadStatus').after('<input type="hidden" name="Element_OphCoDocument_Document['+index+']" id="Element_OphCoDocument_Document_'+index+'" value="'+value+'">');
                        }
                        var elem = generateViewToFile(response , index, value , filedata  );
                        clearInputFile( index );

                        $('#Document_'+index).closest(".upload-box").after(elem);
                        $('#Document_'+index).closest(".upload-box").hide();
                    });
                }
                
               
            },
            error:function( xhr, ajaxOptions, thrownError ){
                alert( xhr.responseText );
            },
            complete:function(){

            }
	});
 
}

function showIMGProgress(evt) {
    if (evt.lengthComputable) {		
        var percentComplete = (evt.loaded / evt.total) * 100;
        $('#showUploadStatus').text( parseInt ( percentComplete ) + "%" );
        $('#showUploadStatus').width( percentComplete*9);
    }  
}

function generateViewToFile( res , index, value , filedata ){
    extension = filedata[filedata.length-1].toLowerCase();
    
    switch (extension) {
        case 'jpg':
        case 'jpeg':
        case 'png':
            result = createOPHCOImageContainer( res , value , extension , index );
        break;
        case 'pdf':
            result = createOPHCODocumentContainer( res , value , extension , index );
        break;
    }
    
    return result;
}

function createOPHCOImageContainer( res , value , ext, index ){
    var sideID;
    if(res.single_document_id){
        sideID = res.single_document_id;
    } else if( res.right_document_id ){
        sideID = res.right_document_id;
    } else{
        sideID = res.left_document_id;
    }
   
    imageContainer = '<div id="ophco-image-container-'+sideID+'" class="ophco-image-container">'
        +'<img id="single-image-'+ sideID +'" class="image-upload-del" src="/file/view/'+value+'/image.'+ext+'" border="0">'
        +'<span title="Delete" onclick="deleteOPHCOImage('+sideID+', \''+index+'\' );" class="image-del-icon">X</span>'
        +'</div>';

    return imageContainer;
}

function createOPHCODocumentContainer( res , value , ext, index ){
    var sideID;
    if(res.single_document_id){
        sideID = res.single_document_id;
    } else if( res.right_document_id ){
        sideID = res.right_document_id;
    } else{
        sideID = res.left_document_id;
    }
    
    documentContainer = '<div id="ophco-image-container-'+sideID+'" class="ophco-image-container">'
        +'<object width="90%" height="500px" data="/file/view/'+value+'/image.'+ext+'" type="application/pdf">'
            +'<embed src="/file/view/'+value+'/image.'+ext+'" type="application/pdf" />'
        +'</object>'
        +'<span title="Delete" onclick="deleteOPHCOImage('+sideID+', \''+index+'\' );" class="image-del-icon">X</span>'
        +'</div>';

    return documentContainer;
}


function deleteOPHCOImage( iID , index ){
    deleteConfirm('Do you want to delete this file?', function ()
    {
        $('#ophco-image-container-'+iID+'').remove();
        if($('#Element_OphCoDocument_Document_'+index).length) {
            $('#Element_OphCoDocument_Document_' + index).val('NULL');
        }else{
            $('#showUploadStatus').after('<input type="hidden" name="Element_OphCoDocument_Document['+index+']" id="Element_OphCoDocument_Document_'+index+'" value="NULL">');
        }
        createUploadButton( index );
        clearUploadStatus();
    });
}

function createUploadButton( index ){
    btn = '<div class="upload-box">' +
        '<label for="Document_'+index+'" id="upload_box" class="upload-label" ondrop="drop(event)" ondragover="allowDrop(event)"><svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"/></svg><br> Click to select file or DROP here</label>'+
        '<input autocomplete="off" type="file" name="Document['+index+']" id="Document_'+index+'" style="display:none;">' +
        '</div>';
    $('#'+index+'_row').html(btn);
    
    $('#Document_'+index+'').on('change', function(){
        documentUpload($(this));
    });
}

function deleteConfirm(dialogText, okFunc, cancelFunc, dialogTitle) {
    $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
        draggable: false,
        modal: true,
        resizable: false,
        width: 'auto',
        title: dialogTitle || 'Confirm',
        minHeight: 75,
        buttons: {
            Yes: function () {
            if (typeof (okFunc) == 'function') {
                setTimeout(okFunc, 50);
            }
            $(this).dialog('destroy');
            },
            Cancel: function () {
                if (typeof (cancelFunc) == 'function') {
                    setTimeout(cancelFunc, 50);
                }
                $(this).dialog('destroy');
            }
        }
    });
}

function clearInputFile( index ){
    $('#Document_'+index).val("");
    $('#Document_'+index).prop("files",null);
}

function checkDocumentsUploaded(){
    if($( "input[name='upload_mode']:checked" ).val()=='single')
    {
        if($('#Element_OphCoDocument_Document_single_document_id').val() === undefined || $('#Element_OphCoDocument_Document_single_document_id').val()=='NULL'){
            return false;
        }
    }
    else if ($( "input[name='upload_mode']:checked" ).val()=='double')
    {
        if(($('#Element_OphCoDocument_Document_left_document_id').val() === undefined || $('#Element_OphCoDocument_Document_left_document_id').val()=='NULL')&&
            ($('#Element_OphCoDocument_Document_right_document_id').val() === undefined || $('#Element_OphCoDocument_Document_right_document_id').val()=='NULL')){
            return false;
        }
    }
    else if(!$( "input[name='upload_mode']:checked" ).length)
    {
        return false;
    }
    return true;
}

$(document).ready(function(){
    handleButton($('#et_save'), function (e) {
        if(!checkDocumentsUploaded()){
            e.preventDefault();
            new OpenEyes.UI.Dialog.Alert({
                content: "Please upload at least one document!"
            }).open();
            enableButtons($('#et_save'));
        }
    });

    $('#single_document_uploader').hide();
    $('#double_document_uploader').hide();
    checkUploadMode();

    $('#Document_single_document_id, #Document_right_document_id, #Document_left_document_id').on('change', function(){
        documentUpload($(this));
    });

    $("input[name='upload_mode']").on('change', function(){
        checkUploadMode();
    })
    
    $( "#ophco-document-viewer" ).tabs();

});