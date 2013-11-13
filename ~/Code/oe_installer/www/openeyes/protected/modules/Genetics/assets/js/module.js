
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	$('#search_button').click(function(e) {
		window.location.href = baseUrl+'/Genetics/default/pedigrees?family-id='+$('#family-id').val()+'&gene-id='+$('#gene-id').val()+'&consanguineous='+$('#consanguineous').val()+'&disorder-id='+$('#savedDiagnosis').val();
		e.preventDefault();
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
