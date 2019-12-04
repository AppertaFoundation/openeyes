<?php

class m170331_085338_change_contact_table_collation extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE contact MODIFY first_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->execute("ALTER TABLE contact MODIFY last_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE contact MODIFY first_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_bin;");
        $this->execute("ALTER TABLE contact MODIFY last_name VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_bin;");
    }
}