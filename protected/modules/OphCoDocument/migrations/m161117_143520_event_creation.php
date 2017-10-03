<?php

class m161117_143520_event_creation extends OEMigration
{
    public function up()
    {
        $documentEvent = $this->insertOEEventType('Document', 'OphCoDocument', 'Co');

        $this->insertOEElementType(array('Element_OphCoDocument_Document' => array(
            'name' => 'Document upload',
            'required' => 1,
            'default' => 1,
        )), $documentEvent);


        $this->createOETable(
            'et_ophcodocument_document',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'document_id' => 'int(11)',
            ),
            true
        );
        
    }

    public function down()
    {
        //$this->dropOETable('et_ophinlabresults_details', true);
        $this->delete('element_type', 'class_name = ? ', array('Element_OphCoDocument_Document'));

        $this->delete('event_type', 'name = "Document"');
        $this->dropOETable('et_ophcodocument_document', true);
    }
}
