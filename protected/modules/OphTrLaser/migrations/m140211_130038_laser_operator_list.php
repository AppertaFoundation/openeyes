<?php

class m140211_130038_laser_operator_list extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophtrlaser_laser_operator', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'user_id' => 'int(10) unsigned NOT NULL',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'UNIQUE (`user_id`)',
                'KEY `ophtrlaser_laser_operator_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophtrlaser_laser_operator_cui_fk` (`created_user_id`)',
                'KEY `ophtrlaser_laser_operator_ui_fk` (`user_id`)',
                'CONSTRAINT `ophtrlaser_laser_operator_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrlaser_laser_operator_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophtrlaser_laser_operator_ui_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        foreach ($this->dbConnection->createCommand()
            ->select('user.id')
            ->from('user')
            ->where('is_surgeon = 1')
            ->queryAll() as $row) {
            $this->insert('ophtrlaser_laser_operator', array('user_id' => $row['id']));
        }

        $this->dropForeignKey('et_ophtrlaser_site_surgeon_id_fk', 'et_ophtrlaser_site');
        $this->dropIndex('et_ophtrlaser_site_surgeon_id_fk', 'et_ophtrlaser_site');
        $this->renameColumn('et_ophtrlaser_site', 'surgeon_id', 'operator_id');
        $this->addForeignKey('et_ophtrlaser_site_operator_id_fk', 'et_ophtrlaser_site', 'operator_id', 'user', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophtrlaser_site_operator_id_fk', 'et_ophtrlaser_site');
        $this->dropIndex('et_ophtrlaser_site_operator_id_fk', 'et_ophtrlaser_site');
        $this->renameColumn('et_ophtrlaser_site', 'operator_id', 'surgeon_id');
        $this->addForeignKey('et_ophtrlaser_site_surgeon_id_fk', 'et_ophtrlaser_site', 'surgeon_id', 'user', 'id');

        $this->dropTable('ophtrlaser_laser_operator');
    }
}
