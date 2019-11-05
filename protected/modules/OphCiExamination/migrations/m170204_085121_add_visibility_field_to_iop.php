<?php

class m170204_085121_add_visibility_field_to_iop extends CDbMigration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ophciexamination_instrument` ADD COLUMN `visible` INT(1) UNSIGNED DEFAULT 1');
        $this->execute('ALTER TABLE `ophciexamination_instrument_version` ADD COLUMN `visible` INT(1) UNSIGNED DEFAULT 1');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE `ophciexamination_instrument` DROP COLUMN `visible`');
        $this->execute('ALTER TABLE `ophciexamination_instrument_version` DROP COLUMN `visible`');
    }
}