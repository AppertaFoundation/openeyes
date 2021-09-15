<?php

class m210720_164620_alter_tables_to_match_new_version extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY gp_delivery TINYINT(1) DEFAULT 0");
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY la_delivery TINYINT(1) DEFAULT 0");
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY rco_delivery TINYINT(1) DEFAULT 0");
    }

    public function down()
    {
        echo "m210720_164620_alter_tables_to_match_new_version does not support migration down.\n";

        return false;
    }
}
