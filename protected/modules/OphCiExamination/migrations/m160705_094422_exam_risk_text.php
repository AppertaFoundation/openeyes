<?php

class m160705_094422_exam_risk_text extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_examinationrisk', 'anticoagulant_name', 'varchar(255)');
        $this->addColumn('et_ophciexamination_examinationrisk_version', 'anticoagulant_name', 'varchar(255)');
        $this->addColumn('et_ophciexamination_examinationrisk', 'alpha_blocker_name', 'varchar(255)');
        $this->addColumn('et_ophciexamination_examinationrisk_version', 'alpha_blocker_name', 'varchar(255)');
    }

    public function down()
    {
        $this->dropColumn('ophtroperationbooking_whiteboard', 'anticoagulant_name');
        $this->dropColumn('ophtroperationbooking_whiteboard_version', 'anticoagulant_name');
        $this->dropColumn('ophtroperationbooking_whiteboard', 'alpha_blocker_name');
        $this->dropColumn('ophtroperationbooking_whiteboard_version', 'alpha_blocker_name');
    }
}
