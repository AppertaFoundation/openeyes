<?php

class m161222_161414_docman_log extends OEMigration
{
    public function up()
    {
        $this->createOETable('document_log', array(
            'id' => 'pk',
            'hos_num' => 'varchar(40)',
            'clinician_name' => 'varchar(256)',
            'event_updated' => 'datetime',
            'event_date' => 'datetime',
            'output_date' => 'datetime',
        ));
    }

    public function down()
    {
        $this->dropOETable('document_log');
    }
}
