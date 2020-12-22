<?php
class m200428_234839_event_type_OphTrOperationchecklists extends CDbMigration
{
    public function safeUp()
    {
        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperationchecklists'))->queryRow()) {
            $group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name', array(':name'=>'Clinical events'))->queryRow();
            $this->insert('event_type', array('class_name' => 'OphTrOperationchecklists', 'name' => 'Operation Checklists','event_group_id' => $group['id']));
        }
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperationchecklists'))->queryRow();

        // Procedure List Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Procedure List',
            array('class_name' => 'Element_OphTrOperationchecklists_ProcedureList',
                'display_order' => 1,
                'default' => 1,
                'required' => 0
            )
        );

        // Admission Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Admission',
            array('class_name' => 'Element_OphTrOperationchecklists_Admission',
                'display_order' => 2,
                'default' => 1,
                'required' => 0
            )
        );

        // Notes Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Notes',
            array('class_name' => 'Element_OphTrOperationchecklists_Note',
                'display_order' => 3,
                'default' => 1,
                'required' => 0
            )
        );

        // Documentation Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Documentation',
            array('class_name' => 'Element_OphTrOperationchecklists_Documentation',
                'display_order' => 4,
                'default' => 1,
                'required' => 0
            )
        );

        // Clinical Assessment Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Clinical Assessment',
            array('class_name' => 'Element_OphTrOperationchecklists_ClinicalAssessment',
                'display_order' => 5,
                'default' => 1,
                'required' => 0
            )
        );

        // Nursing/Practitioner Assessment Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Nursing / Practitioner Assessment',
            array('class_name' => 'Element_OphTrOperationchecklists_NursingAssessment',
                'display_order' => 6,
                'default' => 1,
                'required' => 0
            )
        );

        // Patient Support Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Patient Support',
            array('class_name' => 'Element_OphTrOperationchecklists_PatientSupport',
                'display_order' => 9,
                'default' => 1,
                'required' => 0
            )
        );

        // Discharge Element
        $this->addOperationChecklistsElement(
            $event_type['id'],
            'Discharge',
            array('class_name' => 'Element_OphTrOperationchecklists_Discharge',
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
    function addOperationChecklistsElement($event_type, $element_name, $params)
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
        $event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperationchecklists'))->queryRow();

        foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
            $this->delete('audit', 'event_id='.$row['id']);
            $this->delete('event', 'id='.$row['id']);
        }

        $this->delete('element_type', 'event_type_id='.$event_type['id']);
        $this->delete('event_type', 'id='.$event_type['id']);
    }
}
