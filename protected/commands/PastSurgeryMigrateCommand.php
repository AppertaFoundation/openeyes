<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class PastSurgeryMigrateCommand extends PatientLevelMigration
{
    protected $event_type_cls = 'OphCiExamination';
    // Original table is renamed to this during the module database migration
    protected static $archived_entry_table = 'archive_previous_operation';
    protected static $element_class = 'OEModule\OphCiExamination\models\PastSurgery';
    protected static $element_entry_attribute = 'operations';
    protected static $entry_class = 'OEModule\OphCiExamination\models\PastSurgery_Operation';
    protected static $entry_attributes = array(
        'date',
        'side_id',
        'operation'
    );

    public function getHelp()
    {
        return "Migrates the original Previous Operation records to an examination event in change tracker episode\n";
    }

}
