<?php

class m170830_100559_remove_STFB_lids_medical extends CDbMigration
{
    public function up()
    {
        $this->dropColumn('et_ophciexamination_medical_lids', 'right_stfb');
        $this->dropColumn('et_ophciexamination_medical_lids', 'left_stfb');
        $this->dropColumn('et_ophciexamination_medical_lids_version', 'right_stfb');
        $this->dropColumn('et_ophciexamination_medical_lids_version', 'left_stfb');
    }

    public function down()
    {
        echo "m170830_100559_remove_STFB_lids_medical does not support migration down.\n";
        return false;
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
