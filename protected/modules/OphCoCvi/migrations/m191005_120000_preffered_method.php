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

class m191005_120000_preffered_method extends OEMigration
{
    public function up()
    {
        $this->addOEColumn('ophcocvi_clericinfo_preferred_info_fmt', 'version', 'INT UNSIGNED NOT NULL DEFAULT 0', true);

        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'telephone',
            'active' => 1,
            'display_order' => 1,
            'deleted' => 0,
            'version' => 1,
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'email',
            'active' => 1,
            'display_order' => 2,
            'deleted' => 0,
            'version' => 1,
        ]);

        $this->insert('ophcocvi_clericinfo_preferred_info_fmt', [
            'name' => 'letter',
            'active' => 1,
            'display_order' => 3,
            'deleted' => 0,
            'version' => 1,
        ]);
    }

    public function down()
    {
        $this->execute("DELETE FROM ophcocvi_clericinfo_preferred_info_fmt WHERE version = 1");
        $this->dropOEColumn('ophcocvi_clericinfo_preferred_info_fmt', 'version', true);
    }
}
