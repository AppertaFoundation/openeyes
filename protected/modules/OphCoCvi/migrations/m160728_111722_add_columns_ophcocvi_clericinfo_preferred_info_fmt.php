<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m160728_111722_add_columns_ophcocvi_clericinfo_preferred_info_fmt extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'require_email', 'tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `name` ');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'active', 'tinyint(1) unsigned not null default 1 AFTER `require_email` ');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'require_email', 'tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `name` ');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'active', 'tinyint(1) unsigned not null default 1 AFTER `require_email` ');
    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'require_email');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'active');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'require_email');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'active');
    }
}