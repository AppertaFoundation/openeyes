<?php

class m211026_100543_add_nine_postions_zero_movement extends OEMigration
{
    public function up()
    {
        $this->execute('UPDATE ophciexamination_ninepositions_movement SET display_order = display_order + 1 WHERE display_order > 6');
        $this->insert('ophciexamination_ninepositions_movement', [
            'name' => '0',
            'display_order' => 7,
            'active' => 1,
        ]);
    }

    public function down()
    {
        $this->delete('ophciexamination_ninepositions_movement', 'name = ? ', '0');
        $this->execute('UPDATE ophciexamination_ninepositions_movement SET display_order = id');
    }
}
