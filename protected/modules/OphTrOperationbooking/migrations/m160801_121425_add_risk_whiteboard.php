<?php

class m160801_121425_add_risk_whiteboard extends CDbMigration
{
    public function up()
    {
        $columns = $this->dbConnection->createCommand('SHOW COLUMNS FROM `ophtroperationbooking_whiteboard` LIKE "anticoagulant_name"')->execute();

        if ($columns === 0) {
            $this->addColumn('ophtroperationbooking_whiteboard', 'anticoagulant_name', 'varchar(255)');
            $this->addColumn('ophtroperationbooking_whiteboard_version', 'anticoagulant_name', 'varchar(255)');
            $this->addColumn('ophtroperationbooking_whiteboard', 'alpha_blocker_name', 'varchar(255)');
            $this->addColumn('ophtroperationbooking_whiteboard_version', 'alpha_blocker_name', 'varchar(255)');
        }
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_examinationrisk', 'anticoagulant_name');
        $this->dropColumn('et_ophciexamination_examinationrisk_version', 'anticoagulant_name');
        $this->dropColumn('et_ophciexamination_examinationrisk', 'alpha_blocker_name');
        $this->dropColumn('et_ophciexamination_examinationrisk_version', 'alpha_blocker_name');
    }
}
