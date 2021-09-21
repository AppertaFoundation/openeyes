<?php

class m161220_141625_add_et_ophindnasample_sample_version_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('et_ophindnasample_sample_version', array(
          '`id` int(10) unsigned NOT NULL',
          '`event_id` int(10) unsigned NOT NULL',
          '`old_dna_no` int(10) unsigned DEFAULT NULL',
          '`blood_date` date DEFAULT NULL',
          '`comments` text COLLATE utf8_unicode_ci',
          "`type_id` int(10) unsigned NOT NULL DEFAULT '1'",
          '`volume` float NOT NULL',
          "`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1'",
          "`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
          "`created_user_id` int(10) unsigned NOT NULL DEFAULT '1'",
          "`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
          '`other_sample_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL',
          '`consented_by` int(11) DEFAULT NULL',
          '`is_local` tinyint(1) DEFAULT NULL',
          '`destination` text COLLATE utf8_unicode_ci',
          'version_date' => 'datetime NOT NULL',
          'version_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',

          'KEY `et_ophinbloodsample_sample_version_lmui_fk` (`last_modified_user_id`)',
          'KEY `et_ophinbloodsample_sample_version_cui_fk` (`created_user_id`)',
          'KEY `et_ophinbloodsample_sample_version_ev_fk` (`event_id`)',
          'KEY `et_ophindnasample_sample_version_vi_fk` (`version_id`)',


        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
    }

    public function down()
    {
        $this->dropTable('et_ophindnasample_sample_version');
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
