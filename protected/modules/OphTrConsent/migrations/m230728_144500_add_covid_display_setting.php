<?php

class m230728_144500_add_covid_display_setting extends OEMigration
{
    public function safeUp()
    {
        $this->addSetting(
            'display_covid_19_consent',
            'Show COVID 19 additional consent section on the consent form',
            'At the bottom of printed consent forms, an extra section for information relating to COVID 19 can be shown.<br>This setting determines if that section is shown or not',
            'Consent',
            'Checkbox',
            '',
            1
        );
    }

    public function SafeDown()
    {
        $this->deleteSetting('display_covid_19_consent');
    }
}
