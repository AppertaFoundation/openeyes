<?php

class m180425_133636_set_diagnoses_date extends CDbMigration
{
    public function up()
    {
        $dataProvider = new CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExamination_Diagnosis');
        $iterator = new CDataProviderIterator($dataProvider);

        foreach ($iterator as $diagnosis) {
            if (!$diagnosis->date) {
                $data = [
                    'before' => print_r($diagnosis->attributes, true),
                ];
                $diagnosis->date = date('Y-m-d', strtotime($diagnosis->created_date));

                if ($diagnosis->save()) {
                    $data['after'] = print_r($diagnosis->attributes, true);

                    $element = $diagnosis->element_diagnoses;
                    $event = $diagnosis->element_diagnoses->event;
                    $episode_id = isset($event->episode) ? $event->episode->id : null;
                    $patient_id = $event->episode->patient_id ?? null;

                    \Audit::add(
                        'admin',
                        'update',
                        serialize($data),
                        'Set default diagnosis date',
                        array('module' => 'OphCiExamination', 'model' => 'OphCiExamination_Diagnosis', 'event_id' => $element->event_id,
                        'episode_id' => $episode_id,
                        'patient_id' => $patient_id)
                    );
                }
            }
        }
    }

    public function down()
    {
        echo "m180425_133636_set_diagnoses_date does not support migration down.\n";
        return false;
    }
}
