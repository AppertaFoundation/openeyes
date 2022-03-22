<?php

class m210601_060238_add_ae_red_flags_exam_element extends OEMigration
{
    public function safeUp()
    {
        $this->createElementGroupForEventType(
            'Triage',
            'OphCiExamination',
            25
        );

        $this->createElementType('OphCiExamination', 'Red Flags', array(
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AE_RedFlags',
            'display_order' => 540,
            'group_name' => 'Triage',
            'default' => 0,
            'required' => 0,
        ));

        $this->createOETable(
            'et_ophciexamination_ae_red_flags',
            [
                'id' => 'pk',
                'event_id' => 'INT(10) UNSIGNED',
                'nrf_check' => 'boolean'
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_ae_red_flags_option',
            [
                'id' => 'pk',
                'name' => 'text',
                'active' => 'boolean',
                'display_order' => 'int(11)',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_ae_red_flags_option_assignment',
            [
                'id' => 'pk',
                'element_id' => 'INT(10) UNSIGNED',
                'red_flag_id' => 'INT(10) UNSIGNED',
            ],
            true
        );

        $this->insertMultiple('ophciexamination_ae_red_flags_option', [
        ['name' => 'Post-op', 'active' => true,],
        ['name' => 'Systemically unwell', 'active' => true,],
        ['name' => 'Change in Pupils', 'active' => true,],
        ['name' => 'Diplopia', 'active' => true,],
        ['name' => 'Complete Visual Loss', 'active' => true,],
        ['name' => 'Rapid Change in VA', 'active' => true,],
        ['name' => 'Helping Hands', 'active' => true,],
        ]);
    }

    public function safeDown()
    {
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_AE_RedFlags');
        $this->delete('element_type', 'id = ?', [$id]);

        $this->delete('element_group', 'name = ?', ['Triage']);

        $this->dropOETable('et_ophciexamination_ae_red_flags', true);
        $this->dropOETable('ophciexamination_ae_red_flags_option', true);
        $this->dropOETable('ophciexamination_ae_red_flags_option_assignment', true);
    }
}
