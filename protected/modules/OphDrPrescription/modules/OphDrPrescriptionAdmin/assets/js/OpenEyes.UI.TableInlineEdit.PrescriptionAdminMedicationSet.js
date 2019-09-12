class PrescriptionAdminMedicationSet extends OpenEyes.TableInlineEdit
{
    constructor() {
        super();
    }

    showEditControls($tr, $row) {
        super.showEditControls($tr, $row);
        console.log("extended showEditControls");
    }

    hideEditControls($tr, $row) {
        super.hideEditControls($tr, $row);
        console.log("extended hideEditControls");
    }
}
OpenEyes.PrescriptionAdminMedicationSet = PrescriptionAdminMedicationSet;