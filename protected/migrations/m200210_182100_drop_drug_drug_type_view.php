<?php

class m200210_182100_drop_drug_drug_type_view extends CDbMigration
{
    public function up()
    {
        $this->execute("DROP VIEW IF EXISTS drug_drug_type;");
    }

    public function down()
    {
        $this->execute("CREATE VIEW `drug_drug_type` AS SELECT drug.id AS drug_id, drug_type.id AS drug_type_id
                        FROM drug_tag
                        LEFT JOIN drug ON drug.id = drug_tag.drug_id
                        LEFT JOIN drug_type ON drug_type.tag_id = drug_tag.tag_id
                        WHERE drug_type.id IS NOT NULL");
    }
}
