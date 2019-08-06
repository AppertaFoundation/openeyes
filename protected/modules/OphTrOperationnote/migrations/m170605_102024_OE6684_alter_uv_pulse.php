<?php

class m170605_102024_OE6684_alter_uv_pulse extends OEMigration
{
    public function up()
    {
        // Add in nameTemp field
        $this->addColumn('ophtroperationnote_cxl_uv_pulse_duration',
            'nameTemp', 'VARCHAR(128)');
        $this->addColumn('ophtroperationnote_cxl_uv_pulse_duration_version',
            'nameTemp', 'VARCHAR(128)');
        echo "nameTemp column added to ophtroperationnote_cxl_uv_pulse_duration";
        // Add in data
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '0 seconds'), 'id = 1');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '0.5 seconds'), 'id = 2');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '1 second'), 'id = 3');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '1.5 seconds'), 'id = 4');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '2 seconds'), 'id = 5');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '2.5 seconds'), 'id = 6');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '3 seconds'), 'id = 7');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '3.5 seconds'), 'id = 8');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '4 seconds'), 'id = 9');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '4.5 seconds'), 'id = 10');
        $this->update('ophtroperationnote_cxl_uv_pulse_duration',
            array('nameTemp' => '5 seconds'), 'id = 11');

        echo "Data added to nameTemp column";


        // Rename name to nameOLD
        $this->renameColumn('ophtroperationnote_cxl_uv_pulse_duration',
            'name', 'nameOLD');
        $this->renameColumn('ophtroperationnote_cxl_uv_pulse_duration_version',
            'name', 'nameOLD');
        echo "name column renamed to nameOLD";

        // Rename nameTemp to name
        $this->renameColumn('ophtroperationnote_cxl_uv_pulse_duration', 'nameTemp', 'name');
        $this->renameColumn('ophtroperationnote_cxl_uv_pulse_duration_version', 'nameTemp', 'name');
        echo "nameTemp column renamed to name";

        //Drop nameOLD
        $this->dropColumn('ophtroperationnote_cxl_uv_pulse_duration', 'nameOLD');
        $this->dropColumn('ophtroperationnote_cxl_uv_pulse_duration_version', 'nameOLD');
    }

    public function down()
    {
        echo "m170605_102024_OE6684_alter_uv_pulse does not support migration down.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
