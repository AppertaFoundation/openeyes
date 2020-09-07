<?php

class m170317_132408_add_items_to_audit_action extends CDbMigration
{
    public function up()
    {
        $this->insert('audit_action', array('name'=>'change-of-order'));
        $this->insert('audit_action', array('name'=>'change-of-comment'));
    }

    public function down()
    {
        $this->delete('audit_action', "`name` = 'change-of-order'");
        $this->delete('audit_action', "`name` = 'change-of-comment'");
    }
}
