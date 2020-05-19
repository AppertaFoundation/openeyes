<?php

class m131113_163020_patient_pedigree_status_field extends OEMigration
{
    public function up()
    {
        $this->createTable('pedigree_status', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(64) collate utf8_bin NOT NULL',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
                'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `pedigree_status_last_modified_user_id_fk` (`last_modified_user_id`)',
                'KEY `pedigree_status_created_user_id_fk` (`created_user_id`)',
                'CONSTRAINT `pedigree_status_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `pedigree_status_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->initialiseData(dirname(__FILE__));

        $this->addColumn('patient_pedigree', 'status_id', 'int(10) unsigned NOT NULL');
        $this->update('patient_pedigree', array('status_id' => 4));
        $this->createIndex('patient_pedigree_status_id_fk', 'patient_pedigree', 'status_id');
        $this->addForeignKey('patient_pedigree_status_id_fk', 'patient_pedigree', 'status_id', 'pedigree_status', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('patient_pedigree_status_id_fk', 'patient_pedigree');
        $this->dropIndex('patient_pedigree_status_id_fk', 'patient_pedigree');
        $this->dropColumn('patient_pedigree', 'status_id');

        $this->dropTable('pedigree_status');
    }
}
