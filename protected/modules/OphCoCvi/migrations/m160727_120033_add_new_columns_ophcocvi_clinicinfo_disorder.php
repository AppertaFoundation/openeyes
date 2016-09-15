<?php

class m160727_120033_add_new_columns_ophcocvi_clinicinfo_disorder extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clinicinfo_disorder', 'code', 'varchar(20) NOT NULL AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_disorder', 'section_id', 'int(10) unsigned NOT NULL AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_disorder', 'active', 'tinyint(1) unsigned not null default 1 AFTER `section_id` ');
        $this->addColumn('ophcocvi_clinicinfo_disorder_version', 'code', 'varchar(20) NOT NULL AFTER `name` ');
        $this->addColumn('ophcocvi_clinicinfo_disorder_version', 'section_id', 'int(12) NOT NULL AFTER `code` ');
        $this->addColumn('ophcocvi_clinicinfo_disorder_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `section_id`');
    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clinicinfo_disorder', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_disorder', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_disorder', 'section_id');
        $this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'active');
        $this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'code');
        $this->dropColumn('ophcocvi_clinicinfo_disorder_version', 'section_id');
    }
}
