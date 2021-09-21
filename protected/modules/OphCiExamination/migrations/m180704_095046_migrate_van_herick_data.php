<?php

class m180704_095046_migrate_van_herick_data extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\Element_OphCiExamination_Gonioscopy', [
            'criteria' => [
                'condition'=>'left_van_herick_id IS NOT NULL OR right_van_herick_id IS NOT NULL'
            ],
        ]);
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $gonioscopy) {
            $van_herick_element = new \OEModule\OphCiExamination\models\VanHerick();
            $van_herick_element->event_id = $gonioscopy->event_id;
            $van_herick_element->left_van_herick_id = $gonioscopy->left_van_herick_id;
            $van_herick_element->right_van_herick_id =  $gonioscopy->right_van_herick_id;

            $van_herick_element->eye_id = \EYE::BOTH;
            if ($van_herick_element->left_van_herick_id && !$van_herick_element->right_van_herick_id) {
                $van_herick_element->eye_id = \EYE::LEFT;
            } elseif (!$van_herick_element->left_van_herick_id && $van_herick_element->right_van_herick_id) {
                $van_herick_element->eye_id = \EYE::RIGHT;
            }

            if ($van_herick_element->save()) {
                $data = [
                    'Element_OphCiExamination_Gonioscopy' => $gonioscopy->attributes,
                    'VanHerick' => $van_herick_element->attributes,
                ];

                \Audit::add(
                    'admin',
                    'create',
                    serialize($data),
                    'Create Van Herick element',
                    array('module' => 'OphCiExamination', 'model' => 'VanHerick', 'event_id' => $gonioscopy->event_id,
                    'episode_id' => $gonioscopy->event->episode_id,
                    'patient_id' => $gonioscopy->event->episode->patient->id)
                );
            } else {
                return false;
            }
        }
    }

    public function safeDown()
    {
        echo "m180704_095046_migrate_van_herick_data does not support migration down.\n";
        return false;
    }
}
