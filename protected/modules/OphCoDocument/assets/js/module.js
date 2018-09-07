function checkUploadMode() {
    if ($("input[name='upload_mode']:checked").val() == 'single') {
        $('#single_document_uploader').show();
        $('#double_document_uploader').hide();
        clearUploadStatus();
    } else if ($("input[name='upload_mode']:checked").val() == 'double') {
        $('#single_document_uploader').hide();
        $('#double_document_uploader').show();
        clearUploadStatus();
    }
}

function clearUploadStatus() {
    $('#showUploadStatus').width(0);
    $('#showUploadStatus').text('');
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.files;
    $(ev.target).closest(".upload-box").find("input[type=file]").prop("files", data);
}

function paste(files, side) {
    $('#Document_' + side + '_document_id').val("");
    $('#Document_' + side + '_document_id').prop("files", null);
    $('#Document_' + side + '_document_id').prop("files", files);
}

function validateFile(input) {
    var valid = true;

    if (typeof FileReader !== "undefined") {

        var $input = $(input);
        var input_selector = $input.attr('id');
        var file = document.getElementById(input_selector).files[0];
        var size = file.size;

        if ($input.val()) {
            if (size > max_document_size || size > max_content_length) {
                new OpenEyes.UI.Dialog.Alert({
                    content: 'The file you tried to upload exceeds the maximum allowed file size, which is ' + (max_document_size / 1048576) + ' MB'
                }).open();

                valid = false;
            }

            if(file.name.length > max_document_name_length){
                new OpenEyes.UI.Dialog.Alert({
                    content: 'The file you tried to upload exceeds the maximum allowed document name length, which is ' + max_document_name_length + ' characters'
                }).open();

                valid = false;
            }

            if (allowed_file_types.indexOf(file.type) === -1) {
                valid = false;

                new OpenEyes.UI.Dialog.Alert({
                    content: 'Only the following file types can be uploaded: ' + allowed_file_types.join(', ') +
                    '\n\nFor reference, the type of the file you tried to upload is: ' + file.type
                }).open();
            }
        }

    }
    return valid;
}

function documentUpload(field) {
    var formData;
    formData = new FormData($('#document-create')[0]);

    if (!validateFile(field)) {
        return false;
    }

    $.ajax({
        url: '/OphCoDocument/Default/fileUpload',
        type: 'POST',
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
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
        beforeSend: function () {

        },
        success: function (response) {
            if (response.s === 0) {
                clearInputFile(response.index);
                clearUploadStatus();
                new OpenEyes.UI.Dialog.Alert({
                    content: response.msg
                }).open();
            } else {
                clearUploadStatus();
                $.each(response, function (index, value) {
                    filedata = field.val().split('.');
                    extension = filedata[filedata.length - 1];
                    if ($('#Element_OphCoDocument_Document_' + index).length) {
                        $('#Element_OphCoDocument_Document_' + index).val(value);
                    } else {
                        $('#showUploadStatus').after('<input type="hidden" name="Element_OphCoDocument_Document[' + index + ']" id="Element_OphCoDocument_Document_' + index + '" value="' + value + '">');
                    }
                    var elem = generateViewToFile(response, index, value, filedata);
                    clearInputFile(index);

                    $('#Document_' + index).closest(".upload-box").after(elem);
                    $('#Document_' + index).closest(".upload-box").hide();
                });
            }


        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
        },
        complete: function () {

        }
    });

}

function showIMGProgress(evt) {
    if (evt.lengthComputable) {
        var percentComplete = (evt.loaded / evt.total) * 100;
        $('#showUploadStatus').text(parseInt(percentComplete) + "%");
        $('#showUploadStatus').width(percentComplete * 9);
    }
}

