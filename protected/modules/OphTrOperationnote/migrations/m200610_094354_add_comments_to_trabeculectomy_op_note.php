<?php

class m200610_094354_add_comments_to_trabeculectomy_op_note extends OEMigration
{
    public function safeUp()
    {
        $this->addOEColumn('et_ophtroperationnote_trabeculectomy', 'comments', 'text');
    }

    public function safeDown()
    {
        $this->dropOEColumn('et_ophtroperationnote_trabeculectomy', 'comments');
    }
}
