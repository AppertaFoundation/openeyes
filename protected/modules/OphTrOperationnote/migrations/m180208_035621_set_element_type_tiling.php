<?php

class m180208_035621_set_element_type_tiling extends OEMigration
{
    private $class_prefix = 'Element_OphTrOperationnote_';

    private $tiled_elements = array(
        'Comments' => 2,
        'PostOpDrugs' => 1,
    );

    public function up()
    {
        foreach ($this->tiled_elements as $element => $tile_size) {
            $this->update('element_type', array('tile_size' => $tile_size),
                "class_name = '{$this->class_prefix}{$element}'");
        }
    }

    public function down()
    {
        foreach ($this->tiled_elements as $element => $tile_size) {
            $this->update('element_type', array('tile_size' => null),
                "class_name = '{$this->class_prefix}{$element}'");
        }
    }
}