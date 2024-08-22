<?php

class m230609_115900_add_hide_followup_options_setting extends OEMigration
{
    public function safeUp()
    {
        $this->addSetting(
            'hide_addional_followup_options',
            'Hide site, subspecialty and context options in Follow-up adder',
            "When on, The site, subspecialty and context options will be hidden in the Follow-up adder in the Examination->Follow-up element. This is useful if you don't use these options and want to reduce the number of columns shown in the adder.",
            'Examination',
            'Checkbox',
            '',
            0
        );

        return true;
    }

    public function safedown()
    {
        $this->deleteSetting('hide_addional_followup_options');
        return true;
    }
}
