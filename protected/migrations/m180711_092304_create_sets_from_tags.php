<?php

class m180711_092304_create_sets_from_tags extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_risk_tag', 'medication_set_id', 'int(10) AFTER tag_id');
        $this->createIndex('idx_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id');
        $this->addForeignKey('fk_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id', 'medication_set', 'id');


        $tags = Yii::app()->db
            ->createCommand('SELECT id, name FROM tag ORDER BY name ASC')
            ->queryAll();

        if ($tags) {
            $drug_tag_id = \Yii::app()->db->createCommand()->select('id')->from('medication_usage_code')->where('usage_code = :usage_code', [':usage_code' => 'DrugTag'])->queryScalar();
            foreach ($tags as $tag) {
                $command = Yii::app()->db;
                $command->createCommand("INSERT INTO medication_set(name) values ('" . $tag['name'] . "')")->execute();
                $ref_set_id = $command->getLastInsertID();

                if ($drug_tag_id) {
                    Yii::app()->db->createCommand("INSERT INTO medication_set_rule (medication_set_id, usage_code_id) values (" . $ref_set_id . ", $drug_tag_id )")->execute();
                }

                /*
                 * Update ophciexamination_risk_tag
                 */
                Yii::app()->db->createCommand("UPDATE ophciexamination_risk_tag SET medication_set_id = " . $ref_set_id . " WHERE tag_id = " . $tag['id'])->execute();

            }
        }
    }

    public function down()
    {
        echo "m180711_092304_create_sets_from_tags does not support migration down.\n";
        return false;
    }
}
