
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	$('#search_button').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/Genetics/search/index?gene-id='+$('#gene-id').val()+'&disorder-id='+$('#savedDiagnosis').val();
	});

	$('tr.clickable').click(function(e) {
		e.preventDefault();
		window.location.href = $(this).data('uri');
	});
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
