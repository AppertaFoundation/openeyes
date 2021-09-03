<?php

class m200814_044003_create_out_of_office_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('user_out_of_office', array(
            'id' => 'pk',
            'user_id' => 'int(10) unsigned NOT NULL',
            'from_date' => 'datetime',
            'to_date' => 'datetime',
            'alternate_user_id' => 'int(10) unsigned',
            'enabled' => 'tinyint(1) unsigned DEFAULT 0',
            'CONSTRAINT `user_out_of_office_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `user_out_of_office_alternate_user_id_fk` FOREIGN KEY (`alternate_user_id`) REFERENCES `user` (`id`)',
        ), true);
    }

    public function down()
    {
        $this->dropOETable('user_out_of_office', true);
    }
}
