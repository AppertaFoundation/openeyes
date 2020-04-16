<?php

class m180208_034437_add_element_type_tile extends OEMigration
{
    public function safeUp()
    {
        $this->addColumn('element_type', 'tile_size', 'int(2) UNSIGNED');
        $this->addColumn('element_type_version', 'tile_size', 'int(2) UNSIGNED');
    }

    public function safeDown()
    {
        $this->dropColumn('element_type', 'tile_size');
        $this->dropColumn('element_type_version', 'tile_size');
    }
}
