<?php

class m200813_212656_cellsandflare_to_indexsearch extends \OEMigration
{
    public function up()
    {
        $examination_id  = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'parent' => $this->getSearchIndexByTerm('Examination Anterior Segment'),
            'primary_term' => 'Cells and flare',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
            'goto_doodle_class_name' => 'CellsAndFlare'
        ]);
    }

    public function down()
    {
        $this->delete('index_search', 'parent = ? AND primary_term = ?', [
            $this->getSearchIndexByTerm('Examination Anterior Segment'), 'Cells and flare'
        ]);
    }
}
