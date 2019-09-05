<?php

class m180227_104600_add_disorders extends OEMigration
{
    public function up()
    {
        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (48867003, 'Bradycardia (finding)', 'Bradycardia', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (3424008, 'Tachycardia (finding)', 'Tachycardia', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (88610006, 'Heart murmur (finding)', 'Heart murmur', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (60845006, 'Dyspnea on exertion (finding)', 'Dyspnea on exertion', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (399153001, 'Vertigo (finding)', 'Vertigo', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (60862001, 'Tinnitus (finding)', 'Tinnitus', NULL);");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (162290004, 'Dry eyes (finding)', 'Dry eyes', 109);");

    }

    public function down()
    {
        echo 'Down method not supported';
    }

}
