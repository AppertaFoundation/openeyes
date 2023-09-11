<?php

class m230823_163220_adding_lid_mass_doodle_to_search_index extends OEMigration
{
    private array $doodle_properties = [
        ['Attached to', 'Top lid, Bottom lid, No attachment', 'attachment'],
        ['Type', 'Nodular, Cystic, Multicystic, Ulcerated, Necrotic, Pigmented, Papillomatous', 'type'],
        ['Surrounding redness', null, 'surroundingRedness'],
        ['Surface blood vessels', null, 'surfaceBloodVessels'],
        ['Lashes absent', null, 'lashesAbsent'],
        ['Caliper', null, 'caliper'],
    ];
    public function safeUp()
    {
        $this->addToSearchIndex(
            'OphCiExamination',
            'Lids Surgical',
            'Lid lesion',
            'Lid mass',
            'OEModule\OphCiExamination\models\SurgicalLids',
            null,
            null,
            null,
            null,
            null,
            'LidMass',
            null,
            null
        );

        foreach($this->doodle_properties as $properties) {
            $this->addToSearchIndex(
                'OphCiExamination',
                'Lids Surgical',
                $properties[0],
                $properties[1],
                'OEModule\OphCiExamination\models\SurgicalLids',
                null,
                null,
                null,
                null,
                null,
                'LidMass',
                $properties[2],
                null
            );
        }
    }

    public function safeDown()
    {
        $parent_id = $this->dbConnection->createCommand("SELECT id FROM index_search WHERE primary_term = :parent_term")->queryScalar([ ':parent_term' => 'Lids Surgical' ]);
        $this->delete('index_search', "goto_property = '' AND parent_id = :parent_id", ['parent_id' => $parent_id]);


        $this->delete('index_search', "primary_term = 'Lid lesion' AND parent_id = :parent_id", ['parent_id' => $parent_id]);
    }
}
