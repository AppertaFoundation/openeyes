<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_systemicsurgery".
 *
 * The followings are the available columns in table 'et_ophciexamination_systemicsurgery':
 * @property integer $id
 * @property string $event_id
 * @property string $comments
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \Event $event
 * @property \User $lastModifiedUser
 * @property SystemicSurgery_Operation[] $systemicSurgery_Operation
 */
class SystemicSurgery extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    public $widgetClass = 'OEModule\OphCiExamination\widgets\SystemicSurgery';
    protected $default_from_previous = true;
    protected $default_view_order = 10;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_systemicsurgery';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        ];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['event_id, operations, comments, no_systemicsurgery_date', 'safe'],
            // The following rule is used by search().
            ['id, event_id, comments, no_systemicsurgery_date', 'safe', 'on'=>'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'operations' => [self::HAS_MANY, 'OEModule\OphCiExamination\models\SystemicSurgery_Operation',
                'element_id', 'order' => 'operations.date desc, operations.last_modified_date'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event',
            'comments' => 'Comments',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemicSurgery the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        $entries = $this->operations;
        foreach ($entries as $key => $entry) {
            if ($entry->had_operation == SystemicSurgery_Operation::$NOT_CHECKED) {
                unset($entries[$key]);
            }
        }
        $this->operations = $entries;
        return parent::beforeSave();
    }

    public function afterValidate()
    {
        if (!$this->no_systemicsurgery_date && !$this->operations && !$this->comments) {
            $this->addError('no_systemicsurgery_date', 'Please confirm patient has had no previous systemic surgery');
        }

        parent::afterValidate();
    }

    /**
     * Will duplicate values from the current socialhistory property of the given patient.
     *
     * @param static $element
     */
    public function loadFromExisting($element)
    {
        // use previous session's entries
        $operations = $this->operations;

        // if there are no posted entries from previous session
        if (!$operations) {
            // add the entries from the DB
            foreach ($element->operations as $prev) {
                $operation = new SystemicSurgery_Operation();
                $operation->operation = $prev->operation;
                $operation->side_id = $prev->side_id;
                $operation->date = $prev->date;
                $operation->had_operation = $prev->had_operation;
                $operations[] = $operation;
            }
        }

        $this->operations = $operations;
        $this->comments = $element->comments;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' <br /> ', $this->operations);
    }

    public function getTileSize($action)
    {
        return $action === 'view' || $action === 'createImage' || $action === 'renderEventImage' ? 1 : null;
    }

    public function getViewTitle()
    {
        return "Systemic Procedures";
    }
}
