<?php

class m230321_133200_add_admin_setting_to_make_context_mandatory_for_internal_referrals extends OEMigration
{
	public function up()
	{
        $this->addSetting(
            'correspondence_make_context_mandatory_for_internal_referrals',
            'Make context mandatory for internal referrals',
            'An option to make the “Context” fields to be mandatory when Internal Referral is selected',
            'Correspondence',
            'Radio buttons',
            'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            'off',
            'INSTALLATION'
        );
	}

	public function down()
	{
		$this->deleteSetting('correspondence_make_context_mandatory_for_internal_referrals');
	}
}
