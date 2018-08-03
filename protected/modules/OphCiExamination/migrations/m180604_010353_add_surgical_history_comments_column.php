<?php

class m180604_010353_add_surgical_history_comments_column extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('et_ophciexamination_pastsurgery', 'comments', 'text');
        $this->addColumn('et_ophciexamination_pastsurgery_version', 'comments', 'text');
    }

    public function safeDown()
    {
        $this->dropColumn('et_ophciexamination_pastsurgery', 'comments');
        $this->dropColumn('et_ophciexamination_pastsurgery_version', 'comments');
    }
}
