<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Generate a .dot graph file from the RBAC authitems in the database
 */
class VisualiseRbacCommand extends CConsoleCommand
{
	public function run()
	{
		echo "digraph authitems {\n\trankdir=LR;\n\tnode [style=filled color=black];\n";

		$authMan = Yii::app()->authManager;

		foreach ($authMan->authItems as $item) {
			switch ($item->type) {
				case 0:
					$colour = 'red';
					break;
				case 1:
					$colour = 'orange';
					break;
				case 2:
					$colour = 'green';
					break;
				default:
					throw new Exception("Unrecognised authitem type: {$item->type}");
			}

			$name = $this->quote($item->name);
			echo "\t{$name} [fillcolor={$colour}];\n";
			foreach ($authMan->getItemChildren($item->name) as $child) {
				echo "\t{$name} -> " . $this->quote($child->name) . ";\n";
			}
		}

		echo "}\n";
	}

	protected function quote($name)
	{
		return '"' . addslashes($name) . '"';
	}
}