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

class m120104_114028_add_session_metadata extends CDbMigration
{
	public function up()
	{
		$this->addColumn('sequence','consultant','tinyint(1) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('sequence','paediatric','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('sequence','anaesthetist','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','consultant','tinyint(1) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('session','paediatric','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','anaesthetist','tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('sequence','consultant');
		$this->dropColumn('sequence','paediatric');
		$this->dropColumn('sequence','anaesthetist');
		$this->dropColumn('session','consultant');
		$this->dropColumn('session','paediatric');
		$this->dropColumn('session','anaesthetist');
	}

}