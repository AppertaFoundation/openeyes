<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

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
