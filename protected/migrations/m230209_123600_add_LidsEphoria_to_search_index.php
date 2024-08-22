<?php

class m230209_123600_add_LidsEphoria_to_search_index extends OEMigration
{
    public function safeUp()
    {
        // Lids Surgical does not currently have an entry in the eyedraw_canvas table!
        $element_id = $this->dbConnection->createCommand("SELECT id FROM element_type WHERE class_name = :class_name")->queryScalar([':class_name' => 'OEModule\\OphCiExamination\\models\\SurgicalLids']);
        $this->insert('eyedraw_canvas', [
            'canvas_mnemonic' => 'EXAM_LIDS_SURGICAL',
            'canvas_name' => 'Examination Lids Surgical',
            'container_element_type_id' => $element_id
        ]);
        $canvas_id = $this->dbConnection->lastInsertID;

        $this->execute("INSERT INTO eyedraw_doodle
        (eyedraw_class_mnemonic, init_doodle_json, processed_canvas_intersection_tuple)
        VALUES('Epiphora', '', 'EXAM_LIDS_SURGICAL');
        ");

        $this->execute("INSERT INTO eyedraw_canvas_doodle
                        (eyedraw_class_mnemonic, canvas_mnemonic, eyedraw_on_canvas_toolbar_location, eyedraw_on_canvas_toolbar_order, eyedraw_no_tuple_init_canvas_flag, eyedraw_carry_forward_canvas_flag, eyedraw_always_init_canvas_flag)
                        VALUES('Epiphora', 'EXAM_LIDS_SURGICAL', '', 1, 1, 0, 0);
        ");

        // Search index
        $this->addToSearchIndex(
            'OphCiExamination',
            'Lids Surgical',
            'Epiphora',
            null,
            'OEModule\OphCiExamination\models\SurgicalLids',
            null,
            null,
            null,
            null,
            null,
            'Epiphora',
            null,
            null
        );
    }

    public function safeDown()
    {
        $this->alterOEColumn('et_ophciexamination_investigation_codes', 'ecds_code', 'varchar(20) NOT NULL', true);
    }
}