function generateViewToFile(res, index, value, filedata) {
    extension = filedata[filedata.length - 1].toLowerCase();

    switch (extension) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            result = createOPHCOImageContainer(res, value, extension, index);
            break;
        case 'pdf':
            result = createOPHCODocumentContainer(res, value, extension, index);
            break;
        case 'mp4':
        case 'ogg':
        case 'mov':
        case 'quicktime':
            result = createOPHCOVideoContainer(res, value, extension, index);
            break;
    }

    return result;
}

function createOPHCOImageContainer(res, value, ext, index) {
    var sideID;
    if (res.single_document_id) {
        sideID = res.single_document_id;
    } else if (res.right_document_id) {
        sideID = res.right_document_id;
    } else {
        sideID = res.left_document_id;
    }

    imageContainer = '<div id="ophco-image-container-' + sideID + '" class="ophco-image-container">'
        + '<img id="single-image-' + sideID + '" class="image-upload-del" src="/file/view/' + value + '/image.' + ext + '" border="0">'
        + '<span title="Delete" onclick="deleteOPHCOImage(' + sideID + ', \'' + index + '\' );" class="image-del-icon">X</span>'
        + '</div>';

    return imageContainer;
}

function createOPHCOVideoContainer(res, value, ext, index) {
    var sideID;
    if (res.single_document_id) {
        sideID = res.single_document_id;
    } else if (res.right_document_id) {
        sideID = res.right_document_id;
    } else {
        sideID = res.left_document_id;
    }

    imageContainer = '<div id="ophco-image-container-' + sideID + '" class="ophco-image-container">'
        + '<video id="single-image-' + sideID + '" class="image-upload-del" width="320" controls>'
        + '<source src="/file/view/' + value + '/image.' + ext + '" type="video/' + ext + '">'
        + '</video>'
        + '<span title="Delete" onclick="deleteOPHCOImage(' + sideID + ', \'' + index + '\' );" class="image-del-icon">X</span>'
        + '</div>';

    return imageContainer;
}


function createOPHCODocumentContainer(res, value, ext, index) {
    var sideID;
    if (res.single_document_id) {
        sideID = res.single_document_id;
    } else if (res.right_document_id) {
        sideID = res.right_document_id;
    } else {
        sideID = res.left_document_id;
    }

    documentContainer = '<div id="ophco-image-container-' + sideID + '" class="ophco-image-container">'
        + '<object width="90%" height="500px" data="/file/view/' + value + '/image.' + ext + '" type="application/pdf">'
        + '<embed src="/file/view/' + value + '/image.' + ext + '" type="application/pdf" />'
        + '</object>'
        + '<span title="Delete" onclick="deleteOPHCOImage(' + sideID + ', \'' + index + '\' );" class="image-del-icon">X</span>'
        + '</div>';

    return documentContainer;
}


function deleteOPHCOImage(iID, index) {
    deleteConfirm('Do you want to delete this file?', function () {
        $('#ophco-image-container-' + iID + '').remove();
        if ($('#Element_OphCoDocument_Document_' + index).length) {
            $('#Element_OphCoDocument_Document_' + index).val('NULL');
        } else {
            $('#showUploadStatus').after('<input type="hidden" name="Element_OphCoDocument_Document[' + index + ']" id="Element_OphCoDocument_Document_' + index + '" value="NULL">');
        }
        createUploadButton(index);
        clearUploadStatus();
    });
}

function createUploadButton( index ){
    var btn = '<div class="upload-box">' +
        '<label for="Document_'+index+'" id="upload_box" class="upload-label" ondrop="drop(event)" ondragover="allowDrop(event)">' +
      '<i class="oe-i download medium"></i>' +
      '<br> Click to select file or DROP here' +
      '</label>'+
        '<input autocomplete="off" type="file" name="Document['+index+']" id="Document_'+index+'" style="display:none;">' +
        '</div>';
    $('#' + index + '_row').html(btn);

    $('#Document_' + index + '').on('change', function () {
        documentUpload($(this));
    });
}

