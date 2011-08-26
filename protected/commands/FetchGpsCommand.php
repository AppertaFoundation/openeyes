<?php
class FetchGpsCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'FetchGps';
	}

	public function getHelp()
	{
		return 'Fetches all the GPs from PAS and puts them in the OpenEyes DB.';
	}

	public function run($args)
	{
		if (!Yii::app()->params['use_pas']) {
			echo("For this script to run use_pas must be set to true in one of the config files, e.g. params.php\n");
			exit;
		}

		echo "Fetching GPs from PAS...\n";

		$service = new GpService;

		$service->PopulateGps();
	}
}
