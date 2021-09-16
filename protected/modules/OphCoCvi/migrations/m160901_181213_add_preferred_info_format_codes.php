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

class m160901_181213_add_preferred_info_format_codes extends CDbMigration
{
    public function up()
    {
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt', 'code', 'varchar(15)');
        $this->addColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'code', 'varchar(15)');

        $this->update(
            'ophcocvi_clericinfo_preferred_info_fmt',
            array('code' => 'LGPRINT'),
            'name = :name',
            array(':name' => 'In large print')
        );
        $this->update(
            'ophcocvi_clericinfo_preferred_info_fmt',
            array('code' => 'CD'),
            'name = :name',
            array(':name' => 'On CD')
        );
        $this->update(
            'ophcocvi_clericinfo_preferred_info_fmt',
            array('code' => 'BRAILLE'),
            'name = :name',
            array(':name' => 'In braille')
        );
        $this->update(
            'ophcocvi_clericinfo_preferred_info_fmt',
            array('code' => 'EMAIL'),
            'name = :name',
            array(':name' => 'By email')
        );

    }

    public function down()
    {
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt', 'code');
        $this->dropColumn('ophcocvi_clericinfo_preferred_info_fmt_version', 'code');
    }

}
