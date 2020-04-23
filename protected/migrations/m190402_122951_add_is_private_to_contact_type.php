<?php

class m190402_122951_add_is_private_to_contact_type extends CDbMigration
{
    public function up()
    {
        $this->addColumn('contact_label', 'is_private', 'TINYINT(1) DEFAULT 0');
        $this->addColumn('contact_label_version', 'is_private', 'TINYINT(1) DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('contact_label', 'is_private');
        $this->dropColumn('contact_label_version', 'is_private');
    }
}
