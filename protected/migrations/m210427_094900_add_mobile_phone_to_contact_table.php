<?php

class m210427_094900_add_mobile_phone_to_contact_table extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('contact', 'mobile_phone', 'varchar(50) default null AFTER primary_phone', true);
    }

    public function down()
    {
        $this->dropOEColumn('contact', 'mobile_phone', true);
    }
}
