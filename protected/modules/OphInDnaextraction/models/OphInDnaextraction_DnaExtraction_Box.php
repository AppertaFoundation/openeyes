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
 * This is the model class for table "ophindnaextraction_dnaextraction_box".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $value
 * @property int $display_order
 */
class OphInDnaextraction_DnaExtraction_Box extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     *
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
        return 'ophindnaextraction_dnaextraction_box';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('value, maxletter, maxnumber', 'required'),
            array('maxletter', 'match', 'pattern' => '/^[a-zA-Z\s]{1}+$/',
                    'message' => '{attribute} can only contain 1 word character'
            ),
            array('value, maxletter, maxnumber, display_order', 'safe'),
            array('maxnumber', 'numerical', 'integerOnly'=>true, 'min'=> 1 ),
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

    public function boxMaxValues($boxID)
    {
        $boxMaxValues = Yii::app()->db->createCommand()
            ->select('id, value, maxletter, maxnumber')
            ->from('ophindnaextraction_dnaextraction_box')
            ->where('id =:id', array(':id' => $boxID))
            ->queryRow();

        return $boxMaxValues;
    }

    public function attributeLabels()
    {
        return array(
            'value'      => 'Box name',
            'maxletter'  => 'Max letter',
            'maxnumber'  => 'Max number',
        );
    }
}
