<?php

class m180126_123700_tag_common_alphablockers extends CDbMigration
{
	public function up()
	{
        $this->execute("SET @alphatag=(SELECT id from tag where `name` = 'Alphablocker');

            # DM+D
            INSERT IGNORE INTO medication_drug_tag (medication_drug_id, tag_id)
            SELECT id, @alphatag FROM medication_drug WHERE external_code in (108556006, 76058001, 4078311000001100, 129484001, 318778006, 108559004, 17198211000001104, 23675711000001101, 82165003, 424288000);

            # Formulary
            CREATE TEMPORARY TABLE IF NOT EXISTS tmp_drugs ( id INT );

            INSERT INTO tmp_drugs (id)
            SELECT id FROM drug where `name` like 'Doxazosin%';

            INSERT INTO tmp_drugs (id)
            SELECT id FROM drug where `name` like 'Phentolamine%';

            INSERT INTO tmp_drugs (id)
            SELECT id FROM drug where `name` like 'Tamsulosin%';

            INSERT IGNORE INTO drug_tag (drug_id, tag_id)
            SELECT id, @alphatag FROM tmp_drugs;

            DROP TABLE tmp_drugs;");

	}

	public function down()
	{
		echo "m180126_123700_tag_common_alphablockers does not support migration down.\n";
		return false;
	}
}
