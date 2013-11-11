
/* Module-specific javascript can be placed here */

$(document).ready(function() {
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
