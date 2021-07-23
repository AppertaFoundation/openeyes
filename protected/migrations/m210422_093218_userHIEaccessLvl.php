<?php

class m210422_093218_userHIEaccessLvl extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'hie_access_level',
            array(
                'id' => 'pk',
                'name' => 'varchar(200) not null',
                'description' => 'varchar(200)',
                'display_order' => 'int(11)'
            ),
            false
        );

        $rows = array(
            array('name'=>'Level 1 - Default View', 'description'=>'Phlebotomist, Students','display_order'=>1),
            array('name'=>'Level 2 - Admin', 'description'=>'Receptionists, Clerks, HCA, Med Sec','display_order'=>2),
            array('name'=>'Level 3 - Summary', 'description'=>'Nurse, AHP, Pharmacist, Clinical Coder, Practice Manager','display_order'=>3),
            array('name'=>'Level 4 - Extended', 'description'=>'Doctor, Nurse Practitioner, AHP, Practice Manager','display_order'=>4)
        );

        $this->insertMultiple('hie_access_level', $rows);

        $this->createOETable('user_hie_access_lvl_assignment', array(
            'id' => 'pk',
            'user_id' => 'int(10) unsigned NOT NULL',
            'hie_access_level_id' => 'int(11) NOT NULL DEFAULT 1',
        ), true);

        $this->addForeignKey('hie_access_level_fk', 'user_hie_access_lvl_assignment', 'hie_access_level_id', 'hie_access_level', 'id');
        $this->addForeignKey('user_hie_access_level_fk', 'user_hie_access_lvl_assignment', 'user_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('hie_access_level_fk', 'user_hie_access_lvl_assignment');
        $this->dropForeignKey('user_hie_access_level_fk', 'user_hie_access_lvl_assignment');

        $this->dropOETable('user_hie_access_lvl_assignment', true);
        $this->dropOETable('hie_access_level', false);
    }
}