function deleteConfirm(dialogText, okFunc, cancelFunc, dialogTitle) {
  var dialog = new OpenEyes.UI.Dialog.Confirm({
    content: dialogText,
    okButton: 'Yes',
    cancelButton: 'Cancel',
    title: dialogTitle,
  });
  dialog.open();
  dialog.on("ok", function () {
    if (typeof (okFunc) == 'function') {
      setTimeout(okFunc, 50);
    }
  });
  dialog.on('cancel', function() {
    if (typeof (cancelFunc) == 'function') {
      setTimeout(cancelFunc, 50);
    }
  });
}

function clearInputFile(index) {
    $('#Document_' + index).val("");
    $('#Document_' + index).prop("files", null);
}

function checkDocumentsUploaded() {
    if ($("input[name='upload_mode']:checked").val() == 'single') {
        if ($('#Element_OphCoDocument_Document_single_document_id').val() === undefined || $('#Element_OphCoDocument_Document_single_document_id').val() == 'NULL') {
            return false;
        }
    }
    else if ($("input[name='upload_mode']:checked").val() == 'double') {
        if (($('#Element_OphCoDocument_Document_left_document_id').val() === undefined || $('#Element_OphCoDocument_Document_left_document_id').val() == 'NULL') &&
            ($('#Element_OphCoDocument_Document_right_document_id').val() === undefined || $('#Element_OphCoDocument_Document_right_document_id').val() == 'NULL')) {
            return false;
        }
    }
    else if (!$("input[name='upload_mode']:checked").length) {
        return false;
    }
    return true;
}

$(document).ready(function () {
    $(this).on('click', '#et_save', function (e) {
        if (!checkDocumentsUploaded()) {
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

    $('#Document_single_document_id, #Document_right_document_id, #Document_left_document_id').on('change', function () {
        documentUpload($(this));
    });

    $("input[name='upload_mode']").on('change', function () {
        checkUploadMode();
    })

    $("#ophco-document-viewer").tabs();

});

function deleteImage(side) {
    if ($('#Element_OphCoDocument_Document_' + side + '_document_id').val()) {
        $('#ophco-image-container-' + $('#Element_OphCoDocument_Document_' + side + '_document_id').val() + '').remove();
        $('#Element_OphCoDocument_Document_' + side + '_document_id').val('NULL');
        createUploadButton('side' + '_document_id');
        clearUploadStatus();
    }
}

function uploadPastedImage(dialog, side) {
    var files = dialog.data('files');
    deleteImage(side);
    paste(files, side);
    dialog.dialog("close");
}

var dialogKeyPressHandler = function (event, dialog) {
    if (event.key === 'l' || event.key === 'L') {
        uploadPastedImage(dialog, "left");
        $(this).unbind(event);
    }
    if (event.key === 'r' || event.key === 'R') {
        uploadPastedImage(dialog, "right");
        $(this).unbind(event);
    }
}


window.addEventListener("paste", function (event) {
    var files = event.clipboardData.files;
    if (event.clipboardData.files[0]) {
        if (event.clipboardData.files[0].type.includes("image")) {
            if ($("input[name='upload_mode']:checked").val() === 'double') {
                var dialog = $('<h2 class="text-center">Do you want to upload left or right document ?</h2>').data('files', files).dialog({
                    buttons: [
                        {
                            'text': 'Right(R)',
                            click: function () {
                                uploadPastedImage($(this), "right");
                            }
                        },
                        {
                            'text': 'Left(L)',
                            click: function () {
                                uploadPastedImage($(this), "left");
                            }
                        },
                    ],
                    close: function () {
                        $(window).unbind(event);
                    }

                }, event);
                $(window).on('keypress', function (event) {
                    dialogKeyPressHandler(event, dialog);
                });
            } else if ($("input[name='upload_mode']:checked").val() === 'single') {
                deleteImage('single');
                paste(files, "single");
            }
        }
    } else {
        new OpenEyes.UI.Dialog.Alert({
            content: "No image data was found in your clipboard , copy an image (or take a screesnhot)."
        }).open();
    }
}, false);
