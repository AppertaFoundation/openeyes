<?php

class m230926_115820_add_system_setting_pharmacy_worklist_ignore_before_date_and_RBAC extends OEMigration
{
    public function up()
    {
        $this->addSetting(
            'pharmacy_worklists_ignore_before_date',
            'Pharmacy Worklists: Ignore prescriptions before this date',
            'Any prescription events created before the given date will not be shown in the Menu->Pharmacy Worklist" screen. This can be used to exclude prescriptions that occurred before your pharmacy moved to digital dispensing in OpenEyes',
            'Prescription',
            'Text Field',
            '',
            date(Helper::NHS_DATE_FORMAT)
        );

        $this->addRole('Pharmacy');
        $this->addTask('TaskPharmacyDispense');
        $this->addOperation('OprnViewPharmacyWorklist');

        $this->addTaskToRole('TaskPharmacyDispense', 'Pharmacy');
        $this->addOperationToTask('OprnViewPharmacyWorklist', 'TaskPharmacyDispense');
    }

    public function down()
    {
        $this->deleteSetting('pharmacy_worklists_ignore_before_date');

        $this->removeOperation('OprnViewPharmacyWorklist');
        $this->removeOperationFromTask('OprnViewPharmacyWorklist', 'TaskPharmacyDispense');
        $this->removeTask('TaskPharmacyDispense');
        $this->removeTaskFromRole('TaskPharmacyDispense', 'Pharmacy');
        $this->removeRole('Pharmacy');
    }
}
