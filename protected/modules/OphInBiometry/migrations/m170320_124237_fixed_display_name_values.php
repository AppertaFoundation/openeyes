<?php

class m170320_124237_fixed_display_name_values extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE ophinbiometry_lenstype_lens SET display_name = name WHERE LOWER(description) = '(created by iol master input)' ");
        $this->execute("UPDATE ophinbiometry_lenstype_lens SET display_name = description WHERE LOWER(description) != '(created by iol master input)' ");
    }

    public function down()
    {
        $this->execute("UPDATE ophinbiometry_lenstype_lens SET display_name = name");
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}