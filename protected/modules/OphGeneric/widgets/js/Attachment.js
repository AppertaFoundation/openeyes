let bigImageSize = 400;
let currentDialog = null;
let DELAY = 400, clicks = 0, timer = null;
let idSelected = null;

function createSingleView(id, mime_type) {
    let result = '';
    let src = '/Api/attachmentDisplay/view/id/' + id + '?attachment=blob_data&mime=' + mime_type;
    switch (mime_type) {
        case 'image/jpg':
        case 'image/jpeg':
        case 'image/png':
        case 'image/gif':
            result = '<img src="' + src + '"style="width: 100%;">';
            break;
        case 'application/pdf':
            result =
                '<div class="attachment-container-popup">' +
                '<iframe id="pdf-viewer" height="800" width="100%"  type="application/pdf" />' +
                '</div>';
            fetch(src).then((response) => {
                response.blob().then((blob) => {
                    let url = URL.createObjectURL(blob);
                    document.getElementById('pdf-viewer').src = `${OE_core_asset_path}/components/pdfjs/web/viewer.html?file=${url}`;
                });
            });
            break;
        case 'mp4':
        case 'ogg':
        case 'mov':
        case 'quicktime':
            //     result = controller.createOPHCOVideoContainer(res, value, extension, index);
            break;
    }

    return result;
}

// return a div with the close button for the attachment container
function createCloseAttachmentContainer() {
    let close_button =
        '<div align="right">' +
        '<a class="close-attachment-container">' +
        '<i class="oe-i remove-circle small"></i>' +
        '</a>' +
        '</div>';
    return close_button;
}

// create contents for OE UI dialog for a grouped attachment view
function createGroupView(attachments) {
    let ret = '<div class="group flex-layout flex-left break">';
    for (let i = 0; i < attachments.length; i++) {
        let titleShort = attachments[i].title_short;
        let titleFull = attachments[i].title_full;
        let selectedClass = '';
        if (attachments[i].id == idSelected) {
            selectedClass = 'selected';
        }
        ret += '<div class="image-hover" style="width:80px; font-size:70%">' +
            '<img class="js-small-thumbnail-attachment js-attachment-group ' + selectedClass + ' "' +
            'src="/Api/attachmentDisplay/view/id/' + attachments[i].id + '?attachment=thumbnail_small_blob&mime=' + attachments[i].mime + '"' +
            'style="padding:2px"' +
            'data-group_id=' + attachments[i].group_id + ' ' +
            'width="80px" height="80px" ' +
            'data-full-title=' + titleFull + ' ' +
            'data-mime=' + attachments[i].mime + ' ' +
            'data-id=' + attachments[i].id + '>' +
            '<div class="cols-full" style="text-align: center;">' + titleShort + '</div> ' +
            '</div>';
    }
    ret += '</div>';
    return ret;
}

// create a new OE UI dialog with given content and title
function createDialog(content, title = '', width = '1000px', height = 'auto') {
    // close the current dialog
    if (typeof currentDialog !== "undefined" && currentDialog) {
        currentDialog.close();
    }
    // create a new dialog with given content and title
    currentDialog = new OpenEyes.UI.Dialog({
        content: content,
        width: width,
        height: height,
        position: {my: "center top", at: "center top+10"},
        autoOpen: true,
        modal: false,
        popupClass: 'oe-popup attachment-popup',
        title: title,
    });
    // open the new dialog
    currentDialog.open();

    // TODO: styling not allowed in JS
    $('.oe-popup').css("max-width", $(window).width() * 0.95 + 'px');
    $('.oe-popup .oe-popup-content').css("max-height", $(window).height() * 0.85 + 'px');
}

function toggleAttachmentSelection($previousThumbnail, $currentThumbnail) {
    if ($previousThumbnail) {
        // remove border from previously selected attachment thumbnail
        $previousThumbnail.css('border', '');
        // deselect previously selected attachment thumbnail
        $previousThumbnail.removeClass('selected');
        idSelected = null;
    }

    if ($currentThumbnail) {
        // add border to the newly selected attachment thumbnail
        $currentThumbnail.css({
            "border-color": "#ff392b",
            "border-width": "5px",
            "border-style": "solid"
        });

        // select current attachment
        $currentThumbnail.addClass('selected');
        idSelected = $currentThumbnail.data('id');
    }
}

