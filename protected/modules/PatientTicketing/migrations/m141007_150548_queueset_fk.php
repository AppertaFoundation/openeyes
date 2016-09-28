<?php

class m141007_150548_queueset_fk extends CDbMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queueset', 'queueset_filter_id', 'int(11)');
        $this->addColumn('patientticketing_queueset_version', 'queueset_filter_id', 'int(11)');
        $this->addForeignKey('patientticketing_queueset_filter_fk', 'patientticketing_queueset', 'queueset_filter_id', 'patientticketing_queueset_filter', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patientticketing_queueset_filter_fk', 'patientticketing_queueset');
        $this->dropColumn('patientticketing_queueset', 'queueset_filter_id');
        $this->dropColumn('patientticketing_queueset_version', 'queueset_filter_id');
    }
}
