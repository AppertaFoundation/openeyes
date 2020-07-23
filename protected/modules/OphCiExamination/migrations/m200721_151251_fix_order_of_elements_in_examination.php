<?php

class m200721_151251_fix_order_of_elements_in_examination extends CDbMigration
{
    const DISPLAY_ORDERS = [
        [
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CommunicationPreferences',
            'old_display_order' => 10,
            'new_display_order' => 125,
        ],
        [
            'class_name' => 'OEModule\OphCiExamination\models\PupillaryAbnormalities',
            'old_display_order' => 125,
            'new_display_order' => 140,
        ],
        [
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Observations',
            'old_display_order' => 140,
            'new_display_order' => 128,
        ],
    ];

    private function updateDisplayOrders($old_or_new) {
        foreach (self::DISPLAY_ORDERS as $entry) {
            $this->update('element_type',
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
