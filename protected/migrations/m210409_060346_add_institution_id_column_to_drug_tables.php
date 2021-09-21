<?php

class m210409_060346_add_institution_id_column_to_drug_tables extends OEMTMigration
{
    /**
     * @var string[] $tables
     */
    protected function getLevelStructuredTables(): array
    {
        return array(
            'user' => array(),
            'firm' => array(),
            'site' => array(),
            'subspecialty' => array(),
            'specialty' => array(),
            'institution' => array(
                'medication',//For local drugs only
                'ophdrprescription_edit_reasons',
            ),
        );
    }
}
