<?php

class m220810_080050_et_ophciexamination_pcr_risk_pupil_size_nullable extends OEMigration
{
    public function safeUp()
    {
        $this->alterOEColumn('et_ophciexamination_pcr_risk', 'left_pupil_size', 'VARCHAR(10)', true);
        $this->alterOEColumn('et_ophciexamination_pcr_risk', 'right_pupil_size', 'VARCHAR(10)', true);
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophciexamination_pcr_risk', 'left_pupil_size', 'VARCHAR(10) NOT NULL', true);
        $this->alterOEColumn('et_ophciexamination_pcr_risk', 'right_pupil_size', 'VARCHAR(10) NOT NULL', true);
    }
}
