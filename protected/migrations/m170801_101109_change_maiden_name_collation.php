<?php

class m170801_101109_change_maiden_name_collation extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE contact MODIFY maiden_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci AFTER last_name;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE contact MODIFY maiden_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_bin AFTER contact_label_id;");
    }
}