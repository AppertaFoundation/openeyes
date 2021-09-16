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

class m160815_121835_sample_disorder_data extends CDbMigration
{
    public function up()
    {
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Retina', 'comments_allowed' => 1,
            'comments_label' => 'other retinal : please specify', 'display_order' => 1, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Glaucoma', 'comments_allowed' => 1,
            'comments_label' => 'other glaucoma : please specify', 'display_order' => 2, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Globe', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 3, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Neurological', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 4, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Choroid', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 5, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Lens', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 6, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Cornea', 'comments_allowed' => 0,
            'comments_label' => '', 'display_order' => 7, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Paediatric', 'comments_allowed' => 1,
            'comments_label' => 'congenital: please specify syndrome or nature of the malformation',
            'display_order' => 8, 'active' => 1));
        $this->insert('ophcocvi_clinicinfo_disorder_section', array('name'=>'Neoplasia', 'comments_allowed' => 1,
            'comments_label' => 'other neoplasia: please specify', 'display_order' => 9, 'active' => 1));
    }

    public function down()
    {
        $this->truncateTable('ophcocvi_clinicinfo_disorder_section');
    }

}
