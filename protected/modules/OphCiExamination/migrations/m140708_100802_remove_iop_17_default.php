<?php

class m140708_100802_remove_iop_17_default extends OEMigration
{
    public function up()
    {
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure');
        $this->delete('setting_metadata', '`element_type_id` = '.$id.' AND `key` = \'default_reading_id\'');
    }
    public function down()
    {
        $model = \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading::model();
        $reading = $model->find('value = 17');

        $this->insert('setting_metadata', array(
            'element_type_id' => $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure'),
            'field_type_id' => 2, // Dropdown
            'key' => 'default_reading_id',
            'name' => 'Default reading',
            'default_value' => $reading->id,
        ));
    }
}
