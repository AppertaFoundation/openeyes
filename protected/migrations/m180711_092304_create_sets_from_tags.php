<?php

class m180711_092304_create_sets_from_tags extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophciexamination_risk_tag', 'medication_set_id', 'int(10) AFTER tag_id');
        $this->createIndex('idx_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id');
        $this->addForeignKey('fk_ref_set_id', 'ophciexamination_risk_tag', 'medication_set_id', 'medication_set', 'id');
    }

    public function down()
    {
        echo "m180711_092304_create_sets_from_tags does not support migration down.\n";
        return false;
    }
}
