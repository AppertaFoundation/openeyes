<?php

class m141208_135800_audit_model_unique extends CDbMigration
{
    public function safeUp()
    {
        $this->alterColumn('audit_model', 'name', 'string');
        $this->createIndex('audit_model_unique', 'audit_model', 'name', 'true');
    }

    public function safeDown()
    {
        $this->dropIndex('audit_model_unique', 'audit_model');
    }
}
