<?php

class m160706_103845_add_confirmation extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationbooking_whiteboard', 'is_confirmed', 'boolean');
        $this->addColumn('ophtroperationbooking_whiteboard_version', 'is_confirmed', 'boolean');
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_whiteboard', 'is_confirmed');
        $this->dropColumn('ophtroperationbooking_whiteboard_version', 'is_confirmed');
    }
}
