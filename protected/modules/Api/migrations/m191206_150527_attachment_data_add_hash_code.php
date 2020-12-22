<?php

class m191206_150527_attachment_data_add_hash_code extends CDbMigration
{
    public function up()
    {
        $this->addColumn('attachment_data', 'hash_code', 'BIGINT(100) NULL');
    }

    public function down()
    {
        $this->dropColumn('attachment_data', 'hash_code');
    }
}
