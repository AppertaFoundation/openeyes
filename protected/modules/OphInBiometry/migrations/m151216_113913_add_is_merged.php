<?php

class m151216_113913_add_is_merged extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophinbiometry_imported_events', 'is_merged', 'boolean default false after is_linked');
    }

    public function down()
    {
        $this->dropColumn('ophinbiometry_imported_events', 'is_merged');
    }
}
