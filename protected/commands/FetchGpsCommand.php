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
		echo "Fetching GPs from PAS...\n";

		$service = new GpService;

		$service->PopulateGps();
	}
}
