<?php

class m210907_010616_add_options_to_postinjection_drops extends OEMigration
{
    public function safeUp()
    {
        $display_order = $this->getDbConnection()->createCommand('select max(display_order) from ophtrintravitinjection_postinjection_drops')->queryScalar();
        $this->insertMultiple('ophtrintravitinjection_postinjection_drops', [
            [
                'name' => 'G. Chloramphenical 0.5% stat and lubricating drops PRN for 3 days',
                'display_order' => $display_order + 1,
                'active' => 1,
            ],
            ['name' => 'Lubricating drops PRN for 3 days', 'display_order' => $display_order + 2, 'active' => 1],
        ]);
    }

    public function safeDown()
    {
        $this->delete('ophtrintravitinjection_postinjection_drops',
            'name="G. Chloramphenical 0.5% stat and lubricating drops PRN for 3 days"');
        $this->delete('ophtrintravitinjection_postinjection_drops', 'name="Lubricating drops PRN for 3 days"');
    }
}
