<?php

class m210731_090300_add_medical_capacity_advocate_instructed extends OEMigration
{
    public function up()
    {
        if ($this->dbConnection->schema->getTable('ophtrconsent_medical_capacity_advocate_instructed', true) === null) {
            $this->createOETable("ophtrconsent_medical_capacity_advocate_instructed", array(
                'id' => 'pk',
                'name' => 'VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL',
                'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
            ), true);

            $this->insertMultiple(
                'ophtrconsent_medical_capacity_advocate_instructed',
                [
                    ['name' => 'Yes', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')],
                    ['name' => 'No', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')],
                    ['name' => 'N/A', 'display_order' => '1', 'last_modified_date' => date('Y-m-d H:i:s'), 'created_date' => date('Y-m-d H:i:s')]
                ]
            );
        }
    }

    public function down()
    {
        echo "m210731_090300_add_medical_capacity_advocate_instructed does not support migration down.\n";
        return false;
    }
}
