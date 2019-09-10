class PrescriptionAdminMedicationSet extends OpenEyes.TableInlineEdit
{
    constructor() {
        super();
    }

    showEditControls($tr) {
        super.showEditControls($tr);
        console.log("extended showEditControls");
    }
}
OpenEyes.PrescriptionAdminMedicationSet = PrescriptionAdminMedicationSet;