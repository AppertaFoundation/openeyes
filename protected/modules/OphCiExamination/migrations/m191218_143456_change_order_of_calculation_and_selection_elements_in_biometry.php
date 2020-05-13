<?php

class m191218_143456_change_order_of_calculation_and_selection_elements_in_biometry extends CDbMigration
{
    public function up()
    {
        $this->update(
            'element_type',
            [
                'display_order' => 20
            ],
            'name = "Element_OphInBiometry_Calculation"'
        );
        $this->update(
            'element_type',
            [
                'display_order' => 30
            ],
            'name = "Element_OphInBiometry_Selection"'
        );
    }

    public function down()
    {
        $this->update(
            'element_type',
            [
                'display_order' => 30
            ],
            'name = "Element_OphInBiometry_Calculation"'
        );
        $this->update(
            'element_type',
            [
                'display_order' => 20
            ],
            'name = "Element_OphInBiometry_Selection"'
        );
    }
}
