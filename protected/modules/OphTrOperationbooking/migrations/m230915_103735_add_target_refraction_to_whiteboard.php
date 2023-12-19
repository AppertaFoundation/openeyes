<?php

class m230915_103735_add_target_refraction_to_whiteboard extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'target_refraction',
            'varchar(255)',
            true
        );
    }

    public function safeDown()
    {
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'target_refraction',
            true
        );
    }
}
