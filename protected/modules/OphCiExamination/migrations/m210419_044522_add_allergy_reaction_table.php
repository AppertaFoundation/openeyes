<?php

class m210419_044522_add_allergy_reaction_table extends OEMigration
{
    public function up()
    {
        if (Yii::app()->db->schema->getTable('ophciexamination_allergy_reaction', true) === null &&
            Yii::app()->db->schema->getTable('ophciexamination_allergy_reaction_assignment', true) === null) {
            $this->createOETable('ophciexamination_allergy_reaction', array(
                'id' => 'pk',
                'name' => 'varchar(255)',
                'display_order' => 'int unsigned NOT NULL',
                'active' => 'tinyint(1) DEFAULT 1'
            ), true);

            $this->createOETable('ophciexamination_allergy_reaction_assignment', array(
                'id' => 'pk',
                'allergy_entry_id' => 'int(10)',
                'reaction_id' => 'int(10)',
            ), true);

            $this->addForeignKey(
                'ophciallergy_reaction_assignment_allergy_id_fk',
                'ophciexamination_allergy_reaction_assignment',
                'allergy_entry_id',
                'ophciexamination_allergy_entry',
                'id'
            );
            $this->addForeignKey(
                'ophciallergy_reaction_assignment_reaction_id_fk',
                'ophciexamination_allergy_reaction_assignment',
                'reaction_id',
                'ophciexamination_allergy_reaction',
                'id'
            );
        }
    }

    public function down()
    {
        //This down function assumes that the tables did not exist prior to the migration.
        $this->dropForeignKey(
            'ophciallergy_reaction_assignment_reaction_id_fk',
            'ophciexamination_allergy_reaction_assignment',
        );

        $this->dropForeignKey(
            'ophciallergy_reaction_assignment_allergy_id_fk',
            'ophciexamination_allergy_reaction_assignment',
        );

        $this->dropOETable('ophciexamination_allergy_reaction_assignment', true);
        $this->dropOETable('ophciexamination_allergy_reaction', true);
    }
}
