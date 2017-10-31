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

/**
 * This is the model class for table "RTT".
 *
 * The followings are the available columns in table 'rtt':
 *
 * @property int $id
 * @property string $clock_start
 * @property string $clock_end
 * @property string $breach
 * @property int $referral_id
 * @property bool $active
 * @property string comments
 * @property int last_modified_user_id
 * @property string last_modified_date
 * @property int created_user_id
 * @property string created_date
 */
class RTT extends BaseActiveRecord
{
    public $use_pas = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Referral the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Suppress PAS integration.
     *
     * @return Referral
     */
    public function noPas()
    {
        // Clone to avoid singleton problems with use_pas flag
        $model = clone $this;
        $model->use_pas = false;

        return $model;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'rtt';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
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
                'referral' => array(self::BELONGS_TO, 'Referral', 'referral_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Pass through use_pas flag to allow pas supression.
     *
     * @see CActiveRecord::instantiate()
     */
    protected function instantiate($attributes)
    {
        $model = parent::instantiate($attributes);
        $model->use_pas = $this->use_pas;

        return $model;
    }
}
