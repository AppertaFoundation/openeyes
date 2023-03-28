<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 * Creation date: 1 June 2019
 * @package Clinical
 *
 * This is the model class for table "element__comment".
 *
 * The followings are the available columns in table 'element__comment':
 * @property string $id
 * @property string $event_id
 * @property string $created_user_id
 * @property string $created_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $comment
 *
 */

namespace OEModule\OphGeneric\models;

use OE\factories\models\traits\HasFactory;

class Comments extends \BaseEventTypeElement
{
    use HasFactory;

    public $widgetClass = 'OEModule\OphGeneric\widgets\Comments';

    /**
     * Returns the static model of the specified AR class.
     * @return Comments the static model class
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
        return 'et_ophgeneric_comments';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // Only define rules for those attributes with user inputs.
        return array(
            array('comment', 'length', 'max' => 255),
            // Remove attributes that should not be searched.
            array('comment', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'comment' => 'Comment',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('comment', $this->comment, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function isAtTip()
    {
        return true;
    }
}
