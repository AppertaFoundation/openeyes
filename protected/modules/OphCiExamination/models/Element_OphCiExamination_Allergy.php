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
 *
 * Class Element_OphCiExamination_Allergy
 * @package OEModule\OphCiExamination\models
 * @deprecated This class is no longer in use and has been replaced
 * @see Allergies
 */
class Element_OphCiExamination_Allergy extends \BaseEventTypeElement
{
    // Custom attribute to determine the allergy validation
    public $allergy_group;

    public function tableName()
    {
        return 'et_ophciexamination_allergy';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id', 'safe'),
            array('allergy_group', 'validateAllergy'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * To validate the allergy elements
     * @param $attribute
     * @param $param
     */
    public function validateAllergy($attribute, $param)
    {
        if ( $this->event->episode->patient->allergyAssignments &&
            !\Yii::app()->request->getParam('no_allergies') &&
            !\Yii::app()->request->getParam('selected_allergies') &&
            !\Yii::app()->request->getParam('deleted_allergies')
        ) {
            return;
        }
        if (!\Yii::app()->request->getParam('no_allergies') && !\Yii::app()->request->getParam('selected_allergies')) {
            $this->addError($attribute, 'Please select an allergy or confirm patient has no allergies');
        }
    }

}
