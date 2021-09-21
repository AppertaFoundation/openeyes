<?php

class m210018_165900_add_IntraluminalStent_to_index_search extends OEMigration
{
    public function safeUp()
    {
        $examination_event_id = $this->dbConnection->createCommand("SELECT id FROM event_type WHERE class_name = 'OphCiExamination';")->queryScalar();
        $supramid_id = $this->dbConnection->createCommand("SELECT id FROM index_search WHERE primary_term = 'Supramid suture';")->queryScalar();

        // Re-point Supramid index entries to IntraluminalStent
        $this->dbConnection->createCommand("
                UPDATE `index_search`
                SET primary_term = :primary_term, secondary_term_list = :secondary_term_list, img_url = :img_url, goto_doodle_class_name = :goto_doodle_class_name
                WHERE id = :supramid_id")
                    ->execute(
                        array(
                        ':supramid_id' => $supramid_id,
                        ':primary_term' => 'Intraluminal Stent',
                        ':secondary_term_list' => 'Ripcord suture',
                        ':img_url' => 'protected/modules/eyedraw/assets/img/icons/32x32/draw/old/IntraluminalStent.png',
                        ':goto_doodle_class_name' => 'IntraluminalStent')
                    );

        $this->dbConnection->createCommand("
                UPDATE `index_search`
                SET goto_doodle_class_name = :goto_doodle_class_name
                WHERE primary_term = 'Percentage of tube' AND parent = :IntraluminalStent_id;")
                    ->execute(
                        array(
                        ':IntraluminalStent_id' => $supramid_id,
                        ':goto_doodle_class_name' => 'IntraluminalStent')
                    );

        // Add new material property options to index
        $this->dbConnection->createCommand("
                INSERT INTO `index_search` (event_type_id, parent, primary_term, secondary_term_list, open_element_class_name, goto_doodle_class_name, goto_property)
                    VALUES(:event_type_id, :parent, :primary_term, :secondary_term_list, :open_element_class_name, :goto_doodle_class_name, :goto_property);")
                    ->execute(
                        array(
                        ':event_type_id' => $examination_event_id,
                        ':parent' => $supramid_id,
                        ':primary_term' => 'Material',
                        ':secondary_term_list' => 'Supramid, Ethilon, Prolene',
                        ':open_element_class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
                        ':goto_doodle_class_name' => 'IntraluminalStent',
                        ':goto_property' => 'Material'
                        )
                    );
    }

    public function down()
    {
        $supramid_id = $this->dbConnection->createCommand("SELECT id FROM index_search WHERE primary_term = 'Intraluminal Stent';")->queryScalar();

        $this->dbConnection->createCommand("
                UPDATE `index_search`
                SET primary_term = :primary_term, secondary_term_list = NULL, img_url = :img_url, goto_doodle_class_name = :goto_doodle_class_name
                WHERE id = :supramid_id")
                    ->execute(
                        array(
                        ':supramid_id' => $supramid_id,
                        ':primary_term' => 'Supramid suture',
                        ':img_url' => 'protected/modules/eyedraw/assets/img/icons/32x32/draw/old/Supramid.png',
                        ':goto_doodle_class_name' => 'Supramid')
                    );

        $this->dbConnection->createCommand("
                UPDATE `index_search`
                SET goto_doodle_class_name = :goto_doodle_class_name
                WHERE primary_term = 'Percentage of tube' AND parent = :IntraluminalStent_id;")
                    ->execute(
                        array(
                        ':IntraluminalStent_id' => $supramid_id,
                        ':goto_doodle_class_name' => 'Supramid')
                    );

        $this->dbConnection->createCommand("DELETE FROM index_search WHERE goto_doodle_class = 'IntraluminalStent'")->execute();
    }
}
