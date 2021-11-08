<?php

class m161012_084258_add_pupillary_abnormalities extends CDbMigration
{
    private $table = 'ophciexamination_pupillaryabnormalities_abnormality';

    private $pupillary_abnormality = 'Poor Dilator';

    public function up()
    {
        $this->insert($this->table, array('name' => $this->pupillary_abnormality, 'display_order' =>'60'));
    }

    public function down()
    {
        $this->delete($this->table, 'name = :name', array(':name' => $this->pupillary_abnormality));
    }
}
