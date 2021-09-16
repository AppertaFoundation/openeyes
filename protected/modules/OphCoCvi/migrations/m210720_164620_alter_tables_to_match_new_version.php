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

class m210720_164620_alter_tables_to_match_new_version extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY gp_delivery TINYINT(1) DEFAULT 0");
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY la_delivery TINYINT(1) DEFAULT 0");
        $this->execute("ALTER TABLE et_ophcocvi_eventinfo MODIFY rco_delivery TINYINT(1) DEFAULT 0");
    }

    public function down()
    {
        echo "m210720_164620_alter_tables_to_match_new_version does not support migration down.\n";

        return false;
    }
}
