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
        this.imageAnnotators = [];
        this.initialiseTriggers();
    }

    DocumentUploadController._defaultOptions = {
        "wrapperSelector": ".js-document-upload-wrapper",
        "fileInputSelector": ".js-document-file-input",
        "removeButtonSelector": ".js-remove-document-action",
        "singleUploadSelector": "#single_document_uploader",
        "doubleUploadSelector": "#double_document_uploader",
        "dropAreaSelector": ".upload-label",
        "uploadModeSelector": "input[name='upload_mode']",
        "action": "",
        "removedDocumentsSelector": "#removed-docs",
        "finalizeImageSelector": ".js-finalize-image",
        "rotateImageSelector": ".js-rotate-image",
        "cancelAnnotationActionSelector": ".js-cancel-annotation-action",
        "saveAnnotationActionSelector": ".js-save-annotation-action",
        "documentUploadSummarySelector": ".js-document-summary-wrapper",
        "annotateImageActionSelector": ".js-annotate-image-action",
        "downloadImageActionSelector": ".js-download-image-action",
        "documentNameSelector": ".js-document-name",
        "documentSizeSelector": ".js-document-size",
        "pdfPrevButtonSelector": ".js-pdf-prev",
        "pdfNextButtonSelector": ".js-pdf-next",
        "documentEventSubTypeSelector": "#Element_OphCoDocument_Document_event_sub_type",
    };

    DocumentUploadController.prototype.initialiseTriggers = function () {

        let controller = this;

        function saveCancelAnnotation(controller, tdElem, side) {
            // show the save and cancel buttons to the user
            tdElem.querySelectorAll(`${controller.options.saveAnnotationActionSelector}, ${controller.options.cancelAnnotationActionSelector}`).forEach(hideElement);
            tdElem.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector}`).forEach(showElement);

            if (side !== 'single') {
                let oppositeSide = side === 'left' ? 'right' : 'left';
                document.querySelector(`.js-document-summary-wrapper [data-side=${side}]`).className = 'cols-half';
                document.querySelectorAll(`.js-document-summary-wrapper div[data-side=${oppositeSide}]`).forEach(showElement);
                side = 'double';
            }

            document.querySelector(`#${side}_document_uploader`).style.display = '';
            tdElem.querySelectorAll(`${controller.options.saveAnnotationActionSelector}, ${controller.options.cancelAnnotationActionSelector}`).forEach(hideElement);
            controller.clearCanvasData();
        }

        $(controller.options.dropAreaSelector).on({
            "dragenter, dragover": function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
            },
            "drop": function (ev) {
                ev.preventDefault();

                let data = ev.originalEvent.dataTransfer.files;

                $(ev.target).closest(".upload-box").find("input[type=file]").prop("files", data).trigger('change');
            },
        });

        $(controller.options.uploadModeSelector).on('change', function () {

            $(controller.options.singleUploadSelector).toggle();
            $(controller.options.doubleUploadSelector).toggle();

        });

        $(controller.options.wrapperSelector).on('change', controller.options.fileInputSelector, function () {
            let $field = $(this);
            let file_type = this.files[0].type;

            let formData = new FormData();
            formData.append($field.attr('name'), $field.prop('files')[0]);
            const fileName = $field.prop('files')[0].name;
            const fileSize = (Math.round((($field.prop('files')[0].size / (10 ** 6)) + Number.EPSILON) * 100) / 100) + "Mb";
            if (controller.validateFile($field)) {
                $.ajax({
                    url: '/OphCoDocument/Default/fileUpload',
                    type: 'POST',
                    xhr: function () {
                        var myXhr = $.ajaxSettings.xhr();
                        if (myXhr.upload) {
                            myXhr.upload.addEventListener('progress', function (evt) {
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
                    success: function (response) {
                        if (response.s === 0) {
                            new OpenEyes.UI.Dialog.Alert({
                                content: response.msg
                            }).open();
                        } else {
                            $.each(response, function (index, value) {
                                let filedata = $field.val().split('.');
                                let extension = filedata[filedata.length - 1].toLowerCase();
                                let $hidden_field = $('#Element_OphCoDocument_Document_' + index);
                                let $td = $field.closest('td');
                                let side = $td.data('side');
                                if ($hidden_field.length) {
                                    $hidden_field.val(value);
                                }

                                let fileUploadObject = {};
                                fileUploadObject.td = $td;
                                fileUploadObject.value = value;
                                fileUploadObject.extension = extension;
                                fileUploadObject.side = side;
                                fileUploadObject.fileType = file_type;
                                fileUploadObject.fileSize = fileSize;
                                fileUploadObject.fileName = fileName;

                                controller.documentUpload(fileUploadObject, true);
                            });
                        }
                    },
                    error: function (xhr) {
                        alert(xhr.responseText);
                    },
                });
            }
        });

        $(controller.options.documentEventSubTypeSelector).on('change', function () {
            let uploadMode = $("input[name='upload_mode']:checked").val();

            $.ajax({
                url: '/OphCoDocument/Default/getImage',
                type: 'POST',
                data: {
                    subTypeId: this.options[this.selectedIndex].value,
                    uploadMode: uploadMode,
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                success: function (response) {
                    if (response) {
                        response['document'].forEach(function (side) {
                            const td = document.querySelector(`.js-document-upload-wrapper td[data-side=${side}]`);
                            let $hidden_field = td.querySelector(`#Element_OphCoDocument_Document_${side}_document_id`);

                            if (typeof ($hidden_field) !== 'undefined' && $hidden_field != null) {
                                $hidden_field.value = response['file_id'];
                            }

                            let fileUploadObject = {};
                            fileUploadObject.td = $(document.querySelector(`.js-document-upload-wrapper td[data-side=${side}]`));
                            fileUploadObject.value = response['file_id'];
                            fileUploadObject.extension = response['extension'];
                            fileUploadObject.side = side;
                            fileUploadObject.fileType = response['mime_type'];
                            fileUploadObject.fileSize = response['file_size'];
                            fileUploadObject.fileName = response['file_name'];

                            controller.documentUpload(fileUploadObject);
                        });
                    }
                },
                error: function () {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "Sorry, an internal error occurred and we were unable to load the template for the selected document subtype.\n\nPlease contact support for assistance."
                    }).open();
                }
            });
        });

        Array.from(document.querySelectorAll(controller.options.removeButtonSelector)).forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                controller.removeDocument(element.dataset.side);
            });
        });

        Array.from(document.querySelectorAll(controller.options.annotateImageActionSelector)).forEach((element) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();

                const div = element.parentElement;
                // show the save and cancel buttons
                div.querySelectorAll(`${controller.options.saveAnnotationActionSelector}, ${controller.options.cancelAnnotationActionSelector}`).forEach(showElement);
                div.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector}`).forEach(hideElement);

                const summarySideElem = div.closest('div[data-side]');
                const side = summarySideElem.dataset.side;

                document.querySelectorAll(`.js-document-summary-wrapper div[data-side]:not([data-side=${side}])`).forEach(hideElement);

                summarySideElem.className = 'cols-full';

                const template = document.querySelector('#oe-annotate-image-template');
                // Clone the new row and insert it into the table
                const clone = template.content.cloneNode(true);

                const annotateImage = document.getElementById('js-annotate-image');
                annotateImage.appendChild(clone);

                const el = document.querySelector(`${controller.options.wrapperSelector} [data-side="${side}"] .ophco-image-container`);
                const format = el.getAttribute('data-file-format');
                const imageEl = el.querySelector(el.getAttribute('data-image-el'));
                const fileType = el.getAttribute('data-file-type');

                if (fileType === 'pdf') {
                    showElement(document.querySelector('#pdf-message'));
                }

                // let data = {attribute: 'data-side', value : side};
                // OpenEyes.UI.ImageAnnotator.init(imageEl.src, imageEl.naturalWidth, imageEl.naturalHeight, document.querySelectorAll('.js-document-upload-wrapper'), format, data, side);
                const imageAnnotator = new OpenEyes.UI.ImageAnnotator(imageEl.src, {
                    'format': format,
                    'side': side,
                    'canvasModifiedCallback': () => {
                        const $side_input = document.getElementById(`${side}_file_canvas_modified`);
                        if ($side_input) {
                            $side_input.value = 1;
                        }
                    },
                    'afterInit': () => {
                        const $img_wrapper = document.querySelectorAll('.js-document-upload-wrapper');
                        // hide the image
                        $img_wrapper.forEach(function (element) {
                            element.style.display = 'none';
                        });
                    }
                });

                const index = '' + this.imageAnnotators.length;
                this.imageAnnotators.push(imageAnnotator);
                imageEl.dataset.imageAnnotatorId = index;
                annotateImage.dataset.imageAnnotatorId = index;
            });
        });

        Array.from(document.querySelectorAll(controller.options.downloadImageActionSelector)).forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();

                const div = element.parentElement;
                const summarySideElem = div.closest('div[data-side]');
                const side = summarySideElem.dataset.side;

                const el = document.querySelector(`${controller.options.wrapperSelector} [data-side="${side}"] .ophco-image-container`);
                const fileType = el.getAttribute('data-file-type');

                if (fileType !== 'pdf') {
                    const format = el.getAttribute('data-file-format');
                    let url = el.querySelector(el.getAttribute('data-image-el')).src;
                    downloadImage(url, `download.${format}`);
                } else {
                    element.parentElement.querySelector('.js-protected-file-content').value = generatePdfOutput(el, true);
                }
            });
        });

        Array.from(document.querySelectorAll(controller.options.saveAnnotationActionSelector)).forEach((element) => {
            element.addEventListener('click', async (event) => {
                event.preventDefault();

                const div = element.parentElement;
                const summarySideElem = div.closest('div[data-side]');
                const side = summarySideElem.dataset.side;
                const el = document.querySelector(`${controller.options.wrapperSelector} [data-side="${side}"] .ophco-image-container`);
                const imageEl = el.querySelector(el.getAttribute('data-image-el'));
                const fileType = el.getAttribute('data-file-type');
                const imageAnnotator = this.imageAnnotators[imageEl.dataset.imageAnnotatorId];
                const canvasDataUrl = await imageAnnotator.getCanvasDataUrl();
                imageEl.src = canvasDataUrl;
                if (fileType !== 'pdf') {
                    document.querySelector(`#ProtectedFile_${side}_file_content`).value = canvasDataUrl;
                }

                $(`#${side}_document_rotate`).val('');

                saveCancelAnnotation(controller, div, side);
            });
        });

        Array.from(document.querySelectorAll(controller.options.cancelAnnotationActionSelector)).forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();

                const div = element.parentElement;
                const summarySideElem = div.closest('div[data-side]');
                const side = summarySideElem.dataset.side;
                saveCancelAnnotation(controller, div, side);

            });
        });

        Array.from(document.querySelectorAll(controller.options.pdfPrevButtonSelector)).forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();

                const td = element.parentElement.parentElement;
                const side = td.dataset.side;
                const el = td.querySelector(`.ophco-image-container`);
                let currentPage = parseInt(el.getAttribute('data-current-page'));
                let totalPages = parseInt(el.getAttribute('data-total-pages'));
                if (currentPage !== 1) {
                    el.querySelector(`.page-${currentPage}`).style.display = 'none';
                    el.querySelector(`.page-${currentPage - 1}`).style.display = '';
                    el.setAttribute('data-current-page', (currentPage - 1).toString());
                    el.setAttribute('data-image-el', `.page-${currentPage - 1}`);
                    document.querySelector(`.js-document-upload-wrapper td[data-side=${side}] label`).innerText = `Page: ${currentPage - 1}/${totalPages}`;
                }
            });
        });

        Array.from(document.querySelectorAll(controller.options.pdfNextButtonSelector)).forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const td = element.parentElement.parentElement;
                const side = td.dataset.side;
                const el = td.querySelector(`.ophco-image-container`);
                let currentPage = parseInt(el.getAttribute('data-current-page'));
                let totalPages = parseInt(el.getAttribute('data-total-pages'));
                if (currentPage !== totalPages) {
                    el.querySelector(`.page-${currentPage}`).style.display = 'none';
                    el.querySelector(`.page-${currentPage + 1}`).style.display = '';
                    el.setAttribute('data-current-page', (currentPage + 1).toString());
                    el.setAttribute('data-image-el', `.page-${currentPage + 1}`);
                    document.querySelector(`.js-document-upload-wrapper td[data-side=${side}] label`).innerText = `Page: ${currentPage + 1}/${totalPages}`;
                }
            });
        });

        window.addEventListener("paste", function (event) {

            var files = event.clipboardData.files;
            if (files[0] && files[0].type.includes("image")) {

                if ($("input[name='upload_mode']:checked").val() === 'double') {

                    let dialog = new OpenEyes.UI.Dialog({
                        content: $($('#side-selector-popup').html()),
                        title: "Do you want to upload right or left document ?",

                        onOpen: function () {
                            let dialog = this;
                            dialog.content.on("click", ".js-side-picker", function () {
                                let side = $(this).data("side");

                                controller.paste(side, files);
                                $(controller.options.fileInputSelector).trigger('change');
                                dialog.close();
                            });
                        },
                        onClose: function () {
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

        $("#et_save,#et_save_footer").on('click', async function (e) {
            controller.options.action = 'save';
            e.preventDefault();
            let side, containerElem, imageElem;
            let canvasData = false;
            const canvasJs = document.querySelector('#js-annotate-image .canvas-js');

            if (canvasJs) {
                side = canvasJs.getAttribute('data-side');
                containerElem = document.querySelector(`.js-document-upload-wrapper td[data-side=${side}] .ophco-image-container`);
                imageElem = containerElem.querySelector(containerElem.getAttribute('data-image-el'));

                const imageAnnotator = controller.imageAnnotators[imageElem.dataset.imageAnnotatorId];
                canvasData = await imageAnnotator.getCanvasDataUrl();
            }

            document.querySelectorAll(".js-save-annotation-action").forEach(button => {
                if (button.style.display === '') {
                    button.click();
                }
            });

            if ((typeof canvasData !== "boolean")) {
                const fileType = containerElem.getAttribute('data-file-type');
                imageElem.src = canvasData;
                if (fileType === 'image') {
                    containerElem.parentElement.querySelector('.js-protected-file-content').value = canvasData;
                }
            }

            // before submitting it, check if the pdf was uploaded or not
            document.querySelectorAll('.ophco-image-container').forEach(function (element) {
                if ((element.getAttribute('data-file-type') === 'pdf') && (element.parentElement.querySelector('.js-canvas-modified').value === '1')) {
                    // then generate a base64 pdf
                    console.log('generating pdf' + element);
                    element.parentElement.querySelector('.js-protected-file-content').value = generatePdfOutput(element);
                }
            });

            $('#document-create').submit();
        });
    };

    function generatePdfOutput(element, shouldSavePdf = false) {
        let doc;
        let imageObjs = element.querySelectorAll('img');

        imageObjs.forEach(function (imageObj, index) {
            let aspectRatio = (imageObj.height / imageObj.width);
            let orientation = 'portrait';
            if (imageObj.height < imageObj.width) {
                orientation = 'landscape';
            }
            if (index === 0) {
                doc = new jsPDF({ 'orientation': orientation });
            }
            else {
                doc.addPage('a4', orientation);
                doc.setPage(index + 1);
            }
            doc.addImage(
                imageObj.src,
                "PNG",
                0,
                0,
                doc.internal.pageSize.getWidth(),
                doc.internal.pageSize.getWidth() * aspectRatio,
                `page-${index + 1}`,
                undefined
            );
        });

        if (shouldSavePdf) {
            doc.save('preview.pdf');
        } else {
            return doc.output('datauristring');
        }
    }

    DocumentUploadController.prototype.rotateImage = function (type, event) {
        event.preventDefault();

        let image_id = $('#Element_OphCoDocument_Document_' + type + '_document_id').val();
        const imageEl = $(`#${type}-image-${image_id}`);
        let image_src = imageEl.attr('src');
        image_src = image_src.split('?');
        let src = image_src[0];
        let currentDegree = 0;
        if (typeof image_src[1] !== 'undefined') {
            // does not exist
            currentDegree = Number(image_src[1].split('=')[1]);
        }

        let degree = (-90 + currentDegree) % 360;
        imageEl.animate({ transform: degree }, {
            step: function () {
                $(this).attr({
                    'src': src + '?rotate=' + degree
                });
            }
        });

        $(`#${type}_document_rotate`).val(degree);
    };

    DocumentUploadController.prototype.finalizeImage = function (event) {
        let controller = this;
        event.preventDefault();
        const element = event.target;
        const uploadOrientationElem = element.parentElement.parentElement.parentElement;
        const side = uploadOrientationElem.parentElement.parentElement.dataset.side;
        const summarySideSelector = document.querySelector(`${controller.options.documentUploadSummarySelector} [data-side=${side}]`);
        const div = uploadOrientationElem.parentElement;
        const imageElem = uploadOrientationElem.getElementsByTagName('img')[0];
        uploadOrientationElem.remove();
        div.appendChild(imageElem);

        summarySideSelector.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector}`).forEach(showElement);
    };

    DocumentUploadController.prototype.removeDocument = function (side) {
        let controller = this;
        let $td = $("#Document_" + side + "_document_row_id").closest('td');

        $td.find('.ophco-image-container').remove();
        $('#' + side + '_document_rotate').val(0);
        $td.find(".upload-box").show().find('.js-upload-box-text').text("Click to select file or DROP here");

        const tdEl = $td[0];
        hideElement(tdEl.querySelector('.pdf-actions'));

        const documentSummarySideEl = document.querySelector(`${controller.options.documentUploadSummarySelector} div[data-side=${side}]`);

        if (side === 'single') {
            $(controller.options.uploadModeSelector).attr('disabled', false);
            showElement(document.querySelector('#single_document_uploader'));
            document.querySelector('#document-event').className = "cols-11";
            showElement(document.querySelector('#document-event-info'));

            hideElement(documentSummarySideEl);
            hideElement(document.querySelector('#document_summary'));
        } else {
            let opposite_side = side === 'right' ? 'left' : 'right';
            if ($('#Element_OphCoDocument_Document_' + opposite_side + '_document_id').val() === '') {
                $(controller.options.uploadModeSelector).attr('disabled', false);
                showElement(document.querySelector('#double_document_uploader'));
                showElement(document.querySelector('#document-event-info'));
                document.querySelector('#document-event').className = "cols-11";
                hideElement(document.querySelector('#document_summary'));
            } else {
                showElement(document.querySelector('#double_document_uploader'));
                showElement(document.querySelector(`.js-document-summary-wrapper div[data-side=${opposite_side}]`));
            }

            // hideElement(document.querySelector(`.js-document-summary-wrapper div[data-side=${side}]`));
            hideElement(documentSummarySideEl);
            document.querySelectorAll(`.js-document-summary-wrapper div[data-side=${side}], .js-document-summary-wrapper div[data-side=${opposite_side}]`).forEach(function (element) {
                element.className = 'cols-half';
            });
        }

        let deleted_doc = $td.find('.js-document-id').val();
        let $removed_docs = $(controller.options.removedDocumentsSelector);

        if (typeof $removed_docs.data('documents') === 'undefined') {
            $removed_docs.data('documents', []);
        }
        let documents = $removed_docs.data('documents');
        documents.push(deleted_doc);
        $removed_docs.data('documents', documents);

        $(controller.options.fileInputSelector).val("");
        $td.find('.js-document-id').val("");
        $td.find('.js-canvas-modified').val("");

        showElement(document.querySelector('#accepted-file-types'));
        documentSummarySideEl.querySelectorAll(`${controller.options.documentNameSelector}, ${controller.options.documentSizeSelector}`).forEach(clearElementInnerHTML);

        const annotationEl = document.querySelector(`.js-document-summary-wrapper div[data-side=${side}]`);
        annotationEl.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector},
        ${controller.options.saveAnnotationActionSelector}, ${controller.options.cancelAnnotationActionSelector}`).forEach(hideElement);
        document.querySelector(`#ProtectedFile_${side}_file_content`).value = '';
        controller.clearCanvasData();
    };

    DocumentUploadController.prototype.paste = function (side, files) {
        let controller = this;
        let $input = $("#Document_" + side + "_document_row_id");

        controller.removeDocument(side);
        $input.prop("files", files);
    };

    DocumentUploadController.prototype.setUploadStatusText = function (field, text) {
        let $label = $(field).closest('.upload-box').find('.js-upload-box-text');
        $label.text(text);
    };

    DocumentUploadController.prototype.documentUpload = function (fileUploadObject, isRotationRequired) {
        let controller = this;

        let $td = fileUploadObject.td;
        let value = fileUploadObject.value;
        let file_type = fileUploadObject.fileType;
        let extension = fileUploadObject.extension;
        let fileName = fileUploadObject.fileName;
        let fileSize = fileUploadObject.fileSize;
        let side = fileUploadObject.side;

        let view = controller.generateView($td, value, extension, side, isRotationRequired);
        //clearInputFile(index);

        if (file_type !== "application/pdf") {
            $td.find(".upload-box").after(view);
        }

        $td.find(".upload-box").hide();

        $(controller.options.uploadModeSelector + ":not(:checked").attr('disabled', true);

        const documentSummarySideEl = document.querySelector(`${controller.options.documentUploadSummarySelector} [data-side=${side}]`);
        if (side === 'left') {
            documentSummarySideEl.style.marginLeft = 'auto';
        }
        hideElement(document.querySelector('#document-event-info'));
        showElement(document.querySelector('#document_summary'));
        showElement(documentSummarySideEl);
        documentSummarySideEl.querySelector(controller.options.documentNameSelector).innerHTML = fileName;
        documentSummarySideEl.querySelector(controller.options.documentSizeSelector).innerHTML = fileSize;
        hideElement(document.querySelector('#accepted-file-types'));
        document.querySelector('#document-event').className = "cols-full";
    };

    DocumentUploadController.prototype.generatePDF = function ($td, $div, value, extension, side) {
        let controller = this;
        let thePDF = null;
        let numPages = 0;
        let currPage = 1; //Pages are 1-based not 0-based
        // get base64 data of the uploaded pdf
        let loadingTask = pdfjsLib.getDocument('/file/view/' + value + '/image.' + extension);
        loadingTask.promise.then(function (pdf) {
            thePDF = pdf;
            numPages = pdf.numPages;
            //Start with first page
            pdf.getPage(1).then(handlePages.bind(this, controller, thePDF, numPages, currPage, $div, $td, side));
        }, function (reason) {
        });
    };

    function handlePages(controller, thePDF, numPages, currPage, $div, $td, side, page) {
        let canvas = document.createElement("canvas");
        canvas.id = 'page' + currPage;
        let context = canvas.getContext('2d');
        let viewport = page.getViewport({ scale: 1 });
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        // render the page on the canvas
        let renderTask = page.render({ canvasContext: context, viewport: viewport });
        renderTask.promise.then(function () {
            let dataUrl = canvas.toDataURL('image/jpeg', 1.0);
            let image = document.createElement('img');
            image.src = dataUrl;
            image.setAttribute('class', "page-" + currPage);
            image.setAttribute('width', "100%");
            image.setAttribute('height', "auto");
            image.style.display = 'none';
            $div.appendChild(image);

            currPage++;
            if (thePDF !== null && currPage <= numPages) {
                thePDF.getPage(currPage).then(handlePages.bind(this, controller, thePDF, numPages, currPage, $div, $td, side));
            } else {
                $div.setAttribute('data-image-el', '.page-1');
                $div.setAttribute('data-file-type', 'pdf');
                $div.setAttribute('data-current-page', '1');
                $div.setAttribute('data-total-pages', numPages);

                $div.querySelector('.page-1').style.display = '';
                $td.find(".upload-box").after($div);

                const pdfActionsEl = document.querySelector(`.js-document-upload-wrapper td[data-side=${side}] .pdf-actions`);
                showElement(pdfActionsEl);
                pdfActionsEl.querySelector('label').innerText = `Page: 1/${numPages}`;

                const summarySideSelector = document.querySelector(`${controller.options.documentUploadSummarySelector} [data-side=${side}]`);
                summarySideSelector.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector}`).forEach(showElement);
            }
        });
    }

    DocumentUploadController.prototype.generateView = function ($td, value, extension, side, isRotationRequired = false) {
        let controller = this;
        let result;


        let $div = document.createElement('div');
        $div.id = 'ophco-image-container-' + value;
        $div.classList.add('ophco-image-container');

        let $img;

        switch (extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $div.setAttribute('data-image-el', '.image-upload-del');
                $div.setAttribute('data-file-type', 'image');
                $div.setAttribute('data-file-format', extension === 'png' ? 'png' : 'jpeg');

                $img = document.createElement('img');
                $img.id = `${side}-image-${value}`;
                $img.classList.add('image-upload-del');
                $img.src = `/file/view/${value}/image.${extension}`;
                $img.style.width = "100%";
                $img.style.height = "auto";

                if (isRotationRequired) {
                    const template = document.querySelector('#oe-upload-orientation-template');
                    // Clone the template
                    const clone = template.content.cloneNode(true);
                    const adjustmentEl = clone.querySelector('.adjustments');
                    adjustmentEl.parentNode.insertBefore($img, adjustmentEl.nextSibling);
                    $div.appendChild(clone);
                    $div.querySelector(controller.options.rotateImageSelector).addEventListener('click', controller.rotateImage.bind(controller, side));
                    $div.querySelector(controller.options.finalizeImageSelector).addEventListener('click', controller.finalizeImage.bind(controller));
                } else {
                    $div.appendChild($img);
                    const summarySideSelector = document.querySelector(`${controller.options.documentUploadSummarySelector} [data-side=${side}]`);
                    summarySideSelector.querySelectorAll(`${controller.options.annotateImageActionSelector}, ${controller.options.downloadImageActionSelector}`).forEach(showElement);
                }

                result = $div;
                break;
            case 'pdf':
                $div.setAttribute('data-file-format', 'jpeg');
                result = controller.generatePDF($td, $div, value, extension, side);
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

    DocumentUploadController.prototype.validateFile = function ($input) {
        let valid = true;
        if (typeof FileReader !== "undefined") {

            var input_selector = $input.attr('id');
            var file = document.getElementById(input_selector).files[0];
            if (!file) {
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

                if (file.name.length > window.max_document_name_length) {
                    new OpenEyes.UI.Dialog.Alert({
                        content: 'The file you tried to upload exceeds the maximum allowed document name length, which is ' + window.max_document_name_length + ' characters'
                    }).open();

                    valid = false;
                }

                var mimeType = file.type;

                if (mimeType === '' || mimeType === undefined) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var dataURL = event.target.result;
                        mimeType = dataURL.split(",")[0].split(":")[1].split(";")[0];
                        if (!this.validateFileType(mimeType)) {
                            valid = false;
                        }
                    };
                    reader.readAsDataURL(file);
                } else if (!this.validateFileType(mimeType)) {
                    valid = false;
                }
            }

        }

        return valid;
    };

    DocumentUploadController.prototype.validateFileType = function (mimeType) {
        if (window.allowed_file_types.indexOf(mimeType) === -1) {
            new OpenEyes.UI.Dialog.Alert({
                content: 'Only the following file types can be uploaded: ' + window.allowed_file_types.join(', ') +
                    '\n\nFor reference, the type of the file you tried to upload is: ' + mimeType
            }).open();

            return false;
        }
        else {
            return true;
        }
    };

    DocumentUploadController.prototype.clearCanvasData = function () {
        const annotateEl = document.querySelector('#js-annotate-image');

        if (this.imageAnnotators && this.imageAnnotators[annotateEl.dataset.imageAnnotatorId]) {
            const imageAnnotator = this.imageAnnotators[annotateEl.dataset.imageAnnotatorId];
            imageAnnotator.clearCanvas();
            this.imageAnnotators.splice(+annotateEl.dataset.imageAnnotatorId, 1);
        }

        annotateEl.style.display = 'none';
        annotateEl.innerHTML = '';

        hideElement(document.querySelector('#pdf-message'));
    };

    function clearElementInnerHTML(element) {
        element.innerHTML = '';
    }

    function showElement(element) {
        element.style.display = '';
    }

    function hideElement(element) {
        element.style.display = 'none';
    }

    function downloadImage(data, filename = 'untitled.png') {
        let a = document.createElement('a');
        a.href = data;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
    }

    exports.DocumentUploadController = DocumentUploadController;

})(OpenEyes.OphCoDocument);

$(document).ready(function () {
    "use strict";

    autosize($('.autosize'));

    var uploader = new OpenEyes.OphCoDocument.DocumentUploadController();
    $('.js-document-upload-wrapper').data('controller', uploader);

    $(this).on('click', '#et_print', function (e) {
        e.preventDefault();
        printEvent(null);
    });

});
