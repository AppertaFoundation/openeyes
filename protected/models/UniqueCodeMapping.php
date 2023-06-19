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
 * This is the model class for table "unique_codes".
 *
 * The followings are the available columns in table 'unique_codes':
 *
 * @property int $id
 * @property string $name
 */
class UniqueCodeMapping extends BaseActiveRecord
{
    use HasFactory;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return ElementOperation the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'unique_codes_mapping';
    }

    public function rules()
    {
        return array(
            array('id,event_id,unique_code_id,user_id', 'safe'),
            array('unique_code_id', 'required'),
        );
    }

    public function relations()
    {
        return array(
            'event_id' => array(self::BELONGS_TO, 'Event', 'id'),
            'unique_code_id' => array(self::BELONGS_TO, 'UniqueCodes', 'id'),
            'unique_codes' => [self::BELONGS_TO, 'UniqueCodes', 'unique_code_id'],
            'user_id' => array(self::BELONGS_TO, 'User', 'id'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
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
     * @return array default scope (applies only to SELECT statements)
     */
    public function defaultScope()
    {
        return array(
            'alias' => $this->tableName().'table',
        );
    }

    public function lock()
    {
        Yii::app()->db->createCommand('LOCK TABLES `'.UniqueCodes::model()->tableName().'` READ, `'.UniqueCodes::model()->tableName().'` AS `'.UniqueCodes::model()->getTableAlias().'` READ, `'.$this->tableName().'` WRITE,`'.$this->tableName().'` as `'.$this->getTableAlias().'` READ ')->execute();
    }

    public function unlock()
    {
        Yii::app()->db->createCommand('UNLOCK TABLES')->execute();
    }
}
