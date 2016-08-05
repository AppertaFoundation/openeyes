<?php

class OphInVisualfields_Field_Measurement extends Measurement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophinvisualfields_field_measurement';
    }

    public function relations()
    {
        return array(
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'image' => array(self::BELONGS_TO, 'ProtectedFile', 'image_id'),
            'cropped_image' => array(self::BELONGS_TO, 'ProtectedFile', 'cropped_image_id'),
            'strategy' => array(self::BELONGS_TO, 'OphInVisualfields_Strategy', 'strategy_id'),
            'pattern' => array(self::BELONGS_TO, 'OphInVisualfields_Pattern', 'pattern_id'),
        );
    }

    /**
     * Fetch unattached fields for the given patient and eye.
     *
     * If an event is passed in then fields attached to that event will be included too
     *
     * @param Patient $patient
     * @param int     $eye_id
     * @param Event   $event
     *
     * @return OphInVisualfields_Field_Measurement[]
     */
    public function getUnattachedForPatient(Patient $patient, $eye_id, Event $event = null)
    {
        $crit = array(
            'join' => 'inner join patient_measurement pm on pm.id = t.patient_measurement_id '.
                'left join measurement_reference mr on mr.patient_measurement_id = pm.id',
            'condition' => 'pm.patient_id = :patient_id and t.eye_id = :eye_id and '.
                '(mr.id is null'.($event ? ' or mr.event_id = :event_id' : '').')',
            'params' => array(
                ':patient_id' => $patient->id,
                ':eye_id' => $eye_id,
            ),
            'order' => 't.study_datetime',
        );

        if ($event) {
            $crit['params']['event_id'] = $event->id;
        }

        return $this->findAll($crit);
    }
}
