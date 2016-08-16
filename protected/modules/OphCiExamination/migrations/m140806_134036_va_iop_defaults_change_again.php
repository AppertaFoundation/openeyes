<?php

class m140806_134036_va_iop_defaults_change_again extends OEMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
                        'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity'),
                        'field_type_id' => 1, // considered to be redundant information
                        'key' => 'default_rows',
                        'name' => 'Default number of readings',
                        'default_value' => 1,
                ));

        // remove default va value
        $this->delete('setting_metadata', 'element_type_id = '.$this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity').
                        ' and `key` = "default_value"');

        $this->insert('setting_metadata', array(
                        'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'),
                        'field_type_id' => 1, // considered to be redundant information
                        'key' => 'default_rows',
                        'name' => 'Default number of readings',
                        'default_value' => 1,
                ));

        // remove default iop value
        $this->delete('setting_metadata', 'element_type_id = '.$this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure').
                        ' and `key` = "default_reading_id"');
    }

    public function down()
    {
        $reading = $this->dbConnection->createCommand('SELECT id FROM ophciexamination_intraocularpressure_reading WHERE value = 17')->queryRow();
        $this->insert('setting_metadata', array(
                        'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'),
                        'field_type_id' => 2, // Dropdown
                        'key' => 'default_reading_id',
                        'name' => 'Default reading',
                        'default_value' => $reading['id'],
                ));

        $this->delete('setting_metadata', 'element_type_id = '.$this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure').
                ' and `key` = "default_rows"');

        $this->insert('setting_metadata', array(
                        'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity'),
                        'field_type_id' => 2, // Dropdown
                        'key' => 'default_value',
                        'name' => 'Default value',
                        'default_value' => 110,
                ));

        $this->delete('setting_metadata', 'element_type_id = '.$this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity').
                ' and `key` = "default_rows"');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
