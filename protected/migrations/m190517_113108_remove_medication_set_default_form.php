<?php

class m190517_113108_remove_medication_set_default_form extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE medication_set_item SET default_form_id = NULL WHERE 1=1");
    }

    public function down()
    {
        echo "m190517_113108_remove_medication_set_default_form does not support migration down.\n";
        return false;
    }
}
