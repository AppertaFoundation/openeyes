<?php

class m141113_103317_sphere_cylinder_ranges extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_refraction_integer', 'sign_id', 'int(10) unsigned not null');
        $this->update('ophciexamination_refraction_integer', array('sign_id' => 1));

        $this->createIndex('ophciexamination_refraction_sphere_integer_sign_id_fk', 'ophciexamination_refraction_integer', 'sign_id');
        $this->addForeignKey('ophciexamination_refraction_sphere_integer_sign_id_fk', 'ophciexamination_refraction_integer', 'sign_id', 'ophciexamination_refraction_sign', 'id');

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_refraction_integer')->where('sign_id=1')->order('id asc')->queryAll() as $i) {
            $this->insert('ophciexamination_refraction_integer', array(
                'value' => $i['value'],
                'display_order' => $i['display_order'],
                'sign_id' => 2,
            ));
        }

        $this->dropForeignKey('ophciexamination_refraction_integer_cui_fk', 'ophciexamination_refraction_integer');
        $this->dropForeignKey('ophciexamination_refraction_integer_lmui_fk', 'ophciexamination_refraction_integer');

        $this->dropIndex('ophciexamination_refraction_integer_cui_fk', 'ophciexamination_refraction_integer');
        $this->dropIndex('ophciexamination_refraction_integer_lmui_fk', 'ophciexamination_refraction_integer');

        $this->renameTable('ophciexamination_refraction_integer', 'ophciexamination_refraction_sphere_integer');
        $this->renameTable('ophciexamination_refraction_integer_version', 'ophciexamination_refraction_sphere_integer_version');

        $this->createIndex('ophciexamination_refraction_sphere_integer_cui_fk', 'ophciexamination_refraction_sphere_integer', 'created_user_id');
        $this->createIndex('ophciexamination_refraction_sphere_integer_lmui_fk', 'ophciexamination_refraction_sphere_integer', 'last_modified_user_id');

        $this->addForeignKey('ophciexamination_refraction_sphere_integer_cui_fk', 'ophciexamination_refraction_sphere_integer', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_refraction_sphere_integer_lmui_fk', 'ophciexamination_refraction_sphere_integer', 'last_modified_user_id', 'user', 'id');

        $this->createTable('ophciexamination_refraction_cylinder_integer', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'value' => 'varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL',
                'display_order' => 'tinyint(3) unsigned DEFAULT 0',
                'sign_id' => 'int(10) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophciexamination_refraction_cylinder_integer_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophciexamination_refraction_cylinder_integer_cui_fk` (`created_user_id`)',
                'KEY `ophciexamination_refraction_cylinder_integer_sign_id_fk` (`sign_id`)',
                'CONSTRAINT `ophciexamination_refraction_cylinder_integer_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_refraction_cylinder_integer_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophciexamination_refraction_cylinder_integer_sign_id_fk` FOREIGN KEY (`sign_id`) REFERENCES `ophciexamination_refraction_sign` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->versionExistingTable('ophciexamination_refraction_cylinder_integer');

        foreach ($this->dbConnection->createCommand()->select('*')->from('ophciexamination_refraction_sphere_integer')->order('id asc')->queryAll() as $row) {
            $this->insert('ophciexamination_refraction_cylinder_integer', $row);
        }
    }

    public function down()
    {
        $this->dropTable('ophciexamination_refraction_cylinder_integer_version');
        $this->dropTable('ophciexamination_refraction_cylinder_integer');

        $this->dropForeignKey('ophciexamination_refraction_sphere_integer_cui_fk', 'ophciexamination_refraction_sphere_integer');
        $this->dropForeignKey('ophciexamination_refraction_sphere_integer_lmui_fk', 'ophciexamination_refraction_sphere_integer');

        $this->dropIndex('ophciexamination_refraction_sphere_integer_cui_fk', 'ophciexamination_refraction_sphere_integer');
        $this->dropIndex('ophciexamination_refraction_sphere_integer_lmui_fk', 'ophciexamination_refraction_sphere_integer');

        $this->renameTable('ophciexamination_refraction_sphere_integer', 'ophciexamination_refraction_integer');
        $this->renameTable('ophciexamination_refraction_sphere_integer_version', 'ophciexamination_refraction_integer_version');

        $this->createIndex('ophciexamination_refraction_integer_cui_fk', 'ophciexamination_refraction_integer', 'created_user_id');
        $this->createIndex('ophciexamination_refraction_integer_lmui_fk', 'ophciexamination_refraction_integer', 'last_modified_user_id');

        $this->addForeignKey('ophciexamination_refraction_integer_cui_fk', 'ophciexamination_refraction_integer', 'created_user_id', 'user', 'id');
        $this->addForeignKey('ophciexamination_refraction_integer_lmui_fk', 'ophciexamination_refraction_integer', 'last_modified_user_id', 'user', 'id');

        $this->delete('ophciexamination_refraction_integer', 'sign_id = 2');

        $this->dropForeignKey('ophciexamination_refraction_sphere_integer_sign_id_fk', 'ophciexamination_refraction_integer');
        $this->dropColumn('ophciexamination_refraction_integer', 'sign_id');
    }
}
