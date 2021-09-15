<?php

class m160826_143933_change_element_order extends CDbMigration
{

    protected $element_classes = array(
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo',
        'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo');

    public function up()
    {
        $display_order = 10;
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update(
                'element_type',
                array('display_order' => $display_order += 10),
                'id = :et_id',
                array(':et_id' => $element['id'])
            );
        }

    }

    public function down()
    {
        foreach ($this->element_classes as $cls) {
            $element = $this->dbConnection->createCommand()->select('id')->from('element_type')
                ->where('class_name = :name', array(':name' => $cls))
                ->queryRow();

            $this->update(
                'element_type',
                array('display_order' => 1),
                'id = :et_id',
                array(':et_id' => $element['id'])
            );
        }
    }
}
