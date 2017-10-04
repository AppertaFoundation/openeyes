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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_injectmanagecomplex_notreatmentreason". It is used to define a lookup of reasons for not providing an injection treatment.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 * @property string $letter_str
 * @property bool $active
 * @property bool $other - flag to indicate whether this reason would need an other description
 */
class OphCiExamination_InjectionManagementComplex_NoTreatmentReason extends \BaseActiveRecordVersioned
{
    const DEFAULT_LETTER_STRING = 'The patient did not receive an intra-vitreal injection today.';

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
        return 'ophciexamination_injectmanagecomplex_notreatmentreason';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name, letter_str, display_order, active, other', 'safe'),
                array('name, display_order', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, name, letter_str, display_order, active, other', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'letter_str' => 'Correspondence Letter Text',
        );
    }

    /**
     * Get the string to be used in correspondence for this no treatment reason.
     *
     * @return string
     */
    public function getLetter_string()
    {
        $res = $this::DEFAULT_LETTER_STRING;
        if ($this->letter_str) {
            $res = $this->letter_str;
        } elseif (!$this->other) {
            $res .= ' '.$this->name.'.';
        }

        return $res;
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }
}
