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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "common_systemic_disorder".
 *
 * The followings are the available columns in table 'common_systemic_disorder':
 *
 * @property int $id
 * @property string $disorder_id
 * @property int $display_order
 * @property int $group_id
 *
 * The followings are the available model relations:
 * @property Disorder $disorder
 */
class CommonSystemicDisorder extends BaseActiveRecordVersioned
{
    use HasFactory;
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return $this->tableName().'_id';
    }


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
        return array(
            array('disorder_id', 'required'),
            array('disorder_id', 'length', 'max' => 20),
            array('id, disorder_id, group_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'disorder' => [self::BELONGS_TO, 'Disorder', 'disorder_id', 'on' => 'disorder.active = 1'],
            'institutions' => array(self::MANY_MANY, 'Institution', $this->tableName().'_institution('.$this->tableName().'_id, institution_id)'),
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
     * @return Disorder[]
     */
    public static function getDisorders()
    {
        return Disorder::model()->findAll(array(
            'condition' => 'specialty_id is null',
            'join' => 'JOIN common_systemic_disorder csd ON csd.disorder_id = t.id',
            'order' => 'csd.display_order',
        ));
    }

    public static function getCommonSystemicDisorders()
    {
        return CommonSystemicDisorder::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, array(
            'join' => 'JOIN disorder ON t.disorder_id = disorder.id',
            'order' => 't.display_order',
        ));
    }

    public static function getDisordersWithDiabetesInformation()
    {
        $diagnoses = [];
        foreach (CommonSystemicDisorder::getCommonSystemicDisorders() as $common_systemic_disorder) {
            $diagnoses[] = [
                'term' => $common_systemic_disorder->disorder->term,
                'id' => $common_systemic_disorder->disorder->id,
                'group_id' => $common_systemic_disorder->group_id,
                'is_diabetes' => Disorder::model()->ancestorIdsMatch(array($common_systemic_disorder->disorder_id), Disorder::$SNOMED_DIABETES_SET)
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

    /**
     * Fetch disorders that are in a group
     * 
     * @return array
     */
    public static function getDisordersInGroup()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('group_id IS NOT NULL');
        $disorders_in_group = new CActiveDataProvider('CommonSystemicDisorder', array(
            'criteria' => $criteria,
            'pagination' => false,
        ));
        return array_values(
            array_unique(
                array_map(function ($disorder) {
                        return $disorder->group_id;
                },
                    $disorders_in_group->getData()
                )
            )
        );
    }
}
