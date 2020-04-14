<?php

class m180918_042840_optic_disc_additional_options extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->insert('ophciexamination_opticdisc_lens', array(
            'name' => 'Superfield',
            'display_order' => '40'
        ));
        $this->insert('ophciexamination_opticdisc_lens', array(
            'name' => 'Super 66D',
            'display_order' => '50'
        ));
        $this->insert('ophciexamination_opticdisc_lens', array(
            'name' => 'Digital WideField',
            'display_order' => '60'
        ));
        $this->insert('ophciexamination_opticdisc_lens', array(
            'name' => 'Digital High Mag',
            'display_order' => '70'
        ));
    }

    public function safeDown()
    {
        $this->delete('ophciexamination_opticdisc_lens', 'name = \'Superfield\'');
        $this->delete('ophciexamination_opticdisc_lens', 'name = \'Super 66D\'');
        $this->delete('ophciexamination_opticdisc_lens', 'name = \'Digital WideField\'');
        $this->delete('ophciexamination_opticdisc_lens', 'name = \'Digital High Mag\'');
    }

}
