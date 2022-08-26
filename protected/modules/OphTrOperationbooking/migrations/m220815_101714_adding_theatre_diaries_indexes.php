<?php

class m220815_101714_adding_theatre_diaries_indexes extends OEMigration
{
    public function safeUp()
    {
        if ($this->dbConnection->createCommand("SHOW INDEX FROM ophtroperationbooking_operation_theatre WHERE Key_name = 'idx_ophtroperationbooking_operation_theatre_active'")->queryScalar()) {
            $this->execute("DROP INDEX `idx_ophtroperationbooking_operation_theatre_active` ON ophtroperationbooking_operation_theatre");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM ophtroperationbooking_operation_session WHERE Key_name = 'idx_ophtroperationbooking_operation_session_date'")->queryScalar()) {
            $this->execute("DROP INDEX `idx_ophtroperationbooking_operation_session_date` ON ophtroperationbooking_operation_session");
        }
        if ($this->dbConnection->createCommand("SHOW INDEX FROM et_ophinbiometry_measurement WHERE Key_name = 'idx_et_ophinbiometry_measurement_lmd'")->queryScalar()) {
            $this->execute("DROP INDEX `idx_et_ophinbiometry_measurement_lmd` ON et_ophinbiometry_measurement");
        }
        $this->execute("CREATE INDEX idx_ophtroperationbooking_operation_theatre_active ON ophtroperationbooking_operation_theatre(active);");
        $this->execute("CREATE INDEX idx_ophtroperationbooking_operation_session_date ON ophtroperationbooking_operation_session(date);");
        $this->execute("CREATE INDEX idx_et_ophinbiometry_measurement_lmd ON et_ophinbiometry_measurement(last_modified_date);");
    }

    public function safeDown()
    {
        $this->execute("DROP INDEX IF EXISTS idx_ophtroperationbooking_operation_theatre_active ON ophtroperationbooking_operation_theatre;");
        $this->execute("DROP INDEX IF EXISTS idx_ophtroperationbooking_operation_session_date ON ophtroperationbooking_operation_session;");
        $this->execute("DROP INDEX IF EXISTS idx_et_ophinbiometry_measurement_lmd ON et_ophinbiometry_measurement;");
    }
}
