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
 * This is the model class for table "et_ophingeneticresults_test".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property string $result
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class Element_OphInGeneticresults_Test extends BaseEventTypeElement
{
    public $service;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphInGeneticresults_Test the static model class
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
        return 'et_ophingeneticresults_test';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, gene_id, method_id, comments, exon, base_change, method_id, amino_acid_change_id, base_change_id,
                 amino_acid_change, assay, effect_id, method_id homo, result, result_date, withdrawal_source_id, 
                 genomic_coordinate, genome_version, gene_transcript',
                'safe'),
            array('gene_id, homo, method_id, effect_id', 'required'),
            //array('withdrawal_source_id', 'required'),
            array('exon', 'validateForMethod', 'method' => 'Sanger'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, result, ', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'gene' => array(self::BELONGS_TO, 'PedigreeGene', 'gene_id'),
            'effect' => array(self::BELONGS_TO, 'OphInGeneticresults_Test_Effect', 'effect_id'),
            'method' => array(self::BELONGS_TO, 'OphInGeneticresults_Test_Method', 'method_id'),
            'withdrawal_source' => array(self::BELONGS_TO, 'Element_OphInDnaextraction_DnaTests', 'withdrawal_source_id'),
            'base_change_type' => array(self::BELONGS_TO, 'PedigreeBaseChangeType', 'base_change_id'),
            'amino_acid_change_type' => array(self::BELONGS_TO, 'PedigreeAminoAcidChangeType', 'amino_acid_change_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'event_id' => 'Event',
            'gene_id' => 'Gene',
            'method_id' => 'Method',
            'comments' => 'Comments',
            'exon' => 'Exon',
            'base_change' => 'Base Change',
            'amino_acid_change' => 'Amino Acid Change',
            'assay' => 'Assay',
            'effect_id' => 'Effect',
            'homo' => 'Homozygosity',
            'result' => 'Result',
            'result_date' => 'Result date',
            'withdrawal_source_id' => 'Withdrawal Source',
            'base_change_id' => 'Base Change Type',
            'amino_acid_change_id' => 'Amino Acid Change Type'
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
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('result', $this->result);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }


    /**
     * @param Patient $patient
     *
     * @return CActiveRecord[]
     */
    public function possibleWithdrawalEvents(Patient $patient)
    {
        if (!Yii::app()->modules['OphInDnaextraction']) {
            return array();
        }

        $criteria = new CDbCriteria();
        $criteria->condition = 'episode.patient_id = :patient_id';
        $criteria->params = array(
            'patient_id' => $patient->id,
        );

        return Element_OphInDnaextraction_DnaTests::model()->with('event', 'event.episode')->findAll($criteria);
    }

    /**
     * Some attributes are only required for a given method, this checks them
     *
     * @param $attribute
     * @param $params
     */
    public function validateForMethod($attribute, $params)
    {
        if ($this->method) {
            if (isset($params['method']) && ($params['method'] === $this->method->name && !$this->$attribute)) {
                $this->addError($attribute, 'This is required when then method is set to ' . $params['method']);
            }
        }
    }
}
