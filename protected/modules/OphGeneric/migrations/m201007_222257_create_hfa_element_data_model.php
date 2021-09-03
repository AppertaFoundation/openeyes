<?php

class m201007_222257_create_hfa_element_data_model extends OEMigration
{
    public function up()
    {
        $this->createOETable('et_ophgeneric_hfa', [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0',
            'eye_id' => 'INT(10) unsigned'
        ], true);

        $this->createOETable('ophgeneric_hfa_entry', [
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'eye_id' => 'INT(10) unsigned',
            'mean_deviation' => 'text NULL',
            'visual_field_index' => 'text NULL',
        ], true);

        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphGeneric'))
            ->queryScalar();

        $this->insert('element_type', [
            'name' => 'HFA',
            'class_name' => 'OEModule\OphGeneric\models\HFA',
            'event_type_id' => $event_type_id,
            'display_order' => 30,
            'required' => 0]);

        $this->addForeignKey('et_ophgeneric_hfa_eye_fk', 'et_ophgeneric_hfa', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophgeneric_hfa_event_fk', 'et_ophgeneric_hfa', 'event_id', 'event', 'id');
        $this->addForeignKey('ophgeneric_hfa_entry_eye_fk', 'ophgeneric_hfa_entry', 'eye_id', 'eye', 'id');
        $this->addForeignKey('ophgeneric_hfa_entry_element_fk', 'ophgeneric_hfa_entry', 'element_id', 'et_ophgeneric_hfa', 'id');
    }

    public function down()
    {
        $this->dropOETable('ophgeneric_hfa_entry', true);
        $this->dropOETable('et_ophgeneric_hfa', true);
    }
}
