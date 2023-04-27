<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "ophtroperationnote_generic_procedure_data".
 *
 *
 * @property string $id
 * @property int $proc_id
 * @property string $default_text
 *
 * The followings are the available model relations:
 * @property Procedure $procedure
 */
class OphTrOperationNote_Generic_Procedure_Data extends BaseActiveRecord
{
    use HasFactory;

    public $service;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationnote_generic_procedure_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('proc_id, default_text', 'safe'),
            array('proc_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id , proc_id, default_text', 'safe', 'on' => 'search'),
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
            'procedure' => array(self::BELONGS_TO, 'Procedure', 'proc_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'proc_id' => 'Procedure Id',
            'default_text' => 'Default Text'
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

    public function getContainer_form_view()
    {
        return false;
    }
}
