<?php

class m220304_135700_update_element_groupings extends OEMigration
{
    public function safeUp()
    {
        $exam_event_id = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE `class_name` = 'OphCiExamination'")->queryScalar();

        // Create and / or reorder groups
        $this->createElementGroupForEventType(
            'Communication',
            'OphCiExamination',
            28
        );

        $this->createElementGroupForEventType(
            'Other',
            'OphCiExamination',
            120
        );

        $this->createElementGroupForEventType(
            'Contacts',
            'OphCiExamination',
            15
        );

        $this->createElementGroupForEventType(
            'Intraocular Pressure',
            'OphCiExamination',
            90
        );

        $this->createElementGroupForEventType(
            'Orthoptic Testing',
            'OphCiExamination',
            80
        );

        // Rename Investigation group
        $this->update('element_group', ['name' => 'Investigations & Procedures'], '`name` = "Investigation"');

        // Deal with 2x retina groups in [sample] data
        $this->execute(
            "UPDATE element_type
                        SET element_group_id = (SELECT min(id) FROM element_group WHERE `name` = 'Retina' AND event_type_id = :event_type_id)
                        WHERE id IN 
                            (SELECT t.id FROM element_type t INNER JOIN element_group g 
                                ON t.element_group_id = g.id 
                            WHERE g.`name` = 'Retina' AND g.event_type_id = :event_type_id)",
            [ ':event_type_id' => $exam_event_id ]
        );
        $this->execute(
            "DELETE FROM element_group 
                        WHERE id != (SELECT min(id) FROM element_group WHERE `name` = 'Retina' AND event_type_id = :event_type_id)
                            AND `name` = 'Retina'
                            AND event_type_id = :event_type_id",
            [ ':event_type_id' => $exam_event_id ]
        );
        $this->createElementGroupForEventType(
            'Retina',
            'OphCiExamination',
            70
        );

        $elements = array(
            [ 'name' => 'Communication Preferences', 'group' => 'Communication'],
            [ 'name' => 'Contacts', 'group' => 'Communication'],
            [ 'name' => 'Glaucoma Risk', 'group' => 'Clinical Management', 'display_order' => 465],
            [ 'name' => 'PCR Risk', 'group' => 'Clinical Management', 'display_order' => 445 ],
            [ 'name' => 'Post-Op Complications', 'group' => 'Other'],
            [ 'name' => 'Optometrist Comments', 'group' => 'Other'],
            [ 'name' => 'Clinic Procedures', 'group' => 'Investigations & Procedures'],
            [ 'name' => 'Optic Disc', 'group' => 'Retina'],
            [ 'name' => 'Observations', 'group' => 'Other'],
            [ 'name' => 'Drug Administration', 'group' => 'Other'],
            [ 'name' => 'Pain', 'group' => 'Triage'],
            [ 'name' => 'Safeguarding', 'group' => 'Other'],
        );

        foreach ($elements as $element) {
            // build the query
            $cmd = "UPDATE element_type
                            SET `group_title` = :group,
                                `element_group_id` = (SELECT id FROM element_group WHERE event_type_id = :event_type_id AND `name` = :group )
                                ";
            $params = array(':event_type_id' => $exam_event_id, ':element_name' => $element['name'], ':group' => $element['group']);

            // If a new display order has been specified, add it into the query.
            if (!empty($element['display_order'])) {
                $cmd .= ", display_order = :display_order ";
                $params[':display_order'] = $element['display_order'];
            }

            $cmd .= "WHERE  event_type_id = :event_type_id
                          AND `name` = :element_name";

            // execute the update query
            $this->execute($cmd, $params);
        }
    }

    public function safeDown()
    {
        $this->createElementGroupForEventType(
            'Contacts',
            'OphCiExamination',
            20
        );
    }
}
