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
 * This is the model class for table "ophinbiometry_lenstype_lens".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 * @property bool $deleted
 * @property string $description
 * @property int $position_id
 * @property string $comments
 * @property double $acon
 * @property float $sf
 * @property float $pACD
 * @property float $a0
 * @property float $a1
 * @property float $a2
 * @property bool $active
 * @property string $display_name
 *
 * The followings are the available model relations:
 * @property User $user
 * @property User $usermodified
 */
class OphInBiometry_LensType_Lens extends BaseActiveRecordVersionedSoftDelete
{
    public $notDeletedField = 'active';

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphInBiometry_LensType_Lens static model class
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
        return 'ophinbiometry_lenstype_lens';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, display_name, description, comments, position_id, acon, sf, pACD, a0, a1, a2, active', 'safe'),
            array('name, display_name, description, acon, position_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, active', 'safe', 'on' => 'search'),
        );
    }

    public function scopes()
    {
        return array(
            'active' => array(
                'condition' => 'active=1',
            ),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'position' => array(self::BELONGS_TO, 'OphInBiometry_Lens_Position', 'position_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'position_id' => 'Position',
            'acon' => 'A constant',
            'display_name' => 'Display name',
            'sf' => 'SF',
            'pACD' => 'pACD',
            'a0' => 'a0',
            'a1' => 'a1',
            'a2' => 'a2',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
