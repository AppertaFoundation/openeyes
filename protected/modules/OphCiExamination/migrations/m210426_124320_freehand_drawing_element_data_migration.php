<?php

class m210426_124320_freehand_drawing_element_data_migration extends \OEMigration
{
    protected static $archive_prefix = 'archive_';

    private $tables = [
        'et_ophciexamination_freedraw_anterior_segment',
        'et_ophciexamination_freedraw_oncology_anterior_segment',
        'et_ophciexamination_freedraw_fundus',
        'et_ophciexamination_freedraw_oncology_fundus',
        'et_ophciexamination_freedraw_peripheral_fundus',
        'et_ophciexamination_freedraw_macula',
        'et_ophciexamination_freedraw_head_neck',
        'et_ophciexamination_freedraw_lids',
        'et_ophciexamination_freedraw_conjunctiva',
        'et_ophciexamination_freedraw_orbit_shell',
        'et_ophciexamination_freedraw_orbit_lacrimal',
        'et_ophciexamination_freedraw_blepharospasm',
        'archive_et_ophciexamination_synoptophore', // already archived
        'et_ophciexamination_eye_movements',
        'et_ophciaccidentandemergency_doctor_ocular_media',
        'et_ophciaccidentandemergency_nurse_ocular_media'
    ];

    public function safeUp()
    {
        foreach ($this->tables as $table) {
            if ($this->dbConnection->schema->getTable($table, true) !== null) {
                $count = $this->getDbConnection()->createCommand()->select('count(*)')->from($table)->queryScalar();
                echo "\nMigrating Freehand Draw table: {$table} " . "(count: {$count})\n";
                // we don't need thousands of echos
                ob_start();
                $rows = $this->getDbConnection()->createCommand()->select('*')->from($table)->queryAll();
                foreach ($rows as $row) {
                    $this->addFreehandElement($row);
                }

                ob_end_clean();
            } else {
                echo "\nNo data to move - table does not exist: $table \n";
            }
        }

        echo "\nArchive the old tables:\n";
        foreach ($this->tables as $table) {
            // 'archive_et_ophciexamination_synoptophore', is already archived at this point
            if (strpos($table, 'archive') === false) {
                if ($this->dbConnection->schema->getTable($table, true) !== null) {
                    $this->renameTable($table, static::$archive_prefix . $table);

                    $archive_version_table_name = static::$archive_prefix . $table . '_version';
                    if ($archive_version_table_name === 'archive_et_ophciexamination_freedraw_oncology_anterior_segment_version') {
                        $archive_version_table_name = 'archive_et_ophciexam_freedraw_oncology_anterior_segment_version';
                    }
                    $this->renameTable($table . '_version', $archive_version_table_name);
                } else {
                    echo "\nCan't archive because table does not exist: $table \n";
                }
            }
        }

        // remove FKs
        $this->dropForeignKeyFromArchives();

        //remove from element_type
        $this->removeFromElementType();
    }

    private function dropForeignKeyFromArchives()
    {
        foreach ($this->tables as $table) {
            $table_name = static::$archive_prefix . $table;

            $names = [
                "{$table}_cui_fk",
                "{$table}_lmui_fk",
                "fk_{$table}_event",
                "fk_{$table}_pf",
            ];
            foreach ($names as $name) {
                $fk_exists = $this->dbConnection->createCommand("SELECT count(*) 
                                                                    FROM information_schema.table_constraints 
                                                                    WHERE table_schema = DATABASE() 
                                                                      AND table_name = '{$table_name}' 
                                                                      AND constraint_name = '{$name}' 
                                                                      AND constraint_type = 'FOREIGN KEY'")->queryScalar();
                if ($fk_exists) {
                    $this->dropForeignKey($name, $table_name);
                }
            }
        }
    }

    private function removeFromElementType()
    {
        $event_type = $this->getIdOfEventTypeByClassName('OphCiExamination');

        // using varibable so the line length doesn't exceed the line length
        $AE_prefix = 'OEModule\OphCiAccidentandemergency\modules';

        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Anterior_Segment');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Oncology_Anterior_Segment');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Fundus');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Oncology_Fundus');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Peripheral_Fundus');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Macula');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_HeadNeck');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Lids');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Conjunctiva');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_OrbitShell');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Orbit_Lacrimal');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Freedraw_Blepharospasm');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Synoptophore');
        $this->deleteElementType($event_type, 'OEModule\OphCiExamination\models\Element_OphCiExamination_EyeMovements');
        $this->deleteElementType($event_type, "{$AE_prefix}\OphCiAccidentandemergency_Nurseassessment\models\Element_Ocular_Media");
        $this->deleteElementType($event_type, "{$AE_prefix}\OphCiAccidentandemergency_Doctor\models\Element_Ocular_Media");
    }

    private function addFreehandElement($row)
    {
        // check if the event already has this element
        $event_id = $row['event_id'];
        $element_id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->from('et_ophciexamination_freehand_draw')
            ->where('event_id = :event_id', [':event_id' => $event_id])
            ->queryScalar();

        if (!$element_id) {
            $this->insert('et_ophciexamination_freehand_draw', [
                'event_id' => $event_id,
                'last_modified_user_id' => $row['last_modified_user_id'],
                'last_modified_date' => $row['last_modified_date'],
                'created_user_id' => $row['created_user_id'],
                'created_date' => $row['created_date']
            ]);

            $element_id = $this->dbConnection->getLastInsertID();
        }

        $this->insert('ophciexamination_freehand_draw_entry', [
            'element_id' => $element_id,
            'protected_file_id' => $row['protected_file_id'],
            'comments' => $row['comments'],
            'last_modified_user_id' => $row['last_modified_user_id'],
            'last_modified_date' => $row['last_modified_date'],
            'created_user_id' => $row['created_user_id'],
            'created_date' => $row['created_date']
        ]);
    }

    public function down()
    {
        // restore table names and FKs
        foreach ($this->tables as $table) {
            // 'archive_et_ophciexamination_synoptophore', is already archived at this point we do not revert it back
            if (strpos($table, 'synoptophore') === false) {
                $this->renameTable(static::$archive_prefix . $table, $table);
                $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
            }

            $this->addForeignKey("{$table}_cui_fk", $table, 'created_user_id', 'user', 'id');
            $this->addForeignKey("{$table}_lmui_fk", $table, 'last_modified_user_id', 'user', 'id');
            $this->addForeignKey("fk_{$table}_event", $table, 'event_id', 'event', 'id');
            $this->addForeignKey("fk_{$table}_pf", $table, 'protected_file_id', 'protected_file', 'id');
        }
    }
}
