<?php

class m180601_025125_remove_cxl_trabectome_default_flags extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE element_type SET `default` = 0 WHERE `class_name` = 'Element_OphTrOperationnote_CXL'");
        $this->execute("UPDATE element_type SET `default` = 0 WHERE `class_name` = 'Element_OphTrOperationnote_Trabectome'");
        $this->execute("UPDATE element_type SET `default` = 0 WHERE `class_name` = 'Element_OphTrOperationnote_VteAssessment'");

    }

    public function down()
    {
        $this->execute("UPDATE element_type SET `default` = 1 WHERE `class_name` = 'Element_OphTrOperationnote_CXL'");
        $this->execute("UPDATE element_type SET `default` = 1 WHERE `class_name` = 'Element_OphTrOperationnote_Trabectome'");
        $this->execute("UPDATE element_type SET `default` = 1 WHERE `class_name` = 'Element_OphTrOperationnote_VteAssessment'");

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