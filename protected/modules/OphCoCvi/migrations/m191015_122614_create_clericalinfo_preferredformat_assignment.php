<?php

class m191015_122614_create_clericalinfo_preferredformat_assignment extends OEMigration
{
    public function up()
    {
        $this->createOETable("ophcocvi_clericalinfo_preferredformat_assignment", [
            'id' => 'pk',
            'element_id' => 'int(10) unsigned NOT NULL',
            'preferred_format_id' => 'int(10) unsigned NOT NULL',
        ], true);
    }

    public function down()
    {
        $this->dropOETable('ophcocvi_clericalinfo_preferredformat_assignment');
    }

}
