<?php

class m190611_082156_change_display_order_of_biometry_operation_note extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', ['display_order' => 115], 'class_name = "Element_OphTrOperationnote_Biometry"');
    }

    public function down()
    {
        $this->update('element_type', ['display_order' => 40], 'class_name = "Element_OphTrOperationnote_Biometry "');
    }
}
