<?php

class m210715_050833_add_default_pathway_and_step_types extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert(
            'pathway_type',
            array(
                'name' => 'Default pathway',
                'is_preset' => 0,
            )
        );

        $step_types = array(
            array(
                'group' => 'path',
                'type' => 'buff',
                'small_icon' => 'fork',
                'large_icon' => 'i-fork',
                'short_name' => 'fork',
                'widget_view' => 'fork',
                'long_name' => 'Decision / review',
                'active' => 1,
            ),
            array(
                'group' => 'path',
                'type' => 'buff',
                'small_icon' => 'path-break',
                'large_icon' => 'i-break',
                'short_name' => 'break',
                'widget_view' => 'break',
                'long_name' => 'Break in pathway',
                'active' => 1,
            ),
            array(
                'group' => 'path',
                'type' => 'process',
                'small_icon' => 'stop',
                'large_icon' => 'i-discharge',
                'short_name' => 'discharge',
                'widget_view' => 'checkout',
                'long_name' => 'Check out',
                'active' => 1,
            ),
            array(
                'group' => 'path',
                'type' => 'hold',
                'small_icon' => 'time',
                'short_name' => 'onhold',
                'long_name' => 'Hold timer (mins)',
                'widget_view' => 'timer',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'short_name' => 'Bio',
                'long_name' => 'Biometry',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'small_icon' => 'drop',
                'large_icon' => 'i-drug-admin',
                'short_name' => 'drug admin',
                'long_name' => 'Drug Administration Preset Order',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'short_name' => 'Exam',
                'long_name' => 'Examination',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'short_name' => 'Task',
                'long_name' => 'General Task',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'short_name' => 'Letter',
                'long_name' => 'Letter',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'short_name' => 'Rx',
                'long_name' => 'Prescription',
                'active' => 1,
            ),
            array(
                'group' => 'standard',
                'type' => 'process',
                'widget_view' => 'visualfields',
                'short_name' => 'Fields',
                'long_name' => 'Visual Fields',
                'active' => 1,
            ),
        );

        $this->insertMultiple('pathway_step_type', $step_types);
    }

    public function safeDown()
    {
        $this->truncateTable('pathway');
        $this->truncateTable('pathway_step_type');
        $this->truncateTable('pathway_type');
    }
}
