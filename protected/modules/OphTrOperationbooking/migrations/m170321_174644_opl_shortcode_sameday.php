<?php

class m170321_174644_opl_shortcode_sameday extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode', array('method' => 'getLetterProceduresSameDay'), "default_code = 'opl' and code = 'opl'");
    }

    public function down()
    {
        $this->update('patient_shortcode', array('method' => 'getLetterProcedures'), "default_code = 'opl' and code = 'opl'");
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
