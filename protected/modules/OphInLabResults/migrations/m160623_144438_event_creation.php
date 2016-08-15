<?php

class m160623_144438_event_creation extends OEMigration
{
    public function up()
    {
        $labResultsEvent = $this->insertOEEventType('Lab Results', 'OphInLabResults', 'In');

        $this->insertOEElementType(array('Element_OphInLabResults_Details' => array(
            'name' => 'Details',
            'required' => 1,
        )), $labResultsEvent);

        $resultElement = $this->insertOEElementType(array('Element_OphInLabResults_Inr' => array(
            'name' => 'INR Result',
            'default' => '0',
        )), $labResultsEvent);

        $this->createOETable(
            'ophinlabresults_type',
            array(
                'id' => 'pk',
                'type' => 'varchar(255) not null',
                'result_element_id' => 'int(10) unsigned',
            ),
            true
        );

        $this->addForeignKey('labresults_type_result_element', 'ophinlabresults_type', 'result_element_id', 'element_type', 'id');

        $this->createOETable(
            'et_ophinlabresults_details',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'result_type_id' => 'int(11)',
            ),
            true
        );

        $this->addForeignKey('et_labresults_event_id', 'et_ophinlabresults_details', 'event_id', 'event', 'id');
        $this->addForeignKey('et_labresults_type_result_element', 'et_ophinlabresults_details', 'result_type_id', 'ophinlabresults_type', 'id');

        $this->createOETable(
            'et_ophinlabresults_result_timed_numeric',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'time' => 'time',
                'result' => 'float',
                'comment' => 'varchar(255)',
            ),
            true
        );

        $this->insert('ophinlabresults_type', array(
            'type' => 'INR',
            'result_element_id' => $resultElement[0],
        ));
    }

    public function down()
    {
        //$this->dropOETable('et_ophinlabresults_details', true);
        $this->delete('ophinlabresults_type');
        $this->delete('et_ophinlabresults_result_timed_numeric', true);
        foreach (array('Element_OphInLabResults_Inr', 'Element_OphInLabResults_Details') as $element) {
            $this->delete('element_type', 'class_name = ? ', array($element));
        }
        $this->delete('event_type', 'name = "Lab Results"');
        $this->dropOETable('ophinlabresults_type', true);
        $this->dropOETable('et_ophinlabresults_result_timed_numeric', true);
    }
}
