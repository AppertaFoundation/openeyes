<?php

class m210104_233331_add_institution_id_to_audit extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('audit', 'institution_id', 'int(10) unsigned', false);
        $this->addForeignKey('audit_institution_id_fk', 'audit', 'institution_id', 'institution', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('audit_institution_id_fk', 'audit');
        $this->dropOEColumn('audit', 'institution_id', false);
    }
}
