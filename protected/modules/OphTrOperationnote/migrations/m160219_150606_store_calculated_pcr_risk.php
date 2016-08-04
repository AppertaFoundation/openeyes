<?php

class m160219_150606_store_calculated_pcr_risk extends OEMigration
{
    public function up()
    {
        $this->addColumn('et_ophtroperationnote_cataract', 'pcr_risk', 'DECIMAL(5,2)');
        $this->addColumn('et_ophtroperationnote_cataract_version', 'pcr_risk', 'DECIMAL(5,2)');
    }

    public function down()
    {
        $this->dropColumn('et_ophtroperationnote_cataract_version', 'pcr_risk');
        $this->dropColumn('et_ophtroperationnote_cataract', 'pcr_risk');
    }
}
