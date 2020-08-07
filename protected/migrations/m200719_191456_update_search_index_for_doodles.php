<?php

class m200719_191456_update_search_index_for_doodles extends \OEMigration
{
    public function up()
    {
        $examination_id  = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'parent' => $this->getSearchIndexByTerm('Examination Macula'),
            'primary_term' => 'Central serous retinopathy',
            'secondary_term_list' => 'CSR',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PosteriorPole',
            'goto_doodle_class_name' => 'CentralSerousRetinopathy'
        ]);

        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'parent' => $this->getSearchIndexByTerm('Examination Anterior Segment'),
            'primary_term' => 'Conjunctiva',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
            'goto_doodle_class_name' => 'ConjunctivalHaem'
        ]);

        $conjunctiva_id = $this->getSearchIndexByTerm('Conjunctiva');

        $properties = [
            'conjunctivitisType' => 'Conjunctivitis',
            'hyperaemia' => 'Hyperaemia',
            'haemorrhageGrade' => 'Haemorrhage',
            'swellingGrade' => 'Swelling',
            'mucopurulent' => 'Mucopurulent'
        ];

        foreach ($properties as $property => $name) {
            $this->insert('index_search', [
                'event_type_id' => $examination_id,
                'parent' => $conjunctiva_id,
                'primary_term' => $name,
                'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
                'goto_doodle_class_name' => 'ConjunctivalHaem',
                'goto_property' => $property
            ]);
        }

        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'parent' => $this->getSearchIndexByTerm('Examination Anterior Segment'),
            'primary_term' => 'Circumcorneal injection',
            'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
            'goto_doodle_class_name' => 'CircumcornealInjection'
        ]);

        $conjunctivitis_id = $this->getSearchIndexByTerm('Conjunctivitis');

        // Remove Conjunctivitis
        $this->delete('index_search', 'parent = ?', [$conjunctivitis_id]);
        $this->delete('index_search', 'id = ?', [$conjunctivitis_id]);
    }

    public function down()
    {
        $anterior_segment_id = $this->getSearchIndexByTerm('Examination Anterior Segment');
        $conjunctiva_id = $this->getSearchIndexByTerm('Conjunctiva', $anterior_segment_id);
        $examination_id  = $this->getIdOfEventTypeByClassName('OphCiExamination');

        // Remove Central serous retinopathy
        $this->delete('index_search', 'parent = ? AND primary_term = ?', [$this->getSearchIndexByTerm('Examination Macula'), 'Central serous retinopathy']);

        // Remove Conjunctiva properties
        $this->delete('index_search', 'parent = ?', [$conjunctiva_id]);

        // Remove Conjunctiva
        $this->delete('index_search', 'id = ?', [$conjunctiva_id]);

        $this->delete('index_search', 'parent = ? AND primary_term = ?', [$this->getSearchIndexByTerm('Examination Anterior Segment'), 'Circumcorneal injection']);

        // Insert Conjunctivitis
        $this->insert('index_search', [
                'event_type_id' => $examination_id,
                'parent' => $this->getSearchIndexByTerm('Examination Anterior Segment'),
                'primary_term' => 'Conjunctivitis',
                'secondary_term_list' => null,
                'description' => null,
                'general_note' => null,
                'open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
                'goto_id' => null,
                'goto_tag' => null,
                'goto_text' => null,
                'img_url' => 'protected/modules/eyedraw/assets/img/icons/32x32/draw/old/Conjunctivitis.png',
                'goto_subcontainer_class' => null,
                'goto_doodle_class_name' => 'Conjunctivitis',
                'goto_property' => null,
                'warning_note' => null,
            ]);
    }
}
