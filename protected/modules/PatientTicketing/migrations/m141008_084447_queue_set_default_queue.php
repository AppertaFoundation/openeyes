<?php

class m141008_084447_queue_set_default_queue extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queueset', 'default_queue_id', 'int(11) null');
        $this->addColumn('patientticketing_queueset_version', 'default_queue_id', 'int(11) null');

        $this->createIndex('patientticketing_queueset_dqi_fk', 'patientticketing_queueset', 'default_queue_id');
        $this->addForeignKey('patientticketing_queueset_dqi_fk', 'patientticketing_queueset', 'default_queue_id', 'patientticketing_queue', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patientticketing_queueset_dqi_fk', 'patientticketing_queueset');
        $this->dropIndex('patientticketing_queueset_dqi_fk', 'patientticketing_queueset');

        $this->dropColumn('patientticketing_queueset', 'default_queue_id');
        $this->dropColumn('patientticketing_queueset_version', 'default_queue_id');
    }
}
