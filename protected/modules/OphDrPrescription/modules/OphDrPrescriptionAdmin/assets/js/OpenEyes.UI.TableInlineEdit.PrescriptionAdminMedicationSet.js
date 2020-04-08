class PrescriptionAdminMedicationSet extends OpenEyes.TableInlineEdit
{
    constructor() {
        super();
    }

    showEditControls($tr, $row) {
        super.showEditControls($tr, $row);
        togglePrescriptionExtraInputs();
    }

    hideEditControls($tr, $row) {
        super.hideEditControls($tr, $row);
    }
}
OpenEyes.PrescriptionAdminMedicationSet = PrescriptionAdminMedicationSet;