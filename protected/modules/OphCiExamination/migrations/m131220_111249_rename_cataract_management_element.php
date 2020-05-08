<?php

class m131220_111249_rename_cataract_management_element extends CDbMigration
{
    public function up()
    {
        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update(
            'element_type',
            array(
                'name' => 'Cataract Surgical Management',
                'class_name' => 'Element_OphCiExamination_CataractSurgicalManagement',
            ),
            "event_type_id = {$event_type['id']} and class_name = 'Element_OphCiExamination_CataractManagement'"
        );

        $this->renameTable('et_ophciexamination_cataractmanagement', 'et_ophciexamination_cataractsurgicalmanagement');
        $this->renameTable('ophciexamination_cataractmanagement_eye', 'ophciexamination_cataractsurgicalmanagement_eye');
        $this->renameTable('ophciexamination_cataractmanagement_suitable_for_surgeon', 'ophciexamination_cataractsurgicalmanagement_suitable_for_surgeon');
    }

    public function down()
    {
        $this->renameTable('ophciexamination_cataractsurgicalmanagement_suitable_for_surgeon', 'ophciexamination_cataractmanagement_suitable_for_surgeon');
        $this->renameTable('ophciexamination_cataractsurgicalmanagement_eye', 'ophciexamination_cataractmanagement_eye');
        $this->renameTable('et_ophciexamination_cataractsurgicalmanagement', 'et_ophciexamination_cataractmanagement');

        $event_type = $this->dbConnection->createCommand()->select('*')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphCiExamination'))->queryRow();

        $this->update(
            'element_type',
            array(
                'name' => 'Cataract Surgical Management',
                'class_name' => 'Element_OphCiExamination_CataractManagement',
            ),
            "event_type_id = {$event_type['id']} and class_name = 'Element_OphCiExamination_CataractSurgicalManagement'"
        );
    }
}
