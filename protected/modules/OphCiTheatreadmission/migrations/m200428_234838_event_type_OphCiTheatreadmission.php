<?php
class m200428_234838_event_type_OphCiTheatreadmission extends CDbMigration
{
    public function safeUp()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCiTheatreadmission'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name'=>'Clinical events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphCiTheatreadmission', 'name' => 'Theatre Admission','event_group_id' => $group['id']));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCiTheatreadmission'))->queryRow();

        // Procedure List Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Procedure List',
            array('class_name' => 'Element_OphCiTheatreadmission_ProcedureList',
                'display_order' => 1,
                'default' => 1,
                'required' => 0
            )
        );

        // Admission Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Admission',
            array('class_name' => 'Element_OphCiTheatreadmission_AdmissionChecklist',
                'display_order' => 2,
                'default' => 1,
                'required' => 0
            )
        );

        // Case Notes Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Case Notes',
            array('class_name' => 'Element_OphCiTheatreadmission_CaseNote',
                'display_order' => 3,
                'default' => 1,
                'required' => 0
            )
        );

        // Documentation Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Documentation',
            array('class_name' => 'Element_OphCiTheatreadmission_Documentation',
                'display_order' => 4,
                'default' => 1,
                'required' => 0
            )
        );

        // Clinical Assessment Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Clinical Assessment',
            array('class_name' => 'Element_OphCiTheatreadmission_ClinicalAssessment',
                'display_order' => 5,
                'default' => 1,
                'required' => 0
            )
        );

        // Nursing/Practitioner Assessment Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Nursing/Practitioner Assessment',
            array('class_name' => 'Element_OphCiTheatreadmission_NursingAssessment',
                'display_order' => 6,
                'default' => 1,
                'required' => 0
            )
        );

        // DVT Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'DVT',
            array('class_name' => 'Element_OphCiTheatreadmission_DVT',
                'display_order' => 8,
                'default' => 1,
                'required' => 0
            )
        );

        // Patient Support Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Patient Support',
            array('class_name' => 'Element_OphCiTheatreadmission_PatientSupport',
                'display_order' => 9,
                'default' => 1,
                'required' => 0
            )
        );

        // Discharge Element
        $this->addTheatreadmissionElement(
            $event_type['id'],
            'Discharge',
            array('class_name' => 'Element_OphCiTheatreadmission_Discharge',
                'display_order' => 10,
                'default' => 1,
                'required' => 0
            )
        );
    }

    /**
     * @param $event_type
     * @param $element_name
     * @param $params
     */
    function addTheatreadmissionElement($event_type, $element_name, $params)
    {
        $row = array(
                'name' => $element_name,
                'class_name' => isset($params['class_name']) ? $params['class_name'] : "Element_{$event_type}_" . str_replace(' ', '', $element_name),
                'event_type_id' => isset($event_type) ? $event_type : 0,
                'display_order' => isset($params['display_order']) ? $params['display_order'] : 1,
                'default' => isset($params['default']) ? $params['default'] : false,
                'required' => isset($params['required']) ? $params['required'] : false,
        );

        $this->insert('element_type', $row);
    }

    public function safeDown()
    {
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphCiTheatreadmission'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
