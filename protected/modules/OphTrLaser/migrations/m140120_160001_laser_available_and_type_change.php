<?php

class m140120_160001_laser_available_and_type_change extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophtrlaser_site_laser', 'deleted', 'boolean default false');
        $this->createOETable(
            'ophtrlaser_type',
            array(
                'id' => 'tinyint unsigned not null auto_increment primary key',
                'name' => 'varchar(85) not null',
                'unique (name)',
            )
        );
        $this->renameColumn('ophtrlaser_site_laser', 'type', 'type_id');
        $this->alterColumn('ophtrlaser_site_laser', 'type_id', 'tinyint unsigned default 0 not null');
        $this->update('ophtrlaser_site_laser', array('type_id' => 1));

        $migrations_path = dirname(__FILE__);
        $this->initialiseData($migrations_path);

        $this->addForeignKey('ophtrlaser_type_fk', 'ophtrlaser_site_laser', 'type_id', 'ophtrlaser_type', 'id');
    }

    public function down()
    {
        $this->dropColumn('ophtrlaser_site_laser', 'deleted');
        $this->dropForeignKey('ophtrlaser_type_fk', 'ophtrlaser_site_laser');
        $this->dropTable('ophtrlaser_type');
        $this->alterColumn('ophtrlaser_site_laser', 'type_id', 'varchar(128) DEFAULT NULL');
        $this->update('ophtrlaser_site_laser', array('type_id' => 'Unknown'));
        $this->renameColumn('ophtrlaser_site_laser', 'type_id', 'type');
    }
}
