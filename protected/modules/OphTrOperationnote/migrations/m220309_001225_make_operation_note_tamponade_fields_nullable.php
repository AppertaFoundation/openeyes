<?php

class m220309_001225_make_operation_note_tamponade_fields_nullable extends OEMigration
{
    public function safeUp()
    {
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_type_id', 'int unsigned DEFAULT NULL');
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_percentage_id', 'int unsigned DEFAULT NULL');
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_volume_id', 'int unsigned DEFAULT NULL');

        $this->addColumn('ophtroperationnote_gas_percentage', 'active', 'boolean NOT NULL DEFAULT true');

        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "Air"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "SF6"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "C2F6"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "C3F8"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "PFCL"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "1000c"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "2000c"');
        $this->update('ophtroperationnote_gas_type', array('active' => 0), '`name` = "5000c"');

        $this->insert('ophtroperationnote_gas_type', array('name' => '1300c', 'display_order' => 5));
        $this->insert('ophtroperationnote_gas_type', array('name' => '5500c', 'display_order' => 7));
    }

    public function safeDown()
    {
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_type_id', 'int unsigned');
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_percentage_id', 'int unsigned DEFAULT 0');
        $this->alterColumn('et_ophtroperationnote_tamponade', 'gas_volume_id', 'int unsigned DEFAULT 1');

        $this->dropColumn('ophtroperationnote_gas_percentage', 'active');

        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = Air');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = SF6');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = C2F6');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = C3F8');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = PFCL');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = 1000c');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = 2000c');
        $this->update('ophtroperationnote_gas_type', array('active' => 1), 'name = 5000c');

        $this->delete('ophtroperationnote_gas_type', 'name = 1300c');
        $this->delete('ophtroperationnote_gas_type', 'name = 5500c');
    }
}
