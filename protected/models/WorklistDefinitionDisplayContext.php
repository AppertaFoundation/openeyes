<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class WorklistDefinitionDisplayContext.
 *
 * @property int $worklist_definition_id
 * @property int $institution_id
 * @property int $site_id
 * @property int $subspecialty_id
 * @property int $firm_id
 * @property WorklistDefinition $worklist_definition
 * @property Institution $institution
 * @property Site $site
 * @property Subspecialty $subspecialty
 * @property Firm $firm
 */
class WorklistDefinitionDisplayContext extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_definition_display_context';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('worklist_definition_id', 'required'),
            array('institution_id, site_id, subspecialty_id, firm_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, worklist_definition_id, institution_id, site_id, subspecialty_id, firm_id', 'safe', 'on' => 'search'),
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
            'worklist_definition' => array(self::BELONGS_TO, 'WorklistDefinition', 'worklist_definition_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    public function afterValidate()
    {
        $one_of = array('institution_id', 'site_id', 'subspecialty_id', 'firm_id');
        $found = false;
        foreach ($one_of as $attr) {
            if ($this->$attr) {
                $found = true;
            }
        }
        if (!$found) {
            $this->addError(null, 'At least one of '.implode(', ', array_map(function ($attr) {
                    return $this->getAttributeLabel($attr);
            }, $one_of)).' must be set.');
        }

        parent::afterValidate();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspecialty',
            'firm_id' => Firm::contextLabel(),
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
        $criteria->compare('worklist_definition_id', $this->worklist_definition_id, true);
        $criteria->compare('institution_id', $this->institution_id, true);
        $criteria->compare('site_id', $this->site_id, true);
        $criteria->compare('subspecialty_id', $this->subspecialty_id, true);
        $criteria->compare('firm_id', $this->firm_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Check if the Institution is supported in this display context.
     *
     * @param Institution $institution
     *
     * @return bool
     */
    public function checkInstitution(Institution $institution)
    {
        return !$this->institution_id || ($this->institution_id === $institution->id);
    }

    /**
     * Check if the Site is supported in this display context.
     *
     * @param Site $site
     *
     * @return bool
     */
    public function checkSite(Site $site)
    {
        return !$this->site_id || ($this->site_id == $site->id);
    }

    /**
     * Check if the Firm is supported in this display context.
     *
     * @param Firm $firm
     *
     * @return bool
     */
    public function checkFirm(Firm $firm)
    {
        if ($this->firm_id) {
            return $firm->id == $this->firm_id;
        }

        if ($this->subspecialty_id) {
            $firm_subspecialty = $firm->getSubspecialty();

            return $firm_subspecialty && $firm_subspecialty->id == $this->subspecialty_id;
        }

        // no restriction on firm or subspecialty
        return true;
    }

    public function getInstitutionDisplay()
    {
        return $this->institution ? $this->institution->name : 'Any';
    }

    public function getSiteDisplay()
    {
        return $this->site ? $this->site->getShortname() : 'Any';
    }

    public function getSubspecialtyDisplay()
    {
        if ($this->firm) {
            return $this->firm->subspecialty->name;
        }

        return $this->subspecialty ? $this->subspecialty->name : 'Any';
    }

    public function getFirmDisplay()
    {
        return $this->firm ? $this->firm->name : 'Any';
    }
}
