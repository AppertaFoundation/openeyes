<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_further_findings".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property Finding[] $further_findings
 * @property User $user
 * @property User $usermodified
 * @property Event $event
 */
class Element_OphCiExamination_FurtherFindings extends \BaseEventTypeElement
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_FurtherFindings the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_further_findings';
    }

    public function rules()
    {
        return array(
            array('further_findings', 'safe'),
            array('id, event_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'further_findings_assignment' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_FurtherFindings_Assignment', 'element_id'),
            'further_findings' => array(self::MANY_MANY, 'Finding', 'ophciexamination_further_findings_assignment(element_id, finding_id)', 'order' => 'display_order, name'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'further_findings' => 'Findings',
        );
    }

    public function canViewPrevious()
    {
        return true;
    }

    public function getFurtherFindingsAssigned()
    {
        $further_findings = array();

        if ($this->id) {
            foreach (OphCiExamination_FurtherFindings_Assignment::model()->findAll('element_id=?', array($this->id)) as $ff) {
                $further_findings[] = $ff->finding_id;
            }
        }

        return $further_findings;
    }

    public function getFurtherFindingsAssignedString($ignore_ids = array())
    {
        $further_findings = array();

        if (!empty($this->further_findings_assignment)) {
            foreach ($this->further_findings_assignment as $assignment) {
                if (!in_array($assignment->finding_id, $ignore_ids)) {
                    $further_findings[] = $assignment->finding->requires_description ? $assignment->finding->name.': '.$assignment->description : $assignment->finding->name;
                }
            }
        }

        return implode(', ', $further_findings);
    }

    public function afterValidate()
    {
        if ($this->further_findings_assignment) {
            foreach ($this->further_findings_assignment as $assignment) {
                if (!$assignment->validate()) {
                    foreach ($assignment->errors as $field => $errors) {
                        $this->addError($field, $errors[0]);
                    }
                }
            }
        } else {
            $this->addError('further_findings_assignment', 'Please select at least one finding');
        }
        parent::afterValidate();
    }

    public function isChild($action){
        if ($action=='view') {
            return false;
        } else {
            return parent::isChild($action);
        }
    }
}
