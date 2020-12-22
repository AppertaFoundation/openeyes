<?php

class m161103_153904_nullable_doctor_id extends OEMigration
{
    public function up()
    {
        $this->alterColumn('et_ophciexamination_pcr_risk', 'left_doctor_grade_id', 'int(11) null');
        $this->alterColumn('et_ophciexamination_pcr_risk_version', 'left_doctor_grade_id', 'int(11) null');
        $this->alterColumn('et_ophciexamination_pcr_risk', 'right_doctor_grade_id', 'int(11) null');
        $this->alterColumn('et_ophciexamination_pcr_risk_version', 'right_doctor_grade_id', 'int(11) null');
    }

    public function down()
    {
        $this->alterColumn('et_ophciexamination_pcr_risk', 'left_doctor_grade_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_pcr_risk_version', 'left_doctor_grade_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_pcr_risk', 'right_doctor_grade_id', 'int(11)');
        $this->alterColumn('et_ophciexamination_pcr_risk_version', 'right_doctor_grade_id', 'int(11)');
    }

}
