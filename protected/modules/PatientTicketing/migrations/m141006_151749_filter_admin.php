<?php

class m141006_151749_filter_admin extends OEMigration
{
    public function safeup()
    {
        $this->createOETable('patientticketing_queueset_filter', array(
            'id' => 'pk',
            'patient_list' => 'boolean NOT NULL DEFAULT true',
            'priority' => 'boolean NOT NULL DEFAULT true',
            'subspecialty' => 'boolean NOT NULL DEFAULT true',
            'firm' => 'boolean NOT NULL DEFAULT true',
            'my_tickets' => 'boolean NOT NULL DEFAULT true',
            'closed_tickets' => 'boolean NOT NULL DEFAULT true',
        ), true);
    }

    public function safedown()
    {
        $this->dropTable('patientticketing_queueset_filter');
        $this->dropTable('patientticketing_queueset_filter_version');
    }
}
