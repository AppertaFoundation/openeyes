<?php

class m201116_044256_add_enabled_column_to_default_sso_rights extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('sso_default_user_rights', 'default_enabled', 'tinyint(1) unsigned NOT NULL DEFAULT 0', true);
    }

    public function down()
    {
        $this->dropOEColumn('sso_default_user_rights', 'default_enabled', true);
    }
}
