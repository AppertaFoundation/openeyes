<?php

class m191004_110000_describe_ethnic extends CDbMigration
{
    public function up()
    {
	$this->addColumn('et_ophcocvi_demographics', 'describe_ethnics', 'varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL');    
	$this->addColumn('et_ophcocvi_demographics_version', 'describe_ethnics', 'varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL');    
    }

    public function down()
    {
        $this->dropColumn('et_ophcocvi_demographics_version', 'describe_ethnics');
        $this->dropColumn('et_ophcocvi_demographics', 'describe_ethnics');
    }
}