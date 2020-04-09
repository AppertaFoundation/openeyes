<?php

class m170607_204145_gonioscopy_autoreport extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_gonioscopy', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_gonioscopy', 'left_ed_report', 'text');
        $this->addColumn('et_ophciexamination_gonioscopy_version', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_gonioscopy_version', 'left_ed_report', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_gonioscopy_version', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_gonioscopy_version', 'right_ed_report');
        $this->dropColumn('et_ophciexamination_gonioscopy', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_gonioscopy', 'right_ed_report');
    }
}
