<?php

class m210810_110000_add_authorised_decision extends OEMigration
{
    public function up()
    {
        $this->createOETable("ophtrconsent_authorised_decision", array(
            'id' => 'pk',
            'name' => 'VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL',
            'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
        ), true);

        $this->insertMultiple(
            'ophtrconsent_authorised_decision',
            [
                ['name' => 'under a Lasting Power  of Attorney.', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')],
                ['name' => 'as a Court Appointed Deputy.', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')]
            ]
        );
    }

    public function down()
    {
        $this->dropOETable("ophtrconsent_authorised_decision", true);
    }
}
