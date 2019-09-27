<?php

class m190905_005939_add_provider_no_to_contact_practice_associate_version_table extends CDbMigration
{
    public function up()
    {
        $this->addColumn('contact_practice_associate_version' , 'provider_no' , 'varchar(255) DEFAULT NULL AFTER practice_id');
    }

    public function down()
    {
        $this->dropColumn('contact_practice_associate_version', 'provider_no');
    }
}