<?php

class m220211_160000_add_edd_subspecialty extends OEMigration
{
    public function safeUp()
    {
        $this->insert('subspecialty', array(
            'name' => "Electrophysiology Department",
            'short_name' => "EDD",
            'ref_spec' => "ED",
            'specialty_id' => 109
        ));


        $this->update('subspecialty',
            array('specialty_id' => 109),
            "name = 'Optometry'"
        );

        $this->update('subspecialty',
            array('specialty_id' => 109),
            "name = 'Pharmacy'"
        );

        $this->update('subspecialty',
            array('specialty_id' => 109),
            "name = 'Orthoptics'"
        );
    }

    public function safeDown()
    {
        $this->delete('subspecialty', 'name = "Electrophysiology Department"');

        $this->update('subspecialty',
            array('specialty_id' => 267),
            "name = 'Optometry'"
        );

        $this->update('subspecialty',
            array('specialty_id' => 267),
            "name = 'Pharmacy'"
        );

        $this->update('subspecialty',
            array('specialty_id' => 267),
            "name = 'Orthoptics'"
        );
    }
}
