
/* Module-specific javascript can be placed here */

$(document).ready(function() {
			handleButton($('#et_save'),function() {
					});
	
	handleButton($('#et_cancel'),function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_deleteevent'));

	handleButton($('#et_canceldelete'));

	handleButton($('#et_print'),function(e) {
		printIFrameUrl(OE_print_url, null);
		enableButtons();
		e.preventDefault();
	});

    var toolTip = new OpenEyes.UI.Tooltip({
        offset: {
            x: 10,
            y: 10
        },
        viewPortOffset: {
            x: 0,
            y: 32 // height of sticky footer
        }
    });
    $(this).on('mouseover', '.has-tooltip', function() {
        if ($(this).data('tooltip').length) {
            toolTip.setContent($(this).data('tooltip'));
            var offsets = $(this).offset();
            toolTip.show(offsets.left, offsets.top);
        }
    }).mouseout(function (e) {
        toolTip.hide();
    });

    $(this).on('click', '#add-message-comment', function() {
        $('#new-comment-form').toggle();
        $('#add-comment-button-container').toggle();
    });

    $(this).on('click', '#new-comment-cancel', function(e) {
        e.preventDefault();
        $('#new-comment-form').toggle();
        $('#add-comment-button-container').toggle();
    });
});

