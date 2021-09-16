<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m160916_090908_add_site_to_event extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophcocvi_eventinfo', 'site_id', 'int(10) unsigned');
        $this->addForeignKey('et_ophcocvi_eventinfo_site_fk', 'et_ophcocvi_eventinfo', 'site_id', 'site', 'id');
        $this->addColumn('et_ophcocvi_eventinfo_version', 'site_id', 'int(10) unsigned');
    }

    public function down()
    {
        $this->dropForeignKey('et_ophcocvi_eventinfo_site_fk');
        $this->dropColumn('et_ophcocvi_eventinfo_version', 'site_id');
        $this->dropColumn('et_ophcocvi_eventinfo', 'site_id');
    }

}
