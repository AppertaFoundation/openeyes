<?php

class m200128_111511_add_guarded_prognosis_to_cataract_surgical_management extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophciexamination_cataractsurgicalmanagement', 'left_guarded_prognosis', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->addColumn('et_ophciexamination_cataractsurgicalmanagement', 'right_guarded_prognosis', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->addColumn('et_ophciexamination_cataractsurgicalmanagement_version', 'left_guarded_prognosis', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->addColumn('et_ophciexamination_cataractsurgicalmanagement_version', 'right_guarded_prognosis', 'TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn('et_ophciexamination_cataractsurgicalmanagement', 'left_guarded_prognosis');
        $this->dropColumn('et_ophciexamination_cataractsurgicalmanagement', 'right_guarded_prognosis');
        $this->dropColumn('et_ophciexamination_cataractsurgicalmanagement_version', 'left_guarded_prognosis');
        $this->dropColumn('et_ophciexamination_cataractsurgicalmanagement_version', 'right_guarded_prognosis');
    }
}
