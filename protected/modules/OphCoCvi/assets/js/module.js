
/* Module-specific javascript can be placed here */

$(document).ready(function() {

    if (typeof(cvi_do_print) !== 'undefined' && cvi_do_print == 1) {
        doPrint();
    }

  $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').hide();

  $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_id').change(function(){
    var label_name = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_id').find(":selected").text();
    if (label_name.toLowerCase().indexOf("other") >= 0) {
      $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').show();
    } else {
      $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_language_text').hide();
    }
  });


  $('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();

	$('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').change(function(){
		var label_name = $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_preferred_info_fmt_id').find(":selected").text();
		if (label_name.toLowerCase().indexOf("email") >= 0) {
			$('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').show();
		} else {
			$('#div_OEModule_OphCoCvi_models_Element_OphCoCvi_ClericalInfo_info_email').hide();
		}
	});

    $(this).on('click','#et_cancel',function(e) {
            if (m = window.location.href.match(/\/update\/[0-9]+/)) {
                    window.location.href = window.location.href.replace('/update/','/view/');
            } else {
                    window.location.href = baseUrl+'/patient/summary/'+OE_patient_id;
            }
            e.preventDefault();
    });

    handleButton($('#capture-patient-signature'), function(e) {

        $('#capture-patient-signature-instructions').show();
        $('#capture-patient-signature').parent().hide();
        // I honestly don't know wny this works, but it works and we have a demo to do:
        // FIXME: this seems ridiculous
        setTimeout(function() {e.preventDefault(); enableButtons();}, 100);
        return false;
    });

    $('#remove-patient-signature').on('click', function(e) {

        e.preventDefault();

        var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
            title: "Remove Patient Signature",
            'content': 'Are you sure you want to delete the current Patient Signature?',
            'okButton': 'Remove'
        });
        confirmDialog.open();
        // suppress default ok behaviour
        confirmDialog.content.off('click', '.ok');
        // manage form submission and response
        confirmDialog.content.on('click', '.ok', function() {
            $('#remove-consent-signature-form').submit();
        });

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

    $(this).on('click','#et_print',function(e) {
        doPrint(e);
    });
    
    $(this).on('click','#et_print_labels',function(e) {

        var table = generateTable();
        var dialogContainer = '<div id="label-print-dialog">' + generateLabelInput() + table.outerHTML +'</div>';
       
        var labelDialog = new OpenEyes.UI.Dialog({
            content: dialogContainer,
            title: "Print Labels",
            autoOpen: false,
            onClose: function() { enableButtons(); },
            buttons: {
                "Close" : {
                    text: "Close",
                    id: "my-button-id",
                    click: function(){
                        $( this ).dialog( "close" );
                        enableButtons();
                    }   
                },
                "Print":{
                    text: "Print",
                    id: "my-button-id",
                    click: function(){
                        var num = $('#firstLabel').val();
                        
                        if(num > 0){
                            var data = {'firstLabel':num};
                            printIFrameUrl(label_print_url, data);

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
                        } else {
                            new OpenEyes.UI.Dialog.Alert({
                               content: 'The value cannot be less than 1'
                            }).open();
                        }
                        
                    }   
                }
               
            }
        });
        
        labelDialog.open();

        $('#printLabelPanel tr td').click(function(){
            $('#printLabelPanel tr td').removeClass('active-panel');
            $('#printLabelPanel tr td').text('Label');
            
            var tdID = $(this).attr('id').match(/\d+/);
            $('input#firstLabel').val(tdID);
            for(var i = 1; i<= tdID; i++ ){
                if(i == tdID){
                   $('#labelPanel_'+i).text('First empty label');
                } else {
                   $('#labelPanel_'+i).addClass('active-panel');
                    $('#labelPanel_'+i).text('Used label'); 
                }
            } 
        });
        $('#firstLabel').keyup(function() {
            $('#printLabelPanel tr td').removeClass('active-panel');
            $('#printLabelPanel tr td').text('Label');
            
            var tdID = $(this).val();
            for(var i = 1; i <= tdID; i++ ){
                if(i == tdID){
                   $('#labelPanel_'+i).text('First empty label');
                } else {
                    $('#labelPanel_'+i).addClass('active-panel');
                    $('#labelPanel_'+i).text('Used label');
                }
            } 
        });
     
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
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').prop('disabled', 'disabled').closest('.data-group').hide();
        } else {
            $('#OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_representative_name').removeProp('disabled').closest('.data-group').show();
        }

    });

    autosize($('.autosize'));

    if($('#createdby_auto_complete').length > 0){
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#createdby_auto_complete'),
            url: '/user/autocomplete',
            onSelect: function(){
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                addCreatedByToList(AutoCompleteResponse);
                return false;
            }
        });
    }

    if($('#consultant_auto_complete').length > 0){
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#consultant_auto_complete'),
            url: '/user/autocomplete',
            onSelect: function(){
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                addConsultantToList(AutoCompleteResponse);
                return false;
            }
        });
    }
    if($('#oe-autocompletesearch').length > 0){
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#oe-autocompletesearch'),
            url: '/OphCoCvi/localAuthority/autocomplete',
            onSelect: function(){
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                updateLAFields(AutoCompleteResponse);
            }
        });
    }
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

function generateLabelInput(){
    var inputField = '<div class="large-8 column">'
        +'<label>Please enter the start label number:</label>'
    +'</div>'
    +'<div class="large-4 column">'
        +'<input type="text" name="firstLabel" id="firstLabel" value=""/>'
    +'</div>';
    
    return inputField;
}

function generateTable(){
    var tbl     = document.createElement("table");
    tbl.setAttribute("id", "printLabelPanel");
    var tblBody = document.createElement("tbody");
    var counter = 1;
    for (var j = 1; j <= 8; j++) {
        // table row creation
        var row = document.createElement("tr");

        for (var i = 1; i <= 3; i++) {
            
            var cell = document.createElement("td");   
            cell.setAttribute("id", 'labelPanel_'+counter);
            var cellText = document.createTextNode("Label"); 

            cell.appendChild(cellText);
            row.appendChild(cell);
            counter++;
        }

        //row added to end of table body
        tblBody.appendChild(row);
    }

    
    tbl.appendChild(tblBody);
    
    return tbl;
}