<?php

class m221215_110000_add_iris_trauma_to_index extends OEMigration
{
    public function safeUp()
    {
        $this->execute("INSERT INTO eyedraw_doodle
        (eyedraw_class_mnemonic, init_doodle_json, processed_canvas_intersection_tuple)
        VALUES('IrisTrauma', '', 'EXAM_ANT_SEG,OP_NOTE_CAT_ANT_SEG');
        ");

        $this->execute("INSERT INTO eyedraw_canvas_doodle
                        (eyedraw_class_mnemonic, canvas_mnemonic, eyedraw_on_canvas_toolbar_location, eyedraw_on_canvas_toolbar_order, eyedraw_no_tuple_init_canvas_flag, eyedraw_carry_forward_canvas_flag, eyedraw_always_init_canvas_flag)
                        VALUES('IrisTrauma', 'EXAM_ANT_SEG', '', 0, 0, 1, 0);
        ");

        $this->execute("INSERT INTO eyedraw_canvas_doodle
                        (eyedraw_class_mnemonic, canvas_mnemonic, eyedraw_on_canvas_toolbar_location, eyedraw_on_canvas_toolbar_order, eyedraw_no_tuple_init_canvas_flag, eyedraw_carry_forward_canvas_flag, eyedraw_always_init_canvas_flag)
                        VALUES('IrisTrauma', 'OP_NOTE_CAT_ANT_SEG', '', 0, 0, 1, 0);
                        ");

        // Search index
        $exam_id = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE `name` = 'Examination'")->queryScalar();
        $parent_id = $this->dbConnection->createCommand("SELECT id FROM index_search WHERE primary_term = 'Anterior segment'")->queryScalar();
        $this->execute(
            "INSERT INTO index_search (
                event_type_id,
                parent,
                primary_term,
                secondary_term_list,
                open_element_class_name,
                goto_id,
                goto_tag,
                goto_text,
                img_url,
                goto_subcontainer_class,
                goto_doodle_class_name,
                goto_property,
                warning_note
            )
            VALUES(
                :exam_id,
                :parent_id,
                'Iris Trauma',
                NULL,
                'OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_AnteriorSegment',
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                'AntSeg',
                'Iris trauma',
                NULL
            );
            ",
            [':exam_id' => $exam_id,
            ':parent_id' => $parent_id]
        );

        // Pupil distortion
        $this->execute(
            "INSERT INTO index_search (
                event_type_id,
                parent,
                primary_term,
                secondary_term_list,
                open_element_class_name,
                goto_id,
                goto_tag,
                goto_text,
                img_url,
                goto_subcontainer_class,
                goto_doodle_class_name,
                goto_property,
                warning_note
            )
            VALUES(
                :exam_id,
                :parent_id,
                'Pupil distortion',
                'deformed pupil,deformed iris,pupil shape',
                'OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_AnteriorSegment',
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                'AntSeg',
                'Pupil shape',
                NULL
            );
            ",
            [':exam_id' => $exam_id,
            ':parent_id' => $parent_id]
        );

        // Aniridia
        $this->execute(
            "INSERT INTO index_search (
                event_type_id,
                parent,
                primary_term,
                secondary_term_list,
                open_element_class_name,
                goto_id,
                goto_tag,
                goto_text,
                img_url,
                goto_subcontainer_class,
                goto_doodle_class_name,
                goto_property,
                warning_note
            )
            VALUES(
                :exam_id,
                :parent_id,
                'Aniridia',
                NULL,
                'OEModule\\\OphCiExamination\\\models\\\Element_OphCiExamination_AnteriorSegment',
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                'AntSeg',
                'Aniridia',
                NULL
            );
            ",
            [':exam_id' => $exam_id,
            ':parent_id' => $parent_id]
        );
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophciexamination_investigation_codes', 'ecds_code', 'varchar(20) NOT NULL', true);
    }
}
