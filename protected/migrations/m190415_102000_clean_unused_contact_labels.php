<?php

class m190415_102000_clean_unused_contact_labels extends CDbMigration
{
    public function up()
    {
        $this->execute("DELETE IGNORE FROM contact;
                        DELETE IGNORE FROM contact_label;
                        INSERT IGNORE INTO contact_label (`name`, active) VALUES ('Optometrist', 1);
                        INSERT IGNORE INTO contact_label (`name`, active) VALUES ('Consultant Oncologist', 1);
                        ");
    }

    public function down()
    {
    }
}
