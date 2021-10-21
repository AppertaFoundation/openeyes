<?php

class m210505_055201_add_dispense_condition_and_location_institution_mapping_tables extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable(
            'ophdrprescription_dispense_condition_institution',
            array(
                'id' => 'pk',
                'dispense_condition_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(11) unsigned NOT NULL',
            ),
            true
        );

        $this->createOETable(
            'ophdrprescription_dispense_location_institution',
            array(
                'id' => 'pk',
                'dispense_location_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(11) unsigned NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_condition_institution_dc_fk',
            'ophdrprescription_dispense_condition_institution',
            'dispense_condition_id',
            'ophdrprescription_dispense_condition',
            'id'
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_condition_institution_i_fk',
            'ophdrprescription_dispense_condition_institution',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_location_institution_dl_fk',
            'ophdrprescription_dispense_location_institution',
            'dispense_location_id',
            'ophdrprescription_dispense_location',
            'id'
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_location_institution_i_fk',
            'ophdrprescription_dispense_location_institution',
            'institution_id',
            'institution',
            'id'
        );

        // Only create mappings for active dispense conditions & locations.
        $all_dc_mappings = $this->dbConnection->createCommand()
            ->select('d.id AS dispense_condition_id, i.id AS institution_id')
            ->from('ophdrprescription_dispense_condition d')
            ->crossJoin('institution i')
            ->where('d.active = 1')
            ->queryAll();

        $all_dl_mappings = $this->dbConnection->createCommand()
            ->select('d.id AS dispense_location_id, i.id AS institution_id')
            ->from('ophdrprescription_dispense_location d')
            ->crossJoin('institution i')
            ->where('d.active = 1')
            ->queryAll();

        if(!empty($all_dc_mappings)){
            $this->insertMultiple(
                'ophdrprescription_dispense_condition_institution',
                $all_dc_mappings
            );
        }

        if(!empty($all_dl_mappings)){
            $this->insertMultiple(
                'ophdrprescription_dispense_location_institution',
                $all_dl_mappings
            );
        }

        $all_dc_assignment_mappings = $this->dbConnection->createCommand()
            ->select('di.id AS dispense_condition_institution_id, li.id AS dispense_location_institution_id')
            ->from('ophdrprescription_dispense_condition_assignment da')
            ->join('ophdrprescription_dispense_condition d', 'd.id = da.dispense_condition_id')
            ->join('ophdrprescription_dispense_condition_institution di', 'di.dispense_condition_id = d.id')
            ->join('ophdrprescription_dispense_location l', 'l.id = da.dispense_location_id')
            ->join('ophdrprescription_dispense_location_institution li', 'li.dispense_location_id = l.id')
            ->queryAll();

        $this->dropOEColumn(
            'ophdrprescription_dispense_condition',
            'active',
            true
        );
        $this->dropOEColumn(
            'ophdrprescription_dispense_location',
            'active',
            true
        );

        $this->delete(
            'ophdrprescription_dispense_condition_assignment'
        );
        $this->dropForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_condition_id',
            'ophdrprescription_dispense_condition_assignment'
        );
        $this->dropForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_location_id',
            'ophdrprescription_dispense_condition_assignment'
        );

        $this->renameOEColumn(
            'ophdrprescription_dispense_condition_assignment',
            'dispense_condition_id',
            'dispense_condition_institution_id',
            true
        );

        $this->renameOEColumn(
            'ophdrprescription_dispense_condition_assignment',
            'dispense_location_id',
            'dispense_location_institution_id',
            true
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_condition_assignment_ci_fk',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_condition_institution_id',
            'ophdrprescription_dispense_condition_institution',
            'id'
        );

        $this->addForeignKey(
            'ophdrprescription_dispense_condition_assignment_li_fk',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_location_institution_id',
            'ophdrprescription_dispense_location_institution',
            'id'
        );

        if(!empty($all_dc_assignment_mappings)){
            $this->insertMultiple(
                'ophdrprescription_dispense_condition_assignment',
                $all_dc_assignment_mappings
            );
        }
    }

    public function safeDown()
    {
        // Get all unique mappings in a flat structure.
        $all_dc_assignment_mappings = $this->dbConnection->createCommand()
            ->select('di.dispense_condition_id, li.dispense_location_id')
            ->from('ophdrprescription_dispense_condition_assignment da')
            ->join('ophdrprescription_dispense_condition_institution di', 'di.id = da.dispense_condition_institution_id')
            ->join('ophdrprescription_dispense_location_institution li', 'li.id = da.dispense_location_institution_id')
            ->group('di.dispense_condition_id, li.dispense_location_id')
            ->queryAll();

        // Delete the existing mappings. These will be replaced with the flattened mappings.
        $this->delete(
            'ophdrprescription_dispense_condition_assignment'
        );

        $this->dropForeignKey(
            'ophdrprescription_dispense_condition_assignment_ci_fk',
            'ophdrprescription_dispense_condition_assignment',
        );

        $this->dropForeignKey(
            'ophdrprescription_dispense_condition_assignment_li_fk',
            'ophdrprescription_dispense_condition_assignment',
        );

        $this->renameOEColumn(
            'ophdrprescription_dispense_condition_assignment',
            'dispense_condition_institution_id',
            'dispense_condition_id',
            true
        );

        $this->renameOEColumn(
            'ophdrprescription_dispense_condition_assignment',
            'dispense_location_institution_id',
            'dispense_location_id',
            true
        );

        $this->addForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_condition_id',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_condition_id',
            'ophdrprescription_dispense_condition',
            'id'
        );
        $this->addForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_location_id',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_location_id',
            'ophdrprescription_dispense_location',
            'id'
        );

        if(!empty($all_dc_assignment_mappings)){
            // Re-insert the flattened mappings.
            $this->insertMultiple(
                'ophdrprescription_dispense_condition_assignment',
                $all_dc_assignment_mappings
            );
        }

        $active_dc_ids = $this->dbConnection->createCommand()
            ->select('DISTINCT dispense_condition_id')
            ->from('ophdrprescription_dispense_condition_institution')
            ->queryColumn();

        $active_dl_ids = $this->dbConnection->createCommand()
            ->select('DISTINCT dispense_location_id')
            ->from('ophdrprescription_dispense_location_institution')
            ->queryColumn();

        $this->addOEColumn(
            'ophdrprescription_dispense_condition',
            'active',
            'tinyint(1) DEFAULT 1',
            true
        );

        $this->update(
            'ophdrprescription_dispense_condition',
            array('active' => 0),
            'id NOT IN (' . implode(', ', $active_dc_ids) . ')'
        );

        $this->addOEColumn(
            'ophdrprescription_dispense_location',
            'active',
            'tinyint(1) DEFAULT 1',
            true
        );

        $this->update(
            'ophdrprescription_dispense_location',
            array('active' => 0),
            'id NOT IN (' . implode(', ', $active_dl_ids) . ')'
        );

        $this->dropForeignKey(
            'ophdrprescription_dispense_condition_institution_dc_fk',
            'ophdrprescription_dispense_condition_institution'
        );
        $this->dropForeignKey(
            'ophdrprescription_dispense_condition_institution_i_fk',
            'ophdrprescription_dispense_condition_institution'
        );

        $this->dropForeignKey(
            'ophdrprescription_dispense_location_institution_dl_fk',
            'ophdrprescription_dispense_location_institution'
        );
        $this->dropForeignKey(
            'ophdrprescription_dispense_location_institution_i_fk',
            'ophdrprescription_dispense_location_institution'
        );

        $this->dropOETable(
            'ophdrprescription_dispense_condition_institution',
            true
        );
        $this->dropOETable(
            'ophdrprescription_dispense_location_institution',
            true
        );
    }
}
