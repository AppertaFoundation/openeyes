<?php

class RbacCommand extends CConsoleCommand
{
    private $_authManager;

    public function getHelp()
    {
        return <<<EOD
Rebuilds OpenEyes basic RBAC permissions.

EOD;
    }

    public function run($args)
	{
		if (($this->_authManager = Yii::app()->authManager) === null)
		{
			echo "No authManager configured.\n";
			return;
		}

		echo "Rebuild OpenEyes basic RBAC permissions? [Yes|No]\n";

		if (!strncasecmp(trim(fgets(STDIN)), 'y', 1))
		{
			$this->_authManager->clearAll();

			$this->_authManager->createOperation('create User', 'create User');
			$this->_authManager->createOperation('view User', 'view User');
			$this->_authManager->createOperation('update User', 'update User');
			$this->_authManager->createOperation('delete User', 'delete User');

			$this->_authManager->createOperation('Rbac', 'Rbac');

			$role = $this->_authManager->createRole('admin');

			$role->addChild('create User');
			$role->addChild('view User');
			$role->addChild('update User');
			$role->addChild('delete User');
			$role->addChild('Rbac');
		}
		else
		{
			echo "Exiting.\n";
		}
	}
}