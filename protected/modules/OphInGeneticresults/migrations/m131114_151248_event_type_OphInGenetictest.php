<?php

class m131114_151248_event_type_OphInGenetictest extends OEMigration
{
    public function up()
    {
        if (!Yii::app()->hasModule('Genetics')) {
            echo '
            -----------------------------------
            Skipping OphInGenetictest - missing module dependency
            -----------------------------------
            ';

            return false;
            //throw new Exception("OphTrIntravitrealinjection is required for this module to work");
        }

        $parent_id = null;

        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name' => 'OphInGenetictest'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name' => 'Investigation events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphInGenetictest', 'name' => 'Genetic Results', 'event_group_id' => $group['id'], 'parent_id' => $parent_id));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name=:class_name',
            array(':class_name' => 'OphInGenetictest')
        )->queryRow();
        if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where(
            'name=:name and event_type_id=:eventTypeId',
            array(':name' => 'Test', ':eventTypeId' => $event_type['id'])
        )->queryRow()
        ) {
            $this->insert('element_type', array('name' => 'Test', 'class_name' => 'Element_OphInGenetictest_Test', 'event_type_id' => $event_type['id'], 'display_order' => 1));
        }

        $element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where(
            'event_type_id=:eventTypeId and name=:name',
            array(':eventTypeId' => $event_type['id'], ':name' => 'Test')
        )->queryRow();

        $this->createTable('ophingenetictest_test_method', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophingenetictest_test_method_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophingenetictest_test_method_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophingenetictest_test_method_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophingenetictest_test_method_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('ophingenetictest_test_effect', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `ophingenetictest_test_effect_lmui_fk` (`last_modified_user_id`)',
            'KEY `ophingenetictest_test_effect_cui_fk` (`created_user_id`)',
            'CONSTRAINT `ophingenetictest_test_effect_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `ophingenetictest_test_effect_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->createTable('et_ophingenetictest_test', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'event_id' => 'int(10) unsigned NOT NULL',
            'gene_id' => 'int(10) unsigned NULL',
            'method_id' => 'int(10) unsigned NULL',
            'comments' => 'varchar(2048) collate utf8_bin',
            'exon' => 'varchar(64) collate utf8_bin',
            'prime_rf' => 'varchar(64) collate utf8_bin',
            'prime_rr' => 'varchar(64) collate utf8_bin',
            'base_change' => 'varchar(64) collate utf8_bin',
            'amino_acid_change' => 'varchar(64) collate utf8_bin',
            'assay' => 'varchar(64) collate utf8_bin',
            'effect_id' => 'int(10) unsigned NULL',
            'homo' => 'tinyint(1) unsigned',
            'result' => 'varchar(255) COLLATE utf8_bin DEFAULT \'\'',
            'result_date' => 'date',
            'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
            'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
            'PRIMARY KEY (`id`)',
            'KEY `et_ophingenetictest_test_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophingenetictest_test_cui_fk` (`created_user_id`)',
            'KEY `et_ophingenetictest_test_ev_fk` (`event_id`)',
            'KEY `et_ophingenetictest_test_ge_fk` (`gene_id`)',
            'KEY `et_ophingenetictest_test_me_fk` (`method_id`)',
            'KEY `et_ophingenetictest_test_ef_fk` (`effect_id`)',
            'CONSTRAINT `et_ophingenetictest_test_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophingenetictest_test_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
            'CONSTRAINT `et_ophingenetictest_test_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            'CONSTRAINT `et_ophingenetictest_test_ge_fk` FOREIGN KEY (`gene_id`) REFERENCES `pedigree_gene` (`id`)',
            'CONSTRAINT `et_ophingenetictest_test_me_fk` FOREIGN KEY (`method_id`) REFERENCES `ophingenetictest_test_method` (`id`)',
            'CONSTRAINT `et_ophingenetictest_test_ef_fk` FOREIGN KEY (`effect_id`) REFERENCES `ophingenetictest_test_effect` (`id`)',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

        $this->initialiseData(dirname(__FILE__));
    }

    public function down()
    {
        $this->dropTable('et_ophingenetictest_test');
        $this->dropTable('ophingenetictest_test_method');
        $this->dropTable('ophingenetictest_test_effect');

        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name=:class_name',
            array(':class_name' => 'OphInGenetictest')
        )->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where(
            'event_type_id=:event_type_id',
            array(':event_type_id' => $event_type['id'])
        )->queryAll() as $row) {
            $this->delete('audit', 'event_id=' . $row['id']);
            $this->delete('event', 'id=' . $row['id']);
        }

        $this->delete('element_type', 'event_type_id=' . $event_type['id']);
        $this->delete('event_type', 'id=' . $event_type['id']);
    }
}
