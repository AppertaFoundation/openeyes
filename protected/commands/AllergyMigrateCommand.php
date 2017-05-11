\<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class AllergyMigrateCommand extends PatientLevelMigration
{
    protected $event_type = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
        protected static $archived_entry_table = 'archive_patient_allergy_assignment';
    // column on patient record indicating no entries have been explicitly recorded
    protected static $archived_no_values_col = 'archive_no_allergies_date';
    protected static $no_values_col = 'no_allergies_date';
    protected static $element_class = 'OEModule\OphCiExamination\models\Allergies';
    protected static $entry_class = 'OEModule\OphCiExamination\models\AllergyEntry';
    protected static $entry_attributes = array(
        'allergy_id',
        'other',
        'comments'
    );

    public function getHelp()
    {
        return "Migrates the original Allergy records to an examination event in change tracker episode\n";
    }

}