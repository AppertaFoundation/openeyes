<?php

class m201121_125347_create_pgdpsd_tables extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        // ophdrpgdpsd_pgdpsd START
        $this->createOETable('ophdrpgdpsd_pgdpsd', array(
            'id' => 'pk',
            'name' => 'varchar(50)',
            'description' => 'varchar(255)',
            'institution_id' => 'int(10) unsigned NOT NULL',
            'type' => 'varchar(5)',
            'active' => 'tinyint(1) NOT NULL',
        ), true);
        $this->addForeignKey('ophdrpgdpsd_pgdpsd_institution_fk', 'ophdrpgdpsd_pgdpsd', 'institution_id', 'institution', 'id');
        // ophdrpgdpsd_pgdpsd END

        // ophdrpgdpsd_pgdpsd_meds START
        $this->createOETable('ophdrpgdpsd_pgdpsd_meds', array(
            'id' => 'pk',
            'pgdpsd_id' => 'int(11) NOT NULL',
            'medication_id' => 'int(11) NOT NULL',
            'dose' => 'smallint NOT NULL',
            'dose_unit_term' => 'varchar(255) NOT NULL',
            'route_id' => 'int(11) NOT NULL',
        ), true);

        $this->addForeignKey('ophdrpgdpsd_pgdpsd_meds_med_id_fk', 'ophdrpgdpsd_pgdpsd_meds', 'medication_id', 'medication', 'id');
        $this->addForeignKey('ophdrpgdpsd_pgdpsd_meds_route_id_fk', 'ophdrpgdpsd_pgdpsd_meds', 'route_id', 'medication_route', 'id');
        $this->addForeignKey('ophdrpgdpsd_pgdpsd_meds_pgdpsd_id_fk', 'ophdrpgdpsd_pgdpsd_meds', 'pgdpsd_id', 'ophdrpgdpsd_pgdpsd', 'id');
        // ophdrpgdpsd_pgdpsd_meds END

        // ophdrpgdpsd_assigneduser START
        $this->createOETable('ophdrpgdpsd_assigneduser', array(
            'id' => 'pk',
            'pgdpsd_id' => 'int(11) NOT NULL',
            'user_id' => 'int(10) unsigned',
        ), true);

        $this->addForeignKey('ophdrpgdpsd_assigneduser_pgdpsd_id_fk', 'ophdrpgdpsd_assigneduser', 'pgdpsd_id', 'ophdrpgdpsd_pgdpsd', 'id');
        $this->addForeignKey('ophdrpgdpsd_assigneduser_user_id_fk', 'ophdrpgdpsd_assigneduser', 'user_id', 'user', 'id');
        // ophdrpgdpsd_assigneduser END

        // ophdrpgdpsd_assignedteam START
        $this->createOETable('ophdrpgdpsd_assignedteam', array(
            'id' => 'pk',
            'pgdpsd_id' => 'int(11) NOT NULL',
            'team_id' => 'int(11)',
        ), true);

        $this->addForeignKey('ophdrpgdpsd_assignedteam_pgdpsd_id_fk', 'ophdrpgdpsd_assignedteam', 'pgdpsd_id', 'ophdrpgdpsd_pgdpsd', 'id');
        $this->addForeignKey('ophdrpgdpsd_assignedteam_team_id_fk', 'ophdrpgdpsd_assignedteam', 'team_id', 'team', 'id');
        // ophdrpgdpsd_assignedteam END
    }

    public function safeDown()
    {
        // drop ophdrpgdpsd_pgdpsd_meds -> medication foreign key
        $this->dropForeignKey('ophdrpgdpsd_pgdpsd_meds_med_id_fk', 'ophdrpgdpsd_pgdpsd_meds');
        // drop ophdrpgdpsd_pgdpsd_meds -> medication_route foreign key
        $this->dropForeignKey('ophdrpgdpsd_pgdpsd_meds_route_id_fk', 'ophdrpgdpsd_pgdpsd_meds');
        // drop ophdrpgdpsd_pgdpsd_meds -> ophdrpgdpsd_pgdpsd foreign key
        $this->dropForeignKey('ophdrpgdpsd_pgdpsd_meds_pgdpsd_id_fk', 'ophdrpgdpsd_pgdpsd_meds');

        // drop ophdrpgdpsd_assigneduser -> ophdrpgdpsd_pgdpsd foreign key
        $this->dropForeignKey('ophdrpgdpsd_assigneduser_pgdpsd_id_fk', 'ophdrpgdpsd_assigneduser');
        // drop ophdrpgdpsd_assigneduser -> user foreign key
        $this->dropForeignKey('ophdrpgdpsd_assigneduser_user_id_fk', 'ophdrpgdpsd_assigneduser');

        // drop ophdrpgdpsd_assignedteam -> ophdrpgdpsd_pgdpsd foreign key
        $this->dropForeignKey('ophdrpgdpsd_assignedteam_pgdpsd_id_fk', 'ophdrpgdpsd_assignedteam');
        // drop ophdrpgdpsd_assignedteam -> team foreign key
        $this->dropForeignKey('ophdrpgdpsd_assignedteam_team_id_fk', 'ophdrpgdpsd_assignedteam');
        // drop ophdrpgdpsd_pgdpsd -> institution foreign key
        $this->dropForeignKey('ophdrpgdpsd_pgdpsd_institution_fk', 'ophdrpgdpsd_pgdpsd');

        // drop tables
        $this->dropOETable('ophdrpgdpsd_pgdpsd_meds', true);
        $this->dropOETable('ophdrpgdpsd_assigneduser', true);
        $this->dropOETable('ophdrpgdpsd_assignedteam', true);
        $this->dropOETable('ophdrpgdpsd_pgdpsd', true);
    }
}
