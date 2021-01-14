<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;


use OEModule\OphCiExamination\widgets\VanHerick as VanHerickWidget;

/**
 * Class VanHerick
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 * @property int $left_van_herick_id
 * @property int $right_van_herick_id
 *
 * @property \Event $event
 * @property \EventType $eventType
 * @property \User $user
 * @property \User $usermodified
 */
class VanHerick extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected $widgetClass = VanHerickWidget::class;

    public function tableName()
    {
        return 'et_ophciexamination_van_herick';
    }

    public function behaviors()
    {
        return array(
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'right_van_herick_id' => 'Grade',
            'left_van_herick_id' => 'Grade',
            'right_notes' => 'Comments',
            'left_notes' => 'Comments'
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, left_van_herick_id, right_van_herick_id, eye_id, right_notes, left_notes', 'safe'),
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_van_herick' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Van_Herick', 'left_van_herick_id'),
            'right_van_herick' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Van_Herick', 'right_van_herick_id'),
        );
    }

    public function canCopy()
    {
        return true;
    }
}
