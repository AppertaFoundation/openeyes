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
 * This is the model class for table "common_systemic_disorder".
 *
 * The followings are the available columns in table 'common_systemic_disorder':
 *
 * @property int $id
 * @property string $disorder_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 */
class CommonSystemicDisorder extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return CommonSystemicDisorder the static model class
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
        return 'common_systemic_disorder';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('disorder_id', 'required'),
            array('disorder_id', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, disorder_id', 'safe', 'on' => 'search'),
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
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'disorder_id' => 'Disorder',
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
        $criteria->compare('disorder_id', $this->disorder_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return Disorders[]
     */
    public static function getDisorders()
    {
        return Disorder::model()->findAll(array(
            'condition' => 'specialty_id is null',
            'join' => 'JOIN common_systemic_disorder cad ON cad.disorder_id = t.id',
            'order' => 'term',
        ));
    }

    public static function getDisordersWithDiabetesInformation()
    {
        $diagnoses = [];
        foreach (CommonSystemicDisorder::getDisorders() as $disorder) {
            $diagnoses[] = [
                'term' => $disorder->term,
                'id' => $disorder->id,
                'is_diabetes' => Disorder::model()->ancestorIdsMatch(array($disorder->id), Disorder::$SNOMED_DIABETES_SET)
            ];
        }
        return $diagnoses;
    }

    /**
     * @param $firm
     * @return array
     */
    public static function getList($firm)
    {
        // it's unclear why this method expects a firm parameter when it is unused
        // possibly a future proofing idea that never bore fruit
        return CHtml::listData(static::getDisorders(), 'id', 'term');
    }
}
