<?php

class m180208_105029_logMarCFHMPLNPL extends CDbMigration
{
    public function up()
    {
            $this->execute("INSERT IGNORE INTO ophciexamination_visual_acuity_unit_value (`unit_id`, `value`, `base_value`)
                            SELECT (SELECT id FROM ophciexamination_visual_acuity_unit WHERE LOWER(`name`) = 'logmar'),
                    			`value`
                                ,base_value
                    		FROM ophciexamination_visual_acuity_unit_value
                            WHERE `value` IN ('CF', 'HM', 'PL', 'NPL')
                    			AND unit_id = (SELECT id FROM ophciexamination_visual_acuity_unit WHERE LOWER(`name`) = 'logmar single-letter')
                                AND `value` NOT IN (SELECT `value` from ophciexamination_visual_acuity_unit_value WHERE unit_id = (SELECT id FROM ophciexamination_visual_acuity_unit WHERE LOWER(`name`) = 'logmar'));
                                ");
    }

    public function down()
    {
        // down not supported
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
