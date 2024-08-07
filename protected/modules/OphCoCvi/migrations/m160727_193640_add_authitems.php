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

class m160727_193640_add_authitems extends CDbMigration
{
    private $authitems = array(
        array('name' => 'Clinical CVI', 'type' => 2),
        array('name' => 'Clerical CVI', 'type' => 2),
        array('name' => 'TaskClinicalCvi', 'type' => 1),
        array('name' => 'TaskClericalCvi', 'type' => 1),
        array('name' => 'OprnCreateCvi', 'type' => 0, 'bizrule' => 'OphCoCvi.canCreateOphCoCvi'),
        array('name' => 'OprnEditCvi', 'type' => 0, 'bizrule' => 'OphCoCvi.canEditOphCoCvi'),
        array('name' => 'OprnEditClinicalCvi', 'type' => 0, 'bizrule' => 'OphCoCvi.canEditClinicalOphCoCvi'),
        array('name' => 'OprnEditClinicalCviExplicit', 'type' => 0),
        array('name' => 'OprnEditClericalCvi', 'type' => 0),
        array('name' => 'OprnPatientSignatureCvi', 'type' => 0),
        array('name' => 'OprnConsultantSignatureCvi', 'type' => 0),
        array('name' => 'OprnCompleteCvi', 'type' => 0)
    );

    private $parents = array(
        // These must be set under a role that any logged in user must have, as just receiving true from the bizrule
        // is insufficient if the user does not have a specific authitem assigned to them that is a parent of it.
        'OprnCreateCvi' => 'User',
        'OprnEditCvi' => 'User',
        'OprnEditClinicalCvi' => 'User',
        // general hierarchy
        'OprnEditClericalCvi' => 'TaskClericalCvi',
        'OprnPatientSignatureCvi' => 'TaskClericalCvi',
        'OprnCompleteCvi' => 'TaskClericalCvi',
        'OprnEditClinicalCviExplicit' => 'TaskClinicalCvi',
        'OprnPatientSignatureCvi' => 'TaskClinicalCvi',
        'OprnConsultantSignatureCvi' => 'TaskClinicalCvi',
        'TaskClinicalCvi' => 'Clinical CVI',
        'TaskClericalCvi' => 'Clerical CVI'
    );

    public function up()
    {
        foreach ($this->authitems as $authitem) {
            $this->insert('authitem', $authitem);
        }

        foreach ($this->parents as $child => $parent) {
            $this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
        }
    }

    public function down()
    {
        foreach ($this->parents as $child => $parent) {
            $this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
        }

        foreach ($this->authitems as $authitem) {
            $this->delete('authitem', 'name = ?', array($authitem['name']));
        }
    }
}
