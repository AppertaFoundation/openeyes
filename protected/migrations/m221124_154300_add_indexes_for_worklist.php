<?php

class m221124_154300_add_indexes_for_worklist extends OEMigration
{
    public function safeUp()
    {
        $this->execute("CREATE INDEX worklist_start_IDX USING BTREE ON worklist (`start`)");
        $this->execute("CREATE INDEX worklist_end_IDX USING BTREE ON worklist (`end`)");
    }

    public function safeDown()
    {
        $this->execute("DROP INDEX worklist_start_IDX ON worklist");
        $this->execute("DROP INDEX worklist_end_IDX ON worklist");
    }
}
