<?php

class m170613_135445_genetics_add_more_relation_type extends CDbMigration
{
    public function up()
    {
        $this->update('genetics_relationship', array('relationship' => 'Sibling (full)'), 'relationship = "Sibling"');

        $relationships = array(
            "Son",
            "Daughter",
            "Twin (monozygous)",
            "Twin (dizygous)",
            "Twin (unknown)",
            "Half sibling (maternal)",
            "Half sibling (paternal)",
            "Maternal aunt",
            "Paternal aunt",
            "Maternal uncle",
            "Paternal uncle",
            "Niece",
            "Nephew",
            "Maternal cousin (first cousin)",
            "Paternal cousin (first cousin)",
            "Maternal grandmother",
            "Paternal grandmother",
            "Maternal grandfather",
            "Paternal grandfather",
            "Grandson",
            "Granddaughter",
        );

        foreach ($relationships as $relationship) {
            $this->insert('genetics_relationship', array('relationship' => $relationship));
        }
    }

    public function down()
    {
        $this->update('genetics_relationship', array('relationship' => 'Sibling'), 'relationship = "Sibling (full)"');

        $relationships = array(
            "Son",
            "Daughter",
            "Twin (monozygous)",
            "Twin (dizygous)",
            "Twin (unknown)",
            "Half sibling (maternal)",
            "Half sibling (paternal)",
            "Maternal aunt",
            "Paternal aunt",
            "Maternal uncle",
            "Paternal uncle",
            "Niece",
            "Nephew",
            "Maternal cousin (first cousin)",
            "Paternal cousin (first cousin)",
            "Maternal grandmother",
            "Paternal grandmother",
            "Maternal grandfather",
            "Paternal grandfather",
            "Grandson",
            "Granddaughter",
        );

        foreach ($relationships as $relationship) {
            $this->delete('genetics_relationship', 'relationship = "' . $relationship . '"');
        }
    }
}
