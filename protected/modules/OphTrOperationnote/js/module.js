
function callbackAddProcedure(procedure) {
	if (procedure == 'Vitrectomy') {
		addOptionalElement('ElementVitrectomy');
	}
}

function callbackRemoveProcedure(procedure) {
	if (procedure == 'Vitrectomy') {
		removeOptionalElement('ElementVitrectomy');
	}
}
