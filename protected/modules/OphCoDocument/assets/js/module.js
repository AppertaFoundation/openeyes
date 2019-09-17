/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCoDocument = OpenEyes.OphCoDocument || {};

(function (exports) {
    "use strict";

    function DocumentUploadController(options) {
        this.options = $.extend(true, {}, DocumentUploadController._defaultOptions, options);

        this.initialiseTriggers();
    }

    DocumentUploadController._defaultOptions = {
        "wrapperSelector": ".js-document-upload-wrapper",
        "fileInputSelector": ".js-document-file-input",
        "removeButtonSelector": ".js-remove-document-wrapper button",
        "singleUploadSelector": "#single_document_uploader",
        "doubleUploadSelector": "#double_document_uploader",
        "dropAreaSelector": ".upload-label",
        "uploadModeSelector": "input[name='upload_mode']",
        "action": "",
        "removedDocuments": "#removed-docs"
    };

    DocumentUploadController.prototype.initialiseTriggers = function () {

        let controller = this;

        $(controller.options.dropAreaSelector).on({
            "dragenter, dragover": function(ev){
                ev.preventDefault();
                ev.stopPropagation();
            },
            "drop": function(ev){
                ev.preventDefault();

                let data = ev.originalEvent.dataTransfer.files;
                $(ev.target).closest(".upload-box").find("input[type=file]").prop("files", data);
                $(controller.options.fileInputSelector).trigger('change');
            },
        });

        $(controller.options.uploadModeSelector).on('change', function () {

            $(controller.options.singleUploadSelector).toggle();
            $(controller.options.doubleUploadSelector).toggle();

        });

        $(controller.options.wrapperSelector).on('change', controller.options.fileInputSelector, function () {
            controller.documentUpload($(this));
        });

        $(controller.options.wrapperSelector).on('click', controller.options.removeButtonSelector, function (e) {
            e.preventDefault();
            controller.removeDocument($(this).data('side'));
        });

        window.addEventListener("paste", function (event) {

            var files = event.clipboardData.files;
            if (files[0] && files[0].type.includes("image")) {

                    if ($("input[name='upload_mode']:checked").val() === 'double') {

                        let dialog = new OpenEyes.UI.Dialog({
                            content: $($('#side-selector-popup').html()),
                            title: "Do you want to upload right or left document ?",

                            onOpen: function() {
                                let dialog = this;
                                dialog.content.on("click", ".js-side-picker", function() {
                                    let side = $(this).data("side");

                                    controller.paste(side, files);
                                    $(controller.options.fileInputSelector).trigger('change');
                                    dialog.close();
                                });
                            },
                            onClose: function() {
                                this.destroy();
                            }
                        });
                        dialog.open();

                        $(window).on('keypress', function (event) {

                            if (event.key === 'l' || event.key === 'L') {
                                controller.paste("left", files);
                                dialog.close();
                                $(this).unbind(event);
                            }
                            if (event.key === 'r' || event.key === 'R') {
                                controller.paste("right", files);
                                dialog.close();
                                $(this).unbind(event);
                            }
                            $(controller.options.fileInputSelector).trigger('change');
                        });
                    } else if ($("input[name='upload_mode']:checked").val() === 'single') {
                        controller.paste("single", files);
                        $(controller.options.fileInputSelector).trigger('change');
                    }

            } else {
                new OpenEyes.UI.Dialog.Alert({
                    content: "No image data was found in your clipboard , copy an image (or take a screenshot)."
                }).open();
            }
        }, false);

        $("a.button.header-tab.red").on('click', function () {
           controller.options.action = 'cancel';
        });

        $("#et_save").on('click', function () {
           controller.options.action = 'save';
        });
    };

    DocumentUploadController.prototype.removeDocument = function (side) {
        let controller = this;
        let $td = $("#Document_" + side + "_document_row_id").closest('td');

        $td.find('.ophco-image-container').remove();
        $td.find(".upload-box").show().find('.js-upload-box-text').text("Click to select file or DROP here");
        $td.find('.js-remove-document-wrapper').hide();
        $(controller.options.uploadModeSelector).attr('disabled', false);

        $(controller.options.fileInputSelector).val("");
        //$td.find(controller.options.fileInputSelector).prop('files', null);
        //$file_input.replaceWith($file_input.val("").clone(true));

        let deleted_doc = $td.find('.js-document-id').val();
        $td.find('.js-document-id').val("");
        let $removed_docs = $(controller.options.removedDocuments);
        $removed_docs.val(deleted_doc + ';' + $removed_docs.val());
    };

    DocumentUploadController.prototype.paste = function (side, files) {
        let controller = this;
        let $input = $("#Document_" + side + "_document_row_id");

        controller.removeDocument(side);
        $input.prop("files", files);
    };

    DocumentUploadController.prototype.setUploadStatusText = function(field, text) {
        let $label = $(field).closest('.upload-box').find('.js-upload-box-text');
        $label.text(text);
    };

    DocumentUploadController.prototype.documentUpload = function($field) {
        let controller = this;
        let formData = new FormData();
        formData.append($field.attr('name'), $field.prop('files')[0]);

        if (controller.validateFile($field)) {
            $.ajax({
                url: '/OphCoDocument/Default/fileUpload',
                type: 'POST',
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', function(evt) {
                            if (evt.lengthComputable) {
                                let percentage = (evt.loaded / evt.total) * 100;
                                controller.setUploadStatusText($field, 'Uploading: ' + parseInt(percentage) + '%');
                            }
                        }, false);
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
                        new OpenEyes.UI.Dialog.Alert({
                            content: response.msg
                        }).open();
                    } else {
                        $.each(response, function (index, value) {
                            let filedata = $field.val().split('.');
                            let $hidden_field = $('#Element_OphCoDocument_Document_' + index);
                            let $td = $field.closest('td');
                            if ($hidden_field.length) {
                                $hidden_field.val(value);
                            }

                            var view = controller.generateView(response, index, value, filedata);
                            //clearInputFile(index);

                            $td.find(".upload-box").after(view);
                            $td.find(".upload-box").hide();

                            $field.closest('td').find('.js-remove-document-wrapper').show();

                            $(controller.options.uploadModeSelector + ":not(:checked").attr('disabled', true);
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
    };

    DocumentUploadController.prototype.generateView = function(res, index, value, filedata) {

        let extension = filedata[filedata.length - 1].toLowerCase();
        let result;

        let side_id;
        if (res.single_document_id) {
            side_id = res.single_document_id;
        } else if (res.right_document_id) {
            side_id = res.right_document_id;
        } else {
            side_id = res.left_document_id;
        }

        let $div = $('<div>', {"id": 'ophco-image-container-' + side_id, "class": "ophco-image-container"});
        let $img;

        switch (extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                    $img = $('<img>', {
                        "id": "single-image-" + side_id,
                        "class": "image-upload-del", "src": "/file/view/" + value + "/image." + extension,
                        "width": "100%"
                        });
                    result = $div.append($img);
                break;
            case 'pdf':
                result =
                    '<div id="ophco-image-container-' + side_id + '" class="ophco-image-container">' +
                        '<object height="800" width="100%" data="/file/view/' + value + '/image.' + extension + '" type="application/pdf">' +
                            '<embed height="100%" width="100%" src="/file/view/' + value + '/image.' + extension + '" type="application/pdf" />' +
                        '</object>' +
                    '</div>';
                break;
            case 'mp4':
            case 'ogg':
            case 'mov':
            case 'quicktime':
           //     result = controller.createOPHCOVideoContainer(res, value, extension, index);
                break;
        }

        return result;
    };

    DocumentUploadController.prototype.validateFile = function($input) {
        let valid = true;
        if (typeof FileReader !== "undefined") {

            var input_selector = $input.attr('id');
            var file = document.getElementById(input_selector).files[0];
            if(!file){
                return false;
            }
            var size = file.size;

            if ($input.val()) {
                if (size > window.max_document_size || size > window.max_content_length) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'The file you tried to upload exceeds the maximum allowed file size, which is ' + (window.max_document_size / 1048576) + ' MB'
                    }).open();

                    valid = false;
                }

                if(file.name.length > window.max_document_name_length){
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'The file you tried to upload exceeds the maximum allowed document name length, which is ' + window.max_document_name_length + ' characters'
                    }).open();

                    valid = false;
                }

                if (window.allowed_file_types.indexOf(file.type) === -1) {
                    valid = false;

                    new OpenEyes.UI.Dialog.Alert({
                        content: 'Only the following file types can be uploaded: ' + window.allowed_file_types.join(', ') +
                            '\n\nFor reference, the type of the file you tried to upload is: ' + file.type
                    }).open();
                }
            }

        }
        return valid;
    };


    exports.DocumentUploadController = DocumentUploadController;

})(OpenEyes.OphCoDocument);

$(document).ready(function () {
    "use strict";

    autosize($('.autosize'));

    var uploader = new OpenEyes.OphCoDocument.DocumentUploadController();
    $('.js-document-upload-wrapper').data('controller', uploader);
});


