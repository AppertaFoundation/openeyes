<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_visualfunction".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $eye_id
 * @property string $left_comments
 * @property string $right_comments
 *
 * The followings are the available model relations:

 * @property User $user
 * @property User $usermodified
 * @property Eye eye
 * @property Event $event
 */

class Element_OphCiExamination_VisualFunction extends \SplitEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
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
        return 'et_ophciexamination_visualfunction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('left_comments, right_comments,  eye_id, left_rapd, right_rapd', 'safe'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, left_comments, right_comments, eye_id', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('comments', 'rapd');
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_comments' => 'Comments',
            'right_comments' => 'Comments',
            'left_rapd' => 'RAPD',
            'right_rapd' => 'RAPD',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('left_comments', $this->left_comments);
        $criteria->compare('right_comments', $this->right_comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function afterValidate()
    {
        if ($this->scenario == 'formHasNoChildren') {
            $values = false;
            if ($this->hasLeft()) {
                if ($this->left_rapd || $this->left_comments) {
                    $values = true;
                }
            }
            if (!$values && $this->hasRight()) {
                if ($this->right_rapd || $this->right_comments) {
                    $values = true;
                }
            }
            if (!$values) {
                $this->addError(null, "Visual Function requires data");
            }
        }
        parent::afterValidate();
    }

    public function canViewPrevious()
    {
        return true;
    }
}
