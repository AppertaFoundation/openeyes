<?php

class m190215_102307_create_sets_from_tags extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CDbException
     * @throws CException
     */
    public function safeUp()
    {
        $this->addColumn('ophciexamination_risk_tag', 'medication_set_id', 'int(10) AFTER tag_id');
        $this->createIndex('idx_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id');
        $this->addForeignKey('fk_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id', 'medication_set', 'id');


        $tags = $this->dbConnection
            ->createCommand('SELECT id, name FROM tag WHERE active = 1 ORDER BY name ASC')
            ->queryAll();

        if ($tags) {
            $drug_tag_id = $this->dbConnection->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'DrugTag'])->queryScalar();
            foreach ($tags as $tag) {
                $command = $this->dbConnection;
                $command->createCommand("INSERT INTO medication_set(name) values ('" . $tag['name'] . "')")->execute();
                $ref_set_id = $command->getLastInsertID();

                if($drug_tag_id) {
                    $this->dbConnection->createCommand("INSERT INTO medication_set_rule (medication_set_id, usage_code_id) values (" . $ref_set_id . ", $drug_tag_id )")->execute();
                }

                /*
                 * Update ophciexamination_risk_tag
                 */
                $this->dbConnection->createCommand("UPDATE ophciexamination_risk_tag SET medication_set_id = " . $ref_set_id . " WHERE tag_id = " . $tag['id'])->execute();

                $drugTags = $this->dbConnection
                    ->createCommand('SELECT drug_id FROM drug_tag WHERE tag_id = ' . $tag['id'] . ' AND drug_id in (SELECT id from drug)')
                    ->queryAll();

                if ($drugTags) {
                    foreach ($drugTags as $drugTag) {
                        $ref_medication_id =$this->dbConnection
                            ->createCommand("SELECT id FROM medication WHERE source_old_id = '" . $drugTag['drug_id'] . "' AND source_subtype = 'drug'")
                            ->queryRow();

                        if ($ref_medication_id['id']) {
                            $this->dbConnection->createCommand("INSERT INTO medication_set_item(medication_id, medication_set_id) values (" . $ref_medication_id['id'] . ", " . $ref_set_id . " )")->execute();
                        }
                    }

                    $ref_medication_id = null;
                }

                $medicationDrugTags = $this->dbConnection
                    ->createCommand('SELECT medication_drug_id FROM medication_drug_tag WHERE tag_id = ' . $tag['id'] . ' AND medication_drug_id in (SELECT id from medication_drug)')
                    ->queryAll();

                if ($medicationDrugTags) {
                    foreach ($medicationDrugTags as $medicationDrugTag) {
                        $ref_medication_id = $this->dbConnection
                            ->createCommand("SELECT id FROM medication WHERE source_old_id = '" . $medicationDrugTag['medication_drug_id'] . "' AND source_subtype='medication_drug';")
                            ->queryRow();

                        if ($ref_medication_id['id']) {
                            $this->dbConnection->createCommand("INSERT INTO medication_set_item (medication_id, medication_set_id) values (" . $ref_medication_id['id'] . ", " . $ref_set_id . " )")->execute();
                        }
                    }
                }
            }
            $ref_set_id = null;
        }
    }

    public function down()
    {
        echo "m190215102307_create_sets_from_tags does not support migration down.\n";
        return false;
    }
}
