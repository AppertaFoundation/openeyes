<?php

class m191205_093052_add_request_details_table extends OEMigration
{
    public function safeUp()
    {
        $this->createOETable('request_details', [
            'id' => 'pk',
            'request_id' => 'INT(10) NOT NULL',
            'name' => 'VARCHAR(100)',
            'value' => 'text'
        ], true);

        $this->addForeignKey('request_fk_request_details', 'request_details', 'request_id', 'request', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('request_fk_request_details', 'request_details');
        $this->dropOETable('request_details', true);
    }
}
