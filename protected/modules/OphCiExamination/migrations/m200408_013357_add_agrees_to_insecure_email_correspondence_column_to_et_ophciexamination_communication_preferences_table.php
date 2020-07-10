<?php

class m200408_013357_add_agrees_to_insecure_email_correspondence_column_to_et_ophciexamination_communication_preferences_table extends OEMigration
{
    public function up()
    {
        $this->addOEColumn(
            'et_ophciexamination_communication_preferences',
            'agrees_to_insecure_email_correspondence',
            'tinyint(1) unsigned NOT NULL',
            true
        );
    }

    public function down()
    {
        $this->dropOEColumn('et_ophciexamination_communication_preferences', 'agrees_to_insecure_email_correspondence', true);
    }
}
