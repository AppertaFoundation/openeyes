<?php
class FetchPASReferralsCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'FetchPASReferrals';
	}
	public function getHelp()
	{
		return 'Fetches the latest referrals from PAS and puts them in the OpenEyes DB.';
	}

	public function run($args)
	{
		echo "Fetching referrals from PAS...\n";	
	}
}
