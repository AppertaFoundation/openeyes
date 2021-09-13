<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class WorklistFilter.
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $filter
 */
class WorklistRecentFilter extends BaseActiveRecord
{
    public const MAX_RECENT_FILTERS = 5;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_recent_filter';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('filter', 'safe'),
            array('filter', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id', 'safe', 'on' => 'search'),
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
            'owner' => [self::BELONGS_TO, 'User', 'created_user_id']
        );
    }

    public function getForCurrentUser()
    {
        $criteria = new CDbCriteria();
        $current_user_id = Yii::app()->session['user']->id ?? Yii::app()->user->id;

        $criteria->addCondition('created_user_id = :user_id');
        $criteria->params = array(':user_id' => $current_user_id);

        return self::model()->findAll($criteria);
    }

    public function countForCurrentUser()
    {
        $criteria = new CDbCriteria();
        $current_user_id = Yii::app()->session['user']->id ?? Yii::app()->user->id;

        $criteria->addCondition('created_user_id = :user_id');
        $criteria->params = array(':user_id' => $current_user_id);

        return (int)self::model()->count($criteria);
    }

    public function removeOldFiltersForCurrentUser()
    {
        $criteria = new CDbCriteria();
        $current_user_id = Yii::app()->session['user']->id ?? Yii::app()->user->id;

        $criteria->addCondition('created_user_id = :user_id');
        $criteria->params = array(':user_id' => $current_user_id);

        $count = self::model()->count($criteria);

        if ($count > self::MAX_RECENT_FILTERS) {
            $criteria->order = "last_modified_date ASC";
            $criteria->limit = $count - self::MAX_RECENT_FILTERS;

            self::model()->deleteAll($criteria);
        }
    }
}
