<?php

class m230123_115648_add_admin_setting_to_apply_city_country_postcode_on_same_line extends OEMigration
{
	public function up()
	{
        $this->addSetting(
            'correspondence_address_force_city_state_postcode_on_same_line',
            'Force city, state and postcode on same line in correspondence',
            'When set, city, state and postcode in correspondence print outs addresses will be printed in the final line, separated by spaces. Max number of lines setting will be ignored',
            'Correspondence',
            'Radio buttons',
            'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'off',
            'INSTALLATION'
        );
	}

	public function down()
	{
		$this->deleteSetting('correspondence_address_force_city_state_postcode_on_same_line');
	}
}
