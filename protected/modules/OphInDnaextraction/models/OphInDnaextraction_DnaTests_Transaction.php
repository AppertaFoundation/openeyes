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
 * This is the model class for table "ophindnaextraction_dnatests_transaction".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property string $value
  */
class OphInDnaextraction_DnaTests_Transaction extends BaseActiveRecord
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
        return 'ophindnaextraction_dnatests_transaction';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, date, study_id, volume, comments', 'safe'),
            array('volume', 'compare', 'operator' => '>', 'compareValue' => 0),
            array('date, study_id, volume', 'required'),
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
            'element' => array(self::BELONGS_TO, 'Element_OphInDnaextraction_DnaTests', 'element_id'),
            'study'   => array(self::BELONGS_TO, 'OphInDnaextraction_DnaTests_Study', 'study_id'),
        );
    }

    public function beforeValidate()
    {
        $is_error = false;
        $posted_volume = 0;
        $transactions = Yii::app()->request->getPost('OphInDnaextraction_DnaTests_Transaction', array());

        $existing_volumes = 0;
        $new_volumes = 0;

        foreach ($transactions as $transaction) {
            $posted_volume = $posted_volume + $transaction['volume'];

            //lets collect all transactions with id, as user can modify the volume
            if ( isset($transaction['id']) && $transaction['id'] ) {
                $existing_volumes += $transaction['volume'];
            } else {
                $new_volumes += $transaction['volume'];
            }
        }

        //ok, so the all the existing and modified volumes cannot be less than the original extracted values
        if ($existing_volumes == 0 && $new_volumes > 0) {
            // if no existing value present it means the user wants to add a new transaction from elsewhere (didn't post all the transaction) or there is just none
            //we can just check the remaining value

            if (($api = Yii::app()->moduleAPI->get('OphInDnaextraction')) && isset($this->element->event_id)) {
                $volume_remaining = $api->volumeRemaining($this->element->event_id);

                if ( ($volume_remaining - $new_volumes) < 0) {
                    $is_error = true;
                }
            }
        } else {
            //this is when the POST probably coming from the update page where all the transactions listed and posted back

            $element = Element_OphInDnaextraction_DnaExtraction::model()->find('event_id = ?', array($this->element->event_id));

            //$existing_volumes means they were already saved but the user may/or may not modified them
            //so we subtract from the original value and check
            $volume_remaining = $element->volume - $existing_volumes;

            if ($volume_remaining < 0) {
                $is_error = true;
            }

            //now we have to make sure the volume wont't go below 0 when we add the new extractions
            if ( ($volume_remaining - $new_volumes) < 0 ) {
                $is_error = true;
            }
        }

        // alright, alright, what if this post coming from the create page ?
        // we have to validate the model based on only the post data
        if ( Yii::app()->controller->action->id === 'create' ) {
            $et_extraction = Yii::app()->request->getPost('Element_OphInDnaextraction_DnaExtraction');
            $volume_remaining = isset($et_extraction['volume']) ? $et_extraction['volume'] : 0;

            if ( ($volume_remaining - $new_volumes) < 0) {
                $is_error = true;
            } else {
                $is_error = false;
            }
        }

        if ($is_error) {
            $this->addError('volume', 'The remaining extraction volume cannot be less zero. Current remaining volume: ' . $volume_remaining);
        }



        return parent::beforeValidate();
    }

    public function beforeSave()
    {
        $date = new DateTime( $this->date );
        $this->date = $date->format('Y-m-d');

        return parent::beforeSave();
    }

    public function afterFind()
    {
        $date = new DateTime( $this->date );
        $this->date = $date->format('d M Y');

        return parent::afterFind();
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'date' => 'Date',
            'study_id' => 'Study',
            'volume' => 'Volume',
            'comments' => 'Withdrawn by',
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

    public function setDefaultOptions()
    {
        $this->date = date('j M Y');
    }
}
