<?php

class m200902_004830_add_display_order_to_medication_frequency_table extends OEMigration
{
    private array $display_order_list = [1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 7, 21];

    public function up()
    {
        $this->addOEColumn('medication_frequency', 'display_order', 'int(10) unsigned', true);
        foreach ($this->display_order_list as $i => $display_order) {
            // Matching on original_id as this should not change after the initial migration, whereas the new IDs might.
            $this->update(
                'medication_frequency',
                array('display_order' => $display_order),
                'original_id = :id',
                array(':id' => ($i + 1)),
            );
        }
    }

    public function down()
    {
        $this->dropOEColumn('medication_frequency', 'display_order');
    }
}
