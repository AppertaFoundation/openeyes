<?php

class m210810_111000_add_considered_decision extends OEMigration
{
    public function up()
    {
        $this->createOETable("ophtrconsent_considered_decision", array(
            'id' => 'pk',
            'name' => 'VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
        ), true);

        $this->insertMultiple(
            'ophtrconsent_considered_decision',
            [
                ['name' => 'Yes', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')],
                ['name' => 'No', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')]
            ]
        );
    }

    public function down()
    {
        $this->dropOETable("ophtrconsent_considered_decision", true);
    }
}
