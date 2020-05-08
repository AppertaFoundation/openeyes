<?php

class m140617_141435_digital_to_palpation_instrument extends OEMigration
{
    public function up()
    {
        $this->update(
            'ophciexamination_instrument',
            array('name' => 'Palpation'),
            'name = "Digital"'
        );
    }

    public function down()
    {
        $this->update(
            'ophciexamination_instrument',
            array('name' => 'Digital'),
            'name = "Palpation"'
        );
    }
}
