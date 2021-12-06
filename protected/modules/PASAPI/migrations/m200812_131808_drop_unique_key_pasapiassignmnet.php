<?php

class m200812_131808_drop_unique_key_pasapiassignmnet extends OEMigration
{
    public function up()
    {
        $this->dropIndex('resource_key', 'pasapi_assignment');
    }

    public function down()
    {
        $this->createIndex('resource_key', 'pasapi_assignment', 'resource_id, resource_type', true);
    }
}
