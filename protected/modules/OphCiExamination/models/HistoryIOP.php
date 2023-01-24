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

use OEModule\OphCiExamination\widgets\HistoryIOP as HistoryIOPWidget;

/**
 * This is the model class for table "et_ophciexamination_history_iop".
 *
 * The followings are the available columns in table 'et_ophciexamination_history_iop':
 *
 * @property integer $id
 * @property string $event_id
 * @property string $eye_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Event $event
 * @property Eye $eye
 * @property User $lastModifiedUser
 */
class HistoryIOP extends \SplitEventTypeElement
{
    use traits\CustomOrdering;

    public $container_view_view = 'element_container_no_view';
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    protected $widgetClass = HistoryIOPWidget::class;
    protected $default_from_previous = false;

    public $examination_dates = [];

    public function behaviors()
    {
        return [
            'PatientLevelElementBehaviour' => 'PatientLevelElementBehaviour',
        ];
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_history_iop';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['examination_dates', 'dateValidatorNotFuture'],
            ['event_id, eye_id, last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            ['id, event_id, eye_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
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
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * Validate examination dates
     *
     * @param $attribute
     * @param $params
     */
    public function dateValidatorNotFuture($attribute, $params)
    {
        foreach (['left_values', 'right_values'] as $side_values) {
            if (isset($this->$attribute) && $this->$attribute) {
                $side_attributes = $this->$attribute;
                if (isset($side_attributes[$side_values])) {
                    foreach ($side_attributes[$side_values] as $index => $date) {
                        if (!isset($date) || !$date) {
                            $this->addError($side_values . '_' . $index . '_examination_date', 'there must be a date set for the iop value');
                        } else {
                            $error_eye_side = $side_values . '_' . $index . '_examination_date';
                            $error_message = 'Date is wrongly formated: format accepted: dd-mm-yyyy';
                            if (preg_match('/^(\d{2}-\d{2}-\d{4})$/', $date)) {
                                $dateTime = \DateTime::createFromFormat('d-m-Y', $date);
                                $errorsDate = \DateTime::getLastErrors();

                                if (!$dateTime || !empty($errorsDate['warning_count'])) {
                                    $this->addError($error_eye_side, $error_message);
                                } else {
                                    // don't accept event dates set in the future
                                    if (\DateTime::createFromFormat('d-m-Y', $date)->getTimestamp() > time()) {
                                        $this->addError($error_eye_side, 'Event Date cannot be in the future.');
                                    }
                                }
                            } else {
                                $this->addError($error_eye_side, $error_message);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event',
            'eye_id' => 'Eye',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    public function getContainer_print_view()
    {
        return '//patient/element_container_no_view';
    }
}
