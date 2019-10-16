<?php

class m180711_092304_create_sets_from_tags extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_risk_tag', 'medication_set_id', 'int(10) AFTER tag_id');
        $this->createIndex('idx_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id');
        $this->addForeignKey('fk_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id', 'medication_set', 'id');
        $command = Yii::app()->db;

        $glaucoma_tag = $command->createCommand('SELECT id, name FROM tag WHERE name = "Glaucoma"')->queryRow();

        if ($glaucoma_tag) {
            // creating Glaucoma set
            $command->createCommand("INSERT INTO medication_set(name) values ('" . $glaucoma_tag['name'] . "')")->execute();
            $medication_set_id = $command->getLastInsertID();

            // get the id of the OEScape usage code
            $oescape_usage_code_id = $command->createCommand()
                ->select('id')
                ->from('medication_usage_code')
                ->where('usage_code = :usage_code', [':usage_code' => 'OEScape'])
                ->queryScalar();

            // get Glaucoma subspecialty's Id
            $gl_subspecialty_id = $command->createCommand()
                ->select('id')
                ->from('subspecialty')
                ->where('ref_spec = :ref_spec', [':ref_spec' => 'GL'])
                ->queryScalar();

            // add rule to OEScape Glaucoma
            $command->createCommand("INSERT INTO medication_set_rule (medication_set_id, subspecialty_id, usage_code_id)
											VALUES ($medication_set_id, $gl_subspecialty_id, $oescape_usage_code_id )")->execute();

            // get all the items belong to Glaucoma
            $drug_tags = $command
                ->createCommand('SELECT drug_id FROM drug_tag WHERE tag_id = ' . $glaucoma_tag['id'])
                ->queryAll();

            if ($drug_tags) {
                foreach ($drug_tags as $drug_tag) {
                    $ref_medication_id = $command
                        ->createCommand("SELECT id FROM medication WHERE source_old_id = '" . $drug_tag['drug_id'] . "' AND source_subtype = 'drug'")
                        ->queryScalar();

                    if ($ref_medication_id) {
                        $command->createCommand("INSERT INTO medication_set_item(medication_id, medication_set_id) values (" . $ref_medication_id . ", " . $medication_set_id . " )")->execute();
                    }
                }
            }

            //Ok, I am not sure if this part is actually needed
            $medication_drug_tags = Yii::app()->db
                ->createCommand('SELECT medication_drug_id FROM medication_drug_tag WHERE tag_id = ' . $glaucoma_tag['id'])
                ->queryAll();

            if ($medication_drug_tags) {
                foreach ($medication_drug_tags as $medication_drug_tag) {
                    $ref_medication_id = $command
                        ->createCommand("SELECT id FROM medication WHERE source_old_id = '" . $medication_drug_tag['medication_drug_id'] . "' AND source_subtype='medication_drug';")
                        ->queryRow();

                    if ($ref_medication_id['id']) {
                        $command->createCommand("INSERT INTO medication_set_item (medication_id, medication_set_id) values (" . $ref_medication_id['id'] . ", " . $medication_set_id . " )")->execute();
                    }
                }
            }
        }
    }

    public function down()
    {
        echo "m180711_092304_create_sets_from_tags does not support migration down.\n";
        return false;
    }
}
