<?php

class m210302_053753_add_institution_to_admin_settings extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('ophcotherapya_email_recipient', 'institution_id', 'int(10) unsigned AFTER id', true);
        $this->addOEColumn('ophcotherapya_filecoll', 'institution_id', 'int(10) unsigned AFTER name', true);
        $this->addOEColumn('ophcotherapya_decisiontree', 'institution_id', 'int(10) unsigned AFTER name', true);
    }

    public function safeDown()
    {
        $this->dropOEColumn('ophcotherapya_email_recipient', 'institution_id', true);
        $this->dropOEColumn('ophcotherapya_filecoll', 'institution_id', true);
        $this->dropOEColumn('ophcotherapya_decisiontree', 'institution_id', true);
    }
}
