<?php

class m200902_004830_add_display_order_to_medication_frequency_table extends OEMigration
{
    private array $display_order_list = [
        'every fifteen minute' => 1,
        'every half hour' => 2,
        'every hour' => 3,
        'every two hours' => 4,
        'every four to six hours when needed' => 5,
        'five times a day' => 6,
        'every 6 hours' => 8,
        'four times a day' => 9,
        'three times a day' => 10,
        'twice a day' => 11,
        'once a day' => 12,
        'in the morning' => 13,
        'at bedtime' => 14,
        'at night' => 15,
        'alternate days' => 16,
        'three times a week' => 17,
        'twice a week' => 18,
        'once a week' => 19,
        'when needed' => 20,
        'other' => 22,
        'six times a day' => 7,
        'immediately' => 21
    ];

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->addOEColumn('medication_frequency', 'display_order', 'int(10) unsigned', true);
        foreach ($this->display_order_list as $term => $display_order) {
            // Matching on original_id as this should not change after the initial migration, whereas the new IDs might.
            $this->update(
                'medication_frequency',
                array('display_order' => $display_order),
                'term = :term',
                array(':term' => $term),
            );
        }
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->dropOEColumn('medication_frequency', 'display_order');
    }
}
