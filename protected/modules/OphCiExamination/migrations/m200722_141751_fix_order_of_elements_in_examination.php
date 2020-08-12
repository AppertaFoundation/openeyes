<?php

class m200722_141751_fix_order_of_elements_in_examination extends CDbMigration
{
    const DISPLAY_ORDERS = [
        [
            'class_name' => 'OEModule\OphCiExamination\models\MedicationManagement',
            'old_display_order' => 440,
            'new_display_order' => 445,
        ],
        [
            'class_name' => 'OEModule\OphCiExamination\models\Allergies',
            'old_display_order' => 50,
            'new_display_order' => 45,
        ],
    ];

    private function updateDisplayOrders($old_or_new)
    {
        foreach (self::DISPLAY_ORDERS as $entry) {
            $this->update(
                'element_type',
                [
                    'display_order' => $entry[$old_or_new . '_display_order'],
                ],
                'class_name = :class_name',
                [
                    ':class_name' => $entry['class_name'],
                ]
            );
        }
    }

    public function safeUp()
    {
        $this->updateDisplayOrders('new');
    }

    public function safeDown()
    {
        $this->updateDisplayOrders('old');
    }
}
