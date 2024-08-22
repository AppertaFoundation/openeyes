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
 * This is the model class for table "ethnic_group".
 *
 * The followings are the available columns in table 'ethnic_group':
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $display_order
 */
class EthnicGroup extends BaseActiveRecordVersionedSoftDelete
{
    const CVI_GROUPS = [
        'White' => ['A', 'B', 'C'],
        'Mixed/Multiple ethnic groups' => ['D', 'E', 'F', 'G'],
        'Asian/Asian British' => ['H', 'J', 'K', 'L'],
        'Black/African/Caribbean/Black British' => ['N', 'M', 'P'],
        'Chinese/Chinese British' => ['R', 'ZH'],
        'Other ethnic group' => ['Z']
    ];

    /**
     * Returns the static model of the specified AR class.
     *
     * @return EthnicGroup the static model class
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
        return 'ethnic_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, code, display_order', 'required'),
            array('id, name, code, display_order', 'safe', 'on' => 'search'),
            array('id_assignment', 'idAssignmentValidator'),
            array('code', 'uniqueCodeValidator')
        );
    }

    public function uniqueCodeValidator($attribute, $params)
    {
        if ($this->exists('id <> :id AND code = :code', [':id' => $this->id, ':code' => $this->code])) {
            $this->addError('code', 'This group has a code that is already in use: ' . $this->name);

            return false;
        }

        return true;
    }

    public function idAssignmentValidator($attribute, $params)
    {
        if (empty($this->id_assignment)) {
            return true;
        }

        if ($this->id_assignment == $this->id) {
            $this->addError('id_assignment', 'This group is attempting to be set as a parent of itself: ' . $this->name);

            return false;
        } elseif ($this->exists('id_assignment = :id', [':id' => $this->id])) {
            $this->addError('id_assignment', 'This group is a parent of other groups and cannot be a child of another: ' . $this->name);

            return false;
        }

        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function findAllAndGroup()
    {
        $ethnic_groups = $this->findAll();

        // creating empty data structure like $data = ['White' => [], 'Mixed/Multiple ethnic groups' => [], ...];
        $data = array_fill_keys(array_keys(self::CVI_GROUPS), []);

        // fill up empty arrays with models based on 'code'
        array_walk($data, function (&$value, $key) use ($ethnic_groups) {
            $value = array_filter($ethnic_groups, function ($group) use ($key) {
                return in_array($group->code, self::CVI_GROUPS[$key]);
            });
        });

        // now replace the name as required for the national form
        array_walk_recursive($data, function (&$value) {
            $value->name = $this->getCviName($value->name);
        });

        return $data;
    }

    /**
     * For CVI printout (national form) sometimes we need a slightly different name
     * @param null $name
     * @return string
     */
    public function getCviName($name = null): string
    {
        $term = is_null($name) ? $this->name : $name;
        switch ($term) {
            case 'White – British': // A
                $name = 'English/Northern Irish/Scottish/Welsh/British';
                break;
            case 'White – Irish': // B
                $name = 'Irish';
                break;
            case 'White – Any other background': // C
                $name = 'Any other White background, describe below';
                break;
            case 'Mixed – White/Black Caribbean': // D
                $name = 'White and Black Caribbean';
                break;
            case 'Mixed – White/Black African': // E
                $name = 'White and Black African';
                break;
            case 'Mixed – White and Asian': // F
                $name = 'White and Asian';
                break;
            case 'Mixed – Any other': // G
                $name = 'Any other Mixed/Multiple ethnic background, describe below';
                break;
            case 'Asian – Indian': // H
                $name = 'Indian';
                break;
            case 'Asian – Pakistani': // J
                $name = 'Pakistani';
                break;
            case 'Asian – Bangladeshi': // K
                $name = 'Bangladeshi';
                break;
            case 'Asian – Any other background': // L
                $name = 'Any other Asian background, describe below';
                break;
            case 'Black – African': // N
                $name = 'African';
                break;
            case 'Black – Caribbean': // M
                $name = 'Caribbean';
                break;
            case 'Black – Any other background': // P
                $name = 'Any other Black/African/Caribbean background, describe below';
                break;
            case 'Other – Chinese': // R
                $name = 'Chinese';
                break;
            default:
                $name = $term;
                break;
        }

        return $name;
    }
}
