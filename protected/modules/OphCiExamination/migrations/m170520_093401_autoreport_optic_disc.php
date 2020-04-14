<?php

class m170520_093401_autoreport_optic_disc extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_opticdisc', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_opticdisc', 'left_ed_report', 'text');
        $this->addColumn('et_ophciexamination_opticdisc_version', 'right_ed_report', 'text');
        $this->addColumn('et_ophciexamination_opticdisc_version', 'left_ed_report', 'text');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_posteriorpole_version', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_posteriorpole_version', 'right_ed_report');
        $this->dropColumn('et_ophciexamination_posteriorpole', 'left_ed_report');
        $this->dropColumn('et_ophciexamination_posteriorpole', 'right_ed_report');
    }
}
