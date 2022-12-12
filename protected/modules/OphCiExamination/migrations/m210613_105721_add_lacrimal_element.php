<?php

class m210613_105721_add_lacrimal_element extends OEMigration
{
    public function safeUp()
    {
        if (!$this->verifyTableExists('et_ophciexamination_lacrimal')) {
            $this->createOETable('et_ophciexamination_lacrimal', array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'left_eyedraw' => 'text',
                'left_ed_report' => 'text',
                'left_comments' => 'text',
                'right_eyedraw' => 'text',
                'right_ed_report' => 'text',
                'right_comments' => 'text',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
                'CONSTRAINT `et_ophciexamination_lacrimal_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
                'CONSTRAINT `et_ophciexamination_lacrimal_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
            ), true);
        }

        $this->createElementType('OphCiExamination', 'Lacrimal', [
           'class_name' => 'OEModule\OphCiExamination\models\Lacrimal',
           'group_name' => 'Adnexal',
           'display_order' => 195
        ]);

        $exam_id = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE `name` = 'Examination'")->queryScalar();

        $this->execute(
            "INSERT INTO index_search (event_type_id ,primary_term,secondary_term_list,open_element_class_name,goto_id)
	                    VALUES (:exam_id,'Lacrimal','duct','OEModule\\\OphCiExamination\\\models\\\Lacrimal','OEModule_OphCiExamination_models_Lacrimal_eye_id');",
            [ ':exam_id' => $exam_id ]
        );

        $lacrimal_id = $this->dbConnection->lastInsertID;
        $this->execute(
            "INSERT INTO index_search (event_type_id,primary_term,open_element_class_name,goto_doodle_class_name, img_url, parent)
	                    VALUES (:exam_id, 'Laxity','OEModule\\\OphCiExamination\\\models\\\Lacrimal','LidLaxity', null, :lacrimal_id);",
            [':exam_id' => $exam_id,
            ':lacrimal_id' => $lacrimal_id]
        );

        $this->execute(
            "INSERT INTO index_search (event_type_id,primary_term,open_element_class_name,goto_doodle_class_name, img_url, parent)
	                    VALUES (:exam_id, 'Mucocele','OEModule\\\OphCiExamination\\\models\\\Lacrimal','Mucocele', null, :lacrimal_id);",
            [':exam_id' => $exam_id,
            ':lacrimal_id' => $lacrimal_id]
        );
    }

    public function safeDown()
    {
        $event_type_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->deleteElementType(
            'OphCiExamination',
            'OEModule\OphCiExamination\models\Lacrimal',
            $event_type_id
        );
        $this->dropOETable('et_ophciexamination_lacrimal', true);
    }
}
