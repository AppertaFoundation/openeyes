<?php

class m190523_130701_alter_constraint_drug_allergy_assignment_allergy_id_fk extends CDbMigration
{
    const TABLE_NAME = 'drug_allergy_assignment';
    const FOREIGN_KEY_CONSTRAINT_NAME = 'drug_allergy_assignment_allergy_id_fk';

    public function safeUp()
    {
        $this->dropForeignKey(self::FOREIGN_KEY_CONSTRAINT_NAME, self::TABLE_NAME);
        $this->alterColumn(self::TABLE_NAME, 'allergy_id', 'INT NOT NULL');
        $this->addForeignKey(self::FOREIGN_KEY_CONSTRAINT_NAME, self::TABLE_NAME, 'allergy_id', 'ophciexamination_allergy', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey(self::FOREIGN_KEY_CONSTRAINT_NAME, self::TABLE_NAME);
        $this->alterColumn(self::TABLE_NAME, 'allergy_id', 'INT(10) unsigned NOT NULL');
        $this->addForeignKey(self::FOREIGN_KEY_CONSTRAINT_NAME, self::TABLE_NAME, 'allergy_id', 'archive_allergy', 'id');
    }
}
