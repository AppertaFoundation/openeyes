<?php
class FetchUsersCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'FetchUsers';
	}

	public function getHelp()
	{
		return 'Fetches all the Users from the MEH central user database and puts them in the OpenEyes DB.';
	}

	public function run($args)
	{
		echo "Fetching Users from MUU...\n";

		$users = MUUIDStaffTable::model()->findAll();

		foreach ($users as $user) {
			var_dump($user);
			exit;
		}
	}
}
