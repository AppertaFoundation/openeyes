<?php

class m140804_090552_create_iop_and_va_defaults extends OEMigration
{
    public function up()
    {
        $reading = $this->dbConnection->createCommand('SELECT id FROM ophciexamination_intraocularpressure_reading WHERE value = 17')->queryRow();

        $this->insert('setting_metadata', array(
                        'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'),
                        'field_type_id' => 2, // Dropdown
                        'key' => 'default_reading_id',
                        'name' => 'Default reading',
                        'default_value' => $reading['id'],
                ));
    }

    public function down()
    {
        $element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure');
        $this->delete('setting_metadata', '`element_type_id` = '.$element_type_id.' AND `key` = \'default_reading_id\'');
    }
}
