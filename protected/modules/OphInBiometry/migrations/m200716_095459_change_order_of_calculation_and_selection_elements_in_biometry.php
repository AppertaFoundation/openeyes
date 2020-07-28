<?php

/**
 * Note that this migration does the job of m191218_143456_change_order_of_calculation_and_selection_elements_in_biometry
 * which does not do anything because of a mistake
 */

class m200716_095459_change_order_of_calculation_and_selection_elements_in_biometry extends CDbMigration
{
    public function safeUp()
    {
        $this->update('element_type',
            [
                'display_order' => 20
            ],
            'class_name = "Element_OphInBiometry_Calculation"'
        );
        $this->update('element_type',
            [
                'display_order' => 30
            ],
            'class_name = "Element_OphInBiometry_Selection"'
        );
    }

    public function safeDown()
    {
        $this->update('element_type',
            [
                'display_order' => 20
            ],
            'class_name = "Element_OphInBiometry_Selection"'
        );
        $this->update('element_type',
            [
                'display_order' => 30
            ],
            'class_name = "Element_OphInBiometry_Calculation"'
        );
    }
}
