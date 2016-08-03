<?php

class m140722_141400_iol_metadata extends OEMigration
{
    public function up()
    {
        $this->delete('ophinbiometry_lenstype_lens');

        $this->createTable('ophinbiometry_lens_position', array(
                'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
                'name' => 'varchar(255) NOT NULL',
                'display_order' => 'tinyint(1) unsigned not null',
                'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
                'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
                'PRIMARY KEY (`id`)',
                'KEY `ophinbiometry_lens_position_lmui_fk` (`last_modified_user_id`)',
                'KEY `ophinbiometry_lens_position_cui_fk` (`created_user_id`)',
                'CONSTRAINT `ophinbiometry_lens_position_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
                'CONSTRAINT `ophinbiometry_lens_position_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addColumn('ophinbiometry_lenstype_lens', 'description', 'varchar(255) NOT NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'position_id', 'int(10) unsigned NOT NULL');
        $this->createIndex('ophinbiometry_lenstype_lens_position_id_fk', 'ophinbiometry_lenstype_lens', 'position_id');
        $this->addForeignKey('ophinbiometry_lenstype_lens_position_id_fk', 'ophinbiometry_lenstype_lens', 'position_id', 'ophinbiometry_lens_position', 'id');
        $this->addColumn('ophinbiometry_lenstype_lens', 'comments', 'varchar(1024) NOT NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'acon', 'float NOT NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'sf', 'float NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'pACD', 'float NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'a0', 'float NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'a1', 'float NULL');
        $this->addColumn('ophinbiometry_lenstype_lens', 'a2', 'float NULL');

        $this->initialiseData(dirname(__FILE__));
    }

    public function down()
    {
        $this->dropColumn('ophinbiometry_lenstype_lens', 'description');
        $this->dropForeignKey('ophinbiometry_lenstype_lens_position_id_fk', 'ophinbiometry_lenstype_lens');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'position_id');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'comments');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'acon');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'sf');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'pACD');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'a0');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'a1');
        $this->dropColumn('ophinbiometry_lenstype_lens', 'a2');

        $this->dropTable('ophinbiometry_lens_position');
    }
}
