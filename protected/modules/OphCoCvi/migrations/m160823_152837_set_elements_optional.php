<?php

class m160823_152837_set_elements_optional extends CDbMigration
{
    protected $element_classes = array('OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo', 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo');

    public function up()
    {
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update('element_type', array('required' => false), 'id = :et_id', array(':et_id' => $element['id']));
        }
    }

    public function down()
    {
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update('element_type', array('required' => true), 'id = :et_id', array(':et_id' => $element['id']));
        }
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
