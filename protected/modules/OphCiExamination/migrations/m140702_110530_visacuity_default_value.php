<?php

class m140702_110530_visacuity_default_value extends OEMigration
{
    public function up()
    {
        $value = $this->dbConnection->createCommand("SELECT base_value FROM ophciexamination_visual_acuity_unit_value WHERE value = '6/6'")->queryRow();

        $this->insert('setting_metadata', array(
            'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity'),
            'field_type_id' => 2, // Dropdown
            'key' => 'default_value',
            'name' => 'Default value',
            'default_value' => $value['base_value'],
        ));
    }

    public function down()
    {
        $element_type_id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity');
        $this->delete('setting_metadata', '`element_type_id` = '.$element_type_id.' AND `key` = \'default_value\'');
    }
}
