<?php

class m170505_145600_antseg_v2 extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_anteriorsegment', 'right_eyedraw2', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment', 'left_eyedraw2', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment_version', 'right_eyedraw2', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment_version', 'left_eyedraw2', 'text');

        $this->addColumn('et_ophciexamination_anteriorsegment', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment', 'left_ed_report', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment_version', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_anteriorsegment_version', 'left_ed_report', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'right_ed_report');
        $this->dropColumn('et_ophciexamination_anteriorsegment', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_anteriorsegment', 'right_ed_report');

        $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'left_eyedraw2');
        $this->dropColumn('et_ophciexamination_anteriorsegment_version', 'right_eyedraw2');
        $this->dropColumn('et_ophciexamination_anteriorsegment', 'left_eyedraw2');
        $this->dropColumn('et_ophciexamination_anteriorsegment', 'right_eyedraw2');
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