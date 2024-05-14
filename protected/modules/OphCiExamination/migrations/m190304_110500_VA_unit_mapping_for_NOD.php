<?php

class m190304_110500_VA_unit_mapping_for_NOD extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("CREATE TEMPORARY TABLE tmp190304 (id int);");

        $this->execute("INSERT INTO tmp190304 (SELECT max(id) FROM ophciexamination_visual_acuity_unit_value GROUP BY unit_id, base_value HAVING count(*) > 1);");

        $this->execute("DELETE FROM ophciexamination_visual_acuity_unit_value WHERE id IN (SELECT id FROM tmp190304);");

        $this->execute("DROP TABLE tmp190304;");

        $this->execute("ALTER TABLE ophciexamination_visual_acuity_unit_value ADD CONSTRAINT unique_unit_base UNIQUE (unit_id, base_value);");
        $this->execute("ALTER TABLE ophciexamination_visual_acuity_unit_value ADD CONSTRAINT unique_unit_value UNIQUE (unit_id, value);");

        $unit_id = $this->dbConnection->createCommand()->select('id')->from('ophciexamination_visual_acuity_unit')->where('LOWER(name) = ?', array('logmar single-letter'))->queryScalar();

        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.78,  21);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.70,  25);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.68,  26);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.66,  27);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.64,  28);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.62,  29);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.60,  30);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.58,  31);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.56,  32);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.54,  33);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id,  1.52,  34);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.50, 135);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.52, 136);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.54, 137);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.56, 138);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.58, 139);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.60, 140);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.62, 141);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.64, 142);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.66, 143);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.68, 144);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.70, 145);");
        $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (unit_id, value, base_value) VALUES ($unit_id, -0.80, 150);");
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE ophciexamination_visual_acuity_unit_value DROP INDEX unique_unit_base;");
    }
}
