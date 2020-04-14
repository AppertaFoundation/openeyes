<?php

class m170401_110717_autoreport_eyedraw_fundus extends CDbMigration
{

    public function up()
    {
        $this->addColumn('et_ophciexamination_fundus', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_fundus', 'left_ed_report', 'text');
        $this->addColumn('et_ophciexamination_fundus_version', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_fundus_version', 'left_ed_report', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_fundus_version', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_fundus_version', 'right_ed_report');
        $this->dropColumn('et_ophciexamination_fundus', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_fundus', 'right_ed_report');
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
