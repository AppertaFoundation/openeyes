<?php

class m210426_124255_add_freehand_drawing_element extends \OEMigration
{
    public function safeUp()
    {
        $this->createElementType('OphCiExamination', 'Freehand draw', [
            'class_name' => 'OEModule\OphCiExamination\models\FreehandDraw',
            'display_order' => 20,
            'event_type_id' => 'OphCiExamination',
            'group_name' => 'Investigation'
        ]);

        $this->createOETable('et_ophciexamination_freehand_draw', [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED NOT NULL',
        ], true);

        $this->createOETable('ophciexamination_freehand_draw_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'protected_file_id' => 'INT(11) UNSIGNED DEFAULT NULL',
            'comments' => 'TEXT'
        ), true);

        $this->addForeignKey(
            'et_ophciexamination_freehand_draw_ev_fk',
            'et_ophciexamination_freehand_draw',
            'event_id',
            'event',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_freehand_draw_el_fk',
            'ophciexamination_freehand_draw_entry',
            'element_id',
            'et_ophciexamination_freehand_draw',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('et_ophciexamination_freehand_draw_el_fk', 'ophciexamination_freehand_draw_entry');
        $this->dropForeignKey('et_ophciexamination_freehand_draw_ev_fk', 'et_ophciexamination_freehand_draw');

        $this->dropOETable('ophciexamination_freehand_draw_entry', true);
        $this->dropOETable('et_ophciexamination_freehand_draw', true);

        $this->deleteElementType('OphCiExamination', 'OEModule\OphCiExamination\models\FreehandDraw');
    }
}