$(document).ready(function () {


    // open container for the preselected attachment
    if ($('.attachment').find('.js-small-thumbnail-attachment').length == 1 &&
        $('.attachment').find('.js-small-thumbnail-attachment').hasClass('selected') &&
        !$('.attachment').data('is-examination')) {

        // add border to the selected attachment thumbnail
        toggleAttachmentSelection(null, $('.js-small-thumbnail-attachment.selected'));
        // open the container for the selecte attachment thumbnail
        $('.attachment-container').html(
            createCloseAttachmentContainer() +
            createSingleView(
                $('.js-small-thumbnail-attachment.selected').data('id'),
                $('.js-small-thumbnail-attachment.selected').data('mime'),
                'blob_data'
            ));
    }

    // hovering over an attachment
    $(document).on('mouseenter', '.js-small-thumbnail-attachment', (function (e) {
        let $thumbnail = $(this);
        // distance from top and left relative to the window
        let offsetLeft = $thumbnail.offset().left - $(window).scrollLeft();
        let offsetTop = $thumbnail.offset().top - $(window).scrollTop();
        let src = "/Api/attachmentDisplay/view/id/" + $thumbnail.data('id') + "?attachment=thumbnail_medium_blob&mime=" + $thumbnail.data('mime');
        // create the structure to display the medium thumbnail
        let $bigIMG =
            '<div class="js-medium-thumbnail-attachment" style="border-radius: 15px; pointer-events: none; background-color: rgba(256, 256, 256, 0.4); ' +
            'position: fixed; left: ' + offsetLeft + 'px; top: ' + offsetTop + 'px; z-index: 1000">' +
            '<div class="full-title" style="text-align:center;">' + $thumbnail.data('full-title') + '</div>' +
            '<img src="' + src + '" width="' + bigImageSize + '"px;"/>' +
            '</div>';
        $('.open-eyes').prepend($bigIMG);

    }));

    // hovering out of an attachment
    $(document).on('mouseleave', '.js-small-thumbnail-attachment', (function (e) {
        // remove the big image
        $('.open-eyes').find('.js-medium-thumbnail-attachment').remove();
    }));

    // clicking on a single attachment
    $(document).on('click', '.js-small-thumbnail-attachment', (function (e) {
        let $thumbnail = $(this);
        let $oct_row = $thumbnail.closest('.js-oct-row');
        let $attachment_container = $('.attachment-container');

        if ($oct_row.length != 0) {
            $attachment_container = $thumbnail.closest('.js-oct-row').find('.attachment-container');
        }

        if ($thumbnail.hasClass('js-attachment-group')) {
            let group_id = $thumbnail.data('group_id');
            $attachment_container = $('img[class="js-thumbnail-group-attachment"][data-group_id="' + group_id+'"]').closest('.js-oct-row').find('.attachment-container');
        }

        clicks++;
        $thumbnail.mouseleave();

        if (clicks === 1) {
            timer = setTimeout(function () {
                // reset counter
                clicks = 0;

                if ($thumbnail.hasClass('selected')) {
                    // deselect current thumbnail
                    toggleAttachmentSelection($thumbnail, null);
                    // clear container
                    $attachment_container.html('');
                } else {
                    // remove the folder popup
                    $('.oe-popup.attachment-popup').closest('.oe-popup-wrap').remove();

                    // select this thumbnail
                    toggleAttachmentSelection($('.js-small-thumbnail-attachment.selected'), $thumbnail);
                    // set content of the container to THIS at	tachment
                    $attachment_container.html(
                        createCloseAttachmentContainer() +
                        createSingleView(
                            $thumbnail.data('id'),
                            $thumbnail.data('mime'),
                            'blob_data'
                        ));
                }
            }, DELAY);
        } else {
            // prevent single click
            clearTimeout(timer);
            // reset counter
            clicks = 0;

            // open the dialog
            createDialog(
                createSingleView(
                    $thumbnail.data('id'),
                    $thumbnail.data('mime'),
                    'blob_data'
                ),
                $thumbnail.data('full-title'),
                $(window).width() * 0.9 + 'px',
                $(window).height() * 0.7 + 'px'
            );
        }
    }));

    $(document).on('dblclick', '.js-small-thumbnail-attachment', (function (e) {
        // prevent double-click system event (this is handled by single click)
        e.preventDefault();
    }));

    // clicking on a folder (group) of attachments
    $(document).on('click', '.js-thumbnail-group-attachment', function (e) {
        if ($('.oe-popup.attachment-popup').length) {
            $('.oe-popup.attachment-popup').closest('.oe-popup-wrap').show();
        } else {
            createDialog(
                createGroupView($(this).data('group')),
                $(this).data('full-title')
            );

            // apply border to the selected thumbnail after creating the popup dialog
            toggleAttachmentSelection(null, $('.js-small-thumbnail-attachment.selected'));
        }
    });

    $(document).on('click', '.close-attachment-container', function (e) {
        let $oct_row = $(this).closest('.js-oct-row');
        let $thumbnail = $('.js-small-thumbnail-attachment.selected');
        // show hidden attachments
        $('.attachment').find('.element-fields.element-eyes').show();

        if ($oct_row.length !== 0) {
            $thumbnail = $oct_row.find('.js-small-thumbnail-attachment.selected');
        }
        // deselect the current attachment thumbnail
        toggleAttachmentSelection($thumbnail, null);
        // clear container
        $(this).closest('.attachment-container').html('');
    });
});