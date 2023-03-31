<?php

class m230320_132400_activate_all_gas_types extends OEMigration
{
    public function safeUp()
    {
        $this->update('ophtroperationnote_gas_type', array('active' => 1));

        // insert value 0.5 into openeyes.ophtroperationnote_gas_volume and set display_order to 5
        $this->insert('ophtroperationnote_gas_volume', array('value' => 0.5, 'display_order' => 5));

        return true;
    }

    public function down()
    {
        // remove value 0.5 from openeyes.ophtroperationnote_gas_volume
        $this->delete('ophtroperationnote_gas_volume', 'value = 0.5');

        echo("gas type active status cannot be reverted.\n");

        return true;
    }
}
