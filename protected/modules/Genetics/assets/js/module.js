
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	$('#search_pedigrees').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/Genetics/default/pedigrees?&gene-id='+$('#gene-id').val()+'&consanguineous='+$('#consanguineous').val()+'&disorder-id='+$('#savedDiagnosis').val();
	});

	$('#search_pedigree_family_id').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/Genetics/default/pedigrees?family-id='+$('#family-id').val();
	});

	$('#search_genetics_patients').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/Genetics/search/geneticPatients?first-name='+$('#first-name').val()+'&last-name='+$('#last-name').val()+'&dob='+$('#dob').val()+'&comments='+$('#comments').val()+'&disorder-id='+$('#savedDiagnosis').val()+'&part-first-name='+$('#part_first_name').is(':checked')+'&part-last-name='+$('#part_last_name').is(':checked')+'&search=search';
	});

	$('#add_patient_pedigree').click(function(e) {

		var pedigreeId = '';
		if($(this).data('pedigreeId')){
			pedigreeId = $(this).data('pedigreeId');
		}

		var patientId = '';
		if($(this).data('patientId')){
			patientId = $(this).data('patientId');
		}

		e.preventDefault();
		window.location.href = baseUrl+'/Genetics/default/addPatientToPedigree/?patient='+patientId+'&pedigree='+pedigreeId
	});

	$('tr.clickable').click(function(e) {
		e.preventDefault();
		window.location.href = $(this).data('uri');
	});

	Genetics_patient_hovers();
});

function Genetics_load_pedigrees()
{
	$.ajax({
		type: 'GET',
		url: baseUrl+'/Genetics/default/pedigrees',
		success: function(html) {
			$('#pedigree_data').html(html);
		}
	});
}

function Genetics_patient_hovers()
{
	var offsetY = 28;
	var offsetX = 10;
	var tipWidth = 0;

	$('tr.hover').hover(function(e) {
		var titleText = $(this).data('hover');

		var tooltip = $('<div class="tooltip alerts"></div>').appendTo('body');

		$(this).data({
			'tipText': titleText,
			'tooltip': tooltip
		}).removeAttr('hover');

		tooltip.text(' ' + titleText);

		tipWidth = tooltip.outerWidth();
		tooltip.css('top', (e.pageY - offsetY) + 'px').css('left', (e.pageX - (tipWidth + offsetX)) + 'px').fadeIn('fast');
	},function(e){
		$(this).data('hover',$(this).data('tipText'));
		$(this).data('tooltip').remove();
	}).mousemove(function(e) {
		$(this).data('tooltip')
			.css('top', (e.pageY - offsetY) + 'px')
			.css('left', (e.pageX - (tipWidth + offsetX)) + 'px');
	});
}
