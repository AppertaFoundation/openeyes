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
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'axis',
            'decimal(6, 1)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'flat_k',
            'decimal(6, 2)'
        );
        $this->addOEColumn(
            'ophtroperationbooking_whiteboard',
            'steep_k',
            'decimal(6, 2)'
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
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'axis'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'flat_k'
        );
        $this->dropOEColumn(
            'ophtroperationbooking_whiteboard',
            'steep_k'
        );
    }
}