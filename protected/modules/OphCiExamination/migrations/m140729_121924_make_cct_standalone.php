<?php

class m140729_121924_make_cct_standalone extends OEMigration
{
    public function up()
    {
        $id = $this->getIdOfElementTypeByClassName('OEModule\\OphCiExamination\\models\\Element_OphCiExamination_AnteriorSegment_CCT');
        $this->update('element_type', array('parent_element_type_id' => null, 'display_order' => 49), 'id = '.$id);
    }

    public function down()
    {
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment_CCT');
        $aid = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment');
        $this->update('element_type', array('parent_element_type_id' => $aid, 'display_order' => 1), 'id = '.$id);
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
