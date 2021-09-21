<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ophtrconsent_template".
 *
 * The followings are the available columns in table 'ophtrconsent_template':
 *
 * @property string $id
 * @property string $name
 * @property int $institution_id
 * @property int $site_id
 * @property int $subspecialty_id
 * @property int $type_id
 *
 * The followings are the available model relations:
 * @property TemplateProcedure $template_procedure
 */
class OphTrConsent_Template extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     *
     * @return Template the static model class
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
        return 'ophtrconsent_template';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, type_id', 'required'),
            array('id', 'length', 'max' => 20),
            array('name, institution_id, site_id, subspecialty_id, type_id', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'type' => array(self::BELONGS_TO, 'OphTrConsent_Type_Type', 'type_id'),
            'procedureTemplate' => array(self::HAS_MANY, 'TemplateProcedure', 'template_id'),
            'procedures' => array(
                self::MANY_MANY,
                'Procedure',
                'ophtrconsent_template_procedure(procedure_id, template_id)'
            )
        );
    }

    public function canAutocomplete()
    {
        return true;
    }

    public function getAutocompleteField()
    {
        return 'term';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspecialty',
            'type_id' => 'Type'
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
        $criteria->compare('lower(name)', strtolower($this->name), true);

        return new CActiveDataProvider(get_class($this), array('criteria' => $criteria));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param array $procedures
     * @throws CDbException
     */
    public function saveProcedures(array $procedures)
    {
        $transaction = Yii::app()->db->beginTransaction();
        OphTrConsent_TemplateProcedure::model()->deleteAll('template_id = :template_id', array('template_id' => $this->id));
        foreach ($procedures as $procedure) {
            $templateProcedure = new OphTrConsent_TemplateProcedure();
            $templateProcedure->template_id = $this->id;
            $templateProcedure->procedure_id = $procedure;
            if (!$templateProcedure->insert()) {
                throw new CDbException('Unable to save procedure assignment');
            }
        }
        $transaction->commit();
    }
}
