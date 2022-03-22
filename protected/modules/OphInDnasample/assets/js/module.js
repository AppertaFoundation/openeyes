
/* Module-specific javascript can be placed here */

$(document).ready(function() {

    $('#DiagnosisSelection_principal_diagnosis_0').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });


    $(this).on('click','#et_print',function(e) {
        e.preventDefault();
        printEvent(null);
    });

	$(this).on('click','#et_cancel',function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
		}
		e.preventDefault();
	});

	$('#sample_result').on('click','tr.clickable', function(){
        window.location.href = $(this).data('uri');
    });

  $(this).on('click','#et_canceldelete',function(e) {
		if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/delete/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/summary/'+et_patient_id;
		}
		e.preventDefault();
	});

	$('#search_dna_sample').click(function(e) {

		e.preventDefault();
        var query = $.param( $('#searchform').serializeArray() );
		window.location.href = baseUrl+'/OphInDnasample/search/dnaSample?date-from='+$('#date-from').val()+'&genetics_patient_id=' + $('#genetics_patient_id').val() + '&genetics_pedigree_id=' + $('#genetics_pedigree_id').val()  + '&date-to='+$('#date-to').val()+'&sample-type='+$('#sample-type').val()+'&comment='+$('#comment').val()+'&disorder-id='+$('#savedDiagnosis').val() + '&first_name=' + $('#first_name').val() + '&last_name=' + $('#last_name').val() + '&maiden_name='+ $('#maiden_name').val() + '&hos_num=' + $('#hos_num').val() + '&search=search&sample_id=' + $("#sample_id").val();
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

	(function addNewTest() {

		var html = $('#add-new-test-template').html();

		var dialog = new OpenEyes.UI.Dialog({
			destroyOnClose: false,
			title: 'Add a new test',
			content: html,
			dialogClass: 'dialog event add-event',
			width: 580,
			id: 'add-new-test-dialog'
		});

        $('[id*="et_add_test"]').click(function() {
            dialog.open();
        });
	}());

	$('#Element_OphInDnasample_Sample_type_id').on('change',function(){
		if( $(this).val() == 4 ){ //as 'other'
			$('#div_Element_OphInDnasample_Sample_other_sample_type').slideDown();
		}
		else{
			$('#div_Element_OphInDnasample_Sample_other_sample_type').slideUp();
			$('#Element_OphInDnasample_Sample_other_sample_type').val('');
		}
	});

    //invode datepicker on ajax inputs
    $('.transactions').on('click', '.dna-hasDatepicker', function(){
        $(this).datepicker({
            maxDate: 'today',
            dateFormat: 'd M yy'
        });
        $(this).datepicker("show");
    });

});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		// handle event
	}
}
