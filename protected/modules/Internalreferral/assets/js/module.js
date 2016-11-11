
/* Module-specific javascript can be placed here */

$(document).ready(function() {
        handleButton($('#et_save'),function() {});
	
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
        
        $('#event-content').on('click', '#external-referral-button a', function(e){
            var link = $(this).attr('href');
            e.preventDefault();
            createNewWindow(link);

            // get new link as we need new message_id to re-open the window
            $.get( "/Internalreferral/default/getIntegratedServiceUrlForEvent/type/create/patient_id/" + OE_patient_id + "/event_id/" + OE_event_id, function( data ) {
                var json = JSON.parse(data);
                $('#external-referral-button').attr('href', json.link);
            });
        });

        $('#event-content').on('click', '.windip-help', function(e){
            e.preventDefault();
            var html = '';
            
            if( isIE() ){
                html = 'Please contact support.';
            } else {
                html = 'To access WinDIP you will need to Install the IETAB browser extension - please contact support.';
            }
            new OpenEyes.UI.Dialog({
                title: 'Problems reaching WinDIP ?',
                content: html,
                dialogClass: 'dialog event'
            }).open();
            
        });

});

/**
 * detect IE up to 11 - well, EDGE could be the 12
 * returns version of IE or false, if browser is not Internet Explorer
 */
function isIE(){
    
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }
}

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
        // handle event
    }
}

function createNewWindow(link){
    OpenEyes.UI.Window.createNewWindow(link, 'Internalreferralintegration',
        function(popup) {
                popup.focus();
                $('#external-referral-status').removeClass('hidden');
        },
        function(){ 
              $('#external-referral-popup-blocked').removeClass('hidden');
        }
    );
}
