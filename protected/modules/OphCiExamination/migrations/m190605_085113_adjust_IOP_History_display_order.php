<?php

class m190605_085113_adjust_IOP_History_display_order extends CDbMigration
{
    public function up()
    {
        $this->update(
            'element_type',
            ['display_order' => 310],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\HistoryIOP']
        );
        $this->update(
            'element_type',
            ['display_order' => 300],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation']
        );
        $this->update(
            'element_type',
            ['display_order' => 305],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure']
        );

        $this->update('element_group', ['display_order' => 70], 'name =:name', [':name' => 'IOP History']);
        $this->update('element_group', ['display_order' => 60], 'name =:name', [':name' => 'Drops']);
        $this->update('element_group', ['display_order' => 65], 'name =:name', [':name' => 'Intraocular Pressure']);
    }

    public function down()
    {
        $this->update(
            'element_type',
            ['display_order' => 310],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\HistoryIOP']
        );
        $this->update(
            'element_type',
            ['display_order' => 310],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Dilation']
        );
        $this->update(
            'element_type',
            ['display_order' => 300],
            'class_name = :class_name',
            [':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure']
        );

        $this->update('element_group', ['display_order' => 65], 'name =:name', [':name' => 'IOP History']);
        $this->update('element_group', ['display_order' => 70], 'name =:name', [':name' => 'Drops']);
        $this->update('element_group', ['display_order' => 60], 'name =:name', [':name' => 'Intraocular Pressure']);
    }

}
