<?php

class m190916_004623_add_new_whiteboard_columns extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'axial_length',
            'varchar(100)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'acd',
            'varchar(100)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'formula',
            'varchar(100)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'aconst',
            'varchar(100)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'complexity',
            'int'
        );
    }

    public function down()
    {
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'axial_length'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'acd'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'formula'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'aconst'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'complexity'
        );
    }
}