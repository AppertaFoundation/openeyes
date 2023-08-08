<?php

class m230320_002457_add_pas_key_for_contact_models extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('contact', 'pas_id', 'varchar(100)', true);
    }

    public function down()
    {
        $this->dropOEColumn('contact', 'pas_id', true);
    }
}
