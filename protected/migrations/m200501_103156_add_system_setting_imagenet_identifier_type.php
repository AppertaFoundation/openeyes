<?php

class m200501_103156_add_system_setting_imagenet_identifier_type extends CDbMigration
{
    public function safeUp()
    {
        $text_field_id = $this->dbConnection->createCommand('SELECT id FROM setting_field_type WHERE name="Text Field"')->queryScalar();
        $identifier_type = $this->dbConnection->createCommand()
            ->select('id')
            ->from('patient_identifier_type')
            ->where('usage_type = "LOCAL"')
            ->order('id')
            ->queryScalar();
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 27,
            'field_type_id' => $text_field_id,
            'key' => 'imagenet_patient_identifier_type',
            'name' => 'ImageNET Patient Identifier Type',
            'default_value' => $identifier_type
        ));
        $this->insert('setting_installation', array(
            'key' => 'imagenet_patient_identifier_type',
            'value' => $identifier_type
        ));
        $institution_id = $this->getDbConnection()->createCommand("SELECT * FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")->queryScalar();
        $identifier_type = $this->dbConnection->createCommand()
            ->select('id')
            ->from('patient_identifier_type')
            ->where('usage_type = "LOCAL" AND institution_id = :institution_id AND site_id IS NULL')
            ->bindValues(array(':institution_id' => $institution_id))
            ->queryScalar();
        $this->insert('setting_institution', array(
            'institution_id' => $institution_id,
            'key' => 'imagenet_patient_identifier_type',
            'value' => $identifier_type,
        ));
    }

    public function safeDown()
    {
        $this->delete('setting_installation', '`key` = :key', [':key' => 'imagenet_patient_identifier_type']);
        $this->delete('setting_metadata', '`key` = :key', [':key' => 'imagenet_patient_identifier_type']);
        $this->delete('setting_institution', '`key` = :key', [':key' => 'imagenet_patient_identifier_type']);
    }
}
