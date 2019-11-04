<?php
class m180814_133405_change_display_order_operation_booking extends CDbMigration
{
    public function up()
    {
        $this->update('element_type', array('display_order' => 30), 'name="Schedule operation"');
    }
    public function down()
    {
        $this->update('element_type', array('display_order' => 1), 'name="Schedule operation"');
    }
}
