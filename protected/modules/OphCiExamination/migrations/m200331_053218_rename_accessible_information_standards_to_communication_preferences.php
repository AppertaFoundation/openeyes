<?php

class m200331_053218_rename_accessible_information_standards_to_communication_preferences extends OEMigration
{
    public function safeUp()
    {
        $this->renameTable('et_ophciexamination_accessible_information_standards', 'et_ophciexamination_communication_preferences');
        $this->renameTable('et_ophciexamination_accessible_information_standards_version', 'et_ophciexamination_communication_preferences_version');

        $this->dropForeignKey('et_ophciexamination_accessible_information_standards_ev_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_communication_preferences_ev_fk', 'et_ophciexamination_communication_preferences', 'event_id', 'event', 'id');

        $this->dropForeignKey('et_ophciexamination_accessible_information_standards_cui_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_communication_preferences_cui_fk', 'et_ophciexamination_communication_preferences', 'created_user_id', 'user', 'id');

        $this->dropForeignKey('et_ophciexamination_accessible_information_standards_lmui_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_communication_preferences_lmui_fk', 'et_ophciexamination_communication_preferences', 'last_modified_user_id', 'user', 'id');

        $this->update('element_group', array('name' => 'Communication Preferences'), "name = 'Accessible Information Standards'");

        $elementTypeId = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name = :name', array(':name' => 'Accessible Information Standards'))->queryScalar();
        $this->update(
            'element_type',
            array('name' => 'Communication Preferences', 'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences'),
            'id = :id',
            array(':id' => $elementTypeId)
        );
    }

    public function safeDown()
    {
        $elementTypeId = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('name = :name', array(':name' => 'Communication Preferences'))->queryScalar();
        $this->update(
            'element_type',
            array('name' => 'Accessible Information Standards', 'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences'),
            'id = :id',
            array(':id' => $elementTypeId)
        );
        $this->update('element_group', array('name' => 'Accessible Information Standards'), "name = 'Communication Preferences'");

        $this->dropForeignKey('et_ophciexamination_communication_preferences_lmui_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_accessible_information_standards_lmui_fk', 'et_ophciexamination_communication_preferences', 'last_modified_user_id', 'user', 'id');

        $this->dropForeignKey('et_ophciexamination_communication_preferences_cui_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_accessible_information_standards_cui_fk', 'et_ophciexamination_communication_preferences', 'created_user_id', 'user', 'id');

        $this->dropForeignKey('et_ophciexamination_communication_preferences_ev_fk', 'et_ophciexamination_communication_preferences');
        $this->addForeignKey('et_ophciexamination_accessible_information_standards_ev_fk', 'et_ophciexamination_communication_preferences', 'event_id', 'event', 'id');

        $this->renameTable('et_ophciexamination_communication_preferences_version', 'et_ophciexamination_accessible_information_standards_version');
        $this->renameTable('et_ophciexamination_communication_preferences', 'et_ophciexamination_accessible_information_standards');
    }
}
