<?php

class m160705_094422_exam_risk_text extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_examinationrisk', 'anticoagulant_name', 'varchar(255)');
        $this->addColumn('et_ophciexamination_examinationrisk_version', 'anticoagulant_name', 'varchar(255)');

        $this->addColumn('et_ophciexamination_examinationrisk', 'alphablocker_name', 'varchar(255)');
        $this->addColumn('et_ophciexamination_examinationrisk_version', 'alphablocker_name', 'varchar(255)');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_examinationrisk', 'anticoagulant_name');
        $this->dropColumn('et_ophciexamination_examinationrisk_version', 'anticoagulant_name');

        $this->dropColumn('et_ophciexamination_examinationrisk', 'alphablocker_name');
        $this->dropColumn('et_ophciexamination_examinationrisk_version', 'alphablocker_name');
    }
}