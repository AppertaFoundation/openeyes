<?php

class m180501_092100_add_disorders extends OEMigration
{
    public function up()
    {
        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (247167001, 'Retinal operculated tear (disorder)', 'Retinal operculated tear', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (373426005, 'Epithelial basement membrane dystrophy (disorder)', 'Epithelial basement membrane dystrophy', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (247215006, 'Optic disc - myopic changes (finding)', 'Optic disc - myopic changes', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (82180009, 'Quadrantanopia (finding)', 'Quadrantanopia', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (193679001, 'Homonymous quadrant anopia (finding)', 'Homonymous quadrant anopia', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (762440005, 'Binasal heteronymous quadrantanopia (finding)', 'Binasal heteronymous quadrantanopia', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (762441009, 'Bitemporal heteronymous quadrantanopia (finding)', 'Bitemporal heteronymous quadrantanopia', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (373424008, 'Corneal guttata (finding)', 'Corneal guttata', (SELECT id FROM specialty WHERE code = 130));");

        $this->execute("INSERT IGNORE INTO disorder (id, fully_specified_name, term, specialty_id) VALUES (420515004, 'Retinal venous tortuosity (finding)', 'Retinal venous tortuosity', (SELECT id FROM specialty WHERE code = 130));");

    }

    public function down()
    {
        echo 'Down method not supported';
    }

}
