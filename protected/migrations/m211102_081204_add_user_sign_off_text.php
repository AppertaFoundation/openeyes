<?php

class m211102_081204_add_user_sign_off_text extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            "user",
            "correspondence_sign_off_text",
            "TINYTEXT NULL DEFAULT 'Sincerely,\\n'",
            true
        );
    }

    public function down()
    {
        $this->dropOEColumn(
            "user",
            "correspondence_sign_off_text",
            true
        );
    }
}
