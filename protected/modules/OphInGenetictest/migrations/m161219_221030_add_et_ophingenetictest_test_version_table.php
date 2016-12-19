<?php

class m161219_221030_add_et_ophingenetictest_test_version_table extends CDbMigration
{
	public function up()
	{
	    
        $this->createTable('et_ophingenetictest_test_version', array(
            'id' => 'int(10) unsigned NOT NULL',
            'event_id' => 'int(10) unsigned NOT NULL',
            'gene_id' => 'int(10) unsigned DEFAULT NULL',
            'method_id' => 'int(10) unsigned DEFAULT NULL',
            'comments' => 'varchar(2048) COLLATE utf8_bin DEFAULT NULL',
            'exon' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'dna_quality' => ' float DEFAULT NULL',
            'dna_quantity' => 'float DEFAULT NULL',
            'prime_rf' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'prime_rr' => ' varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'base_change' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'amino_acid_change' => ' varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'assay' => 'varchar(64) COLLATE utf8_bin DEFAULT NULL',
            'effect_id' => 'int(10) unsigned DEFAULT NULL',
            'homo' => 'tinyint(1) unsigned DEFAULT NULL',
            'result' => "varchar(255) COLLATE utf8_bin DEFAULT ''",
            'result_date' => 'date DEFAULT NULL',
            'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
            'last_modified_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
            'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
            'created_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
            'external_source_id' => 'int(11) DEFAULT NULL',
            'version_date' => 'datetime NOT NULL',
            'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            
            'KEY `et_ophingenetictest_test_version_lmui_fk` (`last_modified_user_id`)',
            'KEY `et_ophingenetictest_test_version_cui_fk` (`created_user_id`)',
            'KEY `et_ophingenetictest_test_version_ev_fk` (`event_id`)',
            'KEY `et_ophingenetictest_test_version_ge_fk` (`gene_id`)',
            'KEY `et_ophingenetictest_test_version_me_fk` (`method_id`)',
            'KEY `et_ophingenetictest_test_version_ef_fk` (`effect_id`)',
            'KEY `et_ophingenetictest_test_version_external_source_id` (`external_source_id`)',
            
            'KEY `et_ophingenetictest_test_version_vi_fk` (`version_id`)',
            
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');	    
        
	}

	public function down()
	{
		$this->dropTable('et_ophingenetictest_test_version');
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}