<?php

class m160802_094027_type_specification_for_visual_acuity_method extends CDbMigration
{
    private $table = 'ophciexamination_visualacuity_method';
    public function up()
    {

        $this->addColumn('ophciexamination_visualacuity_method', 'type', 'TINYINT(1) NOT NULL DEFAULT 3');
        $this->execute("UPDATE ".$this->table." SET type = 1 where name = 'Unaided'");
        $this->execute("UPDATE ".$this->table." SET type = 2 where name = 'Contact lens' or name = 'Glasses'");
    }

    public function down()
    {
        $this->dropColumn('ophciexamination_visualacuity_method', 'type');
    }
}
