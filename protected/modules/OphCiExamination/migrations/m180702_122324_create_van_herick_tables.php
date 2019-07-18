<?php

class m180702_122324_create_van_herick_tables extends \OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophciexamination_van_herick',
        array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'left_van_herick_id' => 'int(10) unsigned DEFAULT NULL',
            'right_van_herick_id' => 'int(10) unsigned DEFAULT NULL',
            'eye_id' => "int(10) unsigned NOT NULL DEFAULT '3'",
        ),true);

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();
        $this->insertOEElementType(['OEModule\OphCiExamination\models\VanHerick' => ['name' => 'Van Herick', 'display_order' => 37]], $event_type_id);

        $this->renameTable('ophciexamination_gonioscopy_van_herick', 'ophciexamination_van_herick');
        $this->renameTable('ophciexamination_gonioscopy_van_herick_version', 'ophciexamination_van_herick_version');

    }

    public function down()
    {
        $this->dropOETable('et_ophciexamination_van_herick', true);
        $this->renameTable('ophciexamination_van_herick', 'ophciexamination_gonioscopy_van_herick');
        $this->renameTable('ophciexamination_van_herick_version', 'ophciexamination_gonioscopy_van_herick_version');

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryScalar();
        $this->delete('element_type', 'name = :name AND event_type_id = :event_type_id', [':name' => 'Van Herick', ':event_type_id' => $event_type_id, ]);
    }
}