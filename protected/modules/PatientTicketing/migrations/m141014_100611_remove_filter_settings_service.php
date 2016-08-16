<?php

class m141014_100611_remove_filter_settings_service extends OEMigration
{
    public function up()
    {
        $this->addColumn('patientticketing_queueset', 'filter_priority', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset', 'filter_subspecialty', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset', 'filter_firm', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset', 'filter_my_tickets', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset', 'filter_closed_tickets', 'boolean NOT NULL DEFAULT true');

        $this->addColumn('patientticketing_queueset_version', 'filter_priority', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset_version', 'filter_subspecialty', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset_version', 'filter_firm', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset_version', 'filter_my_tickets', 'boolean NOT NULL DEFAULT true');
        $this->addColumn('patientticketing_queueset_version', 'filter_closed_tickets', 'boolean NOT NULL DEFAULT true');

        $this->execute('update patientticketing_queueset set filter_priority = (select priority from  patientticketing_queueset_filter where patientticketing_queueset_filter.id=patientticketing_queueset.id)');
        $this->execute('update patientticketing_queueset set filter_subspecialty = (select subspecialty from  patientticketing_queueset_filter where patientticketing_queueset_filter.id=patientticketing_queueset.id)');
        $this->execute('update patientticketing_queueset set filter_firm = (select firm from  patientticketing_queueset_filter where patientticketing_queueset_filter.id=patientticketing_queueset.id)');
        $this->execute('update patientticketing_queueset set filter_my_tickets = (select my_tickets from  patientticketing_queueset_filter where patientticketing_queueset_filter.id=patientticketing_queueset.id)');
        $this->execute('update patientticketing_queueset set filter_closed_tickets = (select closed_tickets from  patientticketing_queueset_filter where patientticketing_queueset_filter.id=patientticketing_queueset.id)');

        $this->execute('update patientticketing_queueset set queueset_filter_id = null');
        $this->execute('delete from patientticketing_queueset_filter');

        $this->dropForeignKey('patientticketing_queueset_filter_fk', 'patientticketing_queueset');
        $this->dropColumn('patientticketing_queueset', 'queueset_filter_id');
        $this->dropColumn('patientticketing_queueset_version', 'queueset_filter_id');

        $this->dropTable('patientticketing_queueset_filter');
        $this->dropTable('patientticketing_queueset_filter_version');
    }

    public function down()
    {
        $this->dropColumn('patientticketing_queueset_version', 'filter_priority');
        $this->dropColumn('patientticketing_queueset_version', 'filter_subspecialty');
        $this->dropColumn('patientticketing_queueset_version', 'filter_firm');
        $this->dropColumn('patientticketing_queueset_version', 'filter_my_tickets');
        $this->dropColumn('patientticketing_queueset_version', 'filter_closed_tickets');
        $this->dropColumn('patientticketing_queueset', 'filter_priority');
        $this->dropColumn('patientticketing_queueset', 'filter_subspecialty');
        $this->dropColumn('patientticketing_queueset', 'filter_firm');
        $this->dropColumn('patientticketing_queueset', 'filter_my_tickets');
        $this->dropColumn('patientticketing_queueset', 'filter_closed_tickets');

        $this->createOETable('patientticketing_queueset_filter', array(
            'id' => 'pk',
            'patient_list' => 'boolean NOT NULL DEFAULT true',
            'priority' => 'boolean NOT NULL DEFAULT true',
            'subspecialty' => 'boolean NOT NULL DEFAULT true',
            'firm' => 'boolean NOT NULL DEFAULT true',
            'my_tickets' => 'boolean NOT NULL DEFAULT true',
            'closed_tickets' => 'boolean NOT NULL DEFAULT true',
        ), true);

        $this->addColumn('patientticketing_queueset', 'queueset_filter_id', 'int(11)');
        $this->addColumn('patientticketing_queueset_version', 'queueset_filter_id', 'int(11)');
        $this->addForeignKey('patientticketing_queueset_filter_fk', 'patientticketing_queueset', 'queueset_filter_id', 'patientticketing_queueset_filter', 'id');

        $this->execute('insert into patientticketing_queueset_filter (id) select (id) from patientticketing_queueset');
        $this->execute('update patientticketing_queueset set queueset_filter_id = id');
    }
}
