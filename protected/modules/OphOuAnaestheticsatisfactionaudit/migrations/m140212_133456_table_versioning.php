<?php

class m140212_133456_table_versioning extends OEMigration
{
    public function up()
    {
        $this->versionExistingTable('et_ophouanaestheticsataudit_anaesthetis');
        $this->versionExistingTable('et_ophouanaestheticsataudit_notes');
        $this->versionExistingTable('et_ophouanaestheticsataudit_satisfactio');
        $this->versionExistingTable('et_ophouanaestheticsataudit_vitalsigns');
        $this->versionExistingTable('ophouanaestheticsataudit_anaesthetist_lookup');
        $this->versionExistingTable('ophouanaestheticsataudit_notes_ready_for_discharge');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_body_temp');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_conscious_lvl');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_heart_rate');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_oxygen_saturation');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_respiratory_rate');
        $this->versionExistingTable('ophouanaestheticsataudit_vitalsigns_systolic');
    }

    public function down()
    {
        $this->dropTable('et_ophouanaestheticsataudit_anaesthetis_version');
        $this->dropTable('et_ophouanaestheticsataudit_notes_version');
        $this->dropTable('et_ophouanaestheticsataudit_satisfactio_version');
        $this->dropTable('et_ophouanaestheticsataudit_vitalsigns_version');
        $this->dropTable('ophouanaestheticsataudit_anaesthetist_lookup_version');
        $this->dropTable('ophouanaestheticsataudit_notes_ready_for_discharge_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_body_temp_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_conscious_lvl_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_heart_rate_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_oxygen_saturation_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_respiratory_rate_version');
        $this->dropTable('ophouanaestheticsataudit_vitalsigns_systolic_version');
    }
}
