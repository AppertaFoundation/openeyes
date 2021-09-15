<?php

class m191004_110000_describe_ethnic extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('et_ophcocvi_demographics', 'describe_ethnics', 'varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL', true);
    }

    public function down()
    {
        $this->dropOEColumn('et_ophcocvi_demographics', 'describe_ethnics', true);
    }
}
