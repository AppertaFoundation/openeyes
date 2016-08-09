<?php

class m131016_091958_proc_term_index extends CDbMigration
{
    public function up()
    {
        $this->createIndex('term', 'proc', 'term', true);
    }

    public function down()
    {
        $this->dropIndex('term', 'proc');
    }
}
