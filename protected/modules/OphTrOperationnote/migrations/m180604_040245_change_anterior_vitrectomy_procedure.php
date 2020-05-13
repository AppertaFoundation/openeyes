<?php

class m180604_040245_change_anterior_vitrectomy_procedure extends OEMigration
{
    private $vitrectomy_proc;
    private $membrane_peel_element;
    private $vitrectomy_element;

    public function init()
    {
        $this->vitrectomy_proc = $this->dbConnection->createCommand()->select('*')->from('proc')->where("term = 'Anterior Vitrectomy'")->queryRow();
        $this->membrane_peel_element = $this->dbConnection->createCommand()->select('*')->from('element_type')->where("name = 'Membrane peel'")->queryRow();
        $this->vitrectomy_element = $this->dbConnection->createCommand()->select('*')->from('element_type')->where("name = 'Vitrectomy'")->queryRow();
    }

    public function safeUp()
    {
        $this->init();

        // Change the Anterior Vitrectomy procedure to be linked to the Vitrectomy element instead of the Membrane Peel element
        $this->update(
            'ophtroperationnote_procedure_element',
            array('element_type_id' => $this->vitrectomy_element['id']),
            'procedure_id = :proc_id AND element_type_id = :elem_id',
            array(':proc_id' => $this->vitrectomy_proc['id'], ':elem_id' => $this->membrane_peel_element['id'])
        );
    }

    public function safeDown()
    {
        $this->init();
        $this->update(
            'ophtroperationnote_procedure_element',
            array('element_type_id' => $this->membrane_peel_element['id']),
            'procedure_id = :proc_id AND element_type_id = :elem_id',
            array(':proc_id' => $this->vitrectomy_proc['id'], ':elem_id' => $this->vitrectomy_element['id'])
        );
    }
}
