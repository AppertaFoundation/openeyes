<?php

class m170518_140328_add_social_history extends OEMigration
{
    private static $duplicate_tables = array(
        'socialhistory_occupation' => array(),
        'socialhistory_smoking_status' => array('active' => 'tinyint(1)'),
        'socialhistory_accommodation' => array(),
        'socialhistory_carer' => array('deleted' => 'tinyint(1)'),
        'socialhistory_substance_misuse' => array('deleted' => 'tinyint(1)'),
        'socialhistory_driving_status' => array()
    );

    private static $duplicates_not_versioned = array(
        'socialhistory_accommodation'
    );

    private static $archive_tables = array(
        'socialhistory',
        'socialhistory_driving_status_assignment'
    );

    protected static $archive_prefix = 'archive_';

    public function up()
    {
        $this->createElementType('OphCiExamination', 'Social History', array(
            'class_name' => 'OEModule\OphCiExamination\models\SocialHistory',
            'display_order' => 25,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History'
        ));

        $this->createOETable('et_ophciexamination_socialhistory', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'occupation_id' => 'int(11)',
            'smoking_status_id' => 'int(11)',
            'accommodation_id' => 'int(11)',
            'carer_id' => 'int(11)',
            'substance_misuse_id' => 'int(11)',
            'alcohol_intake' => 'int(11)',
            'comments' => 'text',
            'type_of_job' => 'varchar(255)',
        ), true);

        foreach (static::$duplicate_tables as $table_name => $extra_cols) {
            $this->duplicateTable(
                $table_name,
                'ophciexamination_' . $table_name,
                array_merge(
                    array('name' => 'varchar(128)',
                    'display_order' => 'tinyint(3)'),
                    $extra_cols
                )
            );
            if (in_array('deleted', array_keys($extra_cols))) {
                $this->update('ophciexamination_' . $table_name, array('active' => 0), 'deleted = :deleted', array(':deleted' => true));
            }
        }

        $this->addForeignKey(
            'et_ophciexamination_socialhistory_occ_fk',
            'et_ophciexamination_socialhistory',
            'occupation_id',
            'ophciexamination_socialhistory_occupation',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_socialhistory_smok_fk',
            'et_ophciexamination_socialhistory',
            'smoking_status_id',
            'ophciexamination_socialhistory_smoking_status',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_socialhistory_acc_fk',
            'et_ophciexamination_socialhistory',
            'accommodation_id',
            'ophciexamination_socialhistory_accommodation',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_socialhistory_car_fk',
            'et_ophciexamination_socialhistory',
            'carer_id',
            'ophciexamination_socialhistory_carer',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_socialhistory_sub_fk',
            'et_ophciexamination_socialhistory',
            'substance_misuse_id',
            'ophciexamination_socialhistory_substance_misuse',
            'id'
        );


        $this->createOETable('ophciexamination_socialhistory_driving_status_assignment', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'driving_status_id' => 'int(11) NOT NULL',
            'display_order' => 'tinyint(1) unsigned NOT NULL'
        ), true);

        $this->addForeignKey(
            'ophciexamination_drivingstatus_assignment_el_fk',
            'ophciexamination_socialhistory_driving_status_assignment',
            'element_id',
            'et_ophciexamination_socialhistory',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_drivingstatus_assignment_ds_fk',
            'ophciexamination_socialhistory_driving_status_assignment',
            'driving_status_id',
            'ophciexamination_socialhistory_driving_status',
            'id'
        );

        foreach (array_merge(array_keys(static::$duplicate_tables), static::$archive_tables) as $table) {
            $this->renameTable($table, static::$archive_prefix . $table);
            if (!in_array($table, static::$duplicates_not_versioned)) {
                $this->renameTable($table . '_version', static::$archive_prefix . $table . '_version');
            }
        }
    }

    public function down()
    {
        foreach (array_merge(array_keys(static::$duplicate_tables), static::$archive_tables) as $table) {
            $this->renameTable(static::$archive_prefix . $table, $table);
            if (!in_array($table, static::$duplicates_not_versioned)) {
                $this->renameTable(static::$archive_prefix . $table . '_version', $table . '_version');
            }
        }

        $this->dropOETable('ophciexamination_socialhistory_driving_status_assignment', true);
        $this->dropOETable('et_ophciexamination_socialhistory', true);

        foreach (array_keys(static::$duplicate_tables) as $table) {
            // dropping later so no FKs remain for these lookup tables
            $this->dropOETable('ophciexamination_' . $table, true);
        }

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphCiExamination')
        )->queryScalar();
        $element_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where(
                'class_name = :class_name AND event_type_id = :eid',
                array(':class_name' => 'OEModule\OphCiExamination\models\SocialHistory', ':eid' => $event_type_id)
            )
            ->queryScalar();
        $this->delete(
            'ophciexamination_element_set_item',
            'element_type_id = :element_type_id',
            array(':element_type_id' => $element_type_id)
        );
        $this->delete(
            'element_type',
            'id = :id',
            array(':id' => $element_type_id)
        );
    }
}
