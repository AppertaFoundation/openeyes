<?php

class m160915_140800_add_alert_dismiss_flag_VA extends CDbMigration
{
    public function safeUp()
    {
            $this->addColumn('et_ophciexamination_visualacuity', 'cvi_alert_dismissed', 'TINYINT(1)');
            $this->addColumn('et_ophciexamination_visualacuity_version', 'cvi_alert_dismissed', 'TINYINT(1)');
    }

    public function safeDown()
    {
        $this->dropColumn('et_ophciexamination_visualacuity', 'cvi_alert_dismissed');
        $this->dropColumn('et_ophciexamination_visualacuity_version', 'cvi_alert_dismissed');
    }
}
