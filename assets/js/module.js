
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	
	$('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();
	$('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').change(function(){
		var label_name = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').find(":selected").text();
		if (label_name.toLowerCase().indexOf("email") >= 0) {
			$('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').show();
		} else {
			$('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();
		}
	});

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

    handleButton($('#capture-patient-signature'), function(e) {

        $('#capture-patient-signature-instructions').show();
        $('#capture-patient-signature').parent().hide();
        // I honestly don't know wny this works, but it works and we have a demo to do:
        // FIXME: this seems ridiculous
        setTimeout(function() {e.preventDefault(); enableButtons();}, 100);
        return false;

    });
    
    handleButton( $('#print-for-signature'),function(e) {
        var data = {'firstPage':'1'};
	    printIFrameUrl($(e.target).data('print-url'), data);
        
        iframeId = 'print_content_iframe',
        $iframe = $('iframe#print_content_iframe');
        
        $iframe.load(function() {
    		enableButtons();
    		e.preventDefault();            
           
            try{
                var PDF = document.getElementById(iframeId);
                PDF.focus();
                PDF.contentWindow.print();
            } catch (e) {
                alert("Exception thrown: " + e);
            }                                    
        });
    });
    
    handleButton($('#et_print'),function(e) {
        
	printIFrameUrl(OE_print_url, null);
		
        iframeId = 'print_content_iframe',
        $iframe = $('iframe#print_content_iframe');
        
        $iframe.load(function() {
    		enableButtons();
    		e.preventDefault();            
           
            try{
                var PDF = document.getElementById(iframeId);
                PDF.focus();
                PDF.contentWindow.print();
            } catch (e) {
                alert("Exception thrown: " + e);
            }                                    
        });
    });

    handleButton($('#la-search-toggle'), function(e) {
        e.preventDefault();
        $('#local_authority_search_wrapper').show();
        setTimeout(function() {$(e.target).blur(); enableButtons(); $(e.target).addClass('disabled'); }, 100);
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

    handleButton($('#et_sign_cvi'),function (e) {
        e.preventDefault();
        $('#signature_error').remove();
        urldata = window.location.href.split('/');
        eventId = urldata[urldata.length-1];
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/OphCoCvi/default/signCVI/'+eventId,
            'dataType': 'text',
            'data': {
                'signaturePin': $('#signature_pin').val(),
                'YII_CSRF_TOKEN': $('#YII_CSRF_TOKEN').val()
            },
            'success': function (result) {
                if(result == 0) {
                    $('#div_signature_pin').after('<div id="signature_error" class="row field-row"><div class="large-6 column"></div><div class="large-6 column"><label>ERROR: You entered an invalid PIN number, please try again</label></div></div>');
                }else
                {
                    $('#div_signature_pin').html('<div class="large-12 column"><label>'+result+'</label></div>');
                }
                enableButtons();
            }
        });
    });
});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
            // handle event
    }
}

function updateLAFields(item) {
    if (item.service) {
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_name').val(item.service.name);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_address').val(item.service.address);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_telephone').val(item.service.telephone);
    } else if (item.body) {
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_name').val(item.body.name);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_address').val(item.body.address);
        $('#OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_la_telephone').val(item.body.telephone);
    }
    $('#local_authority_search_wrapper').hide();
    $('#la-search-toggle').removeClass('disabled');
}