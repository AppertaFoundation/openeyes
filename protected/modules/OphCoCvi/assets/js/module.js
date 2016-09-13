
/* Module-specific javascript can be placed here */

$(document).ready(function() {

    if (typeof(cvi_do_print) !== 'undefined' && cvi_do_print == 1) {
        doPrint();
    }

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
        doPrint(e);
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

    // if a disorder is a main cause, it should be marked as "affected"
    $(document).on('change', '.disorder-main-cause', function(e) {
        if (e.target.checked) {
            $(this).closest('.column').find('.affected-selector[value="1"]').prop('checked', 'checked');
        }
    });

    $(document).on('change', 'input[name="OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature[is_patient]"][type="radio"]',function(e) {
        if ($(e.target).val() === '1') {
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').prop('disabled', 'disabled').closest('.field-row').hide();
        } else {
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').removeProp('disabled').closest('.field-row').show();
        }

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

function doPrint(e) {
    printIFrameUrl(cvi_print_url, null);

    iframeId = 'print_content_iframe',
        $iframe = $('iframe#print_content_iframe');

    $iframe.load(function() {
        if (e != undefined) {
            enableButtons();
            e.preventDefault();
        }

        try{
            var PDF = document.getElementById(iframeId);
            PDF.focus();
            PDF.contentWindow.print();
        } catch (e) {
            alert("Exception thrown: " + e);
        }
    });
}