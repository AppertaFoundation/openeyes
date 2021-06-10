<?php

class m210427_080924_create_index_for_attachment_data_hash_code extends CDbMigration
{

    public function safeUp()
    {
        // We need to do try catch in case this was added before the migration manually and
        // I haven't found a way to check if an index exists
        try {
            $this->createIndex('index_hash_code', 'attachment_data', 'hash_code');
        } catch (Exception $exception) {
            echo 'IF Duplicate KEY error then its fine otherwise needs investigation ' . $exception->getMessage();
        }
    }

    public function safeDown()
    {
        $this->dropIndex('index_hash_code', 'attachment_data');
    }
}
